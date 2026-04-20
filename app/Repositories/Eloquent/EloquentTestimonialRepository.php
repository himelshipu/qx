<?php

declare (strict_types = 1);

namespace App\Repositories\Eloquent;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class EloquentTestimonialRepository implements TestimonialRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 15): LengthAwarePaginator
    {
        return Testimonial::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        $summary = Testimonial::query()
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
        return (int) Testimonial::max('sort_order') + 1;
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(array $data): Testimonial
    {
        return Testimonial::create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(Testimonial $testimonial, array $data): Testimonial
    {
        $testimonial->update($data);

        return $testimonial->refresh();
    }

    public function delete(Testimonial $testimonial): bool
    {
        return (bool) $testimonial->delete();
    }

    public function toggleStatus(Testimonial $testimonial): Testimonial
    {
        $testimonial->update([
            'is_published' => !$testimonial->is_published,
        ]);

        return $testimonial->refresh();
    }

    /**
     * @param array<int> $testimonialIds
     */
    public function updateSortOrder(array $testimonialIds): void
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $testimonialIds)));

        if ($normalizedIds === []) {
            return;
        }

        $cases = [];
        foreach ($normalizedIds as $order => $testimonialId) {
            $cases[] = 'WHEN ' . $testimonialId . ' THEN ' . ($order + 1);
        }

        $caseSql = 'CASE id ' . implode(' ', $cases) . ' END';

        Testimonial::query()
            ->whereIn('id', $normalizedIds)
            ->update(['sort_order' => DB::raw($caseSql)]);
    }
}
