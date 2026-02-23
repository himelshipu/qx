<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ContentLibraryController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(): View
    {
        return view('frontend.pages.content-library', [
            'title' => 'Content Library',
        ]);
    }


}
