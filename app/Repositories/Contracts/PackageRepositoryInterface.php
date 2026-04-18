<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Package;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface PackageRepositoryInterface
 *
 * Defines package data access operations.
 */
interface PackageRepositoryInterface
{
    /**
     * Get paginated packages for dashboard listing.
     *
     * @param array<string, mixed> $filters
     */
    public function paginateForDashboard(array $filters, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get dashboard package summary stats.
     *
     * @return array{total:int,active:int,inactive:int,in_use:int}
     */
    public function getStats(): array;

    /**
     * Create a package.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Package;

    /**
     * Update a package.
     *
     * @param array<string, mixed> $data
     */
    public function update(Package $package, array $data): Package;

    /**
     * Delete a package.
     */
    public function delete(Package $package): bool;

    /**
     * Count package dependencies before delete.
     */
    public function getDependencyCount(Package $package): int;

    /**
     * Toggle active status and return updated package.
     */
    public function toggleStatus(Package $package): Package;
}
