<?php

namespace App\Repositories\Eloquent;

use App\Models\KnowledgeBaseArticle;
use App\Repositories\Contracts\KnowledgeBaseArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentKnowledgeBaseArticleRepository implements KnowledgeBaseArticleRepositoryInterface
{
    public function paginateForDashboard(
        string $search,
        string $status,
        string $featured,
        int $perPage
    ): LengthAwarePaginator {
        return KnowledgeBaseArticle::query()
            ->forDashboard()
            ->search($search)
            ->dashboardStatus($status)
            ->dashboardFeatured($featured)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): KnowledgeBaseArticle
    {
        return KnowledgeBaseArticle::create($data);
    }

    public function update(KnowledgeBaseArticle $article, array $data): void
    {
        $article->update($data);
    }

    public function delete(KnowledgeBaseArticle $article): void
    {
        $article->delete();
    }

    public function stats(): array
    {
        return [
            'total' => KnowledgeBaseArticle::query()->count(),
            'published' => KnowledgeBaseArticle::query()->where('is_published', true)->count(),
            'draft' => KnowledgeBaseArticle::query()->where('is_published', false)->count(),
            'featured' => KnowledgeBaseArticle::query()->where('is_featured', true)->count(),
        ];
    }

    public function findByIdsInOrder(array $ids): Collection
    {
        return KnowledgeBaseArticle::query()
            ->whereIn('id', $ids)
            ->get();
    }
}