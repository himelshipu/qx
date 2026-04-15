<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class EloquentCategoryRepository
 *
 * Handles category data access operations using Eloquent ORM.
 */
class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    private const STATS_CACHE_KEY = 'dashboard.categories.stats';
    private const STATS_CACHE_TTL_SECONDS = 60;

    /**
     * Get paginated categories for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $featured = 'all', int $perPage = 12): LengthAwarePaginator
    {
        return Category::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardFeatured($featured)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get dashboard category summary stats.
     *
     * @return array{total:int,active:int,inactive:int,linked:int}
     */
    public function getStats(): array
    {
        return Cache::remember(self::STATS_CACHE_KEY, self::STATS_CACHE_TTL_SECONDS, function (): array {
            $summary = Category::query()
                ->selectRaw('COUNT(*) as total')
                ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
                ->selectRaw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')
                ->first();

            $linked = Category::query()
                ->where(function ($query): void {
                    $query->whereHas('influencers')
                        ->orWhereHas('campaigns')
                        ->orWhereHas('onboardingProfiles');
                })
                ->count();

            return [
                'total'    => (int) ($summary->total ?? 0),
                'active'   => (int) ($summary->active ?? 0),
                'inactive' => (int) ($summary->inactive ?? 0),
                'linked'   => $linked,
            ];
        });
    }

    /**
     * Get next sort order value.
     */
    public function getNextSortOrder(): int
    {
        return (int) Category::max('sort_order') + 1;
    }

    /**
     * Create a category.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Category
    {
        $category = Category::create($data);
        $this->forgetDashboardCache();

        return $category;
    }

    /**
     * Update a category.
     *
     * @param array<string, mixed> $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        $this->forgetDashboardCache();

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool
    {
        $deleted = (bool) $category->delete();

        if ($deleted) {
            $this->forgetDashboardCache();
        }

        return $deleted;
    }

    /**
     * Check if a slug exists optionally ignoring one category id.
     */
    public function hasSlug(string $slug, ?int $ignoreId = null): bool
    {
        return Category::query()
            ->when($ignoreId !== null, fn($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * Count category dependencies before delete.
     */
    public function getDependencyCount(Category $category): int
    {
        return $category->influencers()->count()
         + $category->campaigns()->count()
         + $category->onboardingProfiles()->count();
    }

    /**
     * Toggle active status and return updated category.
     */
    public function toggleStatus(Category $category): Category
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        $this->forgetDashboardCache();

        return $category->refresh();
    }

    /**
     * Get top featured categories.
     *
     * @param int $limit Maximum number of featured categories to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedCategories(int $limit = 10): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'slug', 'icon_path', 'image_path', 'featured_order'])
            ->featured()
            ->active()
            ->orderBy('featured_order')
            ->limit($limit)
            ->get();
    }

    /**
     * Search categories by name for modal search.
     *
     * @param string $query Search query
     * @param int $limit Limit results
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function searchCategories(string $query, int $limit = 50): Collection
    {
        return Category::query()
            ->select(['id', 'name', 'slug', 'icon_path', 'image_path', 'is_featured', 'featured_order'])
            ->active()
            ->search($query)
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Update featured order for categories.
     *
     * @param array<int> $categoryIds Ordered list of category IDs
     * @return void
     */
    public function updateFeaturedOrder(array $categoryIds): void
    {
        foreach ($categoryIds as $order => $categoryId) {
            Category::where('id', $categoryId)->update([
                'featured_order' => $order + 1
            ]);
        }

        $this->forgetDashboardCache();
    }

    /**
     * Get count of featured categories.
     */
    public function getFeaturedCount(): int
    {
        return Category::featured()->count();
    }

    /**
     * Get lowest priority featured category (last to be removed).
     */
    public function getLowestPriorityFeatured(): ?Category
    {
        return Category::query()
            ->featured()
            ->orderByDesc('featured_order')
            ->first();
    }

    /**
     * Forget cached dashboard fragments that depend on category mutations.
     */
    private function forgetDashboardCache(): void
    {
        Cache::forget(self::STATS_CACHE_KEY);
    }

}