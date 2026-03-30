<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Creator;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface CreatorRepositoryInterface
 *
 * Defines creator data access operations for admin management.
 */
interface CreatorRepositoryInterface
{
    /**
     * Get paginated creators for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get creator summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int,categorized:int}
     */
    public function getStats(): array;

    /**
     * Get active categories for creator assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getCategoryOptions(): Collection;

    /**
     * Create a user account for a creator.
     *
     * @param array<string, mixed> $data
     */
    public function createUser(array $data): User;

    /**
     * Update a creator user account.
     *
     * @param array<string, mixed> $data
     */
    public function updateUser(User $user, array $data): User;

    /**
     * Create a creator profile.
     *
     * @param array<string, mixed> $data
     */
    public function createCreator(array $data): Creator;

    /**
     * Update a creator profile.
     *
     * @param array<string, mixed> $data
     */
    public function updateCreator(Creator $creator, array $data): Creator;

    /**
     * Sync creator categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Creator $creator, array $categoryIds): void;

    /**
     * Delete a user and cascade to profile records.
     */
    public function deleteUser(User $user): bool;

    /**
     * Delete a creator profile.
     */
    public function deleteCreator(Creator $creator): bool;

    /**
     * Count records that should block creator deletion.
     */
    public function getDependencyCount(Creator $creator): int;

    /**
     * Toggle creator status and return updated record.
     */
    public function toggleStatus(Creator $creator): Creator;

    /**
     * Toggle creator featured status and return updated record.
     */
    public function toggleFeatured(Creator $creator): Creator;
}
