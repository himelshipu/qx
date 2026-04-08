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
     * Influencers see: campaigns they've applied to
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
        } elseif ($user->user_type === 'influencer') {
            // Influencer sees campaigns they've applied to
            $campaigns = Campaign::whereHas('applications', function ($query) use ($user) {
                $query->where('influencer_id', $user->influencer->id);
            })
                ->where('is_active', true)
                ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->when($type !== 'all', fn($q) => $q->where('campaign_type', $type))
                ->orderByDesc('created_at')
                ->paginate(12);

            return view('frontend.campaigns.designed-index', [
                'campaigns'     => $campaigns,
                'userType'      => 'influencer',
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
                [
                    'campaign' => null,
                    'isEditMode' => false
                ]
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
        // - Influencers can see campaigns they've applied to
        if ($user->user_type === 'brand') {
            if ($campaign->created_by !== $user->id) {
                abort(403, 'Unauthorized');
            }
        } elseif ($user->user_type === 'influencer') {
            // Check if influencer has applied to this campaign
            $applied = $campaign->applications()
                ->where('influencer_id', $user->influencer->id)
                ->exists();

            if (!$applied) {
                abort(403, 'You must apply to this campaign first');
            }
        }

        $campaign->load(['applications', 'targetCountries', 'targeting', 'categories', 'brand', 'influencerAssignments.influencer.user']);

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

        $campaign->load(['applications', 'targetCountries', 'targeting', 'categories', 'followerRanges']);

        // Prepare data for the wizard form
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

        return view(
            'frontend.campaigns.designed-create',
            array_merge(
                $this->campaignService->getFormPayload(),
                [
                    'campaign' => $campaign,
                    'campaignData' => $campaignData,
                    'isEditMode' => true
                ]
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
            $data = $request->validated();

            // Use the campaign service to update campaign with relationships
            $this->campaignService->updateCampaign(
                $campaign,
                $data,
                $request->boolean('is_active', true)
            );

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

    /**
     * Update influencer assignment status (approve/reject/cancel).
     */
    public function updateInfluencerStatus(Campaign $campaign, $assignmentId, Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Only the brand who created the campaign can manage assignments
        if ($user->user_type !== 'brand' || $campaign->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled'
        ]);

        try {
            // Find the assignment
            $assignment = $campaign->influencerAssignments()->findOrFail($assignmentId);

            // Update the status
            $assignment->status = $validated['status'];

            // Set appropriate timestamps
            if ($validated['status'] === 'approved') {
                $assignment->approved_at      = now();
                $assignment->rejection_reason = null;
            } elseif ($validated['status'] === 'rejected') {
                $assignment->rejection_reason = $request->input('rejection_reason', 'Manually rejected by brand');
            } elseif ($validated['status'] === 'cancelled') {
                $assignment->approved_at      = null;
                $assignment->rejection_reason = null;
            }

            $assignment->save();

            // Flash success message
            $statusLabel = match ($validated['status']) {
                'approved'  => 'approved',
                'rejected'  => 'rejected',
                'cancelled' => 'cancelled',
                default     => 'updated',
            };

            return redirect()
                ->route('frontend.campaigns.show', $campaign)
                ->with('success', "Influencer assignment {$statusLabel} successfully.");

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Failed to update assignment status. Please try again.');
        }
    }

    /**
     * Update campaign application status (invited influencers only).
     */
    public function updateApplicationStatus(Campaign $campaign, $applicationId, Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Only the brand who created the campaign can manage applications
        if ($user->user_type !== 'brand' || $campaign->created_by !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // Validate the request
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        try {
            // Find the application by ID and ensure it belongs to the campaign
            $application = \App\Models\CampaignApplication::where('campaign_id', $campaign->id)
                ->where('id', $applicationId)
                ->firstOrFail();

            // Only update if status is 'invited'
            if ($application->status !== 'invited') {
                return redirect()
                    ->back()
                    ->with('error', 'Can only approve or decline invited applications.');
            }

            // Update the status
            $application->status = $validated['status'];
            $application->decided_at = now();
            $application->save();

            // Flash success message
            $statusLabel = $validated['status'] === 'approved' ? 'approved' : 'declined';

            return redirect()
                ->route('frontend.campaigns.show', $campaign)
                ->with('success', "Influencer invitation {$statusLabel} successfully.");

        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Failed to update application status. Please try again.');
        }
    }
}
