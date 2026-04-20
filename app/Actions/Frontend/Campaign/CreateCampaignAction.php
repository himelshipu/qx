<?php

namespace App\Actions\Frontend\Campaign;

use App\Models\Campaign;
use App\Services\Admin\CampaignService;

class CreateCampaignAction
{
    public function __construct(
        protected CampaignService $campaignService
    ) {}

    /**
     * Create a new campaign for the authenticated brand.
     *
     * @param array $data Validated campaign data from StoreCampaignRequest
     * @param bool $isActive Whether the campaign is active
     * @return Campaign The created campaign
     */
    public function execute(array $data, bool $isActive = true): Campaign
    {
        return $this->campaignService->createCampaign($data, $isActive);
    }
}
