<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\CreatorPortfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CreatorPortfolioController extends Controller
{
    /**
     * Display portfolio items for a specific creator
     */
    public function index(Creator $creator): View
    {
        $portfolios = $creator->portfolios()->orderBy('sort_order')->get();

        return view('backend.pages.creators.portfolio.index', [
            'creator'    => $creator,
            'portfolios' => $portfolios
        ]);
    }

    /**
     * Show the form for creating a new portfolio item
     */
    public function create(Creator $creator): View
    {
        return view('backend.pages.creators.portfolio.create', [
            'creator' => $creator
        ]);
    }

    /**
     * Store a newly created portfolio item
     */
    public function store(Request $request, Creator $creator): RedirectResponse
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
        $filePath = $file->store("creator-portfolio/{$creator->id}", 'public');

        CreatorPortfolio::create([
            'creator_id'  => $creator->id,
            'media_type'  => $validated['media_type'],
            'file_path'   => $filePath,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'sort_order'  => $validated['sort_order'] ?? 0,
            'is_active'   => (bool) ($validated['is_active'] ?? true)
        ]);

        return redirect()
            ->route('dashboard.creators.portfolio.index', $creator)
            ->with('success', '✓ Portfolio item added successfully! The file has been uploaded and is now visible in the portfolio.');
    }

    /**
     * Show the form for editing a portfolio item
     */
    public function edit(Creator $creator, CreatorPortfolio $portfolio): View
    {
        // Ensure the portfolio belongs to this creator
        abort_if($portfolio->creator_id !== $creator->id, 404);

        return view('backend.pages.creators.portfolio.edit', [
            'creator'   => $creator,
            'portfolio' => $portfolio
        ]);
    }

    /**
     * Update the specified portfolio item
     */
    public function update(Request $request, Creator $creator, CreatorPortfolio $portfolio): RedirectResponse
    {
        // Ensure the portfolio belongs to this creator
        abort_if($portfolio->creator_id !== $creator->id, 404);

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
            $filePath               = $file->store("creator-portfolio/{$creator->id}", 'public');
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
            ->route('dashboard.creators.portfolio.index', $creator)
            ->with('success', '✓ Portfolio item updated successfully! All changes have been saved.');
    }

    /**
     * Delete a portfolio item
     */
    public function destroy(Creator $creator, CreatorPortfolio $portfolio): RedirectResponse
    {
        // Ensure the portfolio belongs to this creator
        abort_if($portfolio->creator_id !== $creator->id, 404);

        // Delete the file from storage
        if ($portfolio->file_path) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return redirect()
            ->route('dashboard.creators.portfolio.index', $creator)
            ->with('success', '✓ Portfolio item deleted successfully! The file has been removed.');
    }

    /**
     * Reorder portfolio items via AJAX
     */
    public function reorder(Request $request, Creator $creator): JsonResponse
    {
        $validated = $request->validate([
            'items'              => 'required|array',
            'items.*.id'         => 'required|integer',
            'items.*.sort_order' => 'required|integer'
        ]);

        foreach ($validated['items'] as $item) {
            CreatorPortfolio::where('id', $item['id'])
                ->where('creator_id', $creator->id)
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => '✓ Portfolio items reordered successfully!']);
    }

    /**
     * Toggle portfolio item visibility
     */
    public function toggle(Creator $creator, CreatorPortfolio $portfolio): JsonResponse
    {
        // Ensure the portfolio belongs to this creator
        abort_if($portfolio->creator_id !== $creator->id, 404);

        $portfolio->update(['is_active' => !$portfolio->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $portfolio->is_active
        ]);
    }
}
