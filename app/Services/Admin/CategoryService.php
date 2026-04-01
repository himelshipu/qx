<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class CategoryService
 *
 * Handles business rules for dashboard category management.
 */
final class CategoryService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * Build category listing payload for the dashboard index view.
     *
     * @return array{categories:LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,linked:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        return [
            'categories' => $this->categoryRepository->paginateForDashboard($search, $status),
            'stats'      => $this->categoryRepository->getStats(),
            'search'     => $search,
            'status'     => $status
        ];
    }

    /**
     * Get next form defaults for create page.
     *
     * @return array{nextSortOrder:int}
     */
    public function getCreatePayload(): array
    {
        return [
            'nextSortOrder' => $this->categoryRepository->getNextSortOrder()
        ];
    }

    /**
     * Create a category.
     *
     * @param array<string, mixed> $validated
     */
    public function createCategory(array $validated, bool $isActive, ?UploadedFile $iconFile, ?UploadedFile $imageFile): Category
    {
        $slugSeed = (string) ($validated['slug'] ?? $validated['name']);

        return $this->categoryRepository->create([
            'name'            => $validated['name'],
            'slug'            => $this->buildUniqueSlug($slugSeed),
            'description'     => $this->nullableString($validated['description'] ?? null),
            'icon_path'       => $this->storeUploadedAsset($iconFile, 'categories/icons'),
            'image_path'      => $this->storeUploadedAsset($imageFile, 'categories/images'),
            'sort_order'      => $validated['sort_order'] ?? $this->categoryRepository->getNextSortOrder(),
            'is_featured'     => $validated['is_featured'] ?? false,
            'featured_order'  => $validated['featured_order'] ?? null,
            'is_active'       => $isActive
        ]);
    }

    /**
     * Update a category.
     *
     * @param array<string, mixed> $validated
     */
    public function updateCategory(
        Category      $category,
        array         $validated,
        bool          $isActive,
        ?UploadedFile $iconFile,
        ?UploadedFile $imageFile
    ): Category {
        $slugSeed = (string) ($validated['slug'] ?? $validated['name']);

        $iconPath = $category->icon_path;
        if ($iconFile) {
            $this->deleteStoredAsset($category->icon_path);
            $iconPath = $this->storeUploadedAsset($iconFile, 'categories/icons');
        }

        $imagePath = $category->image_path;
        if ($imageFile) {
            $this->deleteStoredAsset($category->image_path);
            $imagePath = $this->storeUploadedAsset($imageFile, 'categories/images');
        }

        return $this->categoryRepository->update($category, [
            'name'            => $validated['name'],
            'slug'            => $this->buildUniqueSlug($slugSeed, $category->id),
            'description'     => $this->nullableString($validated['description'] ?? null),
            'icon_path'       => $iconPath,
            'image_path'      => $imagePath,
            'sort_order'      => $validated['sort_order'] ?? $category->sort_order,
            'is_featured'     => $validated['is_featured'] ?? $category->is_featured,
            'featured_order'  => $validated['featured_order'] ?? $category->featured_order,
            'is_active'       => $isActive
        ]);
    }

    /**
     * Delete a category if it has no dependencies.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteCategory(Category $category): array
    {
        $dependencyCount = $this->categoryRepository->getDependencyCount($category);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Category cannot be deleted because it is already linked to active records.'
            ];
        }

        $this->deleteStoredAsset($category->icon_path);
        $this->deleteStoredAsset($category->image_path);
        $this->categoryRepository->delete($category);

        return [
            'deleted' => true,
            'message' => 'Category deleted successfully.'
        ];
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(Category $category): bool
    {
        return $this->categoryRepository->toggleStatus($category)->is_active;
    }

    /**
     * Build a unique slug for categories.
     */
    private function buildUniqueSlug(string $slugSeed, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slugSeed);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'category';

        $slug    = $baseSlug;
        $counter = 2;

        while ($this->categoryRepository->hasSlug($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Persist uploaded category asset and return public path.
     */
    private function storeUploadedAsset(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    /**
     * Delete stored asset only if it belongs to public storage.
     * Handles paths with or without 'storage/' prefix for backward compatibility.
     */
    private function deleteStoredAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        // Remove 'storage/' prefix if present (for backward compatibility)
        $cleanPath = str_starts_with($path, 'storage/') ? Str::after($path, 'storage/') : $path;

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    /**
     * Normalize nullable string inputs.
     */
    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
