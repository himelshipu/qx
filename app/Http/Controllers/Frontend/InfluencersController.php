<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class InfluencersController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index(): View
    {
        return view('frontend.pages.influencers', [
            'title' => 'Influencers List',
        ]);
    }

}
