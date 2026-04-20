<?php

namespace App\Actions\Frontend\Campaign;

use App\Models\Campaign;

class DeleteCampaignAction
{
    /**
     * Delete a campaign.
     *
     * @param Campaign $campaign The campaign to delete
     * @return bool True if successfully deleted
     */
    public function execute(Campaign $campaign): bool
    {
        return (bool) $campaign->delete();
    }
}
