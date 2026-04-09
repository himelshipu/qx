<?php

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    /**
     * Determine if the user can view the campaign.
     */
    public function view(User $user, Campaign $campaign): bool
    {
        // Admin can view all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can view campaigns assigned to them (regardless of who created it)
        if ($user->user_type === 'brand' && $campaign->brand_id === $user->brand?->id) {
            return true;
        }

        // Public campaigns can be viewed
        if ($campaign->is_active) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create campaigns.
     */
    public function create(User $user): bool
    {
        return in_array($user->user_type, ['brand', 'admin', 'superadmin']);
    }

    /**
     * Determine if the user can update the campaign.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        // Admin can update all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can update campaigns assigned to them (regardless of who created it)
        return $user->user_type === 'brand' && $campaign->brand_id === $user->brand?->id;
    }

    /**
     * Determine if the user can delete the campaign.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        // Admin can delete all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can delete campaigns assigned to them (regardless of who created it)
        return $user->user_type === 'brand' && $campaign->brand_id === $user->brand?->id;
    }
}
