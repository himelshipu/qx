<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\FeaturedCollaboration;
use App\Repositories\Contracts\FeaturedCollaborationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class EloquentFeaturedCollaborationRepository implements FeaturedCollaborationRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, string $assetType = 'all', int $perPage = 15): LengthAwarePaginator
    {
        return FeaturedCollaboration::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardType($assetType)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        $summary = FeaturedCollaboration::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published')
            ->selectRaw('SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as unpublished')
            ->first();

        return [
            'total' => (int) ($summary->total ?? 0),
            'published' => (int) ($summary->published ?? 0),
            'unpublished' => (int) ($summary->unpublished ?? 0),
        ];
    }

    public function getNextSortOrder(): int
    {
        return (int) FeaturedCollaboration::max('sort_order') + 1;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): FeaturedCollaboration
    {
        return FeaturedCollaboration::create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(FeaturedCollaboration $collaboration, array $data): FeaturedCollaboration
    {
        $collaboration->update($data);

        return $collaboration->refresh();
    }

    public function delete(FeaturedCollaboration $collaboration): bool
    {
        return (bool) $collaboration->delete();
    }

    public function toggleStatus(FeaturedCollaboration $collaboration): FeaturedCollaboration
    {
        $collaboration->update([
            'is_published' => !$collaboration->is_published,
        ]);

        return $collaboration->refresh();
    }

    /**
     * @param array<int> $ids
     */
    public function updateSortOrder(array $ids): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $ids)));

        if ($normalizedIds === []) {
            return;
        }

        $cases = [];
        foreach ($normalizedIds as $order => $id) {
            $cases[] = 'WHEN ' . $id . ' THEN ' . ($order + 1);
        }

        $caseSql = 'CASE id ' . implode(' ', $cases) . ' END';

        FeaturedCollaboration::query()
            ->whereIn('id', $normalizedIds)
            ->update(['sort_order' => DB::raw($caseSql)]);
    }
}
