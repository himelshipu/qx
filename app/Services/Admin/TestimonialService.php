<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class TestimonialService
{
    public function __construct(
        private readonly TestimonialRepositoryInterface $testimonialRepository
    ) {}

    /**
     * @return array{testimonials:LengthAwarePaginator,stats:array{total:int,published:int,unpublished:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        $perPage = (int) config('testimonial.per_page', 15);

        return [
            'testimonials' => $this->testimonialRepository->paginateForDashboard($search, $status, $perPage),
            'stats' => $this->testimonialRepository->getStats(),
            'search' => $search,
            'status' => $status,
        ];
    }

    public function getNextSortOrder(): int
    {
        return $this->testimonialRepository->getNextSortOrder();
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function createTestimonial(array $validated): Testimonial
    {
        return $this->testimonialRepository->create([
            'author_name' => $validated['author_name'],
            'author_role' => $validated['author_role'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'quote' => $validated['quote'],
            'rating' => $validated['rating'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'sort_order' => $validated['sort_order'] ?? $this->testimonialRepository->getNextSortOrder(),
        ]);
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function updateTestimonial(Testimonial $testimonial, array $validated): Testimonial
    {
        return $this->testimonialRepository->update($testimonial, [
            'author_name' => $validated['author_name'],
            'author_role' => $validated['author_role'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'quote' => $validated['quote'],
            'rating' => $validated['rating'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'sort_order' => $validated['sort_order'] ?? $testimonial->sort_order,
        ]);
    }

    public function deleteTestimonial(Testimonial $testimonial): bool
    {
        return $this->testimonialRepository->delete($testimonial);
    }

    public function toggleStatus(Testimonial $testimonial): Testimonial
    {
        return $this->testimonialRepository->toggleStatus($testimonial);
    }

    /**
     * @param array<int> $testimonialIds
     */
    public function reorderTestimonials(array $testimonialIds): void
    {
        $ids = array_values(array_filter(
            array_unique(array_map('intval', $testimonialIds)),
            fn (int $id): bool => $id > 0
        ));

        $this->testimonialRepository->updateSortOrder($ids);
    }
}
