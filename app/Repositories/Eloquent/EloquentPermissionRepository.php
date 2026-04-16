<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Permission;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

final class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    public function paginateWithRoles(int $perPage = 20): LengthAwarePaginator
    {
        return Permission::query()
            ->with('roles')
            ->dashboardOrder()
            ->paginate($perPage);
    }

    public function getActiveGroupedByModule(): SupportCollection
    {
        return Permission::query()
            ->active()
            ->dashboardOrder()
            ->get()
            ->groupBy('module');
    }

    public function getActiveGroupedByModuleWithRoles(): SupportCollection
    {
        return Permission::query()
            ->with('roles')
            ->active()
            ->dashboardOrder()
            ->get()
            ->groupBy('module');
    }

    public function getActive(): Collection
    {
        return Permission::query()
            ->active()
            ->dashboardOrder()
            ->get();
    }

    public function findByIdOrFail(int $id): Permission
    {
        return Permission::query()->with('roles')->findOrFail($id);
    }

    public function create(array $data): Permission
    {
        return Permission::query()->create($data);
    }

    public function update(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission->refresh();
    }

    public function delete(Permission $permission): bool
    {
        return (bool) $permission->delete();
    }

    public function toggleStatus(Permission $permission): Permission
    {
        $permission->update([
            'is_active' => !$permission->is_active,
        ]);

        return $permission->refresh();
    }
}