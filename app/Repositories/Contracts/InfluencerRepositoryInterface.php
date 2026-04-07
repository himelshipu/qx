<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Influencer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get influencer summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,categorized:int}
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
}
