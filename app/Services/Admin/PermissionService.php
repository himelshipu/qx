<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Support\Str;

final class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository
    ) {
    }

    /**
     * @return array{permissions:\Illuminate\Contracts\Pagination\LengthAwarePaginator,groupedPermissions:\Illuminate\Support\Collection<string,\Illuminate\Database\Eloquent\Collection<int,\App\Models\Permission>>}
     */
    public function getIndexPayload(): array
    {
        return [
            'permissions' => $this->permissionRepository->paginateWithRoles(20),
            'groupedPermissions' => $this->permissionRepository->getActiveGroupedByModuleWithRoles(),
        ];
    }

    public function getPermissionById(int $id): Permission
    {
        return $this->permissionRepository->findByIdOrFail($id);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function createPermission(array $validated, bool $isActive): Permission
    {
        return $this->permissionRepository->create([
            'name' => (string) $validated['name'],
            'slug' => Str::slug((string) $validated['name']),
            'description' => $validated['description'] ?? null,
            'module' => (string) $validated['module'],
            'is_active' => $isActive,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function updatePermission(int $id, array $validated, bool $isActive): Permission
    {
        $permission = $this->permissionRepository->findByIdOrFail($id);

        return $this->permissionRepository->update($permission, [
            'name' => (string) $validated['name'],
            'slug' => Str::slug((string) $validated['name']),
            'description' => $validated['description'] ?? null,
            'module' => (string) $validated['module'],
            'is_active' => $isActive,
        ]);
    }

    public function deletePermission(int $id): bool
    {
        $permission = $this->permissionRepository->findByIdOrFail($id);

        $permission->roles()->detach();

        return $this->permissionRepository->delete($permission);
    }

    public function toggleStatus(int $id): bool
    {
        $permission = $this->permissionRepository->findByIdOrFail($id);

        return $this->permissionRepository->toggleStatus($permission)->is_active;
    }
}
