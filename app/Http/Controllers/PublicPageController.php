<?php

namespace App\Http\Controllers;

use App\Models\StaticPage;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * Display a static page by slug.
     */
    public function show(string $slug): View
    {
        $page = StaticPage::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('frontend.pages.static', [
            'page' => $page,
        ]);
    }
}
