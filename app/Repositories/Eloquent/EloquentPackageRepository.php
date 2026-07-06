<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EloquentPackageRepository
 *
 * Handles package data access operations using Eloquent ORM.
 */
class EloquentPackageRepository implements PackageRepositoryInterface
{
    /**
     * Get paginated packages for dashboard listing.
     */
    public function paginateForDashboard(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $search = (string) ($filters['q'] ?? '');
        $status = (string) ($filters['status'] ?? 'all');
        $platform = (string) ($filters['platform'] ?? 'all');
        $influencerId = $filters['influencer_id'] ?? 'all';

        return Package::query()
            ->forDashboard()
            ->dashboardSearch($search)
            ->dashboardStatus($status)
            ->dashboardPlatform($platform)
            ->dashboardInfluencer($influencerId)
            ->dashboardOrder()
            ->paginate($perPage);
    }

    /**
     * Get dashboard package summary stats.
     *
     * @return array{total:int,active:int,inactive:int,in_use:int}
     */
    public function getStats(): array
    {
        $counts = Package::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN EXISTS (SELECT 1 FROM cart_items WHERE cart_items.package_id = packages.id) OR EXISTS (SELECT 1 FROM order_items WHERE order_items.package_id = packages.id) THEN 1 ELSE 0 END) as in_use')
            ->first();

        return [
            'total' => (int) ($counts?->total ?? 0),
            'active' => (int) ($counts?->active ?? 0),
            'inactive' => (int) ($counts?->inactive ?? 0),
            'in_use' => (int) ($counts?->in_use ?? 0),
        ];
    }

    /**
     * Create a package.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Package
    {
        return Package::create($data);
    }

    /**
     * Update a package.
     *
     * @param array<string, mixed> $data
     */
    public function update(Package $package, array $data): Package
    {
        $package->update($data);

        return $package->refresh();
    }

    /**
     * Delete a package.
     */
    public function delete(Package $package): bool
    {
        return (bool) $package->delete();
    }

    /**
     * Count package dependencies before delete.
     */
    public function getDependencyCount(Package $package): int
    {
        return $package->cartItems()->count() + $package->orderItems()->count();
    }

    /**
     * Toggle active status and return updated package.
     */
    public function toggleStatus(Package $package): Package
    {
        $package->update([
            'is_active' => !$package->is_active
        ]);

        return $package->refresh();
    }
}
