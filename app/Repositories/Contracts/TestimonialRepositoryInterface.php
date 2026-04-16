<?php

declare (strict_types = 1);

namespace App\Repositories\Contracts;

use App\Models\Testimonial;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TestimonialRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 15): LengthAwarePaginator;

    /**
     * @return array{total:int,published:int,unpublished:int}
     */
    public function getStats(): array;

    public function getNextSortOrder(): int;

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): Testimonial;

    /**
     * @param array<string,mixed> $data
     */
    public function update(Testimonial $testimonial, array $data): Testimonial;

    public function delete(Testimonial $testimonial): bool;

    public function toggleStatus(Testimonial $testimonial): Testimonial;

    /**
     * @param array<int> $testimonialIds
     */
    public function updateSortOrder(array $testimonialIds): void;
}
