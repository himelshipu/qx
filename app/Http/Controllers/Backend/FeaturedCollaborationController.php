<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeaturedCollaboration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeaturedCollaborationController extends Controller
{
    /**
     * Display a listing of featured collaborations.
     */
    public function index()
    {
        $collaborations = FeaturedCollaboration::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.pages.featured-collaborations.index', compact('collaborations'));
    }

    /**
     * Show the form for creating a new collaboration.
     */
    public function create()
    {
        $assetTypes = ['image', 'video'];

        return view('backend.pages.featured-collaborations.create', compact('assetTypes'));
    }

    /**
     * Store a newly created collaboration in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name'     => 'required|string|max:255',
            'asset_type'     => 'required|in:image,video',
            'image_path'     => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'video_path'     => 'nullable|mimes:mp4,webm,mov|max:102400',
            'thumbnail_path' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:2048',
            'sort_order'     => 'integer|min:0',
            'is_published'   => 'boolean'
        ]);

        $data = [
            'brand_name'   => $validated['brand_name'],
            'asset_type'   => $validated['asset_type'],
            'sort_order'   => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published')
        ];

        // Handle image upload
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('collaborations', 'public');
        }

        // Handle video upload
        if ($request->hasFile('video_path')) {
            $data['video_path'] = $request->file('video_path')->store('collaborations/videos', 'public');
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_path')) {
            $data['thumbnail_path'] = $request->file('thumbnail_path')->store('collaborations/thumbnails', 'public');
        }

        FeaturedCollaboration::create($data);

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration created successfully.');
    }

    /**
     * Show the form for editing the specified collaboration.
     */
    public function edit(FeaturedCollaboration $featuredCollaboration)
    {
        $assetTypes = ['image', 'video'];

        return view('backend.pages.featured-collaborations.edit', compact('featuredCollaboration', 'assetTypes'));
    }

    /**
     * Update the specified collaboration in storage.
     */
    public function update(Request $request, FeaturedCollaboration $featuredCollaboration)
    {
        $validated = $request->validate([
            'brand_name'     => 'required|string|max:255',
            'asset_type'     => 'required|in:image,video',
            'image_path'     => 'nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'video_path'     => 'nullable|mimes:mp4,webm,mov|max:102400',
            'thumbnail_path' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:2048',
            'sort_order'     => 'integer|min:0',
            'is_published'   => 'boolean'
        ]);

        $data = [
            'brand_name'   => $validated['brand_name'],
            'asset_type'   => $validated['asset_type'],
            'sort_order'   => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published')
        ];

        // Handle image upload
        if ($request->hasFile('image_path')) {
            // Delete old image
            if ($featuredCollaboration->image_path && Storage::disk('public')->exists($featuredCollaboration->image_path)) {
                Storage::disk('public')->delete($featuredCollaboration->image_path);
            }
            $data['image_path'] = $request->file('image_path')->store('collaborations', 'public');
        }

        // Handle video upload
        if ($request->hasFile('video_path')) {
            // Delete old video
            if ($featuredCollaboration->video_path && Storage::disk('public')->exists($featuredCollaboration->video_path)) {
                Storage::disk('public')->delete($featuredCollaboration->video_path);
            }
            $data['video_path'] = $request->file('video_path')->store('collaborations/videos', 'public');
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_path')) {
            // Delete old thumbnail
            if ($featuredCollaboration->thumbnail_path && Storage::disk('public')->exists($featuredCollaboration->thumbnail_path)) {
                Storage::disk('public')->delete($featuredCollaboration->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail_path')->store('collaborations/thumbnails', 'public');
        }

        $featuredCollaboration->update($data);

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration updated successfully.');
    }

    /**
     * Delete the specified collaboration.
     */
    public function destroy(FeaturedCollaboration $featuredCollaboration)
    {
        // Delete associated files
        if ($featuredCollaboration->image_path && Storage::disk('public')->exists($featuredCollaboration->image_path)) {
            Storage::disk('public')->delete($featuredCollaboration->image_path);
        }
        if ($featuredCollaboration->video_path && Storage::disk('public')->exists($featuredCollaboration->video_path)) {
            Storage::disk('public')->delete($featuredCollaboration->video_path);
        }
        if ($featuredCollaboration->thumbnail_path && Storage::disk('public')->exists($featuredCollaboration->thumbnail_path)) {
            Storage::disk('public')->delete($featuredCollaboration->thumbnail_path);
        }

        $featuredCollaboration->delete();

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', 'Featured collaboration deleted successfully.');
    }

    /**
     * Toggle publish status
     */
    public function togglePublish(FeaturedCollaboration $featuredCollaboration)
    {
        $featuredCollaboration->update([
            'is_published' => !$featuredCollaboration->is_published
        ]);

        $status = $featuredCollaboration->is_published ? 'published' : 'unpublished';

        return redirect()->route('dashboard.featured-collaborations.index')
            ->with('success', "Featured collaboration {$status} successfully.");
    }
}
