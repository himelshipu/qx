<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\Frontend\Campaign\CreateCampaignAction;
use App\Actions\Frontend\Campaign\DeleteCampaignAction;
use App\Actions\Frontend\Campaign\GetCampaignsAction;
use App\Actions\Frontend\Campaign\UpdateCampaignAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignInfluencer;
use App\Queries\Frontend\Campaign\CampaignIndexQuery;
use App\Services\Admin\CampaignService;
use App\ViewModels\Frontend\Campaign\CampaignIndexViewModel;
use App\ViewModels\Frontend\Campaign\CampaignShowViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
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
        $user      = Auth::user();
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
            'selectedBrandId'          => Auth::user()->brand?->id ?? 0,
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
        $data['created_by'] = Auth::id();
        $data['brand_id']   = Auth::user()->brand?->id;

        $campaign = $this->createCampaignAction->execute($data, $request->boolean('is_active', true));

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Campaign \"{$campaign->title}\" created successfully.");
    }

    public function show(Campaign $campaign): View
    {
        $user = Auth::user();
        $this->authorizeCampaignView($user, $campaign);

        $campaign = $this->getCampaignsAction->forDisplay($campaign);

        // Load fresh SubOrder data from database to sync with admin dashboard updates
        // Don't use campaign relationship to avoid any caching
        $campaignOrders = \App\Models\Order::where('campaign_id', $campaign->id)
            ->with([
                'subOrders' => fn($q) => $q->select('id', 'order_id', 'campaign_influencer_id', 'influencer_id', 'status', 'amount', 'currency', 'accepted_at', 'completed_at', 'paid_at', 'created_at', 'updated_at'),
                'subOrders.deliverables:id,sub_order_id,uploaded_by_user_id,deliverable_type,file_path,external_url,notes,status,created_at',
                'subOrders.deliverables.uploadedBy:id,name'
            ])
            ->get();

        $latestSubOrdersByInfluencer = $campaignOrders
            ->flatMap(fn($order) => $order->subOrders)
            ->sortByDesc(fn($subOrder) => $subOrder->updated_at ?? $subOrder->created_at)
            ->unique('influencer_id')
            ->keyBy('influencer_id');

        $assignmentByInfluencer = CampaignInfluencer::query()
            ->where('campaign_id', $campaign->id)
            ->get()
            ->keyBy('influencer_id');

        $progressConfig = [
            'pending'     => ['label' => 'Order Pending', 'percent' => 15],
            'accepted'    => ['label' => 'Accepted', 'percent' => 35],
            'in_progress' => ['label' => 'In Progress', 'percent' => 60],
            'on_review'   => ['label' => 'On Review', 'percent' => 80],
            'completed'   => ['label' => 'Completed', 'percent' => 100],
            'cancelled'   => ['label' => 'Cancelled', 'percent' => 0]
        ];

        $workProgress = $campaign->applications
            ->whereIn('status', ['approved', 'completed'])
            ->values()
            ->map(function ($application) use ($latestSubOrdersByInfluencer, $progressConfig, $assignmentByInfluencer, $campaign) {
                $subOrder   = $latestSubOrdersByInfluencer->get($application->influencer_id);
                $assignment = $assignmentByInfluencer->get($application->influencer_id);

                // Sub-order is the canonical work record for campaign fulfillment.
                $statusKey = (string) ($subOrder?->status ?? 'pending');

                $status = $progressConfig[$statusKey] ?? $progressConfig['pending'];

                $agreedAmount = $assignment?->agreed_amount ?? $application->agreed_rate ?? $application->proposed_rate;

                return [
                    'application_id'    => $application->id,
                    'influencer_id'     => $application->influencer_id,
                    'influencer_name'   => $application->influencer->user->name ?? 'Unknown Influencer',
                    'influencer_handle' => $application->influencer->display_name ?? null,
                    'status_key'        => $statusKey,
                    'status_label'      => $status['label'],
                    'progress_percent'  => $status['percent'],
                    'updated_at'        => $subOrder?->updated_at,
                    'decided_at'        => $application->decided_at,
                    'agreed_amount'     => $agreedAmount,
                    'currency'          => strtoupper((string) ($subOrder?->currency ?? $campaign->currency ?? 'USD'))
                ];
            });

        $influencerApplication = null;
        if ($user->user_type === 'influencer') {
            $influencerId = $user->influencer?->id;
            if ($influencerId) {
                $influencerApplication = $campaign->applications->firstWhere('influencer_id', $influencerId);
            }
        }

        $showViewModel = new CampaignShowViewModel(
            $campaign,
            $workProgress,
            $assignmentByInfluencer,
            $influencerApplication,
        );

        $showPayload = $showViewModel->toArray();

        return view('frontend.campaigns.designed-show', array_merge([
            'campaign'                    => $campaign,
            'invitedInfluencers'          => $campaign->applications->where('status', 'invited')->values(),
            'workProgress'                => $showPayload['workProgressCards'],
            'progressByApplication'       => $workProgress->keyBy('application_id'),
            'assignmentByInfluencer'      => $assignmentByInfluencer,
            'latestSubOrdersByInfluencer' => $latestSubOrdersByInfluencer,
            'brandName'                   => $campaign->brand?->brand_name ?? $campaign->createdBy?->name ?? 'Unknown',
            'influencerApplication'       => $influencerApplication
        ], $showPayload));
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
            'targeting_notes'  => $campaign->targeting?->notes
        ];

        return view('frontend.campaigns.designed-create', array_merge($payload, [
            'campaign'                 => $campaign,
            'campaignData'             => $campaignData,
            'isEditMode'               => true,
            'initialStep'              => $this->getWizardStep(),
            'selectedBrandId'          => Auth::user()->brand?->id ?? 0,
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
            $campaign->targeting->notes
        );
    }

    private function authorizeCampaignView($user, Campaign $campaign): void
    {
        if ($user->user_type === 'brand' && $campaign->brand_id !== $user->brand?->id) {
            abort(403);
        }
        // Influencers can view all campaigns
    }
}
