<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface BrandRepositoryInterface
 *
 * Defines brand data access operations for admin management.
 */
interface BrandRepositoryInterface
{
    /**
     * Get paginated brands for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get brand summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,verified:int}
     */
    public function getStats(): array;

    /**
     * Create a user account for a brand.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User;

    /**
     * Update a brand user account.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(User $user, array $data): User;

    /**
     * Create a brand profile.
     *
     * @param array<string, mixed> $data
     */
    public function createBrand(array $data): Brand;

    /**
     * Update a brand profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateBrand(Brand $brand, array $data): Brand;

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool;

    /**
     * Delete a brand profile.
     */
    public function deleteBrand(Brand $brand): bool;

    /**
     * Count records that should block brand deletion.
     */
    public function getDependencyCount(Brand $brand): int;

    /**
     * Toggle brand status and return updated record.
     */
    public function toggleStatus(Brand $brand): Brand;

    /**
     * Get featured brands ordered by featured_order.
     *
     * @return Collection<int, Brand>
     */
    public function getFeaturedBrands(?int $limit = null): Collection;

    /**
     * Search brands by brand name for featured modal.
     *
     * @return Collection<int, Brand>
     */
    public function searchBrands(string $query, int $limit = 50): Collection;

    /**
     * Count how many provided IDs are currently featured.
     *
     * @param array<int> $brandIds
     */
    public function countFeaturedByIds(array $brandIds): int;

    /**
     * Increment featured_order for all featured brands.
     */
    public function incrementFeaturedOrder(?int $excludeBrandId = null): void;

    /**
     * Mark a brand as featured at a given priority.
     */
    public function markAsFeatured(Brand $brand, int $priority = 1): Brand;

    /**
     * Remove featured state from a brand.
     */
    public function unmarkFeatured(Brand $brand): Brand;

    /**
     * Get featured brand IDs ordered by featured_order.
     *
     * @return array<int>
     */
    public function getFeaturedBrandIdsByPriority(): array;

    /**
     * Update featured order for provided brand IDs.
     *
     * @param array<int> $brandIds
     */
    public function updateFeaturedOrder(array $brandIds): void;

    /**
     * Get count of featured brands.
     */
    public function getFeaturedCount(): int;

    /**
     * Get the featured brand with lowest priority.
     */
    public function getLowestPriorityFeatured(): ?Brand;

    /**
     * Clear cached dashboard stats.
     */
    public function clearStatsCache(): void;
}
