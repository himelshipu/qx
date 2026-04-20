<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use App\Models\FaqSection;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of FAQ sections.
     */
    public function indexSections()
    {
        $sections = FaqSection::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.faqs.sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new FAQ section.
     */
    public function createSection()
    {
        return view('backend.pages.faqs.sections.create');
    }

    /**
     * Store a newly created FAQ section in storage.
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'section_code'  => 'required|string|max:120|unique:faq_sections,section_code',
            'section_title' => 'required|string|max:255',
            'audience_type' => 'required|in:all,brand,influencer',
            'sort_order'    => 'integer|min:0'
        ]);

        $section = FaqSection::create([
            'section_code'  => $validated['section_code'],
            'section_title' => $validated['section_title'],
            'audience_type' => $validated['audience_type'],
            'sort_order'    => $validated['sort_order'] ?? 0,
            'is_active'     => true
        ]);

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section created successfully.');
    }

    /**
     * Show the form for editing the specified FAQ section.
     */
    public function editSection(FaqSection $section)
    {
        return view('backend.pages.faqs.sections.edit', compact('section'));
    }

    /**
     * Update the specified FAQ section in storage.
     */
    public function updateSection(Request $request, FaqSection $section)
    {
        $validated = $request->validate([
            'section_code'  => 'required|string|max:120|unique:faq_sections,section_code,' . $section->id,
            'section_title' => 'required|string|max:255',
            'audience_type' => 'required|in:all,brand,influencer',
            'sort_order'    => 'integer|min:0'
        ]);

        $section->update([
            'section_code'  => $validated['section_code'],
            'section_title' => $validated['section_title'],
            'audience_type' => $validated['audience_type'],
            'sort_order'    => $validated['sort_order']
        ]);

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section updated successfully.');
    }

    /**
     * Remove the specified FAQ section and its items from storage.
     */
    public function destroySection(FaqSection $section)
    {
        $section->items()->delete();
        $section->delete();

        return redirect()->route('dashboard.faqs.sections.index')
            ->with('success', 'FAQ section deleted successfully.');
    }

    /**
     * Toggle the active status of a FAQ section.
     */
    public function toggleSectionStatus(FaqSection $section)
    {
        $section->update([
            'is_active' => !$section->is_active
        ]);

        return redirect()->back()
            ->with('success', 'FAQ section status updated successfully.');
    }

    /**
     * Display a listing of FAQ items in a section.
     */
    public function indexItems(FaqSection $section)
    {
        $items = $section->items()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.faqs.items.index', compact('section', 'items'));
    }

    /**
     * Show the form for creating a new FAQ item.
     */
    public function createItem(FaqSection $section)
    {
        return view('backend.pages.faqs.items.create', compact('section'));
    }

    /**
     * Store a newly created FAQ item in storage.
     */
    public function storeItem(Request $request, FaqSection $section)
    {
        $validated = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'sort_order' => 'integer|min:0'
        ]);

        $item = FaqItem::create([
            'faq_section_id' => $section->id,
            'question'       => $validated['question'],
            'answer'         => $validated['answer'],
            'sort_order'     => $validated['sort_order'] ?? 0,
            'is_active'      => true
        ]);

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item created successfully.');
    }

    /**
     * Show the form for editing the specified FAQ item.
     */
    public function editItem(FaqSection $section, FaqItem $item)
    {
        if ($item->faq_section_id !== $section->id) {
            abort(404);
        }

        return view('backend.pages.faqs.items.edit', compact('section', 'item'));
    }

    /**
     * Update the specified FAQ item in storage.
     */
    public function updateItem(Request $request, FaqSection $section, FaqItem $item)
    {
        if ($item->faq_section_id !== $section->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question'   => 'required|string|max:500',
            'answer'     => 'required|string',
            'sort_order' => 'integer|min:0'
        ]);

        $item->update([
            'question'   => $validated['question'],
            'answer'     => $validated['answer'],
            'sort_order' => $validated['sort_order']
        ]);

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item updated successfully.');
    }

    /**
     * Remove the specified FAQ item from storage.
     */
    public function destroyItem(FaqSection $section, FaqItem $item)
    {
        if ($item->faq_section_id !== $section->id) {
            abort(404);
        }

        $item->delete();

        return redirect()->route('dashboard.faqs.items.index', $section)
            ->with('success', 'FAQ item deleted successfully.');
    }
    /**
     * Toggle the active status of a FAQ item.
     */public function toggleItemStatus(FaqSection $section, FaqItem $item)
    {
        if ($item->faq_section_id !== $section->id) {
            abort(404);
        }

        $item->update([
            'is_active' => !$item->is_active
        ]);

        return redirect()->back()
            ->with('success', 'FAQ item status updated successfully.');
    }
}
