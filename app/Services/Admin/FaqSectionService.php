<?php

namespace App\Services\Admin;

use App\Models\FaqSection;
use App\Repositories\Contracts\FaqSectionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FaqSectionService
{
    public function __construct(
        private readonly FaqSectionRepositoryInterface $repository
    ) {
    }

    public function paginateForDashboard(string $search, string $status, string $audience): LengthAwarePaginator
    {
        return $this->repository->paginateForDashboard(
            $search,
            $status,
            $audience,
            (int) config('faq.sections.per_page', 15)
        );
    }

    /**
     * @return array{total:int,active:int,inactive:int}
     */
    public function stats(): array
    {
        return $this->repository->stats();
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function create(array $validated): FaqSection
    {
        return $this->repository->create([
            'section_code' => $validated['section_code'],
            'section_title' => $validated['section_title'],
            'audience_type' => $validated['audience_type'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function update(FaqSection $section, array $validated): void
    {
        $this->repository->update($section, [
            'section_code' => $validated['section_code'],
            'section_title' => $validated['section_title'],
            'audience_type' => $validated['audience_type'],
            'sort_order' => $validated['sort_order'] ?? $section->sort_order,
        ]);
    }

    public function delete(FaqSection $section): void
    {
        $this->repository->delete($section);
    }

    public function toggleStatus(FaqSection $section): FaqSection
    {
        $this->repository->update($section, [
            'is_active' => !$section->is_active,
        ]);

        return $section->fresh();
    }
}