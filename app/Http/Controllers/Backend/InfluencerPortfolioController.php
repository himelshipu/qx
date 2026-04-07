<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\InfluencerPortfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InfluencerPortfolioController extends Controller
{
    /**
     * Display portfolio items for a specific creator
     */
    public function index(Influencer $influencer): View
    {
        $portfolios = $influencer->portfolios()->orderBy('sort_order')->get();

        return view('backend.pages.creators.portfolio.index', [
            'influencer' => $influencer,
            'portfolios' => $portfolios
        ]);
    }

    /**
     * Show the form for creating a new portfolio item
     */
    public function create(Influencer $influencer): View
    {
        return view('backend.pages.creators.portfolio.create', [
            'influencer' => $influencer
        ]);
    }

    /**
     * Store a newly created portfolio item
     */
    public function store(Request $request, Influencer $influencer): RedirectResponse
    {
        $validated = $request->validate(
            [
                'media_type'  => 'required|in:image,video',
                'file'        => 'required|file|mimes:jpeg,png,webp,jpg,mp4,webm,mov|max:10240',
                'title'       => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'nullable|boolean'
            ],
            [
                'file.required'       => 'Please select a file to upload.',
                'file.file'           => 'The file must be a valid file.',
                'file.mimes'          => 'File must be an image (JPG, PNG, WebP) or video (MP4, WebM, MOV).',
                'file.max'            => 'File size must not exceed 10 MB.',
                'media_type.required' => 'Please select a media type (image or video).',
                'media_type.in'       => 'Media type must be either image or video.',
                'title.max'           => 'Title must not exceed 255 characters.',
                'description.max'     => 'Description must not exceed 1000 characters.',
                'sort_order.integer'  => 'Display order must be a valid number.',
                'sort_order.min'      => 'Display order must be 0 or greater.'
            ]
        );

        $file     = $request->file('file');
        $filePath = $file->store("creator-portfolio/{$influencer->id}", 'public');

        InfluencerPortfolio::create([
            'influencer_id' => $influencer->id,
            'media_type'    => $validated['media_type'],
            'file_path'     => $filePath,
            'title'         => $validated['title'],
            'description'   => $validated['description'],
            'sort_order'    => $validated['sort_order'] ?? 0,
            'is_active'     => (bool) ($validated['is_active'] ?? true)
        ]);

        return redirect()
            ->route('dashboard.influencers.portfolio.index', $influencer)
            ->with('success', '✓ Portfolio item added successfully! The file has been uploaded and is now visible in the portfolio.');
    }

    /**
     * Show the form for editing a portfolio item
     */
    public function edit(Influencer $influencer, InfluencerPortfolio $portfolio): View
    {
        // Ensure the portfolio belongs to this influencer
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        return view('backend.pages.creators.portfolio.edit', [
            'influencer' => $influencer,
            'portfolio'  => $portfolio
        ]);
    }

    /**
     * Update the specified portfolio item
     */
    public function update(Request $request, Influencer $influencer, InfluencerPortfolio $portfolio): RedirectResponse
    {
        // Ensure the portfolio belongs to this influencer
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        $validated = $request->validate(
            [
                'media_type'  => 'required|in:image,video',
                'file'        => 'nullable|file|mimes:jpeg,png,webp,jpg,mp4,webm,mov|max:10240',
                'title'       => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'nullable|boolean'
            ],
            [
                'file.file'           => 'The file must be a valid file.',
                'file.mimes'          => 'File must be an image (JPG, PNG, WebP) or video (MP4, WebM, MOV).',
                'file.max'            => 'File size must not exceed 10 MB.',
                'media_type.required' => 'Please select a media type (image or video).',
                'media_type.in'       => 'Media type must be either image or video.',
                'title.max'           => 'Title must not exceed 255 characters.',
                'description.max'     => 'Description must not exceed 1000 characters.',
                'sort_order.integer'  => 'Display order must be a valid number.',
                'sort_order.min'      => 'Display order must be 0 or greater.'
            ]
        );

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($portfolio->file_path) {
                Storage::disk('public')->delete($portfolio->file_path);
            }

            $file                   = $request->file('file');
            $filePath               = $file->store("creator-portfolio/{$influencer->id}", 'public');
            $validated['file_path'] = $filePath;
        }

        $portfolio->update([
            'media_type'  => $validated['media_type'],
            'file_path'   => $validated['file_path'] ?? $portfolio->file_path,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'sort_order'  => $validated['sort_order'] ?? $portfolio->sort_order,
            'is_active'   => (bool) ($validated['is_active'] ?? $portfolio->is_active)
        ]);

        return redirect()
            ->route('dashboard.influencers.portfolio.index', $influencer)
            ->with('success', '✓ Portfolio item updated successfully! All changes have been saved.');
    }

    /**
     * Delete a portfolio item
     */
    public function destroy(Influencer $influencer, InfluencerPortfolio $portfolio): RedirectResponse
    {
        // Ensure the portfolio belongs to this influencer
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        // Delete the file from storage
        if ($portfolio->file_path) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return redirect()
            ->route('dashboard.influencers.portfolio.index', $influencer)
            ->with('success', '✓ Portfolio item deleted successfully! The file has been removed.');
    }

    /**
     * Reorder portfolio items via AJAX
     */
    public function reorder(Request $request, Influencer $influencer): JsonResponse
    {
        $validated = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|integer',
            'items.*.sort_order' => 'required|integer'
        ]);

        foreach ($validated['items'] as $item) {
            InfluencerPortfolio::where('id', $item['id'])
                ->where('influencer_id', $influencer->id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => '✓ Portfolio items reordered successfully!']);
    }

    /**
     * Toggle portfolio item visibility
     */
    public function toggle(Influencer $influencer, InfluencerPortfolio $portfolio): JsonResponse
    {
        // Ensure the portfolio belongs to this influencer
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        $portfolio->update(['is_active' => !$portfolio->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $portfolio->is_active
        ]);
    }
}
