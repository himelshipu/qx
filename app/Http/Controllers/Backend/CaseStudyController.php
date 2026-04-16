<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\CaseStudy\StoreCaseStudyRequest;
use App\Http\Requests\Backend\CaseStudy\UpdateCaseStudyRequest;
use App\Models\CaseStudy;
use App\Services\Admin\CaseStudyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseStudyController extends Controller
{
    public function __construct(
        private readonly CaseStudyService $caseStudyService
    ) {}

    /**
     * Display a listing of the case studies.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return view('backend.pages.case-studies.index', $this->caseStudyService->getListingPayload($search, $status));
    }

    /**
     * Show the form for creating a new case study.
     */
    public function create(): View
    {
        return view('backend.pages.case-studies.create', [
            'caseStudy' => null,
            'nextSortOrder' => $this->caseStudyService->getNextSortOrder(),
            'initialCoverPreview' => null,
        ]);
    }

    /**
     * Store a newly created case study in storage.
     */
    public function store(StoreCaseStudyRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->caseStudyService->createCaseStudy(
            $validated,
            $request->file('cover_image')
        );

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study created successfully.');
    }

    /**
     * Show the case study details page.
     */
    public function show(CaseStudy $caseStudy): View
    {
        return view('backend.pages.case-studies.show', [
            'caseStudy' => $caseStudy,
            'coverUrl' => $this->caseStudyService->buildCoverPreview($caseStudy),
        ]);
    }

    /**
     * Show the form for editing the specified case study.
     */
    public function edit(CaseStudy $caseStudy): View
    {
        return view('backend.pages.case-studies.edit', [
            'caseStudy' => $caseStudy,
            'initialCoverPreview' => $this->caseStudyService->buildCoverPreview($caseStudy),
        ]);
    }

    /**
     * Update the specified case study in storage.
     */
    public function update(UpdateCaseStudyRequest $request, CaseStudy $caseStudy): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->caseStudyService->updateCaseStudy(
            $caseStudy,
            $validated,
            $request->file('cover_image')
        );

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study updated successfully.');
    }

    /**
     * Remove the specified case study from storage.
     */
    public function destroy(CaseStudy $caseStudy): RedirectResponse
    {
        $this->caseStudyService->deleteCaseStudy($caseStudy);

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study deleted successfully.');
    }

    /**
     * Toggle the publish status of a case study.
     */
    public function toggleStatus(Request $request, CaseStudy $caseStudy): JsonResponse|RedirectResponse
    {
        $updated = $this->caseStudyService->toggleStatus($caseStudy);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Case study status updated successfully.',
                'is_published' => (bool) $updated->is_published,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Case study status updated successfully.');
    }

    /**
     * Reorder case studies based on drag-drop sequence.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'min:1'],
        ]);

        $this->caseStudyService->reorderCaseStudies($validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Case studies reordered successfully.',
        ]);
    }
}
