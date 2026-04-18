<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Services\Admin\InfluencerFeaturedService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InfluencerFeaturedController extends Controller
{
    public function __construct(
        private readonly InfluencerFeaturedService $featuredService
    ) {}

    /**
     * Get all featured influencers for modal.
     */
    public function getFeatured(): JsonResponse
    {
        $payload = $this->featuredService->getModalPayload();

        return response()->json([
            'success' => true,
            'data' => [
                'featured' => $this->mapInfluencerCollection($payload['featured']),
                'count' => $payload['count'],
                'maxAllowed' => $payload['maxAllowed'],
            ],
        ]);
    }

    /**
     * Add influencer to featured list.
     */
    public function addFeatured(Influencer $influencer): JsonResponse
    {
        $result = $this->featuredService->addFeatured($influencer);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'removedInfluencer' => $result['removedInfluencer'] ? [
                'id' => $result['removedInfluencer']->id,
                'display_name' => $this->resolveDisplayName($result['removedInfluencer']),
            ] : null,
            'featured' => $this->mapInfluencerCollection($this->featuredService->getFeaturedInfluencers()),
        ]);
    }

    /**
     * Remove influencer from featured list.
     */
    public function removeFeatured(Influencer $influencer): JsonResponse
    {
        $removed = $this->featuredService->removeFeatured($influencer);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Influencer is not featured.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Influencer removed from featured list.',
            'featured' => $this->mapInfluencerCollection($this->featuredService->getFeaturedInfluencers()),
        ]);
    }

    /**
     * Reorder featured influencers (update priority).
     */
    public function reorderFeatured(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:influencers,id',
        ]);

        $success = $this->featuredService->updateOrder($validated['order']);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid influencer IDs or influencers not featured.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Featured influencers reordered successfully.',
            'featured' => $this->mapInfluencerCollection($this->featuredService->getFeaturedInfluencers()),
        ]);
    }

    /**
     * Search influencers by name for modal search.
     */
    public function searchInfluencers(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('q', ''));
        $minLength = (int) config('influencer.search_min_length', 2);

        if (strlen($query) < $minLength) {
            return response()->json([
                'success' => false,
                'message' => 'Query must be at least ' . $minLength . ' characters.',
                'results' => [],
            ]);
        }

        $influencers = $this->featuredService->searchInfluencers($query, 20);

        return response()->json([
            'success' => true,
            'results' => $this->mapInfluencerCollection($influencers),
        ]);
    }

    /**
     * Transform influencer collection for featured responses.
     */
    private function mapInfluencerCollection(Collection $influencers): array
    {
        return $influencers->map(fn (Influencer $influencer) => $this->mapInfluencer($influencer))->all();
    }

    /**
     * Transform influencer model to API payload.
     *
     * @return array{id:int,display_name:string,is_featured:bool,priority:int|null}
     */
    private function mapInfluencer(Influencer $influencer): array
    {
        return [
            'id' => (int) $influencer->id,
            'display_name' => $this->resolveDisplayName($influencer),
            'is_featured' => (bool) $influencer->is_featured,
            'priority' => $influencer->featured_priority,
        ];
    }

    private function resolveDisplayName(Influencer $influencer): string
    {
        $displayName = trim((string) ($influencer->display_name ?? ''));

        if ($displayName !== '') {
            return $displayName;
        }

        return (string) ($influencer->user?->name ?? 'Influencer');
    }
}
