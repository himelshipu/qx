<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Influencer;
use App\Services\Admin\CampaignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignController extends Controller
{
    protected CampaignService $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Show the form to assign creators to a campaign.
     */
    public function assign(): View
    {
        // Get ALL campaigns (including inactive for assignment purposes) - dashboard sees all
        $campaigns = Campaign::orderByDesc('created_at')
            ->get(['id', 'title', 'description', 'campaign_type', 'status', 'start_date', 'end_date', 'budget_min', 'budget_max', 'currency']);

        // Get all creators - dashboard sees all
        $influencers = Influencer::with('user:id,email,name,phone')
            ->orderBy('display_name')
            ->get(['id', 'display_name', 'user_id']);

        // Get counts
        $activeInfluencersCount = $influencers->count();
        $activeCampaignsCount   = $campaigns->count();

        // Get latest campaigns for display purposes (latest 10)
        $latestCampaigns = Campaign::withCount(['applications', 'orders', 'orderItems', 'cartItems'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'title', 'description', 'start_date', 'end_date', 'status', 'is_active']);

        return view('backend.pages.campaigns.assign', compact('campaigns', 'influencers', 'latestCampaigns', 'activeInfluencersCount', 'activeCampaignsCount'));
    }

    /**
     * Handle assignment of influencers to a campaign.
     */
    public function assignStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campaign_id'      => 'required|exists:campaigns,id',
            'influencer_ids'   => 'required|array|min:1',
            'influencer_ids.*' => 'exists:influencers,id'
        ], [
            'influencer_ids.required' => 'Please select at least one influencer to assign to the campaign.',
            'influencer_ids.min'      => 'Please select at least one influencer to assign to the campaign.',
            'influencer_ids.*.exists' => 'One or more selected influencers are invalid.'
        ]);

        $campaignId    = $validated['campaign_id'];
        $influencerIds = $validated['influencer_ids'];

        $now     = now();
        $created = 0;

        foreach ($influencerIds as $influencerId) {
            $exists = CampaignApplication::where('campaign_id', $campaignId)
                ->where('influencer_id', $influencerId)
                ->exists();

            if (!$exists) {
                CampaignApplication::create([
                    'campaign_id'   => $campaignId,
                    'influencer_id' => $influencerId,
                    'status'        => 'invited', // valid enum value
                    'applied_at'    => $now
                ]);
                $created++;
            }
        }

        return redirect()
            ->route('dashboard.campaigns.assign')
            ->with('success', "{$created} creator(s) assigned to the campaign.");
    }

    /**
     * Get assigned influencers for a specific campaign as JSON.
     */
    public function assignedCreatorsJson(Campaign $campaign)
    {
        $assignedCreators = $campaign->applications()
            ->with(['influencer' => function ($query) {
                $query->with('user:id,email,name');
            }])
            ->get()
            ->map(function ($application) {
                return [
                    'id'           => $application->influencer->id,
                    'display_name' => $application->influencer->display_name,
                    'email'        => $application->influencer->user?->email
                ];
            });

        return response()->json($assignedCreators);
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        return view(
            'backend.pages.campaigns.create',
            $this->campaignService->getFormPayload()
        );
    }

    /**
     * Store a newly created campaign.
     */
    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        try {
            $campaign = $this->campaignService->createCampaign(
                $request->validated(),
                $request->boolean('is_active', true)
            );

            return redirect()
                ->route('dashboard.campaigns.standard')
                ->with('success', 'Campaign "' . $campaign->title . '" has been created successfully.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->with('error', 'Please fix the validation errors and try again.')
                ->withInput();

        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display a listing of standard campaigns.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');
        $type   = (string) $request->input('type', 'all');

        return view(
            'backend.pages.campaigns.index',
            $this->campaignService->getListingPayload($search, $status, $type)
        );
    }

    /**
     * Display the specified campaign.
     */
    public function view(Campaign $campaign): View
    {
        return view(
            'backend.pages.campaigns.view',
            $this->campaignService->getDetailPayload($campaign)
        );
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign): View
    {
        return view('backend.pages.campaigns.edit', [
            'campaign' => $campaign->load(['targeting', 'brand', 'categories', 'followerRanges', 'targetCountries']),
            ...$this->campaignService->getFormPayload()
        ]);
    }

    /**
     * Update the specified campaign.
     */
    public function update(StoreCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        try {
            $this->campaignService->updateCampaign(
                $campaign,
                $request->validated(),
                $request->boolean('is_active', true)
            );

            return redirect()
                ->route('dashboard.campaigns.standard')
                ->with('success', 'Campaign "' . $campaign->title . '" has been updated successfully.');

        } catch (ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->with('error', 'Please fix the validation errors and try again.')
                ->withInput();

        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete the specified campaign.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $result = $this->campaignService->deleteCampaign($campaign);

        $redirect = redirect()->route('dashboard.campaigns.standard');

        if ($result['deleted']) {
            return $redirect->with('success', 'Campaign has been deleted successfully.');
        }

        return $redirect->with('error', $result['message']);
    }
}
