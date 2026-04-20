<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\Admin\BrandFeaturedService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandFeaturedController extends Controller
{
    public function __construct(
        private readonly BrandFeaturedService $featuredService
    ) {}

    /**
     * Get all featured brands for modal.
     */
    public function getFeatured(): JsonResponse
    {
        $payload = $this->featuredService->getModalPayload();

        return response()->json([
            'success' => true,
            'data' => [
                'featured' => $this->mapBrandCollection($payload['featured']),
                'count' => $payload['count'],
                'maxAllowed' => $payload['maxAllowed'],
            ],
        ]);
    }

    /**
     * Add brand to featured list.
     */
    public function addFeatured(Brand $brand): JsonResponse
    {
        $result = $this->featuredService->addFeatured($brand);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'removedBrand' => $result['removedBrand'] ? [
                'id' => $result['removedBrand']->id,
                'brand_name' => $result['removedBrand']->brand_name,
            ] : null,
            'featured' => $this->mapBrandCollection($this->featuredService->getFeaturedBrands()),
        ]);
    }

    /**
     * Remove brand from featured list.
     */
    public function removeFeatured(Brand $brand): JsonResponse
    {
        $removed = $this->featuredService->removeFeatured($brand);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Brand is not featured.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Brand removed from featured list.',
            'featured' => $this->mapBrandCollection($this->featuredService->getFeaturedBrands()),
        ]);
    }

    /**
     * Reorder featured brands (update priority).
     */
    public function reorderFeatured(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:brands,id',
        ]);

        $success = $this->featuredService->updateOrder($validated['order']);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid brand IDs or brands not featured.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured brands reordered successfully.',
            'featured' => $this->mapBrandCollection($this->featuredService->getFeaturedBrands()),
        ]);
    }

    /**
     * Search brands by name for modal search.
     */
    public function searchBrands(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q', ''));
        $minLength = (int) config('brand.search_min_length', 2);

        if (strlen($query) < $minLength) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least ' . $minLength . ' characters.',
                'results' => [],
            ]);
        }

        $brands = $this->featuredService->searchBrands($query, 20);

        return response()->json([
            'success' => true,
            'results' => $this->mapBrandCollection($brands),
        ]);
    }

    /**
     * Transform a brand collection for featured responses.
     */
    private function mapBrandCollection(Collection $brands): array
    {
        return $brands->map(fn (Brand $brand) => $this->mapBrand($brand))->all();
    }

    /**
     * Transform a brand model to API payload.
     *
     * @return array{id:int,brand_name:string,is_featured:bool,priority:int|null}
     */
    private function mapBrand(Brand $brand): array
    {
        return [
            'id' => (int) $brand->id,
            'brand_name' => (string) $brand->brand_name,
            'is_featured' => (bool) $brand->is_featured,
            'priority' => $brand->featured_order,
        ];
    }
}
