<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Influencer;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Contracts\InfluencerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class EloquentInfluencerRepository
 *
 * Handles influencer data access using Eloquent ORM.
 */
class EloquentInfluencerRepository implements InfluencerRepositoryInterface
{
    /**
     * Get paginated influencers for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return Influencer::query()
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
     * Get influencer summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,categorized:int}
     */
    public function getStats(): array
    {
        return [
            'total'       => Influencer::count(),
            'active'      => Influencer::where('is_active', true)->count(),
            'inactive'    => Influencer::where('is_active', false)->count(),
            'categorized' => Influencer::whereHas('categories')->count(),
            'featured'    => Influencer::where('is_featured', true)->count()
        ];
    }

    /**
     * Get active categories for influencer assignment.
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
     * Create a user account for an influencer.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update an influencer user account.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }

    /**
     * Create an influencer profile.
     *
     * @param array<string, mixed> $data
     */
    public function createInfluencer(array $data): Influencer
    {
        return Influencer::create($data);
    }

    /**
     * Update an influencer profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateInfluencer(Influencer $influencer, array $data): Influencer
    {
        $influencer->update($data);

        return $influencer->refresh();
    }

    /**
     * Sync influencer categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Influencer $influencer, array $categoryIds): void
    {
        $influencer->categories()->sync($categoryIds);
    }

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool
    {
        return (bool) $user->delete();
    }

    /**
     * Delete an influencer profile.
     */
    public function deleteInfluencer(Influencer $influencer): bool
    {
        return (bool) $influencer->delete();
    }

    /**
     * Count records that should block influencer deletion.
     */
    public function getDependencyCount(Influencer $influencer): int
    {
        $reviewCount = Review::where('influencer_id', $influencer->id)->count();

        return $influencer->campaignApplications()->count()
         + $influencer->orderItems()->count()
         + $influencer->cartItems()->count()
         + $influencer->conversations()->count()
             + $reviewCount;
    }

    /**
     * Toggle influencer status and return updated record.
     */
    public function toggleStatus(Influencer $influencer): Influencer
    {
        $influencer->update([
            'is_active' => !$influencer->is_active
        ]);

        return $influencer->refresh();
    }

    /**
     * Toggle influencer featured status and return updated record.
     */
    public function toggleFeatured(Influencer $influencer): Influencer
    {
        $influencer->update([
            'is_featured' => !$influencer->is_featured
        ]);

        return $influencer->refresh();
    }
}
