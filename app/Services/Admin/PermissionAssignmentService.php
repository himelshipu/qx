<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Role;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;

final class PermissionAssignmentService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly PermissionRepositoryInterface $permissionRepository,
    ) {
    }

    /**
     * @return array{roles:\Illuminate\Database\Eloquent\Collection<int,\App\Models\Role>,permissions:\Illuminate\Support\Collection<string,\Illuminate\Database\Eloquent\Collection<int,\App\Models\Permission>>}
     */
    public function getAssignPayload(): array
    {
        return [
            'roles' => $this->roleRepository->getActiveNonSuperadmin(),
            'permissions' => $this->permissionRepository->getActiveGroupedByModule(),
        ];
    }

    /**
     * @param array<int, int> $permissionIds
     * @return array{role:Role,previousPermissions:array<int,int>,newPermissions:array<int,int>}
     */
    public function assignPermissions(int $roleId, array $permissionIds): array
    {
        $role = $this->roleRepository->findById($roleId);
        if (!$role) {
            throw new \RuntimeException('Role not found.');
        }

        $previousPermissions = $role->permissions()->pluck('permissions.id')->map(fn ($id) => (int) $id)->all();
        $newPermissions = array_values(array_unique(array_map('intval', $permissionIds)));

        $role->syncPermissions($newPermissions);

        return [
            'role' => $role,
            'previousPermissions' => $previousPermissions,
            'newPermissions' => $newPermissions,
        ];
    }
}