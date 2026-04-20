<?php

namespace App\Repositories\Contracts;

use App\Models\KnowledgeBaseArticle;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface KnowledgeBaseArticleRepositoryInterface
{
    public function paginateForDashboard(
        string $search,
        string $status,
        string $featured,
        int $perPage
    ): LengthAwarePaginator;

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): KnowledgeBaseArticle;

    /**
     * @param array<string, mixed> $data
     */
    public function update(KnowledgeBaseArticle $article, array $data): void;

    public function delete(KnowledgeBaseArticle $article): void;

    /**
     * @return array{total:int,published:int,draft:int,featured:int}
     */
    public function stats(): array;

    /**
     * @return Collection<int, KnowledgeBaseArticle>
     */
    public function findByIdsInOrder(array $ids): Collection;
}