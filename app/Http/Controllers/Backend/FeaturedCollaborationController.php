<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\FeaturedCollaboration;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
            'image_path'     => 'required_if:asset_type,image|nullable|image|mimes:jpeg,png,webp,jpg|max:5120',
            'video_path'     => 'required_if:asset_type,video|nullable|mimes:mp4,webm,mov|max:102400',
            'thumbnail_path' => 'exclude_unless:asset_type,video|nullable|image|mimes:jpeg,png,webp,jpg|max:2048',
            'sort_order'     => 'integer|min:0',
            'is_published'   => 'boolean'
        ]);

        $data = [
            'brand_name'   => $validated['brand_name'],
            'asset_type'   => $validated['asset_type'],
            'sort_order'   => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published')
        ];

        try {
            // Ensure upload directories exist before storing files.
            $this->ensureCollaborationDirectories();

            if ($request->hasFile('image_path')) {
                $data['image_path'] = $this->storePublicFile($request->file('image_path'), 'collaborations/images');
            }

            if ($request->hasFile('video_path')) {
                $data['video_path'] = $this->storePublicFile($request->file('video_path'), 'collaborations/videos');
            }

            if ($request->hasFile('thumbnail_path') && $validated['asset_type'] === 'video') {
                $data['thumbnail_path'] = $this->storePublicFile($request->file('thumbnail_path'), 'collaborations/thumbnails');
            }
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors([
                'media_upload' => 'Unable to upload media right now. Please try again.'
            ]);
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
            'thumbnail_path' => 'exclude_unless:asset_type,video|nullable|image|mimes:jpeg,png,webp,jpg|max:2048',
            'sort_order'     => 'integer|min:0',
            'is_published'   => 'boolean',
            'delete_image'   => 'boolean',
            'delete_video'   => 'boolean'
        ]);

        $data = [
            'brand_name'   => $validated['brand_name'],
            'asset_type'   => $validated['asset_type'],
            'sort_order'   => $validated['sort_order'] ?? 0,
            'is_published' => $request->boolean('is_published')
        ];

        if ($validated['asset_type'] === 'image') {
            $this->deleteFromPublicDisk($featuredCollaboration->video_path);
            $this->deleteFromPublicDisk($featuredCollaboration->thumbnail_path);
            $data['video_path'] = null;
            $data['thumbnail_path'] = null;
        }

        if ($validated['asset_type'] === 'video') {
            $this->deleteFromPublicDisk($featuredCollaboration->image_path);
            $data['image_path'] = null;
        }

        // Handle image deletion
        if ($request->boolean('delete_image')) {
            $this->deleteFromPublicDisk($featuredCollaboration->image_path);
            $data['image_path'] = null;
        }

        // Handle video deletion
        if ($request->boolean('delete_video')) {
            $this->deleteFromPublicDisk($featuredCollaboration->video_path);
            $data['video_path'] = null;
            $this->deleteFromPublicDisk($featuredCollaboration->thumbnail_path);
            $data['thumbnail_path'] = null;
        }

        try {
            $this->ensureCollaborationDirectories();

            // Handle image upload
            if ($request->hasFile('image_path')) {
                $this->deleteFromPublicDisk($featuredCollaboration->image_path);
                $data['image_path'] = $this->storePublicFile($request->file('image_path'), 'collaborations/images');
            }

            // Handle video upload
            if ($request->hasFile('video_path')) {
                $this->deleteFromPublicDisk($featuredCollaboration->video_path);
                $data['video_path'] = $this->storePublicFile($request->file('video_path'), 'collaborations/videos');
            }

            // Handle thumbnail upload - only for videos
            if ($request->hasFile('thumbnail_path') && $validated['asset_type'] === 'video') {
                $this->deleteFromPublicDisk($featuredCollaboration->thumbnail_path);
                $data['thumbnail_path'] = $this->storePublicFile($request->file('thumbnail_path'), 'collaborations/thumbnails');
            }
        } catch (Throwable $e) {
            report($e);
            return back()->withInput()->withErrors([
                'media_upload' => 'Unable to upload media right now. Please try again.'
            ]);
        }

        if ($validated['asset_type'] === 'image') {
            $data['video_path'] = null;
            $data['thumbnail_path'] = null;
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
        $this->deleteFromPublicDisk($featuredCollaboration->image_path);
        $this->deleteFromPublicDisk($featuredCollaboration->video_path);
        $this->deleteFromPublicDisk($featuredCollaboration->thumbnail_path);

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

    private function ensureCollaborationDirectories(): void
    {
        $disk = Storage::disk('public');

        foreach (['collaborations/images', 'collaborations/videos', 'collaborations/thumbnails'] as $directory) {
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }
        }
    }

    private function storePublicFile(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public');
    }

    private function deleteFromPublicDisk(?string $path): void
    {
        if (!$path || $path === '0') {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
