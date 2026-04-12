<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait Sortable
{
    /**
     * Reorder items based on provided IDs array.
     * Updates the sort_order column for each item.
     *
     * @param array $itemIds Array of IDs in desired order
     * @param string $modelClass The model class to update
     * @return JsonResponse
     */
    public function reorderItems(array $itemIds, $modelClass): JsonResponse
    {
        try {
            foreach ($itemIds as $index => $id) {
                $modelClass::findOrFail($id)->update([
                    'sort_order' => $index + 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Items reordered successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder items: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all items ordered by sort_order
     *
     * @param $model The model instance
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrderedItems($model)
    {
        return $model->orderBy('sort_order')->get();
    }
}
