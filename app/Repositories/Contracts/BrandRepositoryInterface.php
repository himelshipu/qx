<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
