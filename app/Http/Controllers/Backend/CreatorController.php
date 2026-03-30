<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Creator\StoreCreatorRequest;
use App\Http\Requests\Backend\Creator\UpdateCreatorRequest;
use App\Models\Creator;
use App\Services\Admin\CreatorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CreatorController extends Controller
{
    public function __construct(
        private readonly CreatorService $creatorService
    ) {}

    /**
     * Display a listing of creators with search and status filter.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return view('backend.pages.creators.index', $this->creatorService->getListingPayload($search, $status));
    }

    /**
     * Show the form for creating a new creator.
     */
    public function create(): View
    {
        return view('backend.pages.creators.create', $this->creatorService->getFormPayload());
    }

    /**
     * Store a newly created creator.
     */
    public function store(StoreCreatorRequest $request): RedirectResponse
    {
        $this->creatorService->createCreator(
            $request->validated(),
            $request->boolean('is_active', true),
            $request->boolean('is_featured', false),
            $request->input('featured_priority') !== null ? (int) $request->input('featured_priority') : null,
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.creators.index')
            ->with('success', 'Creator created successfully.');
    }

    /**
     * Display the specified creator details.
     */
    public function view(Creator $creator): View
    {
        return view('backend.pages.creators.view', $this->creatorService->getDetailPayload($creator));
    }

    /**
     * Show the form for editing the specified creator.
     */
    public function edit(Creator $creator): View
    {
        return view('backend.pages.creators.edit', [
            'creator' => $creator->load(['user', 'categories']),
            ...$this->creatorService->getFormPayload()
        ]);
    }

    /**
     * Update the specified creator.
     */
    public function update(UpdateCreatorRequest $request, Creator $creator): RedirectResponse
    {
        $this->creatorService->updateCreator(
            $creator,
            $request->validated(),
            $request->boolean('is_active'),
            $request->boolean('is_featured', false),
            $request->input('featured_priority') !== null ? (int) $request->input('featured_priority') : null,
            $request->file('profile_image_file'),
            $request->file('cover_image_file')
        );

        return redirect()
            ->route('dashboard.creators.index')
            ->with('success', 'Creator updated successfully.');
    }

    /**
     * Remove the specified creator if no dependencies exist.
     */
    public function destroy(Creator $creator): RedirectResponse
    {
        $result = $this->creatorService->deleteCreator($creator);

        return redirect()
            ->route('dashboard.creators.index')
            ->with($result['deleted'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Toggle creator status.
     */
    public function toggleStatus(Creator $creator): JsonResponse
    {
        $isActive = $this->creatorService->toggleStatus($creator);

        return response()->json([
            'success'   => true,
            'message'   => 'Creator status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Toggle creator featured status.
     */
    public function toggleFeatured(Creator $creator): JsonResponse
    {
        $isFeatured = $this->creatorService->toggleFeatured($creator);

        return response()->json([
            'success'     => true,
            'message'     => 'Creator featured status updated successfully.',
            'is_featured' => $isFeatured
        ]);
    }
}
