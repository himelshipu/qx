<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\Frontend\Campaign\CreateCampaignAction;
use App\Actions\Frontend\Campaign\DeleteCampaignAction;
use App\Actions\Frontend\Campaign\GetCampaignsAction;
use App\Actions\Frontend\Campaign\UpdateCampaignAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\CampaignInfluencer;
use App\Models\SubOrder;
use App\Queries\Frontend\Campaign\CampaignIndexQuery;
use App\Services\Admin\CampaignService;
use App\ViewModels\Frontend\Campaign\CampaignIndexViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService      $campaignService,
        protected GetCampaignsAction   $getCampaignsAction,
        protected CreateCampaignAction $createCampaignAction,
        protected UpdateCampaignAction $updateCampaignAction,
        protected DeleteCampaignAction $deleteCampaignAction,
    ) {}

    public function index(Request $request): View
    {
        $user      = auth()->user();
        $campaigns = (new CampaignIndexQuery($user))
            ->withSearch($request->input('q', ''))
            ->withStatus($request->input('status', 'all'))
            ->withType($request->input('type', 'all'))
            ->paginate();

        $viewModel = new CampaignIndexViewModel(
            $campaigns,
            $user,
            $request->input('q', ''),
            $request->input('status', 'all'),
            $request->input('type', 'all')
        );

        return view('frontend.campaigns.designed-index', $viewModel->toArray());
    }

    public function create(): View
    {
        Gate::authorize('create', Campaign::class);
        $payload = $this->campaignService->getFormPayload();

        return view('frontend.campaigns.designed-create', array_merge($payload, [
            'campaign'                 => null,
            'isEditMode'               => false,
            'initialStep'              => $this->getWizardStep(),
            'selectedBrandId'          => auth()->user()->brand?->id ?? 0,
            'selectedCategoryIds'      => [],
            'selectedFollowerRangeIds' => [],
            'selectedCountryCodes'     => [],
            'influencerCount'          => '1',
            'isAdvancedOpen'           => false
        ]));
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        Gate::authorize('create', Campaign::class);
        $data               = $request->validated();
        $data['created_by'] = auth()->id();
        $data['brand_id']   = auth()->user()->brand?->id;

        $campaign = $this->createCampaignAction->execute($data, $request->boolean('is_active', true));

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Campaign \"{$campaign->title}\" created successfully.");
    }

    public function show(Campaign $campaign): View
    {
        $user = auth()->user();
        $this->authorizeCampaignView($user, $campaign);

        $campaign = $this->getCampaignsAction->forDisplay($campaign);

        $latestSubOrdersByInfluencer = $campaign->orders()
            ->with('subOrders')
            ->get()
            ->flatMap(static fn($order) => $order->subOrders)
            ->sortByDesc(static fn($subOrder) => $subOrder->updated_at ?? $subOrder->created_at)
            ->unique('influencer_id')
            ->keyBy('influencer_id');

            $assignmentByInfluencer = CampaignInfluencer::query()
                ->where('campaign_id', $campaign->id)
                ->get()
                ->keyBy('influencer_id');

        $progressConfig = [
            'pending' => ['label' => 'Order Pending', 'percent' => 15],
            'accepted' => ['label' => 'Accepted', 'percent' => 35],
            'in_progress' => ['label' => 'In Progress', 'percent' => 60],
            'on_review' => ['label' => 'On Review', 'percent' => 80],
            'completed' => ['label' => 'Completed', 'percent' => 100],
            'cancelled' => ['label' => 'Cancelled', 'percent' => 0],
        ];

        $workProgress = $campaign->applications
            ->whereIn('status', ['approved', 'completed'])
            ->values()
                ->map(function ($application) use ($latestSubOrdersByInfluencer, $progressConfig, $assignmentByInfluencer, $campaign) {
                $subOrder = $latestSubOrdersByInfluencer->get($application->influencer_id);
                    $assignment = $assignmentByInfluencer->get($application->influencer_id);

                // Sub-order is the canonical work record for campaign fulfillment.
                $statusKey = (string) ($subOrder?->status ?? 'pending');

                $status = $progressConfig[$statusKey] ?? $progressConfig['pending'];

                    $agreedAmount = $assignment?->agreed_amount
                        ?? $application->agreed_rate
                        ?? $application->proposed_rate;

                return [
                    'application_id' => $application->id,
                        'influencer_id' => $application->influencer_id,
                    'influencer_name' => $application->influencer->user->name ?? 'Unknown Influencer',
                    'influencer_handle' => $application->influencer->display_name ?? null,
                    'status_key' => $statusKey,
                    'status_label' => $status['label'],
                    'progress_percent' => $status['percent'],
                    'updated_at' => $subOrder?->updated_at,
                    'decided_at' => $application->decided_at,
                        'agreed_amount' => $agreedAmount,
                        'currency' => strtoupper((string) ($subOrder?->currency ?? $campaign->currency ?? 'USD')),
                ];
            });

        $influencerApplication = null;
        if ($user->user_type === 'influencer') {
            $influencerId = $user->influencer?->id;
            if ($influencerId) {
                $influencerApplication = $campaign->applications->firstWhere('influencer_id', $influencerId);
            }
        }

        return view('frontend.campaigns.designed-show', [
            'campaign'              => $campaign,
            'invitedInfluencers'    => $campaign->applications->where('status', 'invited')->values(),
            'applicationStats'      => [
                'invited'  => $campaign->applications->where('status', 'invited')->count(),
                'applied'  => $campaign->applications->where('status', 'applied')->count(),
                'approved' => $campaign->applications->where('status', 'approved')->count(),
                'rejected' => $campaign->applications->where('status', 'rejected')->count()
            ],
            'workProgress'          => $workProgress,
            'progressByApplication' => $workProgress->keyBy('application_id'),
                'assignmentByInfluencer' => $assignmentByInfluencer,
            'brandName'             => $campaign->brand?->brand_name ?? $campaign->createdBy?->name ?? 'Unknown',
            'influencerApplication' => $influencerApplication,
        ]);
    }

    public function edit(Campaign $campaign): View
    {
        Gate::authorize('update', $campaign);
        $campaign = $this->getCampaignsAction->forEditing($campaign);
        $payload  = $this->campaignService->getFormPayload();

        $campaignData = [
            'campaign_type'    => $campaign->campaign_type,
            'categories'       => $campaign->categories->pluck('id')->toArray(),
            'follower_ranges'  => $campaign->followerRanges->pluck('id')->toArray(),
            'target_countries' => $campaign->targetCountries->pluck('country_code')->toArray(),
            'influencer_count' => $campaign->targeting?->influencer_count ?? '1',
            'target_gender'    => $campaign->targeting?->target_gender,
            'age_min'          => $campaign->targeting?->age_min,
            'age_max'          => $campaign->targeting?->age_max,
            'targeting_notes'  => $campaign->targeting?->targeting_notes
        ];

        return view('frontend.campaigns.designed-create', array_merge($payload, [
            'campaign'                 => $campaign,
            'campaignData'             => $campaignData,
            'isEditMode'               => true,
            'initialStep'              => $this->getWizardStep(),
            'selectedBrandId'          => auth()->user()->brand?->id ?? 0,
            'selectedCategoryIds'      => array_values(array_unique(array_map('intval', $campaignData['categories']))),
            'selectedFollowerRangeIds' => array_values(array_unique(array_map('intval', $campaignData['follower_ranges']))),
            'selectedCountryCodes'     => array_values(array_unique(array_map(
                static fn($code) => strtoupper((string) $code),
                $campaignData['target_countries']
            ))),
            'influencerCount'          => (string) $campaignData['influencer_count'],
            'isAdvancedOpen'           => $this->hasAdvancedTargeting($campaign)
        ]));
    }

    public function update(StoreCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        Gate::authorize('update', $campaign);
        $data = $request->validated();
        $this->updateCampaignAction->execute($campaign, $data, $request->boolean('is_active', true));

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('delete', $campaign);
        $title = $campaign->title;
        $this->deleteCampaignAction->execute($campaign);

        return redirect()->route('frontend.campaigns.index')
            ->with('success', "Campaign \"{$title}\" deleted successfully.");
    }

    public function updateInfluencerStatus(Campaign $campaign, $assignmentId, Request $request): RedirectResponse
    {
        Gate::authorize('update', $campaign);
        $validated = $request->validate(['status' => 'required|in:approved,rejected,cancelled']);

        $assignment = $campaign->influencerAssignments()->findOrFail($assignmentId);

        if ($assignment->status === 'approved' && $validated['status'] === 'rejected') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('warning', 'Approved influencer cannot be declined.');
        }

        if ($assignment->status === 'rejected' && $validated['status'] === 'approved') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('warning', 'Declined influencer cannot be approved again.');
        }

        if ($assignment->status === $validated['status']) {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('info', 'No changes were made. This assignment already has that status.');
        }

        $assignment->status = $validated['status'];

        if ($validated['status'] === 'approved') {
            $assignment->approved_at      = now();
            $assignment->rejection_reason = null;
        } elseif ($validated['status'] === 'rejected') {
            $assignment->rejection_reason = $request->input('rejection_reason', 'Rejected by brand');
        } else {
            $assignment->approved_at      = null;
            $assignment->rejection_reason = null;
        }

        $assignment->save();
        $status = match ($validated['status']) {
            'approved'  => 'approved',
            'rejected'  => 'rejected',
            'cancelled' => 'cancelled',
        };

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Assignment {$status} successfully.");
    }

    public function updateApplicationStatus(Campaign $campaign, $applicationId, Request $request): RedirectResponse
    {
        Gate::authorize('update', $campaign);
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $application = \App\Models\CampaignApplication::where('campaign_id', $campaign->id)
            ->where('id', $applicationId)
            ->firstOrFail();

        if ($application->status === 'completed') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('error', 'Cannot modify completed applications. This influencer has already completed their work on this campaign.');
        }

        if ($campaign->status === 'closed') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('error', 'Cannot modify applications for closed campaigns. The campaign is no longer active.');
        }

        if ($application->status === 'approved' && $validated['status'] === 'rejected') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('warning', 'Approved influencer cannot be declined.');
        }

        if ($application->status === 'rejected' && $validated['status'] === 'approved') {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('warning', 'Declined influencer cannot be approved again.');
        }

        if ($application->status === $validated['status']) {
            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('info', 'No changes were made. This invitation already has that status.');
        }

        \Log::info('Update request', ['app_id' => $application->id, 'old_status' => $application->status, 'new_status' => $validated['status']]);

        try {
            $updateData = [
                'status'     => $validated['status'],
                'decided_at' => now()
            ];

            // Use update() instead of save() to ensure it works
            $updated = $application->update($updateData);

            \Log::info('Update result', ['app_id' => $application->id, 'updated' => $updated, 'new_status' => $validated['status']]);

            // Verify it was actually saved
            $application->refresh();
            \Log::info('After refresh', ['app_id' => $application->id, 'status' => $application->status]);
        } catch (\Exception $e) {
            \Log::error('Update failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }

        $status = $validated['status'] === 'approved' ? 'approved' : 'declined';

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Invitation {$status} successfully.");
    }

    public function updateStatus(Campaign $campaign, Request $request)
    {
        Gate::authorize('update', $campaign);
        
        $validated = $request->validate([
            'status' => 'required|in:draft,published,paused,closed,archived'
        ]);

        $newStatus = $validated['status'];

        // Prevent certain transitions
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
                default     => 'Unknown'
            };

            return response()->json([
                'success' => true,
                'message' => "Campaign status updated to {$statusLabel}",
                'status'  => $newStatus,
                'status_label' => $statusLabel
            ]);
        } catch (\Exception $e) {
            \Log::error('Campaign status update failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update campaign status'
            ], 500);
        }
    }

    private function getWizardStep(): int
    {
        $stepTwoFields = ['title', 'description', 'instructions', 'status', 'currency', 'budget_min', 'budget_max', 'start_date', 'end_date'];
        $requestedStep = (int) request('wizard_step', 1);
        $errors        = session('errors') ?? new \Illuminate\Support\ViewErrorBag();
        $hasErrors     = collect($stepTwoFields)->contains(static fn($field): bool => $errors->has($field));

        return $hasErrors ? max($requestedStep, 2) : max($requestedStep, 1);
    }

    private function hasAdvancedTargeting(Campaign $campaign): bool
    {
        return $campaign->targeting && (
            $campaign->targeting->target_gender ||
            $campaign->targeting->age_min ||
            $campaign->targeting->age_max ||
            $campaign->targeting->targeting_notes
        );
    }

    /**
     * Apply for a campaign
     */
    public function apply(Campaign $campaign): RedirectResponse
    {
        $user = auth()->user();

        if ($user->user_type !== 'influencer') {
            return redirect()->back()->with('error', 'Only influencers can apply to campaigns');
        }

        $influencer = $user->influencer;
        if (! $influencer) {
            return redirect()->back()->with('error', 'Influencer profile not found for this account.');
        }

        // Check current + soft-deleted record to avoid unique key conflicts on re-apply.
        $existingApplication = $campaign->applications()
            ->withTrashed()
            ->where('influencer_id', $influencer->id)
            ->first();

        if ($existingApplication && ! $existingApplication->trashed()) {
            return redirect()->back()->with('warning', 'You have already applied to this campaign');
        }

        if ($existingApplication && $existingApplication->trashed()) {
            $existingApplication->restore();
            $existingApplication->update([
                'status' => 'applied',
                'applied_at' => now(),
                'decided_at' => null,
            ]);

            return redirect()->route('frontend.campaigns.show', $campaign)
                ->with('success', 'Application re-submitted successfully.');
        }

        // Create application
        $campaign->applications()->create([
            'influencer_id' => $influencer->id,
            'status' => 'applied',
            'applied_at' => now(),
        ]);

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', 'Application submitted! The brand will review it and get back to you soon.');
    }

    /**
     * Withdraw application from campaign
     */
    public function withdrawApplication(CampaignApplication $application): RedirectResponse
    {
        $user = auth()->user();
        $influencerId = $user->influencer?->id;

        if ($user->user_type !== 'influencer' || ! $influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized');
        }

        // Only allow withdrawal if not approved or completed
        if (in_array($application->status, ['approved', 'completed'])) {
            return redirect()->back()->with('error', 'Cannot withdraw from an approved or completed application');
        }

        $campaign = $application->campaign;
        $application->delete();

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', 'Application withdrawn successfully');
    }

    /**
     * Update work status by influencer for their approved application
     */
    public function updateInfluencerWorkStatus(CampaignApplication $application, Request $request): RedirectResponse
    {
        $user = auth()->user();
        $influencerId = $user->influencer?->id;

        // Authorize: only influencer can update their own work status
        if ($user->user_type !== 'influencer' || ! $influencerId || (int) $application->influencer_id !== (int) $influencerId) {
            abort(403, 'Not authorized to update this application');
        }

        // Only approved applicants can update work status
        if ($application->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved applications can have their work status updated');
        }

        // Handle work status update
        $validated = $request->validate([
            'work_status' => 'required|in:pending,accepted,in_progress,on_review,completed',
        ]);

        $subOrder = $this->resolveCampaignSubOrder($application);

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

    /**
     * Brand updates approved influencer work status.
     */
    public function updateBrandWorkStatus(Campaign $campaign, CampaignApplication $application, Request $request): RedirectResponse
    {
        $user = auth()->user();

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

    private function authorizeCampaignView($user, Campaign $campaign): void
    {
        if ($user->user_type === 'brand' && $campaign->brand_id !== $user->brand?->id) {
            abort(403);
        }
        // Influencers can view all campaigns
    }
}
