<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Web\HomeService;
use App\Services\Web\InfluencerService;
use Illuminate\View\View;

/**
 * Class HomeController
 *
 * Handles HTTP requests for the homepage.
 */
final class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  HomeService $homeService
     * @return void
     */
    public function __construct(
        private readonly HomeService       $homeService,
        private readonly InfluencerService $influencerService
    ) {}

    /**
     * Display the home page.
     *
     * @return View
     */
    public function index(): View
    {
        $homeData              = $this->homeService->getHomePageData();
        $influencersByPlatform = $this->homeService->getInfluencersByPlatform();
        $featuredInfluencers   = $this->influencerService->getFeaturedInfluencers(4);
        $faqItems              = $this->homeService->getHomeFaqItems();
        $testimonials          = $this->homeService->getTestimonials();

        return view('frontend.pages.home', [
            'title'                 => 'Welcome',
            'appName'               => $homeData->appName,
            'appVersion'            => $homeData->appVersion,
            'totalUsers'            => $homeData->totalUsers,
            'users'                 => $homeData->users,
            'influencersByPlatform' => $influencersByPlatform,
            'featuredInfluencers'   => $featuredInfluencers,
            'faqItems'              => $faqItems,
            'testimonials'          => $testimonials,
            'regionOptions'         => $this->influencerService->getRegionFilters()->all(),
            'genderOptions'         => $this->influencerService->getGenderFilters()->all(),
            'followerRangeOptions'  => $this->influencerService->getFollowerRangeFilters()->all(),
        ]);
    }
}
