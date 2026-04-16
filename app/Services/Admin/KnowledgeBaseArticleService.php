<?php

namespace App\Services\Admin;

use App\Models\KnowledgeBaseArticle;
use App\Repositories\Contracts\KnowledgeBaseArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KnowledgeBaseArticleService
{
    public function __construct(
        private readonly KnowledgeBaseArticleRepositoryInterface $repository
    ) {
    }

    public function paginateForDashboard(
        string $search,
        string $status,
        string $featured
    ): LengthAwarePaginator {
        return $this->repository->paginateForDashboard(
            $search,
            $status,
            $featured,
            (int) config('knowledge-base.dashboard.per_page', 15)
        );
    }

    /**
     * @return array{total:int,published:int,draft:int,featured:int}
     */
    public function stats(): array
    {
        return $this->repository->stats();
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function create(array $validated, bool $isPublished, bool $isFeatured): KnowledgeBaseArticle
    {
        return $this->repository->create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'badge' => $validated['badge'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'read_time_minutes' => $validated['read_time_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'published_at' => $validated['published_at'] ?? ($isPublished ? now() : null),
            'is_featured' => $isFeatured,
            'is_published' => $isPublished,
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    public function update(
        KnowledgeBaseArticle $article,
        array $validated,
        bool $isPublished,
        bool $isFeatured
    ): void {
        $this->repository->update($article, [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? Str::slug($validated['title']),
            'badge' => $validated['badge'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'content' => $validated['content'],
            'read_time_minutes' => $validated['read_time_minutes'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'published_at' => $validated['published_at'] ?? ($isPublished && !$article->published_at ? now() : $article->published_at),
            'is_featured' => $isFeatured,
            'is_published' => $isPublished,
        ]);
    }

    public function delete(KnowledgeBaseArticle $article): void
    {
        $this->repository->delete($article);
    }

    public function toggleStatus(KnowledgeBaseArticle $article): KnowledgeBaseArticle
    {
        $nextStatus = !$article->is_published;

        $this->repository->update($article, [
            'is_published' => $nextStatus,
            'published_at' => $nextStatus ? ($article->published_at ?? now()) : $article->published_at,
        ]);

        return $article->fresh();
    }

    /**
     * @param array<int, int|string> $orderedIds
     */
    public function reorder(array $orderedIds): void
    {
        $ids = array_values(array_map('intval', $orderedIds));
        if ($ids === []) {
            return;
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $sortOrder => $id) {
                KnowledgeBaseArticle::query()
                    ->whereKey($id)
                    ->update(['sort_order' => $sortOrder]);
            }
        });
    }

    public function nextSortOrder(): int
    {
        return (int) KnowledgeBaseArticle::query()->max('sort_order') + 1;
    }
}