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
 * Maximum 10 featured categories enforced.
 */
final class CategoryFeaturedService
{
    private const MAX_FEATURED = 10;

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
        return Category::query()
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('featured_order')
            ->get();
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
                Category::query()
                    ->where('is_featured', true)
                    ->where('id', '!=', $category->id)
                    ->increment('featured_order');

                $category->update([
                    'is_featured' => true,
                    'featured_order' => 1,
                ]);

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
            if ($currentCount >= self::MAX_FEATURED) {
                $removedCategory = $this->categoryRepository->getLowestPriorityFeatured();
                if ($removedCategory) {
                    $removedCategory->update([
                        'is_featured' => false,
                        'featured_order' => null,
                    ]);
                }
            }

            // Shift existing featured categories down and place new one at top.
            Category::query()
                ->where('is_featured', true)
                ->increment('featured_order');

            $category->update([
                'is_featured' => true,
                'featured_order' => 1,
            ]);

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

        $category->update([
            'is_featured'    => false,
            'featured_order' => null
        ]);

        // Recalculate priorities
        $this->updatePriorities();

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
        $normalizedIds = array_values(array_unique(array_map('intval', $categoryIds)));

        if ($normalizedIds === []) {
            return false;
        }

        // Verify all IDs are featured.
        $featured = Category::whereIn('id', $normalizedIds)
            ->where('is_featured', true)
            ->count();

        if ($featured !== count($normalizedIds)) {
            return false;
        }

        // Update featured_order based on array position
        foreach ($normalizedIds as $order => $categoryId) {
            Category::where('id', $categoryId)->update([
                'featured_order' => $order + 1
            ]);
        }

        return true;
    }

    /**
     * Recalculate all featured order values to be sequential (1, 2, 3...).
     * Call this after any featured category is added/removed.
     */
    public function updatePriorities(): void
    {
        $featured = Category::query()
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->get();

        /** @var Category $category */
        foreach ($featured as $order => $category) {
            $category->update([
                'featured_order' => $order + 1
            ]);
        }
    }

    /**
     * Validate and get featured category by ID.
     */
    public function getFeaturedById(int $categoryId): ?Category
    {
        return Category::where('id', $categoryId)
            ->where('is_featured', true)
            ->first();
    }

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
            'maxAllowed' => self::MAX_FEATURED
        ];
    }
}
