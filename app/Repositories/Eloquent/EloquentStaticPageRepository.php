<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\StaticPage;
use App\Repositories\Contracts\StaticPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentStaticPageRepository implements StaticPageRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage): LengthAwarePaginator
    {
        return StaticPage::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function stats(): array
    {
        $summary = StaticPage::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as published')
            ->selectRaw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as draft')
            ->first();

        return [
            'total' => (int) ($summary->total ?? 0),
            'published' => (int) ($summary->published ?? 0),
            'draft' => (int) ($summary->draft ?? 0),
        ];
    }

    public function create(array $data): StaticPage
    {
        return StaticPage::create($data);
    }

    public function update(StaticPage $staticPage, array $data): StaticPage
    {
        $staticPage->update($data);

        return $staticPage->refresh();
    }

    public function delete(StaticPage $staticPage): bool
    {
        return (bool) $staticPage->delete();
    }

    public function hasSlug(string $slug, ?int $ignoreId = null): bool
    {
        return StaticPage::query()
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function toggleStatus(StaticPage $staticPage): StaticPage
    {
        $staticPage->update([
            'is_active' => ! $staticPage->is_active,
        ]);

        return $staticPage->refresh();
    }

    public function getNextSortOrder(): int
    {
        return (int) StaticPage::max('sort_order') + 1;
    }
}
