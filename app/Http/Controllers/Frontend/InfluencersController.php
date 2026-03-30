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
            $categoryIds = explode(',', $request->get('categories'));
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

        $influencers = $category->creators()
            ->with([
                'user:id,name,slug,city,country,profile_image_path,is_active',
                'platformStats' => fn($q) => $q->where('is_active', true)->orderByDesc('follower_count')
            ])
            ->where('is_active', true)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderByDesc('is_featured')
            ->paginate(20)
            ->withQueryString();

        $creatorIds = $influencers->pluck('id')->all();

        // Get reviews summary
        $reviewsByCreator = \App\Models\Review::query()
            ->whereIn('creator_id', $creatorIds)
            ->selectRaw('creator_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('creator_id')
            ->get()
            ->keyBy('creator_id');

        $influencersData = $influencers->map(function ($creator) use ($reviewsByCreator) {
            $stat        = $creator->platformStats->first();
            $platformKey = $stat !== null
            ? $this->normalizePlatformKey((string) $stat->platform)
            : 'other';

            $reviewSummary = $reviewsByCreator->get($creator->id);
            $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
            ? (float) $reviewSummary->average_rating
            : null;

            return [
                'id'               => $creator->id,
                'slug'             => $creator->user->slug,
                'name'             => $creator->display_name ?: $creator->user->name,
                'title'            => $creator->title_name,
                'location'         => $this->resolveCreatorLocation($creator),
                'image_url'        => image_url($creator->user->profile_image_path),
                'platform'         => $platformKey,
                'platform_label'   => ucfirst($platformKey),
                'platform_slug'    => str()->slug($platformKey),
                'handle'           => $stat?->handle ?? '@user',
                'followers_label'  => $this->formatFollowers($stat?->follower_count ?? 0),
                'engagement_label' => $this->formatPercentage((float) ($stat?->engagement_rate ?? 0)),
                'rating_label'     => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
                'reviews_count'    => $reviewSummary ? (int) $reviewSummary->reviews_count : 0
            ];
        });

        $influencers->setCollection($influencersData);

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

    /**
     * Helper: Format follower count.
     */
    private function formatFollowers(?int $count): string
    {
        if (!$count) {
            return '0';
        }

        if ($count >= 1000000) {
            return number_format($count / 1000000, 1) . 'M';
        }

        if ($count >= 1000) {
            return number_format($count / 1000, 1) . 'K';
        }

        return (string) $count;
    }

    /**
     * Helper: Format percentage.
     */
    private function formatPercentage(?float $percentage): string
    {
        if (!$percentage) {
            return '0%';
        }

        return number_format($percentage, 2) . '%';
    }

    /**
     * Helper: Normalize platform key.
     */
    private function normalizePlatformKey(string $platform): string
    {
        $platform = strtolower(trim($platform));

        $map = [
            'twitter'   => 'x',
            'twitter-x' => 'x',
            'ig'        => 'instagram',
            'tik'       => 'tiktok',
            'yt'        => 'youtube',
            'youtube'   => 'youtube'
        ];

        return $map[$platform] ?? $platform;
    }

    /**
     * Helper: Resolve creator location from city and country.
     */
    private function resolveCreatorLocation(\App\Models\Creator $creator): string
    {
        $parts = array_values(array_filter([
            trim((string) ($creator->user?->city ?? '')),
            trim((string) ($creator->user?->country ?? ''))
        ]));

        return $parts !== [] ? implode(', ', $parts) : 'Location not provided';
    }
}
