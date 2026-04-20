<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class EloquentBlogPostRepository implements BlogPostRepositoryInterface
{
    public function paginateForDashboard(string $search, string $status, int $perPage = 12): LengthAwarePaginator
    {
        return BlogPost::query()
            ->with('author')
            ->forDashboard()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->dashboardStatus($status)
            ->dashboardOrder()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'total' => (int) BlogPost::count(),
            'published' => (int) BlogPost::where('is_published', true)->count(),
            'draft' => (int) BlogPost::where('is_published', false)->count(),
            'featured' => (int) BlogPost::where('is_featured', true)->count(),
            'trashed' => (int) BlogPost::onlyTrashed()->count(),
        ];
    }

    public function create(array $data): BlogPost
    {
        return BlogPost::create($data);
    }

    public function update(BlogPost $blogPost, array $data): BlogPost
    {
        $blogPost->update($data);

        return $blogPost->refresh();
    }

    public function delete(BlogPost $blogPost): bool
    {
        return (bool) $blogPost->delete();
    }

    public function restore(BlogPost $blogPost): BlogPost
    {
        $blogPost->restore();

        return $blogPost->refresh();
    }

    public function hasSlug(string $slug, ?int $ignoreId = null): bool
    {
        return BlogPost::query()
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists();
    }

    public function toggleStatus(BlogPost $blogPost): BlogPost
    {
        $blogPost->update([
            'is_published' => ! $blogPost->is_published,
            'published_at' => $blogPost->is_published ? null : ($blogPost->published_at ?? now()),
        ]);

        return $blogPost->refresh();
    }

    public function toggleFeatured(BlogPost $blogPost): BlogPost
    {
        $blogPost->update([
            'is_featured' => ! $blogPost->is_featured,
        ]);

        return $blogPost->refresh();
    }

    public function getNextSortOrder(): int
    {
        return (int) BlogPost::max('sort_order') + 1;
    }
}
