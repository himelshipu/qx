<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Influencer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Interface InfluencerRepositoryInterface
 *
 * Defines influencer data access operations for admin management.
 */
interface InfluencerRepositoryInterface
{
    /**
     * Get paginated influencers for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $featured = 'all', int $perPage = 12): LengthAwarePaginator;

    /**
     * Get influencer summary stats for dashboard.
     *
    * @return array{total:int,active:int,inactive:int,categorized:int,featured:int}
     */
    public function getStats(): array;

    /**
     * Get active categories for influencer assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getCategoryOptions(): Collection;

    /**
     * Create a user account for an influencer.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User;

    /**
     * Update an influencer user account.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(User $user, array $data): User;

    /**
     * Create an influencer profile.
     *
     * @param array<string, mixed> $data
     */
    public function createInfluencer(array $data): Influencer;

    /**
     * Update an influencer profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateInfluencer(Influencer $influencer, array $data): Influencer;

    /**
     * Sync influencer categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Influencer $influencer, array $categoryIds): void;

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool;

    /**
     * Delete an influencer profile.
     */
    public function deleteInfluencer(Influencer $influencer): bool;

    /**
     * Count records that should block influencer deletion.
     */
    public function getDependencyCount(Influencer $influencer): int;

    /**
     * Toggle influencer status and return updated record.
     */
    public function toggleStatus(Influencer $influencer): Influencer;

    /**
     * Toggle influencer featured status and return updated record.
     */
    public function toggleFeatured(Influencer $influencer): Influencer;

    /**
     * Get featured influencers ordered by featured_priority.
     *
     * @return EloquentCollection<int, Influencer>
     */
    public function getFeaturedInfluencers(?int $limit = null): EloquentCollection;

    /**
     * Search influencers by display name/user name for featured modal.
     *
     * @return EloquentCollection<int, Influencer>
     */
    public function searchInfluencers(string $query, int $limit = 50): EloquentCollection;

    /**
     * Count how many provided IDs are currently featured.
     *
     * @param array<int> $influencerIds
     */
    public function countFeaturedByIds(array $influencerIds): int;

    /**
     * Increment featured_priority for all featured influencers.
     */
    public function incrementFeaturedPriority(?int $excludeInfluencerId = null): void;

    /**
     * Mark an influencer as featured at a given priority.
     */
    public function markAsFeatured(Influencer $influencer, int $priority = 1): Influencer;

    /**
     * Remove featured state from an influencer.
     */
    public function unmarkFeatured(Influencer $influencer): Influencer;

    /**
     * Get featured influencer IDs ordered by featured_priority.
     *
     * @return array<int>
     */
    public function getFeaturedInfluencerIdsByPriority(): array;

    /**
     * Update featured priority for provided influencer IDs.
     *
     * @param array<int> $influencerIds
     */
    public function updateFeaturedPriority(array $influencerIds): void;

    /**
     * Get count of featured influencers.
     */
    public function getFeaturedCount(): int;

    /**
     * Get featured influencer with lowest priority.
     */
    public function getLowestPriorityFeatured(): ?Influencer;

    /**
     * Clear cached dashboard stats.
     */
    public function clearStatsCache(): void;
}
