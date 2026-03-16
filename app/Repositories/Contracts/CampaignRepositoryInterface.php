<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Campaign;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface CampaignRepositoryInterface
 *
 * Defines campaign data access operations for admin management.
 */
interface CampaignRepositoryInterface
{
    /**
     * Get paginated campaigns for dashboard listing.
     */
    public function paginateForDashboard(string $search, string $status, string $type, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get campaign summary stats for dashboard.
     *
     * @return array{total:int,published:int,draft:int,active:int}
     */
    public function getStats(): array;

    /**
     * Get active categories for campaign assignment.
     *
     * @return Collection<int, array{id:int,name:string}>
     */
    public function getCategoryOptions(): Collection;

    /**
     * Get active follower ranges for campaign targeting.
     *
     * @return Collection<int, array{id:int,label:string}>
     */
    public function getFollowerRangeOptions(): Collection;

    /**
     * Create a campaign.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): Campaign;

    /**
     * Update a campaign.
     *
     * @param array<string, mixed> $data
     */
    public function update(Campaign $campaign, array $data): Campaign;

    /**
     * Sync campaign categories.
     *
     * @param array<int, int|string> $categoryIds
     */
    public function syncCategories(Campaign $campaign, array $categoryIds): void;

    /**
     * Sync campaign follower ranges.
     *
     * @param array<int, int|string> $followerRangeIds
     */
    public function syncFollowerRanges(Campaign $campaign, array $followerRangeIds): void;

    /**
     * Upsert campaign targeting details.
     *
     * @param array<string, mixed> $targetingData
     */
    public function upsertTargeting(Campaign $campaign, array $targetingData): void;

    /**
     * Sync campaign target countries.
     *
     * @param array<int, array{country_code:string,country_name:string}> $countries
     */
    public function syncTargetCountries(Campaign $campaign, array $countries): void;

    /**
     * Delete a campaign.
     */
    public function delete(Campaign $campaign): bool;

    /**
     * Count records that should block campaign deletion.
     */
    public function getDependencyCount(Campaign $campaign): int;
}
