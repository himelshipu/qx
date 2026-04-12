<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaticPageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:static-pages.index')->only(['index']);
        $this->middleware('permission:static-pages.create')->only(['create', 'store']);
        $this->middleware('permission:static-pages.show')->only(['show']);
        $this->middleware('permission:static-pages.edit')->only(['edit', 'update']);
        $this->middleware('permission:static-pages.toggle-status')->only(['toggleStatus']);
        $this->middleware('permission:static-pages.destroy')->only(['destroy']);
    }

    /**
     * Display a listing of static pages.
     */
    public function index(): View
    {
        $pages = StaticPage::latest('updated_at')->paginate(15);

        $stats = [
            'total' => StaticPage::count(),
            'published' => StaticPage::where('is_active', true)->count(),
            'draft' => StaticPage::where('is_active', false)->count(),
        ];

        return view('backend.pages.static-pages.index', [
            'pages' => $pages,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new static page.
     */
    public function create(): View
    {
        return view('backend.pages.static-pages.create');
    }

    /**
     * Store a newly created static page in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255|unique:static_pages,title',
            'slug'               => 'required|string|max:255|unique:static_pages,slug',
            'content'            => 'required|string',
            'meta_description'   => 'nullable|string|max:500',
            'meta_keywords'      => 'nullable|string|max:500',
            'is_active'          => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $page = StaticPage::create($validated);

        return redirect()
            ->route('dashboard.static-pages.show', $page)
            ->with('success', "Static page '{$page->title}' created successfully.");
    }

    /**
     * Display the specified static page.
     */
    public function show(StaticPage $staticPage): View
    {
        return view('backend.pages.static-pages.show', [
            'page' => $staticPage,
        ]);
    }

    /**
     * Show the form for editing the specified static page.
     */
    public function edit(StaticPage $staticPage): View
    {
        return view('backend.pages.static-pages.edit', [
            'page' => $staticPage,
        ]);
    }

    /**
     * Update the specified static page in storage.
     */
    public function update(Request $request, StaticPage $staticPage): RedirectResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255|unique:static_pages,title,' . $staticPage->id,
            'slug'               => 'required|string|max:255|unique:static_pages,slug,' . $staticPage->id,
            'content'            => 'required|string',
            'meta_description'   => 'nullable|string|max:500',
            'meta_keywords'      => 'nullable|string|max:500',
            'is_active'          => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $staticPage->update($validated);

        return redirect()
            ->route('dashboard.static-pages.show', $staticPage)
            ->with('success', "Static page '{$staticPage->title}' updated successfully.");
    }

    /**
     * Toggle the active status of a static page.
     */
    public function toggleStatus(StaticPage $staticPage)
    {
        try {
            $staticPage->update(['is_active' => !$staticPage->is_active]);

            $status = $staticPage->is_active ? 'enabled' : 'disabled';
            $message = "Static page '{$staticPage->title}' has been {$status}.";

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $staticPage->is_active,
                ], 200);
            }

            return redirect()
                ->back()
                ->with('success', $message);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating page status: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error updating page status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified static page from storage.
     */
    public function destroy(StaticPage $staticPage): RedirectResponse
    {
        $title = $staticPage->title;
        $staticPage->delete();

        return redirect()
            ->route('dashboard.static-pages.index')
            ->with('success', "Static page '{$title}' deleted successfully.");
    }
}
