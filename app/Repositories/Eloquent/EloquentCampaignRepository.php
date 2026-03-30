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

/**
 * Class EloquentCampaignRepository
 *
 * Handles campaign data access using Eloquent ORM.
 */
class EloquentCampaignRepository implements CampaignRepositoryInterface
{
    /**
     * Get paginated campaigns for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $type, ?int $brandId = null, int $perPage = 12): LengthAwarePaginator
    {
        return Campaign::query()
            ->with(['brand:id,brand_name,user_id', 'targeting:id,campaign_id,influencer_count', 'categories:id,name,image_path'])
            ->withCount(['categories', 'applications', 'assets', 'orders', 'orderItems', 'cartItems'])
            ->when($brandId !== null, fn($query) => $query->where('brand_id', $brandId))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('instructions', 'like', '%' . $search . '%')
                        ->orWhere('campaign_type', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%');
                });
            })
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->when($type !== 'all', fn($query) => $query->where('campaign_type', $type))
            ->orderByDesc('updated_at')
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
        $baseQuery = Campaign::query()
            ->when($brandId !== null, fn($query) => $query->where('brand_id', $brandId));

        return [
            'total'     => (clone $baseQuery)->count(),
            'published' => (clone $baseQuery)->where('status', 'published')->count(),
            'draft'     => (clone $baseQuery)->where('status', 'draft')->count(),
            'active'    => (clone $baseQuery)->where('is_active', true)->count()
        ];
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
        return Campaign::create($data);
    }

    /**
     * Update a campaign.
     *
     * @param array<string, mixed> $data
     */
    public function update(Campaign $campaign, array $data): Campaign
    {
        $campaign->update($data);

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
        return (bool) $campaign->delete();
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
}
