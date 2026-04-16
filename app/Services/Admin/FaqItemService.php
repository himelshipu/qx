<?php

namespace App\Services\Admin;

use App\Models\FaqItem;
use App\Models\FaqSection;
use App\Repositories\Contracts\FaqItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FaqItemService
{
    public function __construct(
        private readonly FaqItemRepositoryInterface $repository
    ) {
    }

    public function paginateForDashboard(FaqSection $section, string $search, string $status): LengthAwarePaginator
    {
        return $this->repository->paginateForDashboard(
            $section,
            $search,
            $status,
            (int) config('faq.items.per_page', 15)
        );
    }

    /**
     * @return array{total:int,active:int,inactive:int}
     */
    public function stats(FaqSection $section): array
    {
        return $this->repository->stats($section);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function create(FaqSection $section, array $validated): FaqItem
    {
        return $this->repository->create($section, [
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function update(FaqItem $item, array $validated): void
    {
        $this->repository->update($item, [
            'question' => $validated['question'],
            'answer' => $validated['answer'],
            'sort_order' => $validated['sort_order'] ?? $item->sort_order,
        ]);
    }

    public function delete(FaqItem $item): void
    {
        $this->repository->delete($item);
    }

    public function toggleStatus(FaqItem $item): FaqItem
    {
        $this->repository->update($item, [
            'is_active' => !$item->is_active,
        ]);

        return $item->fresh();
    }
}