<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EloquentCategoryRepository
 *
 * Handles category data access operations using Eloquent ORM.
 */
class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    /**
     * Get paginated categories for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $featured = 'all', int $perPage = 12): LengthAwarePaginator
    {
        return Category::query()
            ->withCount(['influencers', 'campaigns', 'onboardingProfiles'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('slug', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->when($featured === 'featured', fn($query) => $query->where('is_featured', true))
            ->when($featured === 'non-featured', fn($query) => $query->where('is_featured', false))
            ->orderBy('sort_order')
            ->orderBy('name')
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
        return [
            'total'    => Category::count(),
            'active'   => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'linked'   => Category::query()
                ->whereHas('influencers')
                ->orWhereHas('campaigns')
                ->orWhereHas('onboardingProfiles')
                ->count()
        ];
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
        return Category::create($data);
    }

    /**
     * Update a category.
     *
     * @param array<string, mixed> $data
     */
    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
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

        return $category->refresh();
    }

    /**
     * Get top featured categories.
     *
     * @param int $limit Maximum number of featured categories to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeaturedCategories(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()
            ->where('is_featured', true)
            ->where('is_active', true)
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
    public function searchCategories(string $query, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('slug', 'like', '%' . $query . '%');
            })
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
    }

    /**
     * Get count of featured categories.
     */
    public function getFeaturedCount(): int
    {
        return Category::where('is_featured', true)->count();
    }

    /**
     * Get lowest priority featured category (last to be removed).
     */
    public function getLowestPriorityFeatured(): ?Category
    {
        return Category::query()
            ->where('is_featured', true)
            ->orderByDesc('featured_order')
            ->first();
    }

}