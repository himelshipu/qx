<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\Admin\CategoryFeaturedService;
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
                'featured' => $payload['featured']->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'priority' => $category->featured_order,
                        'icon_path' => $category->icon_path,
                        'image_path' => $category->image_path,
                    ];
                }),
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
            'featured' => $this->featuredService->getFeaturedCategories()->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'priority' => $cat->featured_order,
                    'icon_path' => $cat->icon_path,
                    'image_path' => $cat->image_path,
                ];
            })
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
            'featured' => $this->featuredService->getFeaturedCategories()->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'priority' => $cat->featured_order,
                    'icon_path' => $cat->icon_path,
                    'image_path' => $cat->image_path,
                ];
            })
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
            'featured' => $this->featuredService->getFeaturedCategories()->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'priority' => $cat->featured_order,
                    'icon_path' => $cat->icon_path,
                    'image_path' => $cat->image_path,
                ];
            })
        ]);
    }

    /**
     * Search categories by name for modal search.
     */
    public function searchCategories(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least 2 characters.',
                'results' => []
            ]);
        }

        $categories = Category::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('slug', 'like', '%' . $query . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'results' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'is_featured' => (bool) $category->is_featured,
                    'priority' => $category->featured_order,
                    'icon_path' => $category->icon_path,
                    'image_path' => $category->image_path,
                ];
            })
        ]);
    }
}
