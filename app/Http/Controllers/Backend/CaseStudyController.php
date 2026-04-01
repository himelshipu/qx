<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CaseStudyController extends Controller
{
    /**
     * Display a listing of the case studies.
     */
    public function index()
    {
        $caseStudies = CaseStudy::orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate(15);

        return view('backend.pages.case-studies.index', compact('caseStudies'));
    }

    /**
     * Show the form for creating a new case study.
     */
    public function create()
    {
        return view('backend.pages.case-studies.create');
    }

    /**
     * Store a newly created case study in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'required|string',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'external_url' => 'nullable|url',
            'is_published' => 'boolean',
            'sort_order'   => 'integer|min:0'
        ]);

        $imagePath = null;
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('case-studies', 'public');
        }

        $slug    = Str::slug($request->input('title'));
        $counter = 1;
        while (CaseStudy::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->input('title')) . '-' . $counter++;
        }

        CaseStudy::create([
            'title'            => $validated['title'],
            'slug'             => $slug,
            'summary'          => $validated['summary'],
            'cover_image_path' => $imagePath,
            'external_url'     => $validated['external_url'] ?? null,
            'is_published'     => $request->boolean('is_published'),
            'sort_order'       => $validated['sort_order'] ?? 0,
            'published_at'     => $request->boolean('is_published') ? now() : null
        ]);

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study created successfully.');
    }

    /**
     * Show the case study details page.
     */
    public function show(CaseStudy $caseStudy)
    {
        return view('backend.pages.case-studies.show', compact('caseStudy'));
    }

    /**
     * Show the form for editing the specified case study.
     */
    public function edit(CaseStudy $caseStudy)
    {
        return view('backend.pages.case-studies.edit', compact('caseStudy'));
    }

    /**
     * Update the specified case study in storage.
     */
    public function update(Request $request, CaseStudy $caseStudy)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'summary'      => 'required|string',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'external_url' => 'nullable|url',
            'is_published' => 'boolean',
            'sort_order'   => 'integer|min:0'
        ]);

        // Handle image update
        if ($request->hasFile('cover_image')) {
            if ($caseStudy->cover_image_path) {
                Storage::disk('public')->delete($caseStudy->cover_image_path);
            }
            $validated['cover_image_path'] = $request->file('cover_image')->store('case-studies', 'public');
        }

        // Update slug if title changed
        if ($request->input('title') !== $caseStudy->title) {
            $slug    = Str::slug($request->input('title'));
            $counter = 1;
            while (CaseStudy::where('slug', $slug)->where('id', '!=', $caseStudy->id)->exists()) {
                $slug = Str::slug($request->input('title')) . '-' . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['external_url'] = $request->input('external_url') ?? null;
        $validated['is_published'] = $request->boolean('is_published');
        if ($request->boolean('is_published') && !$caseStudy->published_at) {
            $validated['published_at'] = now();
        }

        $caseStudy->update($validated);

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study updated successfully.');
    }

    /**
     * Remove the specified case study from storage.
     */
    public function destroy(CaseStudy $caseStudy)
    {
        if ($caseStudy->cover_image_path) {
            Storage::disk('public')->delete($caseStudy->cover_image_path);
        }

        $caseStudy->delete();

        return redirect()->route('dashboard.case-studies.index')
            ->with('success', 'Case study deleted successfully.');
    }
    /**
     * Toggle the publish status of a case study.
     */public function toggleStatus(CaseStudy $caseStudy)
    {
        $caseStudy->update([
            'is_published' => !$caseStudy->is_published,
            'published_at' => !$caseStudy->is_published ? now() : null
        ]);

        return redirect()->back()
            ->with('success', 'Case study status updated successfully.');
    }
}
