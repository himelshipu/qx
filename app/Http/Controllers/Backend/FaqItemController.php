<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Faq\StoreFaqItemRequest;
use App\Http\Requests\Backend\Faq\UpdateFaqItemRequest;
use App\Models\FaqItem;
use App\Models\FaqSection;
use App\Services\Admin\FaqItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqItemController extends Controller
{
    public function __construct(
        private readonly FaqItemService $service
    ) {
    }

    public function index(Request $request, FaqSection $section): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $items = $this->service->paginateForDashboard($section, $search, $status);
        $items->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.faqs.items.index', [
            'section' => $section,
            'items' => $items,
            'stats' => $this->service->stats($section),
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function table(Request $request, FaqSection $section): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'all');

        $items = $this->service->paginateForDashboard($section, $search, $status);
        $items->appends([
            'q' => $search,
            'status' => $status,
        ]);

        return view('backend.pages.faqs.items._results', [
            'section' => $section,
            'items' => $items,
        ]);
    }

    public function create(FaqSection $section): View
    {
        return view('backend.pages.faqs.items.create', [
            'section' => $section,
            'item' => new FaqItem(),
        ]);
    }

    public function store(StoreFaqItemRequest $request, FaqSection $section): RedirectResponse
    {
        $this->service->create($section, $request->validated());

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item created successfully.');
    }

    public function edit(FaqSection $section, FaqItem $item): View
    {
        abort_unless($item->faq_section_id === $section->id, 404);

        return view('backend.pages.faqs.items.edit', compact('section', 'item'));
    }

    public function update(UpdateFaqItemRequest $request, FaqSection $section, FaqItem $item): RedirectResponse
    {
        abort_unless($item->faq_section_id === $section->id, 404);

        $this->service->update($item, $request->validated());

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item updated successfully.');
    }

    public function destroy(FaqSection $section, FaqItem $item): RedirectResponse
    {
        abort_unless($item->faq_section_id === $section->id, 404);

        $this->service->delete($item);

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item deleted successfully.');
    }

    public function toggleStatus(Request $request, FaqSection $section, FaqItem $item): JsonResponse|RedirectResponse
    {
        abort_unless($item->faq_section_id === $section->id, 404);

        $item = $this->service->toggleStatus($item);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $item->id,
                'is_active' => (bool) $item->is_active,
            ]);
        }

        return redirect()->back()->with('success', 'FAQ item status updated successfully.');
    }
}