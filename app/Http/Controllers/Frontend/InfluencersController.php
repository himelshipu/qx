<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Web\InfluencerService;
use Illuminate\Support\Collection;
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

        $contentTypeIds = array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $request->get('contentTypes', ''))))));

        // Handle sorting
        $sort = $request->get('sort', 'followers_desc');
        $gender = trim((string) $request->get('gender', ''));
        $region = trim((string) $request->get('region', ''));
        $followers = trim((string) $request->get('followers', ''));
        $price = trim((string) $request->get('price', ''));

        $contentTypeOptions = $this->influencerService->getContentTypeFilters();
        $selectedContentTypes = $this->filterSelectedContentTypes($contentTypeOptions, $contentTypeIds);

        $influencers = $this->influencerService->paginateInfluencers($platformKey, 20, [
            'categories' => $categories->pluck('id')->toArray(),
            'contentTypes' => $contentTypeIds,
            'sort'       => $sort,
            'gender'     => $gender,
            'region'     => $region,
            'followers'  => $followers,
            'price'      => $price,
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
            'selectedCategories' => $categories,
            'contentTypeOptions' => $contentTypeOptions->all(),
            'selectedContentTypes' => $selectedContentTypes->all(),
            'priceRange'         => $this->influencerService->getPackagePriceRange(),
            'selectedPriceLabel' => $this->formatPriceRangeFilterLabel($price),
            'regionOptions'      => $this->influencerService->getRegionFilters()->all(),
            'genderOptions'      => $this->influencerService->getGenderFilters()->all(),
            'followerRangeOptions' => $this->influencerService->getFollowerRangeFilters()->all(),
            'selectedFilters'    => [
                'contentTypes' => $selectedContentTypes->pluck('label')->all(),
                'gender'    => in_array($gender, ['male', 'female', 'other'], true) ? ucfirst($gender) : null,
                'region'    => $region !== '' ? $region : null,
                'followers' => $this->formatFollowersFilterLabel($followers),
                'price'     => $this->formatPriceRangeFilterLabel($price),
            ],
        ]);
    }

    /**
     * Display influencers filtered by category.
     */
    public function byCategory(string $categorySlug, Request $request): View
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        $sort = (string) $request->get('sort', 'followers_desc');
        $gender = trim((string) $request->get('gender', ''));
        $region = trim((string) $request->get('region', ''));
        $followers = trim((string) $request->get('followers', ''));
        $price = trim((string) $request->get('price', ''));
        $contentTypeIds = array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $request->get('contentTypes', ''))))));

        $contentTypeOptions = $this->influencerService->getContentTypeFilters();
        $selectedContentTypes = $this->filterSelectedContentTypes($contentTypeOptions, $contentTypeIds);

        $influencers = $this->influencerService->paginateInfluencers(null, 20, [
            'categories' => [$category->id],
            'contentTypes' => $contentTypeIds,
            'sort'       => $sort,
            'gender'     => $gender,
            'region'     => $region,
            'followers'  => $followers,
            'price'      => $price,
        ]);

        return view('frontend.pages.influencers', [
            'title'              => $category->name . ' Influencers',
            'influencers'        => $influencers,
            'platformFilters'    => $this->influencerService->getPlatformFilters(),
            'selectedPlatform'   => null,
            'selectedCategories' => collect([$category]),
            'contentTypeOptions' => $contentTypeOptions->all(),
            'selectedContentTypes' => $selectedContentTypes->all(),
            'priceRange'         => $this->influencerService->getPackagePriceRange(),
            'selectedPriceLabel' => $this->formatPriceRangeFilterLabel($price),
            'regionOptions'      => $this->influencerService->getRegionFilters()->all(),
            'genderOptions'      => $this->influencerService->getGenderFilters()->all(),
            'followerRangeOptions' => $this->influencerService->getFollowerRangeFilters()->all(),
            'selectedFilters'    => [
                'contentTypes' => $selectedContentTypes->pluck('label')->all(),
                'gender'    => in_array($gender, ['male', 'female', 'other'], true) ? ucfirst($gender) : null,
                'region'    => $region !== '' ? $region : null,
                'followers' => $this->formatFollowersFilterLabel($followers),
                'price'     => $this->formatPriceRangeFilterLabel($price),
            ],
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
                'icon' => $cat->icon_path ? \App\Helpers\ImageHelper::url($cat->icon_path) : null
            ]);

        return response()->json(['categories' => $categories]);
    }

    private function formatFollowersFilterLabel(string $value): ?string
    {
        return match (trim($value)) {
            '0-10000' => '0 - 10K',
            '10001-50000' => '10K - 50K',
            '50001-100000' => '50K - 100K',
            '100001-500000' => '100K - 500K',
            '500001+' => '500K+',
            default => null,
        };
    }

    /**
     * @param Collection<int, array{value:string,label:string,price_label:string}> $contentTypeOptions
     * @return Collection<int, array{value:string,label:string,price_label:string}>
     */
    private function filterSelectedContentTypes(Collection $contentTypeOptions, array $contentTypeIds): Collection
    {
        $selectedIds = array_map('strval', $contentTypeIds);

        return $contentTypeOptions
            ->filter(fn (array $contentType): bool => in_array((string) $contentType['value'], $selectedIds, true))
            ->values();
    }

    private function formatPriceRangeFilterLabel(string $value): ?string
    {
        $value = trim($value);

        if ($value === '' || preg_match('/^(\d+(?:\.\d+)?)\-(\d+(?:\.\d+)?)$/', $value, $matches) !== 1) {
            return null;
        }

        $min = (float) $matches[1];
        $max = (float) $matches[2];
        $maxLabel = '$' . number_format($max, $max === (float) (int) $max ? 0 : 2) . ($max >= 3000.0 ? '+' : '');

        return '$' . number_format($min, $min === (float) (int) $min ? 0 : 2) . ' - ' . $maxLabel;
    }

}
