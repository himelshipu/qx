<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Creator;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Contracts\CreatorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class EloquentCreatorRepository
 *
 * Handles creator data access using Eloquent ORM.
 */
class EloquentCreatorRepository implements CreatorRepositoryInterface
{
    /**
     * Get paginated creators for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return Creator::query()
            ->with(['user:id,name,email,is_active,profile_image_path,cover_image_path', 'categories:id,name'])
            ->withCount(['categories', 'campaignApplications', 'orderItems'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('display_name', 'like', '%' . $search . '%')
                        ->orWhere('title_name', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('city', 'like', '%' . $search . '%')
                                ->orWhere('country', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status === 'active', fn($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn($query) => $query->where('is_active', false))
            ->when($status === 'featured', fn($query) => $query->where('is_featured', true))
            ->orderByDesc('updated_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get creator summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,categorized:int}
     */
    public function getStats(): array
    {
        return [
            'total'       => Creator::count(),
            'active'      => Creator::where('is_active', true)->count(),
            'inactive'    => Creator::where('is_active', false)->count(),
            'categorized' => Creator::whereHas('categories')->count(),
            'featured'    => Creator::where('is_featured', true)->count()
        ];
    }

    /**
     * Get active categories for creator assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getCategoryOptions(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn(Category $category) => [
                'id'   => $category->id,
                'name' => $category->name
            ]);
    }

    /**
     * Create a user account for a creator.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a creator user account.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Create a creator profile.
     *
     * @param array<string, mixed> $data
     */
    public function createCreator(array $data): Creator
    {
        return Creator::create($data);
    }

    /**
     * Update a creator profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateCreator(Creator $creator, array $data): Creator
    {
        $creator->update($data);

        return $creator->refresh();
    }

    /**
     * Sync creator categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Creator $creator, array $categoryIds): void
    {
        $creator->categories()->sync($categoryIds);
    }

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Delete a creator profile.
     */
    public function deleteCreator(Creator $creator): bool
    {
        return (bool) $creator->delete();
    }

    /**
     * Count records that should block creator deletion.
     */
    public function getDependencyCount(Creator $creator): int
    {
        $reviewCount = Review::where('creator_id', $creator->id)->count();

        return $creator->campaignApplications()->count()
         + $creator->orderItems()->count()
         + $creator->cartItems()->count()
         + $creator->conversations()->count()
             + $reviewCount;
    }

    /**
     * Toggle creator status and return updated record.
     */
    public function toggleStatus(Creator $creator): Creator
    {
        $creator->update([
            'is_active' => !$creator->is_active
        ]);

        return $creator->refresh();
    }

    /**
     * Toggle creator featured status and return updated record.
     */
    public function toggleFeatured(Creator $creator): Creator
    {
        $creator->update([
            'is_featured' => !$creator->is_featured
        ]);

        return $creator->refresh();
    }
}
