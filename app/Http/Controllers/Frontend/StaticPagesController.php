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

    public function creatorEditProfile(): View
    {
        return view('frontend.pages.creator-edit-profile', [
            'title' => 'Creator Edit Profile',
        ]);
    }

    public function creatorProfile(): View
    {
        return view('frontend.pages.creator-profile', [
            'title' => 'Creator Profile',
        ]);
    }

}
