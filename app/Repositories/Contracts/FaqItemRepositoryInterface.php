<?php

namespace App\Repositories\Contracts;

use App\Models\FaqItem;
use App\Models\FaqSection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FaqItemRepositoryInterface
{
    public function paginateForDashboard(FaqSection $section, string $search, string $status, int $perPage): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(FaqSection $section, array $data): FaqItem;

    /**
     * @param array<string, mixed> $data
     */
    public function update(FaqItem $item, array $data): void;

    public function delete(FaqItem $item): void;

    /**
     * @return array{total:int,active:int,inactive:int}
     */
    public function stats(FaqSection $section): array;
}