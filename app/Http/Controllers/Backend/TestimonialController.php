<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index()
    {
        $testimonials = Testimonial::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create()
    {
        return view('backend.pages.testimonials.create');
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_role'  => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'quote'        => 'required|string',
            'rating'       => 'nullable|integer|min:1|max:5',
            'is_published' => 'boolean',
            'sort_order'   => 'integer|min:0'
        ]);

        $testimonial = Testimonial::create([
            'author_name'  => $validated['author_name'],
            'author_role'  => $validated['author_role'],
            'company_name' => $validated['company_name'],
            'quote'        => $validated['quote'],
            'rating'       => $validated['rating'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'sort_order'   => $validated['sort_order'] ?? 0
        ]);

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial created successfully.');
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial)
    {
        return view('backend.pages.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial in storage.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'author_name'  => 'required|string|max:255',
            'author_role'  => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'quote'        => 'required|string',
            'rating'       => 'nullable|integer|min:1|max:5',
            'is_published' => 'boolean',
            'sort_order'   => 'integer|min:0'
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $testimonial->update($validated);

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial updated successfully.');
    }

    /**
     * Remove the specified testimonial from storage.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('dashboard.testimonials.index')
            ->with('success', 'Testimonial deleted successfully.');
    }
    /**
     * Toggle the publish status of a testimonial.
     */public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_published' => !$testimonial->is_published
        ]);

        return redirect()->back()
            ->with('success', 'Testimonial status updated successfully.');
    }
}
