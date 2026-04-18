<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Campaign;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FollowerRange;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class EloquentCampaignRepository
 *
 * Handles campaign data access using Eloquent ORM.
 */
class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    private const STATS_CACHE_TTL_SECONDS = 60;

    /**
     * Get paginated campaigns for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $type, ?int $brandId = null, int $perPage = 12): LengthAwarePaginator
    {
        return Campaign::query()
            ->forDashboard()
            ->dashboardBrand($brandId)
            ->searchDashboard($search)
            ->dashboardStatus($status)
            ->dashboardType($type)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get campaign summary stats for dashboard.
     *
     * @return array{total:int,published:int,draft:int,active:int}
     */
    public function getStats(?int $brandId = null): array
    {
        $cacheKey = $this->statsCacheKey($brandId);

        return Cache::remember($cacheKey, self::STATS_CACHE_TTL_SECONDS, function () use ($brandId): array {
            $summary = Campaign::query()
                ->dashboardBrand($brandId)
                ->selectRaw('COUNT(*) as total')
                ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published")
                ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
                ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
                ->first();

            return [
                'total' => (int) ($summary->total ?? 0),
                'published' => (int) ($summary->published ?? 0),
                'draft' => (int) ($summary->draft ?? 0),
                'active' => (int) ($summary->active ?? 0),
            ];
        });
    }

    /**
     * Get active brands for admin campaign assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getBrandOptions(): Collection
    {
        return Brand::query()
            ->with('user:id,is_active')
            ->whereHas('user', fn($query) => $query->where('is_active', true))
            ->orderBy('brand_name')
            ->get(['id', 'brand_name'])
            ->map(fn(Brand $brand) => [
                'id'   => $brand->id,
                'name' => $brand->brand_name
            ]);
    }

    /**
     * Get active categories for campaign assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getCategoryOptions(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn(Category $category) => [
                'id'   => $category->id,
                'name' => $category->name
            ]);
    }

    /**
     * Get active follower ranges for campaign targeting.
     *
     * @return Collection<int, array{id:int,label:string}>
     */
    public function getFollowerRangeOptions(): Collection
    {
        return FollowerRange::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get(['id', 'label'])
            ->map(fn(FollowerRange $followerRange) => [
                'id'    => $followerRange->id,
                'label' => $followerRange->label
            ]);
    }

    /**
     * Create a campaign.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Campaign
    {
        $campaign = Campaign::create($data);
        $this->forgetStatsCache((int) $campaign->brand_id);

        return $campaign;
    }

    /**
     * Update a campaign.
     *
     * @param array<string, mixed> $data
     */
    public function update(Campaign $campaign, array $data): Campaign
    {
        $oldBrandId = (int) $campaign->brand_id;
        $campaign->update($data);

        $this->forgetStatsCache($oldBrandId, (int) $campaign->brand_id);

        return $campaign->refresh();
    }

    /**
     * Sync campaign categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Campaign $campaign, array $categoryIds): void
    {
        $campaign->categories()->sync($categoryIds);
    }

    /**
     * Sync campaign follower ranges.
     *
     * @param array<int, int|string> $followerRangeIds
     */
    public function syncFollowerRanges(Campaign $campaign, array $followerRangeIds): void
    {
        $campaign->followerRanges()->sync($followerRangeIds);
    }

    /**
     * Upsert campaign targeting details.
     *
     * @param array<string, mixed> $targetingData
     */
    public function upsertTargeting(Campaign $campaign, array $targetingData): void
    {
        $campaign->targeting()->updateOrCreate(
            ['campaign_id' => $campaign->id],
            $targetingData
        );
    }

    /**
     * Sync campaign target countries.
     *
    * @param array<int, array{country_code:string}> $countries
     */
    public function syncTargetCountries(Campaign $campaign, array $countries): void
    {
        $codes = collect($countries)
            ->pluck('country_code')
            ->values()
            ->all();

        if ($codes === []) {
            $campaign->targetCountries()->delete();

            return;
        }

        $campaign->targetCountries()
            ->whereNotIn('country_code', $codes)
            ->delete();

        foreach ($countries as $country) {
            $campaign->targetCountries()->updateOrCreate(
                ['country_code' => $country['country_code']],
                []
            );
        }
    }

    /**
     * Delete a campaign.
     */
    public function delete(Campaign $campaign): bool
    {
        $brandId = (int) $campaign->brand_id;
        $deleted = (bool) $campaign->delete();

        if ($deleted) {
            $this->forgetStatsCache($brandId);
        }

        return $deleted;
    }

    /**
     * Count records that should block campaign deletion.
     */
    public function getDependencyCount(Campaign $campaign): int
    {
        return $campaign->applications()->count()
         + $campaign->orders()->count()
         + $campaign->orderItems()->count()
         + $campaign->cartItems()->count();
    }

    /**
     * Build cache key for stats by scope.
     */
    private function statsCacheKey(?int $brandId): string
    {
        return 'dashboard.campaigns.stats.' . ($brandId === null ? 'all' : ('brand.' . $brandId));
    }

    /**
     * Forget stats caches for global and scoped brand payloads.
     */
    private function forgetStatsCache(int ...$brandIds): void
    {
        Cache::forget($this->statsCacheKey(null));

        foreach (array_unique($brandIds) as $brandId) {
            if ($brandId > 0) {
                Cache::forget($this->statsCacheKey($brandId));
            }
        }
    }
}
