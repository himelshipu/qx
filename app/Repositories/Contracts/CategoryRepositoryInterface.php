<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

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
}
