<?php

namespace App\Repositories\Eloquent;

use App\Models\FaqSection;
use App\Repositories\Contracts\FaqSectionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentFaqSectionRepository implements FaqSectionRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, string $audience, int $perPage): LengthAwarePaginator
    {
        return FaqSection::query()
            ->withCount('items')
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardAudience($audience)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): FaqSection
    {
        return FaqSection::create($data);
    }

    public function update(FaqSection $section, array $data): void
    {
        $section->update($data);
    }

    public function delete(FaqSection $section): void
    {
        $section->items()->delete();
        $section->delete();
    }

    public function stats(): array
    {
        return [
            'total' => FaqSection::query()->count(),
            'active' => FaqSection::query()->where('is_active', true)->count(),
            'inactive' => FaqSection::query()->where('is_active', false)->count(),
        ];
    }
}