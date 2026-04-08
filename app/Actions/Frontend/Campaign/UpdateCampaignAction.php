<?php

namespace App\Actions\Frontend\Campaign;

use App\Models\Campaign;
use App\Services\Admin\CampaignService;

class UpdateCampaignAction
{
    public function __construct(
        protected CampaignService $campaignService
    ) {}

    /**
     * Update an existing campaign.
     *
     * @param Campaign $campaign The campaign to update
     * @param array $data Validated campaign data from StoreCampaignRequest
     * @param bool $isActive Whether the campaign is active
     * @return Campaign The updated campaign
     */
    public function execute(Campaign $campaign, array $data, bool $isActive = true): Campaign
    {
        return $this->campaignService->updateCampaign($campaign, $data, $isActive);
    }
}
