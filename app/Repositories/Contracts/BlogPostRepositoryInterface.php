<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\BlogPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BlogPostRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator;

    public function getStats(): array;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): BlogPost;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(BlogPost $blogPost, array $data): BlogPost;

    public function delete(BlogPost $blogPost): bool;

    public function restore(BlogPost $blogPost): BlogPost;

    public function hasSlug(string $slug, ?int $ignoreId = null): bool;

    public function toggleStatus(BlogPost $blogPost): BlogPost;

    public function toggleFeatured(BlogPost $blogPost): BlogPost;

    public function getNextSortOrder(): int;
}
