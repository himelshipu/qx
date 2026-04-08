<?php

namespace App\Actions\Frontend\Campaign;

use App\Models\Campaign;

class GetCampaignsAction
{
    /**
     * Load detailed campaign relationships for display.
     * Used by show() and edit() methods.
     *
     * @param Campaign $campaign The campaign to load
     * @return Campaign The campaign with loaded relationships
     */
    public function forDisplay(Campaign $campaign): Campaign
    {
        return $campaign->load([
            'applications',
            'targetCountries',
            'targeting',
            'categories',
            'brand',
            'influencerAssignments.influencer.user',
        ]);
    }

    /**
     * Load editing relationships for the campaign form.
     * Used by edit() method.
     *
     * @param Campaign $campaign The campaign to load
     * @return Campaign The campaign with loaded relationships
     */
    public function forEditing(Campaign $campaign): Campaign
    {
        return $campaign->load([
            'applications',
            'targetCountries',
            'targeting',
            'categories',
            'followerRanges',
        ]);
    }
}
