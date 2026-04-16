<?php

namespace App\Repositories\Eloquent;

use App\Models\FaqItem;
use App\Models\FaqSection;
use App\Repositories\Contracts\FaqItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentFaqItemRepository implements FaqItemRepositoryInterface
{
    public function paginateForDashboard(FaqSection $section, string $search, string $status, int $perPage): LengthAwarePaginator
    {
        return $section->items()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(FaqSection $section, array $data): FaqItem
    {
        return $section->items()->create($data);
    }

    public function update(FaqItem $item, array $data): void
    {
        $item->update($data);
    }

    public function delete(FaqItem $item): void
    {
        $item->delete();
    }

    public function stats(FaqSection $section): array
    {
        return [
            'total' => $section->items()->count(),
            'active' => $section->items()->where('is_active', true)->count(),
            'inactive' => $section->items()->where('is_active', false)->count(),
        ];
    }
}