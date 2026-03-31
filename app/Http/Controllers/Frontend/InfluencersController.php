<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Web\InfluencerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    public function index(Request $request): View
    {
        // Get platformSlug from route parameter or query string
        $platformSlug = $request->route('platformSlug') ?? $request->query('platformSlug');

        $platformKey = $this->influencerService->resolvePlatformKeyFromSlug($platformSlug);

        if ($platformSlug !== null && trim($platformSlug) !== '' && $platformKey === null) {
            abort(404);
        }

        // Handle category filtering - always initialize as collection
        $categories = collect();
        if ($request->has('categories')) {
            $categoryIds = array_filter(array_map('intval', explode(',', (string) $request->get('categories'))));
            $categories  = Category::whereIn('id', $categoryIds)->get();
        }

        // Handle sorting
        $sort = $request->get('sort', 'followers_desc');

        $influencers = $this->influencerService->paginateInfluencers($platformKey, 20, [
            'categories' => $categories->pluck('id')->toArray(),
            'sort'       => $sort
        ]);

        $platformFilters  = $this->influencerService->getPlatformFilters();
        $selectedPlatform = $platformKey !== null
        ? $this->influencerService->getPlatformMeta($platformKey)
        : null;

        return view('frontend.pages.influencers', [
            'title'              => $selectedPlatform !== null
            ? $selectedPlatform['label'] . ' Influencers'
            : 'Influencers',
            'influencers'        => $influencers,
            'platformFilters'    => $platformFilters,
            'selectedPlatform'   => $selectedPlatform,
            'selectedCategories' => $categories
        ]);
    }

    /**
     * Display influencers filtered by category.
     */
    public function byCategory(string $categorySlug, Request $request): View
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $sort = (string) $request->get('sort', 'followers_desc');

        $influencers = $this->influencerService->paginateInfluencers(null, 20, [
            'categories' => [$category->id],
            'sort'       => $sort
        ]);

        return view('frontend.pages.influencers', [
            'title'              => $category->name . ' Influencers',
            'influencers'        => $influencers,
            'platformFilters'    => $this->influencerService->getPlatformFilters(),
            'selectedPlatform'   => null,
            'selectedCategories' => collect([$category])
        ]);
    }

    /**
     * Display UGC page with random 4 categories.
     */
    public function ugc(Request $request): View
    {
        // Get 4 random categories
        $categories = Category::where('is_active', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('frontend.pages.ugc', [
            'title'      => 'User Generated Content (UGC)',
            'categories' => $categories
        ]);
    }

    /**
     * API endpoint to get all active categories.
     */
    public function apiCategories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'icon_path'])
            ->map(fn($cat) => [
                'id'   => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'icon' => $cat->icon_path ? asset('storage/' . $cat->icon_path) : null
            ]);

        return response()->json(['categories' => $categories]);
    }

}
