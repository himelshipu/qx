<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Web\HomeService;
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
     * @param HomeService $homeService
     * @return void
     */
    public function __construct(
        private readonly HomeService $homeService
    ) {}

    /**
     * Display the home page.
     *
     * @return View
     */
    public function index(): View
    {
        $homeData = $this->homeService->getHomePageData();

        return view('frontend.pages.home', [
            'title' => 'Welcome',
            'appName' => $homeData->appName,
            'appVersion' => $homeData->appVersion,
            'totalUsers' => $homeData->totalUsers,
            'users' => $homeData->users,
        ]);
    }
}
