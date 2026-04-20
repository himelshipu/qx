<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Influencer;
use App\Repositories\Contracts\InfluencerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class InfluencerFeaturedService
 *
 * Handles featured influencer management with priority ordering.
 */
final class InfluencerFeaturedService
{
    public function __construct(
        private readonly InfluencerRepositoryInterface $influencerRepository
    ) {}

    /**
     * Get all featured influencers sorted by priority (featured_priority).
     *
     * @return Collection<int, Influencer>
     */
    public function getFeaturedInfluencers(): Collection
    {
        return $this->influencerRepository->getFeaturedInfluencers($this->maxFeatured());
    }

    /**
     * Get count of currently featured influencers.
     */
    public function getFeaturedCount(): int
    {
        return $this->influencerRepository->getFeaturedCount();
    }

    /**
     * Add influencer to featured list.
     *
     * @return array{success:bool,message:string,removedInfluencer:?Influencer}
     */
    public function addFeatured(Influencer $influencer): array
    {
        return DB::transaction(function () use ($influencer): array {
            if ($influencer->is_featured) {
                $this->influencerRepository->incrementFeaturedPriority($influencer->id);
                $this->influencerRepository->markAsFeatured($influencer, 1);

                $this->updatePriorities();

                return [
                    'success' => true,
                    'message' => 'Influencer is already featured and moved to top priority.',
                    'removedInfluencer' => null,
                ];
            }

            $currentCount = $this->getFeaturedCount();
            $removedInfluencer = null;

            if ($currentCount >= $this->maxFeatured()) {
                $removedInfluencer = $this->influencerRepository->getLowestPriorityFeatured();
                if ($removedInfluencer) {
                    $this->influencerRepository->unmarkFeatured($removedInfluencer);
                }
            }

            $this->influencerRepository->incrementFeaturedPriority();
            $this->influencerRepository->markAsFeatured($influencer, 1);

            $this->updatePriorities();

            return [
                'success' => true,
                'message' => 'Influencer added to featured list.',
                'removedInfluencer' => $removedInfluencer,
            ];
        });
    }

    /**
     * Remove influencer from featured list.
     */
    public function removeFeatured(Influencer $influencer): bool
    {
        if (!$influencer->is_featured) {
            return false;
        }

        DB::transaction(function () use ($influencer): void {
            $this->influencerRepository->unmarkFeatured($influencer);
            $this->updatePriorities();
        });

        return true;
    }

    /**
     * Update order of featured influencers.
     *
     * @param array<int> $influencerIds
     */
    public function updateOrder(array $influencerIds): bool
    {
        $normalizedIds = array_values(array_filter(
            array_unique(array_map('intval', $influencerIds)),
            fn (int $id): bool => $id > 0
        ));

        if ($normalizedIds === []) {
            return false;
        }

        $featuredCount = $this->influencerRepository->countFeaturedByIds($normalizedIds);

        if ($featuredCount !== count($normalizedIds)) {
            return false;
        }

        $this->influencerRepository->updateFeaturedPriority($normalizedIds);

        return true;
    }

    /**
     * Recalculate all featured priority values sequentially.
     */
    public function updatePriorities(): void
    {
        $featuredIds = $this->influencerRepository->getFeaturedInfluencerIdsByPriority();

        if ($featuredIds === []) {
            return;
        }

        $this->influencerRepository->updateFeaturedPriority($featuredIds);
    }

    /**
     * Get featured modal payload.
     *
     * @return array{featured:Collection,count:int,maxAllowed:int}
     */
    public function getModalPayload(): array
    {
        return [
            'featured' => $this->getFeaturedInfluencers(),
            'count' => $this->getFeaturedCount(),
            'maxAllowed' => $this->maxFeatured(),
        ];
    }

    /**
     * Search influencers for featured modal.
     *
     * @return Collection<int, Influencer>
     */
    public function searchInfluencers(string $query, int $limit = 20): Collection
    {
        return $this->influencerRepository->searchInfluencers($query, $limit);
    }

    /**
     * Resolve max featured count from config.
     */
    private function maxFeatured(): int
    {
        return (int) config('influencer.max_featured', 20);
    }
}
