<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface CategoryRepositoryInterface
 *
 * Defines category data access operations.
 */
interface CategoryRepositoryInterface
{
    /**
     * Get paginated categories for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $featured = 'all', int $perPage = 12): LengthAwarePaginator;

    /**
     * Get dashboard category summary stats.
     *
     * @return array{total:int,active:int,inactive:int,linked:int}
     */
    public function getStats(): array;

    /**
     * Get next sort order value.
     */
    public function getNextSortOrder(): int;

    /**
     * Create a category.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Category;

    /**
     * Update a category.
     *
     * @param array<string, mixed> $data
     */
    public function update(Category $category, array $data): Category;

    /**
     * Delete a category.
     */
    public function delete(Category $category): bool;

    /**
     * Check if a slug exists optionally ignoring one category id.
     */
    public function hasSlug(string $slug, ?int $ignoreId = null): bool;

    /**
     * Count category dependencies before delete.
     */
    public function getDependencyCount(Category $category): int;

    /**
     * Toggle active status and return updated category.
     */
    public function toggleStatus(Category $category): Category;

    /**
     * Get top featured categories ordered by featured_order.
     *
     * @return Collection<int, Category>
     */
    public function getFeaturedCategories(int $limit = 10): Collection;

    /**
     * Search categories by name/slug.
     *
     * @return Collection<int, Category>
     */
    public function searchCategories(string $query, int $limit = 50): Collection;

    /**
     * Update featured_order for provided category ids.
     *
     * @param array<int> $categoryIds
     */
    public function updateFeaturedOrder(array $categoryIds): void;

    /**
     * Get count of featured categories.
     */
    public function getFeaturedCount(): int;

    /**
     * Get the featured category with lowest priority.
     */
    public function getLowestPriorityFeatured(): ?Category;
}
