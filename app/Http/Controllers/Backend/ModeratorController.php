<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Moderator\StoreModeratorRequest;
use App\Http\Requests\Backend\Moderator\UpdateModeratorRequest;
use App\Models\User;
use App\Services\Admin\ModeratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeratorController extends Controller
{
    public function __construct(
        private readonly ModeratorService $moderatorService
    ) {}

    /**
     * Display a listing of the moderators.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return view('backend.pages.moderator.index', $this->moderatorService->getListingPayload($search, $status));
    }

    /**
     * Show the form for creating a new moderator.
     */
    public function create(): View
    {
        return view('backend.pages.moderator.create');
    }

    /**
     * Store a newly created moderator in storage.
     */
    public function store(StoreModeratorRequest $request): RedirectResponse
    {
        $this->moderatorService->createModerator(
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()->route('dashboard.moderators.index')
            ->with('success', 'Moderator created successfully.');
    }

    /**
     * Display the specified moderator.
     */
    public function show(User $moderator): View
    {
        $moderator = $this->resolveModerator($moderator);

        return view('backend.pages.moderator.show', compact('moderator'));
    }

    /**
     * Show the form for editing the specified moderator.
     */
    public function edit(User $moderator): View
    {
        $moderator = $this->resolveModerator($moderator);

        return view('backend.pages.moderator.edit', compact('moderator'));
    }

    /**
     * Update the specified moderator in storage.
     */
    public function update(UpdateModeratorRequest $request, User $moderator): RedirectResponse
    {
        $moderator = $this->resolveModerator($moderator);

        $this->moderatorService->updateModerator(
            $moderator,
            $request->validated(),
            $request->boolean('is_active', true)
        );

        return redirect()->route('dashboard.moderators.index')
            ->with('success', 'Moderator updated successfully.');
    }

    /**
     * Remove the specified moderator from storage.
     */
    public function destroy(User $moderator): RedirectResponse
    {
        $moderator = $this->resolveModerator($moderator);
        $result    = $this->moderatorService->deleteModerator($moderator);

        return redirect()->route('dashboard.moderators.index')
            ->with($result['deleted'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Toggle moderator status.
     */
    public function toggleStatus(User $moderator): JsonResponse
    {
        $moderator = $this->resolveModerator($moderator);
        $isActive  = $this->moderatorService->toggleStatus($moderator);

        return response()->json([
            'success'   => true,
            'message'   => 'Status updated successfully.',
            'is_active' => $isActive
        ]);
    }

    /**
     * Ensure provided user is a moderator.
     */
    private function resolveModerator(User $moderator): User
    {
        abort_unless($moderator->user_type === 'moderator', 404);

        return $moderator;
    }
}
