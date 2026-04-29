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
     * Display portfolio items for a specific influencer
     */
    public function index(Influencer $influencer): View
    {
        $portfolios = $influencer->portfolios()->orderBy('sort_order')->get();

        return view('backend.pages.influencers.portfolio.index', [
            'influencer' => $influencer,
            'portfolios' => $portfolios,
        ]);
    }

    /**
     * Show the form for creating a new portfolio item
     */
    public function create(Influencer $influencer): View
    {
        return view('backend.pages.influencers.portfolio.create', [
            'influencer' => $influencer,
        ]);
    }

    /**
     * Store a newly created portfolio item (supports single or multiple files)
     */
    public function store(Request $request, Influencer $influencer): RedirectResponse
    {
        $uploadMode = $request->input('upload_mode', 'single');
        
        if ($uploadMode === 'bulk') {
            // Bulk upload validation
            $validator = validator($request->all(), [
                'portfolio_files' => 'required|array|min:1|max:10',
                'portfolio_files.*' => 'required|file|mimes:jpeg,png,webp,jpg,mp4,webm,mov|max:10240',
                'is_active' => 'nullable|boolean',
            ], [
                'portfolio_files.required' => 'Please select at least one file to upload.',
                'portfolio_files.array' => 'Portfolio files must be an array.',
                'portfolio_files.min' => 'Please select at least one file to upload.',
                'portfolio_files.max' => 'You can upload a maximum of 10 files at once.',
                'portfolio_files.*.required' => 'All files must be valid.',
                'portfolio_files.*.file' => 'Each file must be a valid file.',
                'portfolio_files.*.mimes' => 'Each file must be an image (JPG, PNG, WebP) or video (MP4, WebM, MOV).',
                'portfolio_files.*.max' => 'Each file size must not exceed 10 MB.',
            ]);

            if ($validator->fails()) {
                // Store old files info to show in the form
                $oldFiles = [];
                if ($request->hasFile('portfolio_files')) {
                    foreach ($request->file('portfolio_files') as $file) {
                        $oldFiles[] = $file->getClientOriginalName();
                    }
                }
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('active_tab', 'bulk')
                    ->with('_old_files', $oldFiles)
                    ->withErrors($validator);
            }

            $validated = $validator->validated();
            $uploadedCount = 0;
            $failedFiles = [];
            $currentMaxSortOrder = $influencer->portfolios()->max('sort_order') ?? -1;

            // Process each file
            foreach ($request->file('portfolio_files') as $index => $file) {
                try {
                    $filePath = $file->store("influencer-portfolio/{$influencer->id}", 'public');
                    $mimeType = (string) $file->getMimeType();
                    $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';

                    InfluencerPortfolio::create([
                        'influencer_id' => $influencer->id,
                        'media_type' => $mediaType,
                        'file_path' => $filePath,
                        'title' => ucfirst($mediaType) . ' ' . ($currentMaxSortOrder + $uploadedCount + 2),
                        'description' => null,
                        'sort_order' => $currentMaxSortOrder + $uploadedCount + 1,
                        'is_active' => (bool) ($validated['is_active'] ?? true),
                    ]);

                    $uploadedCount++;
                } catch (\Exception $e) {
                    \Log::error('Portfolio file upload error: ' . $e->getMessage());
                    $failedFiles[] = $file->getClientOriginalName();
                }
            }

            if ($uploadedCount === 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('active_tab', 'bulk')
                    ->withErrors(['portfolio_files' => 'Failed to upload any files. Please check file formats and sizes.']);
            }

            $message = $uploadedCount === 1 
                ? '✓ 1 portfolio item added successfully!' 
                : "✓ $uploadedCount portfolio items added successfully!";
            
            if (!empty($failedFiles)) {
                $message .= ' However, ' . count($failedFiles) . ' file(s) failed: ' . implode(', ', $failedFiles);
            }

            return redirect()
                ->route('dashboard.influencers.portfolio.index', $influencer)
                ->with('success', $message);
        } else {
            // Single file upload
            $validator = validator($request->all(), [
                'file' => 'required|file|mimes:jpeg,png,webp,jpg,mp4,webm,mov|max:10240',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:1000',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ], [
                'file.required' => 'Please select a file to upload.',
                'file.file' => 'The file must be a valid file.',
                'file.mimes' => 'File must be an image (JPG, PNG, WebP) or video (MP4, WebM, MOV).',
                'file.max' => 'File size must not exceed 10 MB.',
                'title.max' => 'Title must not exceed 255 characters.',
                'description.max' => 'Description must not exceed 1000 characters.',
                'sort_order.integer' => 'Display order must be a valid number.',
                'sort_order.min' => 'Display order must be 0 or greater.',
            ]);

            if ($validator->fails()) {
                $oldFile = $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null;
                
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('active_tab', 'single')
                    ->with('_old_file', $oldFile)
                    ->withErrors($validator);
            }

            $validated = $validator->validated();
            $file = $request->file('file');
            
            // Auto-detect media type from file
            $mimeType = (string) $file->getMimeType();
            $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            
            $filePath = $file->store("influencer-portfolio/{$influencer->id}", 'public');

            InfluencerPortfolio::create([
                'influencer_id' => $influencer->id,
                'media_type' => $mediaType,
                'file_path' => $filePath,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => (bool) ($validated['is_active'] ?? true),
            ]);

            return redirect()
                ->route('dashboard.influencers.portfolio.index', $influencer)
                ->with('success', '✓ Portfolio item added successfully! The file has been uploaded and is now visible in the portfolio.');
        }
    }

    /**
     * Show the form for editing a portfolio item
     */
    public function edit(Influencer $influencer, InfluencerPortfolio $portfolio): View
    {
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        return view('backend.pages.influencers.portfolio.edit', [
            'influencer' => $influencer,
            'portfolio' => $portfolio,
        ]);
    }

    /**
     * Update the specified portfolio item
     */
    public function update(Request $request, Influencer $influencer, InfluencerPortfolio $portfolio): RedirectResponse
    {
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        $validated = $request->validate([
            'file' => 'nullable|file|mimes:jpeg,png,webp,jpg,mp4,webm,mov|max:10240',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file
            if ($portfolio->file_path) {
                Storage::disk('public')->delete($portfolio->file_path);
            }

            $file = $request->file('file');
            $mimeType = (string) $file->getMimeType();
            $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
            $filePath = $file->store("influencer-portfolio/{$influencer->id}", 'public');
            
            $portfolio->media_type = $mediaType;
            $portfolio->file_path = $filePath;
        }

        $portfolio->title = $validated['title'];
        $portfolio->description = $validated['description'];
        $portfolio->sort_order = $validated['sort_order'] ?? $portfolio->sort_order;
        $portfolio->is_active = (bool) ($validated['is_active'] ?? $portfolio->is_active);
        $portfolio->save();

        return redirect()
            ->route('dashboard.influencers.portfolio.index', $influencer)
            ->with('success', '✓ Portfolio item updated successfully!');
    }

    /**
     * Delete a portfolio item
     */
    public function destroy(Influencer $influencer, InfluencerPortfolio $portfolio): RedirectResponse
    {
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        if ($portfolio->file_path) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return redirect()
            ->route('dashboard.influencers.portfolio.index', $influencer)
            ->with('success', '✓ Portfolio item deleted successfully!');
    }

    /**
     * Reorder portfolio items via AJAX
     */
    public function reorder(Request $request, Influencer $influencer): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.sort_order' => 'required|integer',
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
        abort_if($portfolio->influencer_id !== $influencer->id, 404);

        $portfolio->update(['is_active' => !$portfolio->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $portfolio->is_active,
        ]);
    }
}