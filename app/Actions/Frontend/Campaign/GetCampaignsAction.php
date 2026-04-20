<?php

namespace App\Actions\Frontend\Campaign;

use App\Models\Campaign;

class GetCampaignsAction
{
    /**
     * Load detailed campaign relationships for display.
     * Used by show() and edit() methods.
     *
     * @param  Campaign $campaign The campaign to load
     * @return Campaign The campaign with loaded relationships
     */
    public function forDisplay(Campaign $campaign): Campaign
    {
        return $campaign->load([
            'applications' => function ($query) {
                $query->select([
                    'id',
                    'campaign_id',
                    'influencer_id',
                    'status',
                    'work_status',
                    'pitch_message',
                    'influencer_offer',
                    'brand_offer',
                    'proposed_rate',
                    'agreed_rate',
                    'last_counter_by',
                    'last_counter_at',
                    'agreed_at',
                    'declined_at',
                    'declined_by',
                    'applied_at',
                    'decided_at',
                ])->latest('applied_at');
            },
            'applications.influencer:id,user_id,display_name',
            'applications.influencer.user:id,name',
            'applications.influencer.platformStats:id,influencer_id,follower_count,
            'targetCountries:id,campaign_id,country_code',
            'targeting:id,campaign_id,influencer_count,target_gender,age_min,age_max,notes',
            'categories:id,name',
            'followerRanges:id,label',
            'brand:id,brand_name',
            'createdBy:id,name',
            'influencerAssignments:id,campaign_id,influencer_id,status,agreed_amount,approved_at',
            'influencerAssignments.influencer:id,user_id,display_name',
            'influencerAssignments.influencer.user:id,name'
        ]);
    }

    /**
     * Load editing relationships for the campaign form.
     * Used by edit() method.
     *
     * @param  Campaign $campaign The campaign to load
     * @return Campaign The campaign with loaded relationships
     */
    public function forEditing(Campaign $campaign): Campaign
    {
        return $campaign->load([
            'applications',
            'targetCountries',
            'targeting',
            'categories',
            'followerRanges'
        ]);
    }
}
