<?php

namespace App\Http\Controllers\Frontend;

use App\Actions\Frontend\Campaign\CreateCampaignAction;
use App\Actions\Frontend\Campaign\DeleteCampaignAction;
use App\Actions\Frontend\Campaign\GetCampaignsAction;
use App\Actions\Frontend\Campaign\UpdateCampaignAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Models\Campaign;
use App\Queries\Frontend\Campaign\CampaignIndexQuery;
use App\Services\Admin\CampaignService;
use App\ViewModels\Frontend\Campaign\CampaignIndexViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        protected CampaignService $campaignService,
        protected GetCampaignsAction $getCampaignsAction,
        protected CreateCampaignAction $createCampaignAction,
        protected UpdateCampaignAction $updateCampaignAction,
        protected DeleteCampaignAction $deleteCampaignAction,
    ) {}

    public function index(Request $request): View
    {
        $user = auth()->user();
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
        $this->authorize('create', Campaign::class);
        $payload = $this->campaignService->getFormPayload();

        return view('frontend.campaigns.designed-create', array_merge($payload, [
            'campaign' => null,
            'isEditMode' => false,
            'initialStep' => $this->getWizardStep(),
            'selectedBrandId' => auth()->user()->brand?->id ?? 0,
            'selectedCategoryIds' => [],
            'selectedFollowerRangeIds' => [],
            'selectedCountryCodes' => [],
            'influencerCount' => '1',
            'isAdvancedOpen' => false,
        ]));
    }

    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        Gate::authorize('create', Campaign::class);
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['brand_id'] = auth()->user()->brand?->id;

        $campaign = $this->createCampaignAction->execute($data, $request->boolean('is_active', true));

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Campaign \"{$campaign->title}\" created successfully.");
    }

    public function show(Campaign $campaign): View
    {
        $user = auth()->user();
        $this->authorizeCampaignView($user, $campaign);

        $campaign = $this->getCampaignsAction->forDisplay($campaign);

        return view('frontend.campaigns.designed-show', [
            'campaign' => $campaign,
            'invitedInfluencers' => $campaign->applications->where('status', 'invited')->values(),
            'applicationStats' => [
                'invited' => $campaign->applications->where('status', 'invited')->count(),
                'applied' => $campaign->applications->where('status', 'applied')->count(),
                'accepted' => $campaign->applications->where('status', 'accepted')->count(),
                'rejected' => $campaign->applications->where('status', 'rejected')->count(),
            ],
            'brandName' => $campaign->brand?->brand_name ?? $campaign->createdBy?->name ?? 'Unknown',
            'influencerApplication' => $user->user_type === 'influencer' ? $campaign->applications->first() : null,
        ]);
    }

    public function edit(Campaign $campaign): View
    {
        Gate::authorize('update', $campaign);
        $campaign = $this->getCampaignsAction->forEditing($campaign);
        $payload = $this->campaignService->getFormPayload();

        $campaignData = [
            'campaign_type' => $campaign->campaign_type,
            'categories' => $campaign->categories->pluck('id')->toArray(),
            'follower_ranges' => $campaign->followerRanges->pluck('id')->toArray(),
            'target_countries' => $campaign->targetCountries->pluck('country_code')->toArray(),
            'influencer_count' => $campaign->targeting?->influencer_count ?? '1',
            'target_gender' => $campaign->targeting?->target_gender,
            'age_min' => $campaign->targeting?->age_min,
            'age_max' => $campaign->targeting?->age_max,
            'targeting_notes' => $campaign->targeting?->targeting_notes,
        ];

        return view('frontend.campaigns.designed-create', array_merge($payload, [
            'campaign' => $campaign,
            'campaignData' => $campaignData,
            'isEditMode' => true,
            'initialStep' => $this->getWizardStep(),
            'selectedBrandId' => auth()->user()->brand?->id ?? 0,
            'selectedCategoryIds' => array_values(array_unique(array_map('intval', $campaignData['categories']))),
            'selectedFollowerRangeIds' => array_values(array_unique(array_map('intval', $campaignData['follower_ranges']))),
            'selectedCountryCodes' => array_values(array_unique(array_map(
                static fn($code) => strtoupper((string) $code),
                $campaignData['target_countries']
            ))),
            'influencerCount' => (string) $campaignData['influencer_count'],
            'isAdvancedOpen' => $this->hasAdvancedTargeting($campaign),
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
        $assignment->status = $validated['status'];

        if ($validated['status'] === 'approved') {
            $assignment->approved_at = now();
            $assignment->rejection_reason = null;
        } elseif ($validated['status'] === 'rejected') {
            $assignment->rejection_reason = $request->input('rejection_reason', 'Rejected by brand');
        } else {
            $assignment->approved_at = null;
            $assignment->rejection_reason = null;
        }

        $assignment->save();
        $status = match ($validated['status']) {
            'approved' => 'approved',
            'rejected' => 'rejected',
            'cancelled' => 'cancelled',
        };

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Assignment {$status} successfully.");
    }

    public function updateApplicationStatus(Campaign $campaign, $applicationId, Request $request): RedirectResponse
    {
        Gate::authorize('update', $campaign);
        $validated = $request->validate(['status' => 'required|in:approved,rejected']);

        $application = \App\Models\CampaignApplication::where('campaign_id', $campaign->id)
            ->where('id', $applicationId)
            ->firstOrFail();

        if ($application->status !== 'invited') {
            abort(422, 'Can only approve invited applications.');
        }

        $application->status = $validated['status'];
        $application->decided_at = now();
        $application->save();

        $status = $validated['status'] === 'approved' ? 'approved' : 'declined';

        return redirect()->route('frontend.campaigns.show', $campaign)
            ->with('success', "Invitation {$status} successfully.");
    }

    private function getWizardStep(): int
    {
        $stepTwoFields = ['title', 'description', 'instructions', 'status', 'currency', 'budget_min', 'budget_max', 'start_date', 'end_date'];
        $requestedStep = (int) request('wizard_step', 1);
        $errors = session('errors') ?? new \Illuminate\Support\ViewErrorBag();
        $hasErrors = collect($stepTwoFields)->contains(static fn($field): bool => $errors->has($field));

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

    private function authorizeCampaignView($user, Campaign $campaign): void
    {
        if ($user->user_type === 'brand' && $campaign->created_by !== $user->id) {
            abort(403);
        } elseif ($user->user_type === 'influencer') {
            $applied = $campaign->applications()
                ->where('influencer_id', $user->influencer->id)
                ->exists();
            if (!$applied) {
                abort(403, 'Must apply to campaign first');
            }
        }
    }
}
