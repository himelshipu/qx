<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Faq\StoreFaqSectionRequest;
use App\Http\Requests\Backend\Faq\UpdateFaqSectionRequest;
use App\Models\FaqSection;
use App\Services\Admin\FaqSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqSectionController extends Controller
{
    public function __construct(
        private readonly FaqSectionService $service
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $audience = (string) $request->query('audience', 'all');

        $sections = $this->service->paginateForDashboard($search, $status, $audience);
        $sections->appends([
            'q' => $search,
            'status' => $status,
            'audience' => $audience,
        ]);

        return view('backend.pages.faqs.sections.index', [
            'sections' => $sections,
            'stats' => $this->service->stats(),
            'search' => $search,
            'status' => $status,
            'audience' => $audience,
        ]);
    }

    public function table(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');
        $audience = (string) $request->query('audience', 'all');

        $sections = $this->service->paginateForDashboard($search, $status, $audience);
        $sections->appends([
            'q' => $search,
            'status' => $status,
            'audience' => $audience,
        ]);

        return view('backend.pages.faqs.sections._results', [
            'sections' => $sections,
        ]);
    }

    public function create(): View
    {
        return view('backend.pages.faqs.sections.create', [
            'section' => new FaqSection(),
        ]);
    }

    public function store(StoreFaqSectionRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section created successfully.');
    }

    public function edit(FaqSection $section): View
    {
        return view('backend.pages.faqs.sections.edit', compact('section'));
    }

    public function update(UpdateFaqSectionRequest $request, FaqSection $section): RedirectResponse
    {
        $this->service->update($section, $request->validated());

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section updated successfully.');
    }

    public function destroy(FaqSection $section): RedirectResponse
    {
        $this->service->delete($section);

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section deleted successfully.');
    }

    public function toggleStatus(Request $request, FaqSection $section): JsonResponse|RedirectResponse
    {
        $section = $this->service->toggleStatus($section);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $section->id,
                'is_active' => (bool) $section->is_active,
            ]);
        }

        return redirect()->back()->with('success', 'FAQ section status updated successfully.');
    }
}