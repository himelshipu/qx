<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class CategoryFeaturedService
 *
 * Handles featured category management with priority ordering.
 */
final class CategoryFeaturedService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    /**
     * Get all featured categories sorted by priority (featured_order).
     *
     * @return Collection<int, Category>
     */
    public function getFeaturedCategories(): Collection
    {
        return $this->categoryRepository->getFeaturedCategories($this->maxFeatured());
    }

    /**
     * Get count of currently featured categories.
     */
    public function getFeaturedCount(): int
    {
        return $this->categoryRepository->getFeaturedCount();
    }

    /**
     * Add a category to featured list.
     * If already at max, removes lowest priority category.
     *
     * @return array{success:bool,message:string,removedCategory:?Category}
     */
    public function addFeatured(Category $category): array
    {
        return DB::transaction(function () use ($category): array {
            // If already featured, move it to top priority.
            if ($category->is_featured) {
                $this->categoryRepository->incrementFeaturedOrder($category->id);
                $this->categoryRepository->markAsFeatured($category, 1);

                $this->updatePriorities();

                return [
                    'success' => true,
                    'message' => 'Category is already featured and moved to top priority.',
                    'removedCategory' => null
                ];
            }

            $currentCount = $this->getFeaturedCount();
            $removedCategory = null;

            // If at max, remove the lowest priority (highest featured_order number)
            if ($currentCount >= $this->maxFeatured()) {
                $removedCategory = $this->categoryRepository->getLowestPriorityFeatured();
                if ($removedCategory) {
                    $this->categoryRepository->unmarkFeatured($removedCategory);
                }
            }

            // Shift existing featured categories down and place new one at top.
            $this->categoryRepository->incrementFeaturedOrder();
            $this->categoryRepository->markAsFeatured($category, 1);

            $this->updatePriorities();

            return [
                'success' => true,
                'message' => 'Category added to featured list.',
                'removedCategory' => $removedCategory
            ];
        });
    }

    /**
     * Remove a category from featured list.
     * Recalculates priorities for remaining categories.
     */
    public function removeFeatured(Category $category): bool
    {
        if (!$category->is_featured) {
            return false;
        }

        DB::transaction(function () use ($category): void {
            $this->categoryRepository->unmarkFeatured($category);
            $this->updatePriorities();
        });

        return true;
    }

    /**
     * Update the order of featured categories.
     * $categoryIds should be ordered list from 1 (top) to N (bottom).
     *
     * @param array<int> $categoryIds Ordered list of featured category IDs
     * @return bool Success status
     */
    public function updateOrder(array $categoryIds): bool
    {
        $normalizedIds = array_values(array_filter(
            array_unique(array_map('intval', $categoryIds)),
            fn (int $id): bool => $id > 0
        ));

        if ($normalizedIds === []) {
            return false;
        }

        // Verify all IDs are featured.
        $featured = $this->categoryRepository->countFeaturedByIds($normalizedIds);

        if ($featured !== count($normalizedIds)) {
            return false;
        }

        $this->categoryRepository->updateFeaturedOrder($normalizedIds);

        return true;
    }

    /**
     * Recalculate all featured order values to be sequential (1, 2, 3...).
     * Call this after any featured category is added/removed.
     */
    public function updatePriorities(): void
    {
        $featuredIds = $this->categoryRepository->getFeaturedCategoryIdsByPriority();

        if ($featuredIds === []) {
            return;
        }

        $this->categoryRepository->updateFeaturedOrder($featuredIds);
    }

    /**
     * Validate and get featured category by ID.
     */
    /**
     * Get modal payload with featured categories and stats.
     *
     * @return array{featured:Collection,count:int,maxAllowed:int}
     */
    public function getModalPayload(): array
    {
        return [
            'featured' => $this->getFeaturedCategories(),
            'count' => $this->getFeaturedCount(),
            'maxAllowed' => $this->maxFeatured()
        ];
    }

    /**
     * Search active categories for featured modal.
     *
     * @return Collection<int, Category>
     */
    public function searchCategories(string $query, int $limit = 20): Collection
    {
        return $this->categoryRepository->searchCategories($query, $limit);
    }

    /**
     * Resolve max featured count from configuration.
     */
    private function maxFeatured(): int
    {
        return (int) config('category.max_featured', 20);
    }
}
