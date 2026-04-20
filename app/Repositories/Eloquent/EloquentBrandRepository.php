<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\User;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Class EloquentBrandRepository
 *
 * Handles brand data access using Eloquent ORM.
 */
class EloquentBrandRepository implements BrandRepositoryInterface
{
    private const STATS_CACHE_KEY = 'dashboard:brands:stats';
    private const STATS_CACHE_TTL = 300;

    /**
     * Get paginated brands for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return Brand::query()
            ->select([
                'id',
                'user_id',
                'brand_name',
                'industry',
                'website',
                'is_verified',
                'is_featured',
                'featured_order',
                'sort_order',
                'updated_at',
            ])
            ->with(['user:id,name,email,is_active,profile_image_path'])
            ->withCount(['orders', 'reviews'])
            ->searchDashboard($search)
            ->filterStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get brand summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,verified:int}
     */
    public function getStats(): array
    {
        return Cache::remember(self::STATS_CACHE_KEY, now()->addSeconds(self::STATS_CACHE_TTL), function (): array {
            return [
                'total' => Brand::query()->count(),
                'active' => Brand::query()->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true))->count(),
                'inactive' => Brand::query()->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', false))->count(),
                'verified' => Brand::query()->where('is_verified', true)->count(),
            ];
        });
    }

    /**
     * Create a user account for a brand.
     *
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a brand user account.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Create a brand profile.
     *
     * @param  array<string, mixed>  $data
     */
    public function createBrand(array $data): Brand
    {
        return Brand::create($data);
    }

    /**
     * Update a brand profile.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateBrand(Brand $brand, array $data): Brand
    {
        $brand->update($data);

        return $brand->refresh();
    }

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Delete a brand profile.
     */
    public function deleteBrand(Brand $brand): bool
    {
        return (bool) $brand->delete();
    }

    /**
     * Count records that should block brand deletion.
     */
    public function getDependencyCount(Brand $brand): int
    {
        return $brand->orders()->count() + $brand->reviews()->count();
    }

    /**
     * Toggle brand status and return updated record.
     */
    public function toggleStatus(Brand $brand): Brand
    {
        if ($brand->user) {
            $brand->user->update([
                'is_active' => ! $brand->user->is_active,
            ]);
        }

        return $brand->refresh()->load('user:id,is_active');
    }

    /**
     * Get featured brands ordered by featured_order.
     *
     * @return Collection<int, Brand>
     */
    public function getFeaturedBrands(?int $limit = null): Collection
    {
        $resolvedLimit = $limit ?? (int) config('brand.max_featured', 20);

        return Brand::query()
            ->select(['id', 'brand_name', 'is_featured', 'featured_order'])
            ->featured()
            ->orderBy('featured_order')
            ->limit($resolvedLimit)
            ->get();
    }

    /**
     * Search brands by name for featured modal.
     *
     * @return Collection<int, Brand>
     */
    public function searchBrands(string $query, int $limit = 50): Collection
    {
        $term = trim($query);

        if ($term === '') {
            return new Collection();
        }

        return Brand::query()
            ->select(['id', 'brand_name', 'is_featured', 'featured_order'])
            ->where('brand_name', 'like', "%{$term}%")
            ->orderBy('brand_name')
            ->limit($limit)
            ->get();
    }

    /**
     * Count how many provided IDs are currently featured.
     *
     * @param array<int> $brandIds
     */
    public function countFeaturedByIds(array $brandIds): int
    {
        return Brand::query()
            ->whereIn('id', $brandIds)
            ->featured()
            ->count();
    }

    /**
     * Increment featured_order for all featured brands.
     */
    public function incrementFeaturedOrder(?int $excludeBrandId = null): void
    {
        Brand::query()
            ->featured()
            ->when($excludeBrandId !== null, fn ($query) => $query->where('id', '!=', $excludeBrandId))
            ->increment('featured_order');
    }

    /**
     * Mark a brand as featured at a given priority.
     */
    public function markAsFeatured(Brand $brand, int $priority = 1): Brand
    {
        $brand->update([
            'is_featured' => true,
            'featured_order' => $priority,
        ]);

        $this->clearStatsCache();

        return $brand->refresh();
    }

    /**
     * Remove featured state from a brand.
     */
    public function unmarkFeatured(Brand $brand): Brand
    {
        $brand->update([
            'is_featured' => false,
            'featured_order' => null,
        ]);

        $this->clearStatsCache();

        return $brand->refresh();
    }

    /**
     * Get featured brand IDs ordered by featured_order.
     *
     * @return array<int>
     */
    public function getFeaturedBrandIdsByPriority(): array
    {
        return Brand::query()
            ->featured()
            ->orderBy('featured_order')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Update featured order for brands.
     *
     * @param array<int> $brandIds
     */
    public function updateFeaturedOrder(array $brandIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $brandIds)));

        if ($normalizedIds === []) {
            return;
        }

        $cases = [];
        foreach ($normalizedIds as $order => $brandId) {
            $cases[] = 'WHEN ' . $brandId . ' THEN ' . ($order + 1);
        }

        $caseSql = 'CASE id ' . implode(' ', $cases) . ' END';

        Brand::query()
            ->whereIn('id', $normalizedIds)
            ->update(['featured_order' => DB::raw($caseSql)]);

        $this->clearStatsCache();
    }

    /**
     * Get count of featured brands.
     */
    public function getFeaturedCount(): int
    {
        return Brand::query()->featured()->count();
    }

    /**
     * Get lowest priority featured brand.
     */
    public function getLowestPriorityFeatured(): ?Brand
    {
        return Brand::query()
            ->featured()
            ->orderByDesc('featured_order')
            ->first();
    }

    public function clearStatsCache(): void
    {
        Cache::forget(self::STATS_CACHE_KEY);
    }
}
