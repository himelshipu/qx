<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Http\Requests\Backend\Campaign\UpdateCampaignRequest;
use App\Models\Campaign;
use App\Services\Admin\CampaignService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function __construct(
        private readonly CampaignService $campaignService
    ) {}

    /**
     * Display a listing of campaigns with search and filters.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $type   = (string) $request->string('type', 'all');

        return view('backend.pages.campaigns.index', $this->campaignService->getListingPayload($search, $status, $type));
    }

    /**
     * Display the original designed campaign listing with real data.
     */
    public function indexDesigned(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $type   = (string) $request->string('type', 'all');

        return view('backend.pages.campaigns.designed-index', $this->campaignService->getListingPayload($search, $status, $type));
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        return view('backend.pages.campaigns.create', $this->campaignService->getFormPayload());
    }

    /**
     * Show the original designed wizard for creating a campaign.
     */
    public function createDesigned(): View
    {
        return view('backend.pages.campaigns.designed-create', $this->campaignService->getFormPayload());
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

            $redirectRoute = $request->input('ui_variant') === 'designed'
            ? 'dashboard.campaigns.designed'
            : 'dashboard.campaigns.index';

            return redirect()
                ->route($redirectRoute)
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
                ->with('error', 'Failed to create campaign. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified campaign details.
     */
    public function view(Campaign $campaign): View
    {
        $this->ensureCampaignAccess($campaign);

        return view('backend.pages.campaigns.view', $this->campaignService->getDetailPayload($campaign));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign): View
    {
        $this->ensureCampaignAccess($campaign);

        return view('backend.pages.campaigns.edit', [
            'campaign' => $campaign->load([
                'targeting',
                'categories:id,name',
                'followerRanges:id,label',
                'targetCountries:id,campaign_id,country_code'
            ]),
            ...$this->campaignService->getFormPayload()
        ]);
    }

    /**
     * Update the specified campaign.
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $this->ensureCampaignAccess($campaign);

        try {
            $updatedCampaign = $this->campaignService->updateCampaign(
                $campaign,
                $request->validated(),
                $request->boolean('is_active')
            );

            return redirect()
                ->route('dashboard.campaigns.index')
                ->with('success', 'Campaign "' . $updatedCampaign->title . '" has been updated successfully.');
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
                ->with('error', 'Failed to update campaign. Please try again.')
                ->withInput();
        }
    }

    /**
     * Remove the specified campaign if no dependencies exist.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $this->ensureCampaignAccess($campaign);

        try {
            $campaignTitle = $campaign->title;
            $result        = $this->campaignService->deleteCampaign($campaign);

            if (!$result['deleted']) {
                return redirect()
                    ->route('dashboard.campaigns.index')
                    ->with('warning', $result['message']);
            }

            return redirect()
                ->route('dashboard.campaigns.index')
                ->with('success', 'Campaign "' . $campaignTitle . '" has been deleted successfully.');
        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->route('dashboard.campaigns.index')
                ->with('error', 'Failed to delete campaign. Please try again.');
        }
    }

    private function ensureCampaignAccess(Campaign $campaign): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            abort(403);
        }

        if ((string) $authUser->user_type !== 'brand') {
            return;
        }

        $brandId = (int) ($authUser->brand?->id ?? 0);
        abort_unless($brandId > 0 && $campaign->brand_id === $brandId, 403);
    }
}
