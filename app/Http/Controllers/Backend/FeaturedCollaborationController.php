<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\FeaturedCollaboration\StoreFeaturedCollaborationRequest;
use App\Http\Requests\Backend\FeaturedCollaboration\UpdateFeaturedCollaborationRequest;
use App\Models\FeaturedCollaboration;
use App\Services\Admin\FeaturedCollaborationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedCollaborationController extends Controller
{
    public function __construct(
        private readonly FeaturedCollaborationService $featuredCollaborationService
    ) {}

    /**
     * Display a listing of featured collaborations.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $assetType = (string) $request->string('asset_type', 'all');

        $payload = $this->featuredCollaborationService->getListingPayload($search, $status, $assetType);

        return view('backend.pages.featured-collaborations.index', $payload);
    }

    /**
     * Return dashboard table fragment for ajax filters.
     */
    public function table(Request $request): JsonResponse
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $assetType = (string) $request->string('asset_type', 'all');

        $payload = $this->featuredCollaborationService->getListingPayload($search, $status, $assetType);

        $html = view('backend.pages.featured-collaborations._results', [
            'collaborations' => $payload['collaborations'],
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Show the form for creating a new collaboration.
     */
    public function create(): View
    {
        return view('backend.pages.featured-collaborations.create', [
            'featuredCollaboration' => null,
            'nextSortOrder' => $this->featuredCollaborationService->getNextSortOrder(),
        ]);
    }

    /**
     * Store a newly created collaboration in storage.
     */
    public function store(StoreFeaturedCollaborationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->featuredCollaborationService->createCollaboration(
            $validated,
            $request->file('image_path'),
            $request->file('video_path'),
            $request->file('thumbnail_path')
        );

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration created successfully.');
    }

    /**
     * Show the form for editing the specified collaboration.
     */
    public function edit(FeaturedCollaboration $featuredCollaboration): View
    {
        return view('backend.pages.featured-collaborations.edit', [
            'featuredCollaboration' => $featuredCollaboration,
        ]);
    }

    /**
     * Update the specified collaboration in storage.
     */
    public function update(UpdateFeaturedCollaborationRequest $request, FeaturedCollaboration $featuredCollaboration): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->featuredCollaborationService->updateCollaboration(
            $featuredCollaboration,
            $validated,
            $request->file('image_path'),
            $request->file('video_path'),
            $request->file('thumbnail_path'),
            $request->boolean('delete_image'),
            $request->boolean('delete_video'),
            $request->boolean('delete_thumbnail')
        );

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration updated successfully.');
    }

    /**
     * Delete the specified collaboration.
     */
    public function destroy(FeaturedCollaboration $featuredCollaboration): RedirectResponse
    {
        $this->featuredCollaborationService->deleteCollaboration($featuredCollaboration);

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(Request $request, FeaturedCollaboration $featuredCollaboration): JsonResponse|RedirectResponse
    {
        $updated = $this->featuredCollaborationService->toggleStatus($featuredCollaboration);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Featured collaboration status updated successfully.',
                'is_published' => (bool) $updated->is_published,
            ]);
        }

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration status updated successfully.');
    }

    /**
     * Reorder featured collaborations.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'min:1'],
        ]);

        $this->featuredCollaborationService->reorderCollaborations($validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Featured collaborations reordered successfully.',
        ]);
    }
}
