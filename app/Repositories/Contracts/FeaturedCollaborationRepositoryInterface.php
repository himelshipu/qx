<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\FeaturedCollaboration;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FeaturedCollaborationRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, string $assetType = 'all', int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{total:int,published:int,unpublished:int}
     */
    public function getStats(): array;

    public function getNextSortOrder(): int;

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): FeaturedCollaboration;

    /**
     * @param array<string,mixed> $data
     */
    public function update(FeaturedCollaboration $collaboration, array $data): FeaturedCollaboration;

    public function delete(FeaturedCollaboration $collaboration): bool;

    public function toggleStatus(FeaturedCollaboration $collaboration): FeaturedCollaboration;

    /**
     * @param array<int> $ids
     */
    public function updateSortOrder(array $ids): void;
}
