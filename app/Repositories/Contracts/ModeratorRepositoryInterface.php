<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface ModeratorRepositoryInterface
 *
 * Defines moderator data access operations for admin management.
 */
interface ModeratorRepositoryInterface
{
    /**
     * Get paginated moderators for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get moderator summary stats for dashboard.
     *
     * @return array{total:int,active:int,inactive:int}
     */
    public function getStats(): array;

    /**
     * Create a moderator user.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): User;

    /**
     * Update a moderator user.
     *
     * @param array<string, mixed> $data
     */
    public function update(User $moderator, array $data): User;

    /**
     * Delete a moderator user.
     */
    public function delete(User $moderator): bool;

    /**
     * Count records that should block moderator deletion.
     */
    public function getDependencyCount(User $moderator): int;

    /**
     * Toggle moderator status and return updated record.
     */
    public function toggleStatus(User $moderator): User;
}
