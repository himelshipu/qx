<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Notification;
use App\Models\Review;
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
            'status' => 'required|in:draft,published,paused,closed,archived'
        ]);

        $newStatus = $validated['status'];

        if ($campaign->status === 'closed' && $newStatus !== 'archived') {
            return response()->json([
                'success' => false,
                'message' => 'Closed campaigns can only be archived.'
            ], 422);
        }

        try {
            $campaign->update([
                'status'       => $newStatus,
                'published_at' => $newStatus === 'published' ? now() : $campaign->published_at
            ]);

            $statusLabel = match ($newStatus) {
                'draft'     => 'Draft',
                'published' => 'Published',
                'paused'    => 'Paused',
                'closed'    => 'Closed',
                'archived'  => 'Archived',
                default     => 'Unknown',
            };

            return response()->json([
                'success' => true,
                'message' => "Campaign status updated to {$statusLabel}",
                'status'       => $newStatus,
                'status_label' => $statusLabel
            ]);
        } catch (\Exception $e) {
            Log::error('Campaign status update failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update campaign status'
            ], 500);
        }
    }

    public function updateApplicationStatus(Campaign $campaign, $applicationId, Request $request): RedirectResponse | JsonResponse
    {
        $validated = $request->validate([
            'action'      => 'nullable|in:accept,counter,decline',
            'status'      => 'nullable|in:approved,rejected',
            'brand_offer' => 'nullable|numeric|min:0.01'
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

        if (!in_array($action, ['accept', 'counter', 'decline'], true)) {
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

        if (!$application->canNegotiate()) {
            $message = 'This application cannot be negotiated in its current state.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
        }

        if ($action === 'counter') {
            $offer = $validated['brand_offer'] ?? null;
            if (!is_numeric($offer) || (float) $offer <= 0) {
                $message = 'Please enter a valid counter offer amount.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }

                return redirect()->route('frontend.campaigns.show', $campaign)->with('error', $message);
            }
        }

        $allowMissingOffer = $request->routeIs('dashboard.campaigns.update-application-status', 'campaigns.update-application-status');

        if ($action === 'accept' && ! $allowMissingOffer) {
            $acceptedRate = $application->influencer_offer ?? $application->proposed_rate ?? $application->brand_offer;

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
            $allowMissingOffer,
        );

        $influencerUserId = (int) ($application->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            $title = match ($action) {
                'accept' => 'Campaign offer accepted',
                'counter' => 'New counter offer from brand',
                default => 'Campaign offer declined',
            };
            $body = match ($action) {
                'accept' => sprintf('Your application for "%s" was accepted.', $campaign->title),
                'counter' => sprintf('Brand sent a counter offer for "%s".', $campaign->title),
                default => sprintf('Brand declined your application for "%s".', $campaign->title),
            };

            Notification::create([
                'user_id' => $influencerUserId,
                'type' => 'campaign',
                'title' => $title,
                'body' => $body,
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'application_id' => $application->id,
                    'action' => $action,
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', $message);
    }

    public function respondToOffer(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user         = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || !$influencerId || (int) $application->influencer_id !== (int) $influencerId) {
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
            'action'           => 'required|in:accept,counter,decline',
            'influencer_offer' => 'nullable|numeric|min:0.01'
        ]);

        if ($validated['action'] === 'counter') {
            $offer = $validated['influencer_offer'] ?? null;
            if (!is_numeric($offer) || (float) $offer <= 0) {
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

        $brandUserId = (int) ($campaign->brand?->user_id ?? 0);
        if ($brandUserId > 0) {
            $title = match ((string) $validated['action']) {
                'accept' => 'Influencer accepted your offer',
                'counter' => 'Influencer sent a counter offer',
                default => 'Influencer declined your offer',
            };
            $body = match ((string) $validated['action']) {
                'accept' => sprintf('%s accepted your campaign offer for "%s".', $user->name, $campaign->title),
                'counter' => sprintf('%s sent a counter offer for "%s".', $user->name, $campaign->title),
                default => sprintf('%s declined your campaign offer for "%s".', $user->name, $campaign->title),
            };

            Notification::create([
                'user_id' => $brandUserId,
                'type' => 'campaign',
                'title' => $title,
                'body' => $body,
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'application_id' => $application->id,
                    'action' => (string) $validated['action'],
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

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
        if (!$influencer) {
            return redirect()->back()->with('error', 'Influencer profile not found for this account.');
        }

        $validated = request()->validate([
            'influencer_offer' => 'required|numeric|min:0.01',
            'pitch_message'    => 'nullable|string|max:2000'
        ]);

        $result = $this->campaignNegotiationService->apply(
            $campaign,
            $influencer,
            (float) $validated['influencer_offer'],
            $validated['pitch_message'] ?? null,
        );

        $brandUserId = (int) ($campaign->brand?->user_id ?? 0);
        if ($brandUserId > 0) {
            Notification::create([
                'user_id' => $brandUserId,
                'type' => 'campaign',
                'title' => 'New campaign application',
                'body' => sprintf('%s applied to "%s".', $user->name, $campaign->title),
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'influencer_id' => $influencer->id,
                ],
                'notifiable_type' => Campaign::class,
                'notifiable_id' => $campaign->id,
                'is_read' => false,
            ]);
        }

        $flashType = str_contains($result['message'], 'already applied') ? 'warning' : 'success';

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with($flashType, $result['message']);
    }

    public function withdrawApplication(CampaignApplication $application): RedirectResponse
    {
        $user         = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || !$influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized');
        }

        if (in_array($application->status, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'Cannot withdraw from an approved or completed application');
        }

        $campaign = $application->campaign;

        $brandUserId = (int) ($campaign->brand?->user_id ?? 0);
        if ($brandUserId > 0) {
            Notification::create([
                'user_id' => $brandUserId,
                'type' => 'campaign',
                'title' => 'Campaign application withdrawn',
                'body' => sprintf('%s withdrew from "%s".', $user->name, $campaign->title),
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'application_id' => $application->id,
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

        $application->delete();

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', 'Application withdrawn successfully');
    }

    public function updateInfluencerWorkStatus(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user         = Auth::user();
        $influencerId = $user->influencer?->id;
        $campaign     = $application->campaign;

        if ($user->user_type !== 'influencer' || !$influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized to update this application');
        }

        if ($application->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved applications can have their work status updated');
        }

        $validated = $request->validate([
            'work_status' => 'required|in:pending,accepted,in_progress,delivered,on_review,approved,rejected,completed',
        ]);

        $subOrder = $this->resolveCampaignSubOrder($application);

        if (!$subOrder) {
            return redirect()->back()->with('error', 'This campaign order has not been created yet. Work status can be updated once the order exists.');
        }

        $currentStatus = $this->normalizeCampaignTaskStatus((string) $subOrder->status);
        $nextStatus    = $this->normalizeCampaignTaskStatus((string) $validated['work_status']);

        if (!$this->canTransitionCampaignTask($currentStatus, $nextStatus)) {
            return redirect()->back()->with('error', 'Invalid task status transition.');
        }

        if (!$this->canInfluencerSetCampaignTaskStatus($nextStatus)) {
            return redirect()->back()->with('error', 'Influencer can only move work to In Progress or Delivered.');
        }

        $application->update([
            'work_status' => $this->persistApplicationWorkStatus($nextStatus),
        ]);

        $subOrderUpdates = ['status' => $this->persistSubOrderStatus($nextStatus)];
        if ($nextStatus === 'in_progress' && $subOrder->accepted_at === null) {
            $subOrderUpdates['accepted_at'] = now();
        }
        if ($nextStatus === 'delivered') {
            $subOrderUpdates['delivered_at'] = now();
        }
        $subOrder->update($subOrderUpdates);

        $brandUserId = (int) ($campaign->brand?->user_id ?? 0);
        if ($brandUserId > 0) {
            $statusLabel = ucfirst(str_replace('_', ' ', $nextStatus));

            Notification::create([
                'user_id' => $brandUserId,
                'type' => 'campaign',
                'title' => 'Campaign work updated',
                'body' => sprintf(
                    '%s updated the task status to %s for "%s".',
                    $user->name,
                    $statusLabel,
                    $campaign->title,
                ),
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'application_id' => $application->id,
                    'influencer_id' => $influencerId,
                    'status' => $nextStatus,
                    'status_label' => $statusLabel,
                    'icon_class' => 'blue',
                    'color_class' => 'blue',
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Work status updated successfully: ' . ucfirst(str_replace('_', ' ', $nextStatus)));
    }

    public function storeInfluencerBrandReview(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user         = Auth::user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || !$influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized');
        }

        if (!in_array((string) $application->status, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'You can review the brand only after your work is approved.');
        }

        $subOrder = $this->resolveCampaignSubOrder($application);

        if (!$subOrder) {
            return redirect()->back()->with('error', 'Campaign order is not ready for review yet.');
        }

        $normalizedStatus = $this->normalizeCampaignTaskStatus((string) $subOrder->status);
        if (!in_array($normalizedStatus, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'You can review the brand once your work is approved.');
        }

        $alreadyReviewed = Review::query()
            ->where('sub_order_id', $subOrder->id)
            ->where('reviewer_type', 'influencer')
            ->where('reviewee_type', 'brand')
            ->exists();

        if ($alreadyReviewed) {
            return redirect()->back()->with('error', 'You have already reviewed this brand for this campaign task.');
        }

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'title'   => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1200']
        ]);

        Review::create([
            'order_item_id' => null,
            'sub_order_id'  => (int) $subOrder->id,
            'brand_id'      => (int) $application->campaign->brand_id,
            'influencer_id' => (int) $influencerId,
            'reviewer_type' => 'influencer',
            'reviewee_type' => 'brand',
            'rating'        => (int) $validated['rating'],
            'title'         => $validated['title'] ?? null,
            'comment'       => $validated['comment'] ?? null,
            'is_public'     => true
        ]);

        $brandUserId = (int) ($application->campaign->brand?->user_id ?? 0);
        if ($brandUserId > 0) {
            Notification::create([
                'user_id' => $brandUserId,
                'type' => 'review',
                'title' => 'New brand review received',
                'body' => sprintf('%s submitted a review after campaign completion.', $user->name),
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $application->campaign),
                    'campaign_id' => $application->campaign_id,
                    'application_id' => $application->id,
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Your rating has been submitted successfully.');
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

        if (!in_array((string) $application->status, ['approved', 'completed'], true)) {
            return redirect()->back()->with('error', 'Work status can only be updated for approved influencers.');
        }

        $validated = $request->validate([
            'work_status' => 'required|in:pending,accepted,in_progress,delivered,on_review,approved,rejected,completed',
            'rating'      => 'nullable|integer|min:1|max:5',
            'title'       => 'nullable|string|max:120',
            'comment'     => 'nullable|string|max:1200',
        ]);

        $subOrder = $this->resolveCampaignSubOrder($application);

        if (!$subOrder) {
            return redirect()->back()->with('error', 'This campaign order has not been created yet. Work status can be updated once the order exists.');
        }

        $currentStatus = $this->normalizeCampaignTaskStatus((string) $subOrder->status);
        $nextStatus    = $this->normalizeCampaignTaskStatus((string) $validated['work_status']);

        if (!$this->canTransitionCampaignTask($currentStatus, $nextStatus)) {
            return redirect()->back()->with('error', 'Invalid task status transition.');
        }

        if (!$this->canBrandSetCampaignTaskStatus($nextStatus)) {
            return redirect()->back()->with('error', 'Brand can only approve or reject delivered work.');
        }

        if ($currentStatus === 'delivered' && $request->filled('rating')) {
            return redirect()->back()->with('error', 'Approve work first. After acceptance, submit the review.');
        }

        $applicationUpdates = [
            'work_status' => $this->persistApplicationWorkStatus($nextStatus),
        ];

        if ($nextStatus === 'approved') {
            $applicationUpdates['status'] = 'completed';
            $applicationUpdates['decided_at'] = $application->decided_at ?? now();
        }

        $application->update($applicationUpdates);

        $subOrderUpdates = ['status' => $this->persistSubOrderStatus($nextStatus)];
        if ($nextStatus === 'approved') {
            $subOrderUpdates['approved_at'] = now();
        }
        $subOrder->update($subOrderUpdates);

        $influencerUserId = (int) ($application->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            Notification::create([
                'user_id' => $influencerUserId,
                'type' => 'campaign',
                'title' => 'Campaign task reviewed by brand',
                'body' => sprintf(
                    'Brand marked your task as %s for "%s".',
                    ucfirst(str_replace('_', ' ', $nextStatus)),
                    $campaign->title,
                ),
                'data_json' => [
                    'action_url' => route('frontend.campaigns.show', $campaign),
                    'campaign_id' => $campaign->id,
                    'application_id' => $application->id,
                    'status' => $nextStatus,
                ],
                'notifiable_type' => CampaignApplication::class,
                'notifiable_id' => $application->id,
                'is_read' => false,
            ]);
        }

        $brandReview = null;
        if ($nextStatus === 'approved' && $request->filled('rating')) {
            $brandReview = Review::query()
                ->where('sub_order_id', (int) $subOrder->id)
                ->where('reviewer_type', 'brand')
                ->where('reviewee_type', 'influencer')
                ->first();

            if (! $brandReview) {
                $brandReview = Review::create([
                    'order_item_id' => null,
                    'sub_order_id'  => (int) $subOrder->id,
                    'brand_id'      => (int) $campaign->brand_id,
                    'influencer_id' => (int) $application->influencer_id,
                    'reviewer_type' => 'brand',
                    'reviewee_type' => 'influencer',
                    'rating'        => (int) $validated['rating'],
                    'title'         => $validated['title'] ?? null,
                    'comment'       => $validated['comment'] ?? null,
                    'is_public'     => true,
                ]);
            }
        }

        if ($brandReview && $brandReview->exists) {
            $message = 'Work approved and review submitted successfully.';
        } elseif ($nextStatus === 'approved') {
            $message = 'Work approved successfully. You can now leave a review.';
        } else {
            $message = 'Work status updated to ' . ucfirst(str_replace('_', ' ', $nextStatus)) . '.';
        }

        return redirect()->back()->with('success', $message);
    }

    private function normalizeCampaignTaskStatus(string $status): string
    {
        $normalized = trim($status);

        return match ($normalized) {
            'accepted' => 'in_progress',
            'on_review' => 'delivered',
            'completed' => 'approved',
            default => $normalized,
        };
    }

    private function canTransitionCampaignTask(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending' => ['in_progress'],
            'in_progress' => ['delivered'],
            'delivered' => ['approved', 'rejected'],
            'rejected' => ['in_progress'],
            'approved' => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function canInfluencerSetCampaignTaskStatus(string $status): bool
    {
        return in_array($status, ['in_progress', 'delivered'], true);
    }

    private function canBrandSetCampaignTaskStatus(string $status): bool
    {
        return in_array($status, ['approved', 'rejected'], true);
    }

    private function persistSubOrderStatus(string $normalizedStatus): string
    {
        return $normalizedStatus === 'delivered' ? 'on_review' : $normalizedStatus;
    }

    private function persistApplicationWorkStatus(string $normalizedStatus): string
    {
        return match ($normalizedStatus) {
            'delivered' => 'on_review',
            'approved' => 'completed',
            'rejected' => 'in_progress',
            default => $normalizedStatus,
        };
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
