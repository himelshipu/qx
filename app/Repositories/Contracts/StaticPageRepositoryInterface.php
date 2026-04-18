<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\StaticPage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StaticPageRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage): LengthAwarePaginator;

    /**
     * @return array{total:int,published:int,draft:int}
     */
    public function stats(): array;

    /**
     * @param  array<string,mixed>  $data
     */
    public function create(array $data): StaticPage;

    /**
     * @param  array<string,mixed>  $data
     */
    public function update(StaticPage $staticPage, array $data): StaticPage;

    public function delete(StaticPage $staticPage): bool;

    public function hasSlug(string $slug, ?int $ignoreId = null): bool;

    public function toggleStatus(StaticPage $staticPage): StaticPage;

    public function getNextSortOrder(): int;
}
