<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine if the user can view a role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can update a role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can delete a role.
     */
    public function delete(User $user, Role $role): bool
    {
        // Prevent deletion of superadmin role
        if ($role->isSuperadmin()) {
            return false;
        }

        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can assign permissions to a role.
     */
    public function assignPermissions(User $user, Role $role): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can view all roles.
     */
    public function viewAll(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can restore a role.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine if the user can permanently delete a role.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $user->isSuperadmin();
    }
}
