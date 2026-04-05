<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignInfluencer;
use App\Models\Creator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignInfluencerController extends Controller
{
    /**
     * Show the form to assign influencers to a campaign
     */
    public function create(Campaign $campaign): View
    {
        $campaign->load(['brand', 'influencerAssignments.creator.user']);

        $activeCreators = Creator::with('user:id,email,name')
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get(['id', 'user_id', 'display_name']);

        $assignedCreatorIds = $campaign->influencerAssignments()
            ->whereIn('status', ['assigned', 'approved'])
            ->pluck('creator_id')
            ->toArray();

        return view('backend.pages.campaigns.influencers.create', [
            'campaign'           => $campaign,
            'creators'           => $activeCreators,
            'assignedCreatorIds' => $assignedCreatorIds
        ]);
    }

    /**
     * Store influencer assignment
     */
    public function store(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'creator_ids'   => 'required|array|min:1',
            'creator_ids.*' => 'exists:creators,id'
        ]);

        foreach ($validated['creator_ids'] as $creatorId) {
            // Skip if already assigned
            if (CampaignInfluencer::where('campaign_id', $campaign->id)
                ->where('creator_id', $creatorId)
                ->exists()) {
                continue;
            }

            CampaignInfluencer::create([
                'campaign_id' => $campaign->id,
                'creator_id'  => $creatorId,
                'status'      => 'assigned'
            ]);
        }

        return redirect()
            ->route('dashboard.campaigns.influencers.index', $campaign)
            ->with('success', 'Influencers assigned successfully');
    }

    /**
     * Show list of campaign influencers with approval statuses
     */
    public function index(Campaign $campaign): View
    {
        $campaign->load(['influencerAssignments' => function ($query) {
            $query->with('creator.user')->orderBy('created_at');
        }]);

        return view('backend.pages.campaigns.influencers.index', [
            'campaign'    => $campaign,
            'influencers' => $campaign->influencerAssignments
        ]);
    }

    /**
     * Approve an influencer assignment
     */
    public function approve(CampaignInfluencer $campaignInfluencer): RedirectResponse
    {
        $campaignInfluencer->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Influencer approved for campaign');
    }

    /**
     * Reject an influencer assignment
     */
    public function reject(Request $request, CampaignInfluencer $campaignInfluencer): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $campaignInfluencer->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'approved_by'      => auth()->id(),
            'approved_at'      => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Influencer rejected for campaign');
    }

    /**
     * Cancel an influencer assignment
     */
    public function cancel(CampaignInfluencer $campaignInfluencer): RedirectResponse
    {
        $campaignInfluencer->update([
            'status'       => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Influencer assignment cancelled');
    }

    /**
     * Remove an influencer from campaign (soft delete via status)
     */
    public function destroy(CampaignInfluencer $campaignInfluencer): RedirectResponse
    {
        $campaignInfluencer->delete();

        return redirect()
            ->back()
            ->with('success', 'Influencer removed from campaign');
    }
}
