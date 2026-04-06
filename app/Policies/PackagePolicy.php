<?php

namespace App\Policies;

use App\Models\Package;
use App\Models\User;

class PackagePolicy
{
    /**
     * Determine if the user can view the package.
     */
    public function view(User $user, Package $package): bool
    {
        // Admin can view all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Creator can view their own packages
        if ($user->user_type === 'creator' && $package->creator_id === $user->creator?->id) {
            return true;
        }

        // Public packages can be viewed by anyone
        if ($package->is_active) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create packages.
     */
    public function create(User $user): bool
    {
        return in_array($user->user_type, ['creator', 'admin', 'superadmin']);
    }

    /**
     * Determine if the user can update the package.
     */
    public function update(User $user, Package $package): bool
    {
        // Admin can update all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Creator can update only their own

        return $user->user_type === 'creator' && $package->creator_id === $user->creator?->id;
    }

    /**
     * Determine if the user can delete the package.
     */
    public function delete(User $user, Package $package): bool
    {
        // Admin can delete all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Creator can delete only their own

        return $user->user_type === 'creator' && $package->creator_id === $user->creator?->id;
    }
}
