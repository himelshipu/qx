<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Influencer;
use App\Models\Review;
use App\Models\User;
use App\Repositories\Contracts\InfluencerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Class EloquentInfluencerRepository
 *
 * Handles influencer data access using Eloquent ORM.
 */
class EloquentInfluencerRepository implements InfluencerRepositoryInterface
{
    private const STATS_CACHE_KEY = 'dashboard:influencers:stats';
    private const STATS_CACHE_TTL = 300;

    /**
     * Get paginated influencers for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $featured = 'all', int $perPage = 12): LengthAwarePaginator
    {
        return Influencer::query()
            ->forDashboard()
            ->searchDashboard($search)
            ->filterStatus($status)
            ->dashboardFeatured($featured)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get influencer summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,categorized:int,featured:int}
     */
    public function getStats(): array
    {
        return Cache::remember(self::STATS_CACHE_KEY, now()->addSeconds(self::STATS_CACHE_TTL), function (): array {
            return [
                'total'       => Influencer::query()->count(),
                'active'      => Influencer::query()->where('is_active', true)->count(),
                'inactive'    => Influencer::query()->where('is_active', false)->count(),
                'categorized' => Influencer::query()->whereHas('categories')->count(),
                'featured'    => Influencer::query()->where('is_featured', true)->count(),
            ];
        });
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
        $influencer = Influencer::create($data);
        $this->clearStatsCache();

        return $influencer;
    }

    /**
     * Update an influencer profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateInfluencer(Influencer $influencer, array $data): Influencer
    {
        $influencer->update($data);

        $this->clearStatsCache();

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
        $this->clearStatsCache();
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
        $deleted = (bool) $influencer->delete();

        if ($deleted) {
            $this->clearStatsCache();
        }

        return $deleted;
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

        $this->clearStatsCache();

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

        $this->clearStatsCache();

        return $influencer->refresh();
    }

    /**
     * Get featured influencers ordered by featured_priority.
     *
     * @return EloquentCollection<int, Influencer>
     */
    public function getFeaturedInfluencers(?int $limit = null): EloquentCollection
    {
        $resolvedLimit = $limit ?? (int) config('influencer.max_featured', 20);

        return Influencer::query()
            ->select(['id', 'display_name', 'title_name', 'is_featured', 'featured_priority'])
            ->where('is_featured', true)
            ->orderBy('featured_priority')
            ->limit($resolvedLimit)
            ->get();
    }

    /**
     * Search influencers by display name/user name for featured modal.
     *
     * @return EloquentCollection<int, Influencer>
     */
    public function searchInfluencers(string $query, int $limit = 50): EloquentCollection
    {
        $term = trim($query);

        if ($term === '') {
            return new EloquentCollection();
        }

        return Influencer::query()
            ->select(['id', 'display_name', 'is_featured', 'featured_priority', 'user_id'])
            ->with(['user:id,name'])
            ->where(function ($subQuery) use ($term): void {
                $subQuery
                    ->where('display_name', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($userQuery) use ($term): void {
                        $userQuery->where('name', 'like', "%{$term}%");
                    });
            })
            ->orderBy('display_name')
            ->limit($limit)
            ->get();
    }

    /**
     * Count how many provided IDs are currently featured.
     *
     * @param array<int> $influencerIds
     */
    public function countFeaturedByIds(array $influencerIds): int
    {
        return Influencer::query()
            ->whereIn('id', $influencerIds)
            ->where('is_featured', true)
            ->count();
    }

    /**
     * Increment featured_priority for all featured influencers.
     */
    public function incrementFeaturedPriority(?int $excludeInfluencerId = null): void
    {
        Influencer::query()
            ->where('is_featured', true)
            ->when($excludeInfluencerId !== null, fn ($query) => $query->where('id', '!=', $excludeInfluencerId))
            ->increment('featured_priority');
    }

    /**
     * Mark influencer as featured at a given priority.
     */
    public function markAsFeatured(Influencer $influencer, int $priority = 1): Influencer
    {
        $influencer->update([
            'is_featured' => true,
            'featured_priority' => $priority,
        ]);

        $this->clearStatsCache();

        return $influencer->refresh();
    }

    /**
     * Remove featured state from influencer.
     */
    public function unmarkFeatured(Influencer $influencer): Influencer
    {
        $influencer->update([
            'is_featured' => false,
            'featured_priority' => null,
        ]);

        $this->clearStatsCache();

        return $influencer->refresh();
    }

    /**
     * Get featured influencer IDs ordered by featured_priority.
     *
     * @return array<int>
     */
    public function getFeaturedInfluencerIdsByPriority(): array
    {
        return Influencer::query()
            ->where('is_featured', true)
            ->orderBy('featured_priority')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    /**
     * Update featured priority for influencers.
     *
     * @param array<int> $influencerIds
     */
    public function updateFeaturedPriority(array $influencerIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $influencerIds)));

        if ($normalizedIds === []) {
            return;
        }

        $cases = [];
        foreach ($normalizedIds as $order => $influencerId) {
            $cases[] = 'WHEN ' . $influencerId . ' THEN ' . ($order + 1);
        }

        $caseSql = 'CASE id ' . implode(' ', $cases) . ' END';

        Influencer::query()
            ->whereIn('id', $normalizedIds)
            ->update(['featured_priority' => DB::raw($caseSql)]);

        $this->clearStatsCache();
    }

    /**
     * Get count of featured influencers.
     */
    public function getFeaturedCount(): int
    {
        return Influencer::query()->where('is_featured', true)->count();
    }

    /**
     * Get lowest priority featured influencer.
     */
    public function getLowestPriorityFeatured(): ?Influencer
    {
        return Influencer::query()
            ->where('is_featured', true)
            ->orderByDesc('featured_priority')
            ->first();
    }

    public function clearStatsCache(): void
    {
        Cache::forget(self::STATS_CACHE_KEY);
    }
}
