<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\BlogPost;
use App\Repositories\Contracts\BlogPostRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class BlogPostService
{
    public function __construct(
        private readonly BlogPostRepositoryInterface $blogPostRepository
    ) {}

    public function getListingPayload(string $search, string $status): array
    {
        $perPage = (int) config('blog.per_page', 12);

        return [
            'posts' => $this->blogPostRepository->paginateForDashboard($search, $status, $perPage),
            'stats' => $this->blogPostRepository->getStats(),
            'search' => $search,
            'status' => $status,
        ];
    }

    public function createBlogPost(array $validated, bool $isPublished, bool $isFeatured, ?UploadedFile $imageFile, int $authorId): BlogPost
    {
        $data = [
            'author_id' => $authorId,
            'title' => $validated['title'],
            'slug' => $this->buildUniqueSlug($validated['slug'] ?? '', $validated['title']),
            'excerpt' => $this->nullableString($validated['excerpt'] ?? null),
            'content' => $validated['content'],
            'featured_image_path' => $this->storeUploadedAsset($imageFile, 'blog/posts'),
            'meta_description' => $this->nullableString($validated['meta_description'] ?? null),
            'meta_keywords' => $this->nullableString($validated['meta_keywords'] ?? null),
            'is_published' => $isPublished,
            'is_featured' => $isFeatured,
            'published_at' => $isPublished ? ($validated['published_at'] ?? now()) : null,
            'sort_order' => $validated['sort_order'] ?? $this->getNextSortOrder(),
        ];

        return $this->blogPostRepository->create($data);
    }

    public function updateBlogPost(BlogPost $blogPost, array $validated, bool $isPublished, bool $isFeatured, ?UploadedFile $imageFile): BlogPost
    {
        $imagePath = $blogPost->featured_image_path;
        if ($imageFile) {
            $this->deleteStoredAsset($blogPost->featured_image_path);
            $imagePath = $this->storeUploadedAsset($imageFile, 'blog/posts');
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $this->buildUniqueSlug($validated['slug'] ?? '', $validated['title'], (int) $blogPost->id),
            'excerpt' => $this->nullableString($validated['excerpt'] ?? null),
            'content' => $validated['content'],
            'featured_image_path' => $imagePath,
            'meta_description' => $this->nullableString($validated['meta_description'] ?? null),
            'meta_keywords' => $this->nullableString($validated['meta_keywords'] ?? null),
            'is_published' => $isPublished,
            'is_featured' => $isFeatured,
            'published_at' => $isPublished ? ($blogPost->published_at ?? now()) : null,
            'sort_order' => $validated['sort_order'] ?? $blogPost->sort_order,
        ];

        return $this->blogPostRepository->update($blogPost, $data);
    }

    public function deleteBlogPost(BlogPost $blogPost): bool
    {
        $this->deleteStoredAsset($blogPost->featured_image_path);

        return $this->blogPostRepository->delete($blogPost);
    }

    public function restoreBlogPost(int $id): BlogPost
    {
        $blogPost = BlogPost::withTrashed()->findOrFail($id);

        return $this->blogPostRepository->restore($blogPost);
    }

    public function toggleStatus(BlogPost $blogPost): BlogPost
    {
        return $this->blogPostRepository->toggleStatus($blogPost);
    }

    public function toggleFeatured(BlogPost $blogPost): BlogPost
    {
        return $this->blogPostRepository->toggleFeatured($blogPost);
    }

    public function getNextSortOrder(): int
    {
        return $this->blogPostRepository->getNextSortOrder();
    }

    private function buildUniqueSlug(string $slugValue, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug(trim($slugValue) !== '' ? $slugValue : $title);
        $base = $base !== '' ? $base : 'blog-post';

        $slug = $base;
        $counter = 2;

        while ($this->blogPostRepository->hasSlug($slug, $ignoreId)) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function storeUploadedAsset(?UploadedFile $file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    private function deleteStoredAsset(?string $path): void
    {
        if (! $path) {
            return;
        }

        $cleanPath = str_starts_with($path, 'storage/') ? Str::after($path, 'storage/') : $path;

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
