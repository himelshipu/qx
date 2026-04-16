<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EloquentUserRepository
 *
 * Handles user data access operations using Eloquent ORM.
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    /**
     * Get all users.
     *
     * @return Collection<int, User>
     */
    public function getAll(): Collection
    {
        return User::all();
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Create a new user.
     *
     * @param array<string, mixed> $data
     * @return User
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array<string, mixed> $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function paginateForDashboard(string $search, string $status, ?int $roleId, int $perPage): LengthAwarePaginator
    {
        return User::query()
            ->with([
                'brand:id,user_id',
                'influencer:id,user_id',
                'roles:id,name',
            ])
            ->forDashboard()
            ->dashboardUserTypes()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardRole($roleId)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getDashboardStats(): array
    {
        $base = User::query()->dashboardUserTypes();

        return [
            'total' => (clone $base)->count(),
            'brands' => (clone $base)->where('user_type', 'brand')->count(),
            'influencers' => (clone $base)->where('user_type', 'influencer')->count(),
            'moderators' => (clone $base)->where('user_type', 'moderator')->count(),
            'admins' => (clone $base)->where('user_type', 'admin')->count(),
            'active' => (clone $base)->where('is_active', true)->count(),
            'inactive' => (clone $base)->where('is_active', false)->count(),
        ];
    }

    public function getAssignableUsers(): Collection
    {
        return User::query()
            ->whereNotIn('user_type', ['brand', 'influencer'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'user_type']);
    }

    public function getRoleIds(User $user): array
    {
        return $user->roles()->pluck('roles.id')->map(fn ($id) => (int) $id)->all();
    }

    public function syncRoles(User $user, array $roleIds): void
    {
        $user->roles()->sync($roleIds);
    }

    public function toggleStatus(User $user): User
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return $user->fresh();
    }
}
