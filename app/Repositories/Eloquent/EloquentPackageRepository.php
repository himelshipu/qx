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
    public function paginateForDashboard(string $search, string $status, string $platform, int $perPage = 12): LengthAwarePaginator
    {
        return Package::query()
            ->with('createdBy:id,name')
            ->withCount(['cartItems', 'orderItems'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('platform', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('currency', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->when($platform !== 'all', fn($query) => $query->where('platform', $platform))
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get dashboard package summary stats.
     *
     * @return array{total:int,active:int,inactive:int,in_use:int}
     */
    public function getStats(): array
    {
        return [
            'total'    => Package::count(),
            'active'   => Package::where('is_active', true)->count(),
            'inactive' => Package::where('is_active', false)->count(),
            'in_use'   => Package::query()
                ->whereHas('cartItems')
                ->orWhereHas('orderItems')
                ->count()
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
