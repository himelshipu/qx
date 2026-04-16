<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Admin\CategoryFeaturedService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryFeaturedController extends Controller
{
    public function __construct(
        private readonly CategoryFeaturedService $featuredService
    ) {}

    /**
     * Get all featured categories for modal.
     */
    public function getFeatured(): JsonResponse
    {
        $payload = $this->featuredService->getModalPayload();

        return response()->json([
            'success' => true,
            'data' => [
                'featured' => $this->mapCategoryCollection($payload['featured']),
                'count' => $payload['count'],
                'maxAllowed' => $payload['maxAllowed']
            ]
        ]);
    }

    /**
     * Add category to featured list.
     */
    public function addFeatured(Category $category): JsonResponse
    {
        $result = $this->featuredService->addFeatured($category);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'removedCategory' => $result['removedCategory'] ? [
                'id' => $result['removedCategory']->id,
                'name' => $result['removedCategory']->name,
            ] : null,
            'featured' => $this->mapCategoryCollection($this->featuredService->getFeaturedCategories())
        ]);
    }

    /**
     * Remove category from featured list.
     */
    public function removeFeatured(Category $category): JsonResponse
    {
        $removed = $this->featuredService->removeFeatured($category);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Category is not featured.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category removed from featured list.',
            'featured' => $this->mapCategoryCollection($this->featuredService->getFeaturedCategories())
        ]);
    }

    /**
     * Reorder featured categories (update priority).
     */
    public function reorderFeatured(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:categories,id',
        ]);

        $success = $this->featuredService->updateOrder($validated['order']);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid category IDs or categories not featured.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured categories reordered successfully.',
            'featured' => $this->mapCategoryCollection($this->featuredService->getFeaturedCategories())
        ]);
    }

    /**
     * Search categories by name for modal search.
     */
    public function searchCategories(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q', ''));
        $minLength = (int) config('category.search_min_length', 2);

        if (strlen($query) < $minLength) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least ' . $minLength . ' characters.',
                'results' => []
            ]);
        }

        $categories = $this->featuredService->searchCategories($query, 20);

        return response()->json([
            'success' => true,
            'results' => $this->mapCategoryCollection($categories)
        ]);
    }

    /**
     * Transform a category collection for featured responses.
     */
    private function mapCategoryCollection(Collection $categories): array
    {
        return $categories->map(fn (Category $category) => $this->mapCategory($category))->all();
    }

    /**
     * Transform a category model to API payload.
     *
     * @return array{id:int,name:string,slug:string,is_featured:bool,priority:int|null,icon_path:string|null,image_path:string|null}
     */
    private function mapCategory(Category $category): array
    {
        return [
            'id' => (int) $category->id,
            'name' => (string) $category->name,
            'slug' => (string) $category->slug,
            'is_featured' => (bool) $category->is_featured,
            'priority' => $category->featured_order,
            'icon_path' => $category->icon_path,
            'image_path' => $category->image_path,
        ];
    }
}
