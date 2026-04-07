<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\User;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class EloquentBrandRepository
 *
 * Handles brand data access using Eloquent ORM.
 */
class EloquentBrandRepository implements BrandRepositoryInterface
{
    /**
     * Get paginated brands for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return Brand::query()
            ->with(['user:id,name,email,is_active,profile_image_path'])
            ->withCount(['orders', 'reviews'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('brand_name', 'like', '%'.$search.'%')
                        ->orWhere('industry', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%')
                                ->orWhere('city', 'like', '%'.$search.'%')
                                ->orWhere('country', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($status === 'active', fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true)))
            ->when($status === 'inactive', fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', false)))
            ->orderByDesc('updated_at')
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
        return [
            'total' => Brand::count(),
            'active' => Brand::whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true))->count(),
            'inactive' => Brand::whereHas('user', fn ($userQuery) => $userQuery->where('is_active', false))->count(),
            'verified' => Brand::where('is_verified', true)->count(),
        ];
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
}
