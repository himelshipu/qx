<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:settings.index')->only(['index']);
        $this->middleware('permission:settings.update')->only(['update']);
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $pages = StaticPage::orderBy('title')->get();
        $footerPages = Setting::get('footer_pages', []);

        return view('backend.pages.settings.index', [
            'pages' => $pages,
            'footerPages' => $footerPages,
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'footer_pages' => 'nullable|array',
            'footer_pages.*' => 'exists:static_pages,id',
        ]);

        // Store pages in the order they were sent
        $pageOrder = $validated['footer_pages'] ?? [];
        Setting::set('footer_pages', $pageOrder);

        return redirect()
            ->route('dashboard.settings.index')
            ->with('success', 'Footer pages updated successfully!');
    }

    /**
     * Update page order via AJAX
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:static_pages,id',
        ]);

        Setting::set('footer_pages', $validated['order']);

        return response()->json([
            'success' => true,
            'message' => 'Page order updated successfully'
        ]);
    }
}
