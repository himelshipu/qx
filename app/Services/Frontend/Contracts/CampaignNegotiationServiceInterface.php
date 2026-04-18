<?php

declare(strict_types=1);

namespace App\Services\Frontend\Contracts;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Influencer;

interface CampaignNegotiationServiceInterface
{
    /**
     * @return array{application:CampaignApplication,message:string}
     */
    public function apply(Campaign $campaign, Influencer $influencer, float $offer, ?string $pitchMessage = null): array;

    public function brandRespond(Campaign $campaign, CampaignApplication $application, string $action, ?float $brandOffer = null, ?int $approvedByUserId = null): string;

    public function influencerRespond(CampaignApplication $application, string $action, ?float $influencerOffer = null): string;
}
