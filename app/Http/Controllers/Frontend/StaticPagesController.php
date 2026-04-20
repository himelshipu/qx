<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StaticPagesController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function faq(): View
    {
        return view('frontend.pages.faq', [
            'title' => 'FAQ',
        ]);
    }

    public function support(): View
    {
        return view('frontend.pages.support', [
            'title' => 'support',
        ]);
    }

    public function influencerEditProfile(): View
    {
        // Redirect to dashboard edit (authenticated) — influencer edit should be handled by InfluencerProfileController
        return redirect()->route('dashboard.index');
    }

    public function influencerProfile(): View
    {
        // Public influencer profile should be served by InfluencerProfileController
        return redirect()->route('home');
    }

    public function brandProfile(): View
    {
        // Public brand profile should be served by BrandProfileController
        return redirect()->route('home');
    }

    public function influencers(): View
    {
        return view('frontend.pages.influencers', [
            'title' => 'Influencers',
        ]);
    }
}
