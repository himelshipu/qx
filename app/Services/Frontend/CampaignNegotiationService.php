<?php

declare(strict_types=1);

namespace App\Services\Frontend;

use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CampaignInfluencer;
use App\Models\Influencer;
use App\Services\Frontend\Contracts\CampaignNegotiationServiceInterface;

final class CampaignNegotiationService implements CampaignNegotiationServiceInterface
{
    public function apply(Campaign $campaign, Influencer $influencer, float $offer, ?string $pitchMessage = null): array
    {
        $existingApplication = $campaign->applications()
            ->withTrashed()
            ->where('influencer_id', $influencer->id)
            ->first();

        $normalizedOffer = round($offer, 2);

        if ($existingApplication && ! $existingApplication->trashed()) {
            return [
                'application' => $existingApplication,
                'message' => 'You have already applied to this campaign',
            ];
        }

        if ($existingApplication && $existingApplication->trashed()) {
            $existingApplication->restore();
            $existingApplication->update([
                'status' => 'applied',
                'pitch_message' => $pitchMessage,
                'influencer_offer' => $normalizedOffer,
                'proposed_rate' => $normalizedOffer,
                'brand_offer' => null,
                'last_counter_by' => 'influencer',
                'last_counter_at' => now(),
                'agreed_rate' => null,
                'agreed_at' => null,
                'declined_at' => null,
                'declined_by' => null,
                'applied_at' => now(),
                'decided_at' => null,
            ]);

            return [
                'application' => $existingApplication,
                'message' => 'Application re-submitted successfully.',
            ];
        }

        $application = $campaign->applications()->create([
            'influencer_id' => $influencer->id,
            'status' => 'applied',
            'pitch_message' => $pitchMessage,
            'influencer_offer' => $normalizedOffer,
            'proposed_rate' => $normalizedOffer,
            'last_counter_by' => 'influencer',
            'last_counter_at' => now(),
            'applied_at' => now(),
        ]);

        return [
            'application' => $application,
            'message' => 'Application submitted! The brand will review it and get back to you soon.',
        ];
    }

    public function brandRespond(Campaign $campaign, CampaignApplication $application, string $action, ?float $brandOffer = null, ?int $approvedByUserId = null): string
    {
        if ($action === 'counter') {
            $offer = round((float) $brandOffer, 2);

            $application->update([
                'status' => 'countered_by_brand',
                'brand_offer' => $offer,
                'last_counter_by' => 'brand',
                'last_counter_at' => now(),
                'agreed_rate' => null,
                'agreed_at' => null,
                'declined_at' => null,
                'declined_by' => null,
                'decided_at' => null,
            ]);

            return 'Counter offer sent to influencer.';
        }

        if ($action === 'decline') {
            $application->update([
                'status' => 'declined_by_brand',
                'declined_by' => 'brand',
                'declined_at' => now(),
                'decided_at' => now(),
            ]);

            $this->syncCampaignInfluencerFromApplication($campaign, $application, $approvedByUserId);

            return 'Application declined.';
        }

        $acceptedRate = $application->influencer_offer
            ?? $application->proposed_rate
            ?? $application->brand_offer;

        $application->update([
            'status' => 'approved',
            'agreed_rate' => round((float) $acceptedRate, 2),
            'agreed_at' => now(),
            'decided_at' => now(),
            'declined_at' => null,
            'declined_by' => null,
        ]);

        $this->syncCampaignInfluencerFromApplication($campaign, $application, $approvedByUserId);

        return 'Offer accepted and influencer approved successfully.';
    }

    public function influencerRespond(CampaignApplication $application, string $action, ?float $influencerOffer = null): string
    {
        $campaign = $application->campaign;

        if ($action === 'counter') {
            $offer = round((float) $influencerOffer, 2);

            $application->update([
                'status' => 'countered_by_influencer',
                'influencer_offer' => $offer,
                'proposed_rate' => $offer,
                'last_counter_by' => 'influencer',
                'last_counter_at' => now(),
                'agreed_rate' => null,
                'agreed_at' => null,
                'decided_at' => null,
                'declined_at' => null,
                'declined_by' => null,
            ]);

            return 'Counter offer sent to brand.';
        }

        if ($action === 'decline') {
            $application->update([
                'status' => 'declined_by_influencer',
                'declined_by' => 'influencer',
                'declined_at' => now(),
                'decided_at' => now(),
            ]);

            $this->syncCampaignInfluencerFromApplication($campaign, $application);

            return 'You declined this campaign offer.';
        }

        $application->update([
            'status' => 'approved',
            'agreed_rate' => round((float) $application->brand_offer, 2),
            'agreed_at' => now(),
            'decided_at' => now(),
            'declined_at' => null,
            'declined_by' => null,
        ]);

        $this->syncCampaignInfluencerFromApplication($campaign, $application);

        return 'Offer accepted. Final agreed price has been locked.';
    }

    private function syncCampaignInfluencerFromApplication(Campaign $campaign, CampaignApplication $application, ?int $approvedByUserId = null): void
    {
        $assignment = CampaignInfluencer::query()
            ->where('campaign_id', $campaign->id)
            ->where('influencer_id', $application->influencer_id)
            ->first();

        if ($application->status === 'approved') {
            $payload = [
                'campaign_id' => $campaign->id,
                'influencer_id' => $application->influencer_id,
                'status' => 'approved',
                'agreed_amount' => (float) $application->agreed_rate,
                'approved_by' => $approvedByUserId,
                'approved_at' => now(),
                'cancelled_at' => null,
                'rejection_reason' => null,
            ];

            if ($assignment) {
                $assignment->update($payload);
            } else {
                CampaignInfluencer::create($payload);
            }

            return;
        }

        if ($assignment && in_array((string) $application->status, ['declined_by_brand', 'declined_by_influencer', 'rejected'], true)) {
            $assignment->update([
                'status' => 'rejected',
                'rejection_reason' => 'Negotiation declined',
                'approved_by' => $approvedByUserId,
                'approved_at' => now(),
            ]);
        }
    }
}
