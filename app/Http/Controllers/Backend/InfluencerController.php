<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Influencer\StoreInfluencerRequest;
use App\Http\Requests\Backend\Influencer\UpdateInfluencerRequest;
use App\Models\Influencer;
use App\Services\Admin\InfluencerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InfluencerController extends Controller
{
    public function __construct(
        private readonly InfluencerService $influencerService
    ) {}

    /**
     * Display a listing of influencers with search and status filter.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return view('backend.pages.influencers.index', $this->influencerService->getListingPayload($search, $status));
    }

    /**
     * Show the form for creating a new influencer.
     */
    public function create(): View
    {
        return view('backend.pages.influencers.create', $this->influencerService->getFormPayload());
    }

    /**
     * Store a newly created influencer.
     */
    public function store(StoreInfluencerRequest $request): RedirectResponse
    {
        $this->influencerService->createInfluencer(
            $request->validated(),
            $request->boolean('is_active', true),
            $request->boolean('is_featured', false),
            $request->input('featured_priority') !== null ? (int) $request->input('featured_priority') : null,
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.influencers.index')
            ->with('success', 'Influencer created successfully.');
    }

    /**
     * Display the specified influencer details.
     */
    public function view(Influencer $influencer): View
    {
        return view('backend.pages.influencers.view', $this->influencerService->getDetailPayload($influencer));
    }

    /**
     * Show the form for editing the specified influencer.
     */
    public function edit(Influencer $influencer): View
    {
        return view('backend.pages.influencers.edit', [
            'influencer' => $influencer->load(['user', 'categories']),
            ...$this->influencerService->getFormPayload()
        ]);
    }

    /**
     * Update the specified influencer.
     */
    public function update(UpdateInfluencerRequest $request, Influencer $influencer): RedirectResponse
    {
        $this->influencerService->updateInfluencer(
            $influencer,
            $request->validated(),
            $request->boolean('is_active'),
            $request->boolean('is_featured', false),
            $request->input('featured_priority') !== null ? (int) $request->input('featured_priority') : null,
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.influencers.index')
            ->with('success', 'Influencer updated successfully.');
    }

    /**
     * Remove the specified influencer if no dependencies exist.
     */
    public function destroy(Influencer $influencer): RedirectResponse
    {
        $result = $this->influencerService->deleteInfluencer($influencer);

        return redirect()
            ->route('dashboard.influencers.index')
            ->with($result['deleted'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Toggle influencer status.
     */
    public function toggleStatus(Influencer $influencer): JsonResponse
    {
        $isActive = $this->influencerService->toggleStatus($influencer);

        return response()->json([
            'success'   => true,
            'message'   => 'Influencer status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Toggle influencer featured status.
     */
    public function toggleFeatured(Influencer $influencer): JsonResponse
    {
        $isFeatured = $this->influencerService->toggleFeatured($influencer);

        return response()->json([
            'success'     => true,
            'message'     => 'Influencer featured status updated successfully.',
            'is_featured' => $isFeatured
        ]);
    }
}
