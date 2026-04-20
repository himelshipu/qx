<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

/**
 * RoleService
 *
 * Manages role operations including creation, updating,
 * permission assignment, and superadmin status checks.
 */
class RoleService
{
    /**
     * Create a new role.
     *
     * @param  array  $data Contains: name, slug, description (optional), is_active, is_superadmin (optional)
     * @return Role
     */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    /**
     * Update an existing role.
     *
     * @param  Role   $role
     * @param  array  $data
     * @return bool
     */
    public function updateRole(Role $role, array $data): bool
    {
        return $role->update($data);
    }

    /**
     * Delete a role.
     *
     * @param  Role   $role
     * @return bool
     */
    public function deleteRole(Role $role): bool
    {
        return (bool) $role->delete();
    }

    /**
     * Get all roles.
     *
     * @param  bool         $activeOnly If true, only return active roles
     * @return Collection
     */
    public function getAllRoles(bool $activeOnly = false): Collection
    {
        $query = Role::query();

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /**
     * Get all superadmin roles.
     *
     * @return Collection
     */
    public function getSuperadminRoles(): Collection
    {
        return Role::where('is_superadmin', true)->get();
    }

    /**
     * Check if a role is a superadmin role.
     *
     * @param  Role   $role
     * @return bool
     */
    public function isSuperadmin(Role $role): bool
    {
        return $role->isSuperadmin();
    }

    /**
     * Assign a permission to a role.
     *
     * @param  Role              $role
     * @param  Permission|string $permission Permission instance or slug
     * @return void
     */
    public function assignPermissionToRole(Role $role, $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $role->givePermissionTo($permission);
    }

    /**
     * Revoke a permission from a role.
     *
     * @param  Role              $role
     * @param  Permission|string $permission Permission instance or slug
     * @return void
     */
    public function revokePermissionFromRole(Role $role, $permission): void
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        $role->revokePermissionTo($permission);
    }

    /**
     * Get all permissions for a role.
     *
     * @param  Role         $role
     * @return Collection
     */
    public function getRolePermissions(Role $role): Collection
    {
        return $role->permissions;
    }

    /**
     * Check if a role has a specific permission.
     *
     * @param  Role   $role
     * @param  string $permissionSlug
     * @return bool
     */
    public function roleHasPermission(Role $role, string $permissionSlug): bool
    {
        return $role->hasPermission($permissionSlug);
    }

    /**
     * Sync permissions for a role (replaces existing).
     *
     * @param  Role   $role
     * @param  array  $permissionSlugs
     * @return void
     */
    public function syncPermissionsForRole(Role $role, array $permissionSlugs): void
    {
        $permissions = Permission::whereIn('slug', $permissionSlugs)->get();
        $role->permissions()->sync($permissions->pluck('id')->toArray());
    }

    /**
     * Get all users with a specific role.
     *
     * @param  Role         $role
     * @return Collection
     */
    public function getUsersWithRole(Role $role): Collection
    {
        return $role->users;
    }

    /**
     * Make a role a superadmin.
     *
     * @param  Role   $role
     * @return bool
     */
    public function markAsSuperadmin(Role $role): bool
    {
        return $this->updateRole($role, ['is_superadmin' => true]);
    }

    /**
     * Remove superadmin status from a role.
     *
     * @param  Role   $role
     * @return bool
     */
    public function removeSuperadminStatus(Role $role): bool
    {
        return $this->updateRole($role, ['is_superadmin' => false]);
    }
}
