<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Web\InfluencerService;
use Illuminate\View\View;

/**
 * Class InfluencersController
 *
 * Handles frontend influencer listing pages.
 */
class InfluencersController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private readonly InfluencerService $influencerService
    ) {}

    /**
     * Display paginated influencers, optionally filtered by platform slug.
     */
    public function index(?string $platformSlug = null): View
    {
        $platformKey = $this->influencerService->resolvePlatformKeyFromSlug($platformSlug);

        if ($platformSlug !== null && trim($platformSlug) !== '' && $platformKey === null) {
            abort(404);
        }

        $influencers      = $this->influencerService->paginateInfluencers($platformKey, 20);
        $platformFilters  = $this->influencerService->getPlatformFilters();
        $selectedPlatform = $platformKey !== null
        ? $this->influencerService->getPlatformMeta($platformKey)
        : null;

        return view('frontend.pages.influencers', [
            'title'            => $selectedPlatform !== null
            ? $selectedPlatform['label'] . ' Influencers'
            : 'Influencers',
            'influencers'      => $influencers,
            'platformFilters'  => $platformFilters,
            'selectedPlatform' => $selectedPlatform
        ]);
    }

}
