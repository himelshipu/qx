<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\CaseStudy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CaseStudyRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{total:int,published:int,draft:int}
     */
    public function getStats(): array;

    public function getNextSortOrder(): int;

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): CaseStudy;

    /**
     * @param array<string,mixed> $data
     */
    public function update(CaseStudy $caseStudy, array $data): CaseStudy;

    public function delete(CaseStudy $caseStudy): bool;

    public function hasSlug(string $slug, ?int $ignoreId = null): bool;

    public function toggleStatus(CaseStudy $caseStudy): CaseStudy;

    /**
     * @param array<int> $caseStudyIds
     */
    public function updateSortOrder(array $caseStudyIds): void;
}
