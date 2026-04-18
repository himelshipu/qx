<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\SubOrder;
use App\Services\Frontend\Contracts\CampaignNegotiationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CampaignApplicationController extends Controller
{
    public function __construct(
        protected CampaignNegotiationServiceInterface $campaignNegotiationService,
    ) {}

    public function updateStatus(Campaign $campaign, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,published,paused,closed,archived',
        ]);

        $newStatus = $validated['status'];

        if ($campaign->status === 'closed' && $newStatus !== 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Closed campaigns can only be archived.',
            ], 422);
        }

        try {
            $campaign->update([
                'status' => $newStatus,
                'published_at' => $newStatus === 'published' ? now() : $campaign->published_at,
            ]);

            $statusLabel = match ($newStatus) {
                'draft' => 'Draft',
                'published' => 'Published',
                'paused' => 'Paused',
                'closed' => 'Closed',
                'archived' => 'Archived',
                default => 'Unknown',
            };

            return response()->json([
                'success' => true,
                'message' => "Campaign status updated to {$statusLabel}",
                'status' => $newStatus,
                'status_label' => $statusLabel,
            ]);
        } catch (\Exception $e) {
            Log::error('Campaign status update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update campaign status',
            ], 500);
        }
    }

    public function updateApplicationStatus(Campaign $campaign, $applicationId, Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'action' => 'nullable|in:accept,counter,decline',
            'status' => 'nullable|in:approved,rejected',
            'brand_offer' => 'nullable|numeric|min:0.01',
        ]);

        $application = CampaignApplication::query()
            ->where('campaign_id', $campaign->id)
            ->where('id', $applicationId)
            ->firstOrFail();

        if ($application->status === 'completed') {
            $message = 'Cannot modify completed applications. This influencer has already completed their work on this campaign.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
        }

        if ($campaign->status === 'closed') {
            $message = 'Cannot modify applications for closed campaigns. The campaign is no longer active.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
        }

        $action = (string) ($validated['action'] ?? '');
        if ($action === '') {
            $legacyStatus = (string) ($validated['status'] ?? '');
            if ($legacyStatus === 'approved') {
                $action = 'accept';
            } elseif ($legacyStatus === 'rejected') {
                $action = 'decline';
            }
        }

        if (! in_array($action, ['accept', 'counter', 'decline'], true)) {
            $message = 'Invalid negotiation action.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
        }

        if ($application->isTerminal()) {
            $message = 'This application is already finalized.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('frontend.campaigns.show', $campaign)->with('warning', $message);
        }

        if (! $application->canNegotiate()) {
            $message = 'This application cannot be negotiated in its current state.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
        }

        if ($action === 'counter') {
            $offer = $validated['brand_offer'] ?? null;
            if (! is_numeric($offer) || (float) $offer <= 0) {
                $message = 'Please enter a valid counter offer amount.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
            }
        }

        if ($action === 'accept') {
            $acceptedRate = $application->influencer_offer
                ?? $application->proposed_rate
                ?? $application->brand_offer;

            if ($acceptedRate === null || (float) $acceptedRate <= 0) {
                $message = 'No valid offer is available to accept.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
            }
        }

        $message = $this->campaignNegotiationService->brandRespond(
            $campaign,
            $application,
            $action,
            isset($validated['brand_offer']) ? (float) $validated['brand_offer'] : null,
            Auth::id(),
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', $message);
    }

    public function respondToOffer(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || ! $influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized');
        }

        $campaign = $application->campaign;

        if ($campaign->status === 'closed') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('error', 'Campaign is closed. Negotiation is unavailable.');
        }

        if ($application->isTerminal()) {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('warning', 'This application is already finalized.');
        }

        $validated = $request->validate([
            'action' => 'required|in:accept,counter,decline',
            'influencer_offer' => 'nullable|numeric|min:0.01',
        ]);

        if ($validated['action'] === 'counter') {
            $offer = $validated['influencer_offer'] ?? null;
            if (! is_numeric($offer) || (float) $offer <= 0) {
                return redirect()->route('frontend.campaigns.show', $campaign)
                    ->with('error', 'Please enter a valid counter offer amount.');
            }
        }

        if ($validated['action'] === 'accept') {
            $acceptedRate = $application->brand_offer;
            if ($acceptedRate === null || (float) $acceptedRate <= 0) {
                return redirect()->route('frontend.campaigns.show', $campaign)
                    ->with('error', 'No valid brand offer available to accept.');
            }
        }

        $message = $this->campaignNegotiationService->influencerRespond(
            $application,
            (string) $validated['action'],
            isset($validated['influencer_offer']) ? (float) $validated['influencer_offer'] : null,
        );

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', $message);
    }

    public function apply(Campaign $campaign): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'influencer') {
            return redirect()->back()->with('error', 'Only influencers can apply to campaigns');
        }

        $influencer = $user->influencer;
        if (! $influencer) {
            return redirect()->back()->with('error', 'Influencer profile not found for this account.');
        }

        $validated = request()->validate([
            'influencer_offer' => 'required|numeric|min:0.01',
            'pitch_message' => 'nullable|string|max:2000',
        ]);

        $result = $this->campaignNegotiationService->apply(
            $campaign,
            $influencer,
            (float) $validated['influencer_offer'],
            $validated['pitch_message'] ?? null,
        );

        $flashType = str_contains($result['message'], 'already applied') ? 'warning' : 'success';

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with($flashType, $result['message']);
    }

    public function withdrawApplication(CampaignApplication $application): RedirectResponse
    {
        $user = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || ! $influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized');
        }

        if (in_array($application->status, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'Cannot withdraw from an approved or completed application');
        }

        $campaign = $application->campaign;
        $application->delete();

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', 'Application withdrawn successfully');
    }

    public function updateInfluencerWorkStatus(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || ! $influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized to update this application');
        }

        if ($application->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved applications can have their work status updated');
        }

        $validated = $request->validate([
            'work_status' => 'required|in:pending,accepted,in_progress,on_review,completed',
        ]);

        $subOrder = $this->resolveCampaignSubOrder($application);

        if (! $subOrder) {
            return redirect()->back()->with('error', 'This campaign order has not been created yet. Work status can be updated once the order exists.');
        }

        $updates = ['work_status' => $validated['work_status']];
        if ($validated['work_status'] === 'completed') {
            $updates['status'] = 'completed';
            $updates['decided_at'] = now();
        }

        $application->update($updates);

        if ($subOrder) {
            $subOrderUpdates = ['status' => $validated['work_status']];
            if ($validated['work_status'] === 'accepted' && $subOrder->accepted_at === null) {
                $subOrderUpdates['accepted_at'] = now();
            }
            if ($validated['work_status'] === 'completed') {
                $subOrderUpdates['completed_at'] = now();
            }
            $subOrder->update($subOrderUpdates);
        }

        return redirect()->back()->with('success', 'Work status updated successfully: ' . ucfirst(str_replace('_', ' ', $validated['work_status'])));
    }

    public function updateBrandWorkStatus(Campaign $campaign, CampaignApplication $application, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand' || (int) $campaign->brand_id !== (int) ($user->brand?->id ?? 0)) {
            abort(403, 'Not authorized');
        }

        if ((int) $application->campaign_id !== (int) $campaign->id) {
            abort(404);
        }

        if (! in_array((string) $application->status, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'Work status can only be updated for approved influencers.');
        }

        $validated = $request->validate([
            'work_status' => 'required|in:pending,accepted,in_progress,on_review,completed',
        ]);

        $subOrder = $this->resolveCampaignSubOrder($application);

        if (! $subOrder) {
            return redirect()->back()->with('error', 'This campaign order has not been created yet. Work status can be updated once the order exists.');
        }

        $updates = ['work_status' => $validated['work_status']];
        if ($validated['work_status'] === 'completed') {
            $updates['status'] = 'completed';
            $updates['decided_at'] = $application->decided_at ?? now();
        }

        $application->update($updates);

        if ($subOrder) {
            $subOrderUpdates = ['status' => $validated['work_status']];
            if ($validated['work_status'] === 'accepted' && $subOrder->accepted_at === null) {
                $subOrderUpdates['accepted_at'] = now();
            }
            if ($validated['work_status'] === 'completed') {
                $subOrderUpdates['completed_at'] = now();
            }
            $subOrder->update($subOrderUpdates);
        }

        return redirect()->back()->with('success', 'Work status updated to ' . ucfirst(str_replace('_', ' ', $validated['work_status'])) . '.');
    }

    private function resolveCampaignSubOrder(CampaignApplication $application): ?SubOrder
    {
        return SubOrder::query()
            ->where('influencer_id', $application->influencer_id)
            ->whereHas('order', function ($query) use ($application) {
                $query->where('campaign_id', $application->campaign_id);
            })
            ->orderByDesc('id')
            ->first();
    }
}
