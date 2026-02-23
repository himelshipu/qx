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

    

}
