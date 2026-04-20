<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Testimonial\StoreTestimonialRequest;
use App\Http\Requests\Backend\Testimonial\UpdateTestimonialRequest;
use App\Models\Testimonial;
use App\Services\Admin\TestimonialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function __construct(
        private readonly TestimonialService $testimonialService
    ) {}

    /**
     * Display a listing of the testimonials.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        return view('backend.pages.testimonials.index', $this->testimonialService->getListingPayload($search, $status));
    }

    /**
     * Return only dashboard testimonial table HTML for faster filter updates.
     */
    public function table(Request $request): JsonResponse
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $payload = $this->testimonialService->getListingPayload($search, $status);

        $html = view('backend.pages.testimonials._results', [
            'testimonials' => $payload['testimonials'],
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create(): View
    {
        return view('backend.pages.testimonials.create', [
            'testimonial' => null,
            'nextSortOrder' => $this->testimonialService->getNextSortOrder(),
        ]);
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(StoreTestimonialRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->testimonialService->createTestimonial($validated);

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial): View
    {
        return view('backend.pages.testimonials.edit', [
            'testimonial' => $testimonial,
        ]);
    }

    /**
     * Update the specified testimonial in storage.
     */
    public function update(UpdateTestimonialRequest $request, Testimonial $testimonial): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_published'] = $request->boolean('is_published');

        $this->testimonialService->updateTestimonial($testimonial, $validated);

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified testimonial from storage.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $this->testimonialService->deleteTestimonial($testimonial);

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }

    /**
     * Toggle the publish status of a testimonial.
     */
    public function toggleStatus(Request $request, Testimonial $testimonial): JsonResponse|RedirectResponse
    {
        $updated = $this->testimonialService->toggleStatus($testimonial);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Testimonial status updated successfully.',
                'is_published' => (bool) $updated->is_published,
            ]);
        }

        return redirect()->back()
            ->with('success', 'Testimonial status updated successfully.');
    }

    /**
     * Reorder testimonials via AJAX.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'integer', 'min:1'],
        ]);

        $this->testimonialService->reorderTestimonials($validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Testimonials reordered successfully.',
        ]);
    }
}
