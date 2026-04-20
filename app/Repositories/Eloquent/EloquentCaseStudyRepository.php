<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\CaseStudy;
use App\Repositories\Contracts\CaseStudyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class EloquentCaseStudyRepository implements CaseStudyRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 15): LengthAwarePaginator
    {
        return CaseStudy::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        $summary = CaseStudy::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published')
            ->selectRaw('SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft')
            ->first();

        return [
            'total' => (int) ($summary->total ?? 0),
            'published' => (int) ($summary->published ?? 0),
            'draft' => (int) ($summary->draft ?? 0),
        ];
    }

    public function getNextSortOrder(): int
    {
        return (int) CaseStudy::max('sort_order') + 1;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): CaseStudy
    {
        return CaseStudy::create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(CaseStudy $caseStudy, array $data): CaseStudy
    {
        $caseStudy->update($data);

        return $caseStudy->refresh();
    }

    public function delete(CaseStudy $caseStudy): bool
    {
        return (bool) $caseStudy->delete();
    }

    public function hasSlug(string $slug, ?int $ignoreId = null): bool
    {
        return CaseStudy::query()
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function toggleStatus(CaseStudy $caseStudy): CaseStudy
    {
        $nextState = !$caseStudy->is_published;

        $caseStudy->update([
            'is_published' => $nextState,
            'published_at' => $nextState ? now() : null,
        ]);

        return $caseStudy->refresh();
    }

    /**
     * @param array<int> $caseStudyIds
     */
    public function updateSortOrder(array $caseStudyIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $caseStudyIds)));

        if ($normalizedIds === []) {
            return;
        }

        $cases = [];
        foreach ($normalizedIds as $order => $caseStudyId) {
            $cases[] = 'WHEN ' . $caseStudyId . ' THEN ' . ($order + 1);
        }

        $caseSql = 'CASE id ' . implode(' ', $cases) . ' END';

        CaseStudy::query()
            ->whereIn('id', $normalizedIds)
            ->update(['sort_order' => DB::raw($caseSql)]);
    }
}
