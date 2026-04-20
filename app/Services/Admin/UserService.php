<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;

final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    /**
     * @return array{users:LengthAwarePaginator,stats:array{total:int,brands:int,influencers:int,moderators:int,admins:int,active:int,inactive:int},search:string,status:string,role:string,roles:\Illuminate\Database\Eloquent\Collection<int,\App\Models\Role>}
     */
    public function getListingPayload(string $search, string $status, string $role): array
    {
        $roleId = $role !== '' ? (int) $role : null;

        return [
            'users' => $this->userRepository->paginateForDashboard(
                $search,
                $status,
                $roleId,
                (int) config('user.dashboard.per_page', 12)
            ),
            'stats' => $this->userRepository->getDashboardStats(),
            'search' => $search,
            'status' => $status,
            'role' => $role,
            'roles' => $this->roleRepository->getActiveNonSuperadmin(),
        ];
    }

    public function toggleStatus(User $user): bool
    {
        $updated = $this->userRepository->toggleStatus($user);

        Notification::create([
            'user_id' => $updated->id,
            'type' => 'account',
            'title' => 'Account status updated',
            'body' => sprintf('Your account is now %s.', $updated->is_active ? 'active' : 'inactive'),
            'data_json' => [
                'user_id' => $updated->id,
                'is_active' => $updated->is_active,
            ],
            'notifiable_type' => User::class,
            'notifiable_id' => $updated->id,
            'is_read' => false,
        ]);

        return $updated->is_active;
    }

    /**
     * @return array{users:\Illuminate\Database\Eloquent\Collection<int,\App\Models\User>,roles:\Illuminate\Database\Eloquent\Collection<int,\App\Models\Role>}
     */
    public function getAssignRolePayload(): array
    {
        return [
            'users' => $this->userRepository->getAssignableUsers(),
            'roles' => $this->roleRepository->getActiveNonSuperadminWithPermissionCount(),
        ];
    }

    /**
     * @return array<int, int>
     */
    public function getUserRoleIds(User $user): array
    {
        return $this->userRepository->getRoleIds($user);
    }

    /**
     * @param array<int, int> $roleIds
     * @return array{user:User,message:string}
     */
    public function assignRoles(User $user, array $roleIds, int $actorUserId): array
    {
        if ($user->isSuperadmin()) {
            throw new \RuntimeException('Cannot modify superadmin user roles.');
        }

        $roleIds = array_values(array_unique(array_map('intval', $roleIds)));

        if ($roleIds !== []) {
            $roles = $this->roleRepository->findByIds($roleIds);
            $invalidSuperadminRole = $roles->contains(fn ($role) => $role->is_superadmin);
            if ($invalidSuperadminRole) {
                throw new \RuntimeException('Cannot assign superadmin roles through this interface.');
            }

            if ($user->id === $actorUserId) {
                $hasDashboardAccess = $roles->contains(function ($role): bool {
                    return $this->roleRepository->hasPermission((int) $role->id, 'dashboard.view');
                });

                if (!$hasDashboardAccess) {
                    throw new \RuntimeException('You cannot remove your own dashboard access.');
                }
            }

            $this->userRepository->syncRoles($user, $roleIds);

            $primaryRole = $roles->first();
            if ($primaryRole) {
                $this->userRepository->update($user, ['user_type' => strtolower((string) $primaryRole->name)]);
            }

            $this->notifyRoleChange($user, $roles->pluck('name')->all(), 'Your roles were updated by an administrator.');

            return [
                'user' => $user->fresh('roles'),
                'message' => 'Roles assigned to user successfully.',
            ];
        }

        $this->userRepository->syncRoles($user, []);

        $this->notifyRoleChange($user, [], 'All of your dashboard roles were removed by an administrator.');

        return [
            'user' => $user->fresh('roles'),
            'message' => 'All roles removed from user.',
        ];
    }

    /**
     * @return array{roles:\Illuminate\Database\Eloquent\Collection<int,\App\Models\Role>}
     */
    public function getCreatePayload(): array
    {
        return [
            'roles' => $this->roleRepository->getActiveNonSuperadmin(),
        ];
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function createUser(array $validated, ?UploadedFile $profileImage, ?UploadedFile $coverImage): User
    {
        $role = $this->roleRepository->findById((int) $validated['role_id']);
        if (!$role || $role->is_superadmin) {
            throw new \RuntimeException('Cannot assign superadmin role through this interface.');
        }

        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt((string) $validated['password']),
            'user_type' => strtolower((string) $role->name),
            'is_active' => true,
            'phone' => $validated['phone'] ?? null,
            'address_line' => $validated['address_line'] ?? null,
            'profile_image_path' => $this->storeUploadedAsset($profileImage, 'users/profiles'),
            'cover_image_path' => $this->storeUploadedAsset($coverImage, 'users/covers'),
        ]);

        $this->userRepository->syncRoles($user, [(int) $role->id]);

        $this->notifyRoleChange($user, [$role->name], 'Your account was created with an assigned role.');

        return $user->fresh('roles');
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function updateUser(User $user, array $validated, ?UploadedFile $profileImage, ?UploadedFile $coverImage, int $actorUserId): User
    {
        if ($user->isSuperadmin()) {
            throw new \RuntimeException('Cannot edit superadmin users.');
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address_line' => $validated['address_line'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = bcrypt((string) $validated['password']);
        }

        if ($profileImage) {
            $userData['profile_image_path'] = $this->storeUploadedAsset($profileImage, 'users/profiles');
        }

        if ($coverImage) {
            $userData['cover_image_path'] = $this->storeUploadedAsset($coverImage, 'users/covers');
        }

        $this->userRepository->update($user, $userData);

        if (!empty($validated['role_id'])) {
            $role = $this->roleRepository->findById((int) $validated['role_id']);
            if (!$role || $role->is_superadmin) {
                throw new \RuntimeException('Cannot assign superadmin role through this interface.');
            }

            if ($user->id === $actorUserId && !$this->roleRepository->hasPermission((int) $role->id, 'dashboard.view')) {
                throw new \RuntimeException('You cannot remove your own dashboard access.');
            }

            $this->userRepository->syncRoles($user, [(int) $role->id]);
            $this->userRepository->update($user, ['user_type' => strtolower((string) $role->name)]);

            $this->notifyRoleChange($user, [$role->name], 'Your role was updated by an administrator.');
        }

        return $user->fresh('roles');
    }

    private function storeUploadedAsset(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    private function notifyRoleChange(User $user, array $roleNames, string $body): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'role',
            'title' => 'Account access updated',
            'body' => $body,
            'data_json' => [
                'roles' => array_values($roleNames),
            ],
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'is_read' => false,
        ]);
    }
}