<?php

namespace App\Repositories\Contracts;

use App\Models\FaqSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FaqSectionRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, string $audience, int $perPage): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): FaqSection;

    /**
     * @param array<string, mixed> $data
     */
    public function update(FaqSection $section, array $data): void;

    public function delete(FaqSection $section): void;

    /**
     * @return array{total:int,active:int,inactive:int}
     */
    public function stats(): array;
}