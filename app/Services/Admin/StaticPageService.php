<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\StaticPage;
use App\Repositories\Contracts\StaticPageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

final class StaticPageService
{
    public function __construct(
        private readonly StaticPageRepositoryInterface $staticPageRepository
    ) {}

    /**
     * @return array{pages:LengthAwarePaginator,stats:array{total:int,published:int,draft:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        $perPage = (int) config('static-page.per_page', 15);

        return [
            'pages' => $this->staticPageRepository->paginateForDashboard($search, $status, $perPage),
            'stats' => $this->staticPageRepository->stats(),
            'search' => $search,
            'status' => $status,
        ];
    }

    /**
     * @param  array<string,mixed>  $validated
     */
    public function createStaticPage(array $validated, bool $isActive): StaticPage
    {
        return $this->staticPageRepository->create([
            'title' => $validated['title'],
            'slug' => $this->buildUniqueSlug((string) ($validated['slug'] ?? ''), (string) $validated['title']),
            'content' => $validated['content'],
            'meta_description' => $this->nullableString($validated['meta_description'] ?? null),
            'meta_keywords' => $this->nullableString($validated['meta_keywords'] ?? null),
            'is_active' => $isActive,
            'sort_order' => $validated['sort_order'] ?? $this->getNextSortOrder(),
            'show_on_footer' => $validated['show_on_footer'] ?? true,
        ]);
    }

    /**
     * @param  array<string,mixed>  $validated
     */
    public function updateStaticPage(StaticPage $staticPage, array $validated, bool $isActive): StaticPage
    {
        return $this->staticPageRepository->update($staticPage, [
            'title' => $validated['title'],
            'slug' => $this->buildUniqueSlug((string) ($validated['slug'] ?? ''), (string) $validated['title'], (int) $staticPage->id),
            'content' => $validated['content'],
            'meta_description' => $this->nullableString($validated['meta_description'] ?? null),
            'meta_keywords' => $this->nullableString($validated['meta_keywords'] ?? null),
            'is_active' => $isActive,
            'sort_order' => $validated['sort_order'] ?? $staticPage->sort_order,
            'show_on_footer' => $validated['show_on_footer'] ?? $staticPage->show_on_footer,
        ]);
    }

    public function deleteStaticPage(StaticPage $staticPage): bool
    {
        return $this->staticPageRepository->delete($staticPage);
    }

    public function toggleStatus(StaticPage $staticPage): StaticPage
    {
        return $this->staticPageRepository->toggleStatus($staticPage);
    }

    private function buildUniqueSlug(string $slugValue, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($slugValue) !== '' ? $slugValue : $title);
        $base = $base !== '' ? $base : 'page';

        $slug = $base;
        $counter = 2;

        while ($this->staticPageRepository->hasSlug($slug, $ignoreId)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    public function getNextSortOrder(): int
    {
        return $this->staticPageRepository->getNextSortOrder();
    }
}

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
