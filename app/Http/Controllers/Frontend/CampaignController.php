<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Campaign\StoreCampaignRequest;
use App\Models\Campaign;
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
     * Display a listing of campaigns.
     * Brands see: their own campaigns
     * Creators see: campaigns they've applied to
     */
    public function index(Request $request): View
    {
        $user   = auth()->user();
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');
        $type   = (string) $request->input('type', 'all');

        // Status filter options
        $statusOptions = [
            ['value' => 'all', 'label' => 'All Status'],
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'published', 'label' => 'Published'],
            ['value' => 'paused', 'label' => 'Paused'],
            ['value' => 'closed', 'label' => 'Closed'],
            ['value' => 'archived', 'label' => 'Archived']
        ];

        // Campaign type filter options
        $typeOptions = [
            ['value' => 'all', 'label' => 'All Types'],
            ['value' => 'facebook', 'label' => 'Facebook'],
            ['value' => 'instagram', 'label' => 'Instagram'],
            ['value' => 'tiktok', 'label' => 'TikTok'],
            ['value' => 'linkedin', 'label' => 'LinkedIn'],
            ['value' => 'x', 'label' => 'X'],
            ['value' => 'youtube', 'label' => 'YouTube'],
            ['value' => 'ugc', 'label' => 'UGC'],
            ['value' => 'other', 'label' => 'Other']
        ];

        if ($user->user_type === 'brand') {
            // Brand sees their own campaigns
            $campaigns = Campaign::where('created_by', $user->id)
                ->where('is_active', true)
                ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->when($type !== 'all', fn($q) => $q->where('campaign_type', $type))
                ->orderByDesc('created_at')
                ->paginate(12);

            return view('frontend.campaigns.designed-index', [
                'campaigns'     => $campaigns,
                'userType'      => 'brand',
                'search'        => $search,
                'status'        => $status,
                'type'          => $type,
                'statusOptions' => $statusOptions,
                'typeOptions'   => $typeOptions
            ]);
        } elseif ($user->user_type === 'creator') {
            // Creator sees campaigns they've applied to
            $campaigns = Campaign::whereHas('applications', function ($query) use ($user) {
                $query->where('creator_id', $user->creator->id);
            })
                ->where('is_active', true)
                ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->when($type !== 'all', fn($q) => $q->where('campaign_type', $type))
                ->orderByDesc('created_at')
                ->paginate(12);

            return view('frontend.campaigns.designed-index', [
                'campaigns'     => $campaigns,
                'userType'      => 'creator',
                'search'        => $search,
                'status'        => $status,
                'type'          => $type,
                'statusOptions' => $statusOptions,
                'typeOptions'   => $typeOptions
            ]);
        }

        abort(403, 'Unauthorized');
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): View
    {
        $user = auth()->user();

        if ($user->user_type !== 'brand') {
            abort(403, 'Unauthorized');
        }

        return view(
            'frontend.campaigns.designed-create',
            array_merge(
                $this->campaignService->getFormPayload(),
                ['campaign' => null]
            )
        );
    }

    /**
     * Store a newly created campaign.
     */
    public function store(StoreCampaignRequest $request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->user_type !== 'brand') {
            abort(403, 'Unauthorized');
        }

        try {
            $data               = $request->validated();
            $data['created_by'] = $user->id;
            $data['brand_id']   = $user->brand?->id;

            $campaign = $this->campaignService->createCampaign(
                $data,
                $request->boolean('is_active', true)
            );

            return redirect()
                ->route('frontend.campaigns.show', $campaign)
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
     * Display the specified campaign.
     */
    public function show(Campaign $campaign): View
    {
        $user = auth()->user();

        // Authorization:
        // - Brands can see their own campaigns
        // - Creators can see campaigns they've applied to
        if ($user->user_type === 'brand') {
            if ($campaign->created_by !== $user->id) {
                abort(403, 'Unauthorized');
            }
        } elseif ($user->user_type === 'creator') {
            // Check if creator has applied to this campaign
            $applied = $campaign->applications()
                ->where('creator_id', $user->creator->id)
                ->exists();

            if (!$applied) {
                abort(403, 'You must apply to this campaign first');
            }
        }

        $campaign->load(['applications', 'targetCountries', 'targeting', 'categories', 'brand']);

        return view('frontend.campaigns.designed-show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified campaign.
     */
    public function edit(Campaign $campaign): View
    {
        $user = auth()->user();

        // Only brand who created it can edit
        if ($user->user_type !== 'brand' || $campaign->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $campaign->load(['applications', 'targetCountries', 'targeting']);

        return view(
            'frontend.campaigns.designed-edit',
            array_merge(
                $this->campaignService->getFormPayload(),
                compact('campaign')
            )
        );
    }

    /**
     * Update the specified campaign.
     */
    public function update(StoreCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $user = auth()->user();

        // Only brand who created it can update
        if ($user->user_type !== 'brand' || $campaign->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $campaign->update($request->validated());

            return redirect()
                ->route('frontend.campaigns.show', $campaign)
                ->with('success', 'Campaign updated successfully.');

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
        $user = auth()->user();

        // Only brand who created it can delete
        if ($user->user_type !== 'brand' || $campaign->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        try {
            $title = $campaign->title;
            $campaign->delete();

            return redirect()
                ->route('frontend.campaigns.index')
                ->with('success', "Campaign \"$title\" has been deleted successfully.");

        } catch (\Exception $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete campaign. Please try again.');
        }
    }
}
