<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Role;
use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function getActiveNonSuperadmin(): Collection
    {
        return Role::query()
            ->active()
            ->nonSuperadmin()
            ->orderBy('name')
            ->get();
    }

    public function getActiveNonSuperadminWithPermissionCount(): Collection
    {
        return Role::query()
            ->active()
            ->nonSuperadmin()
            ->withCount('permissions')
            ->orderBy('name')
            ->get();
    }

    public function findByIds(array $ids): Collection
    {
        return Role::query()->whereIn('id', $ids)->get();
    }

    public function findById(int $id): ?Role
    {
        return Role::query()->find($id);
    }

    public function hasPermission(int $roleId, string $permissionSlug): bool
    {
        return Role::query()
            ->whereKey($roleId)
            ->whereHas('permissions', fn ($query) => $query->where('slug', $permissionSlug))
            ->exists();
    }
}