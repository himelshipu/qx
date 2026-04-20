<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class BrandFeaturedService
 *
 * Handles featured brand management with priority ordering.
 */
final class BrandFeaturedService
{
    public function __construct(
        private readonly BrandRepositoryInterface $brandRepository
    ) {}

    /**
     * Get all featured brands sorted by priority (featured_order).
     *
     * @return Collection<int, Brand>
     */
    public function getFeaturedBrands(): Collection
    {
        return $this->brandRepository->getFeaturedBrands($this->maxFeatured());
    }

    /**
     * Get count of currently featured brands.
     */
    public function getFeaturedCount(): int
    {
        return $this->brandRepository->getFeaturedCount();
    }

    /**
     * Add a brand to featured list.
     * If already at max, removes lowest priority brand.
     *
     * @return array{success:bool,message:string,removedBrand:?Brand}
     */
    public function addFeatured(Brand $brand): array
    {
        return DB::transaction(function () use ($brand): array {
            if ($brand->is_featured) {
                $this->brandRepository->incrementFeaturedOrder($brand->id);
                $this->brandRepository->markAsFeatured($brand, 1);

                $this->updatePriorities();

                return [
                    'success' => true,
                    'message' => 'Brand is already featured and moved to top priority.',
                    'removedBrand' => null,
                ];
            }

            $currentCount = $this->getFeaturedCount();
            $removedBrand = null;

            if ($currentCount >= $this->maxFeatured()) {
                $removedBrand = $this->brandRepository->getLowestPriorityFeatured();
                if ($removedBrand) {
                    $this->brandRepository->unmarkFeatured($removedBrand);
                }
            }

            $this->brandRepository->incrementFeaturedOrder();
            $this->brandRepository->markAsFeatured($brand, 1);

            $this->updatePriorities();

            return [
                'success' => true,
                'message' => 'Brand added to featured list.',
                'removedBrand' => $removedBrand,
            ];
        });
    }

    /**
     * Remove a brand from featured list.
     */
    public function removeFeatured(Brand $brand): bool
    {
        if (!$brand->is_featured) {
            return false;
        }

        DB::transaction(function () use ($brand): void {
            $this->brandRepository->unmarkFeatured($brand);
            $this->updatePriorities();
        });

        return true;
    }

    /**
     * Update order of featured brands.
     *
     * @param array<int> $brandIds
     */
    public function updateOrder(array $brandIds): bool
    {
        $normalizedIds = array_values(array_filter(
            array_unique(array_map('intval', $brandIds)),
            fn (int $id): bool => $id > 0
        ));

        if ($normalizedIds === []) {
            return false;
        }

        $featuredCount = $this->brandRepository->countFeaturedByIds($normalizedIds);

        if ($featuredCount !== count($normalizedIds)) {
            return false;
        }

        $this->brandRepository->updateFeaturedOrder($normalizedIds);

        return true;
    }

    /**
     * Recalculate all featured order values to be sequential (1, 2, 3...).
     */
    public function updatePriorities(): void
    {
        $featuredIds = $this->brandRepository->getFeaturedBrandIdsByPriority();

        if ($featuredIds === []) {
            return;
        }

        $this->brandRepository->updateFeaturedOrder($featuredIds);
    }

    /**
     * Get featured modal payload.
     *
     * @return array{featured:Collection,count:int,maxAllowed:int}
     */
    public function getModalPayload(): array
    {
        return [
            'featured' => $this->getFeaturedBrands(),
            'count' => $this->getFeaturedCount(),
            'maxAllowed' => $this->maxFeatured(),
        ];
    }

    /**
     * Search brands for featured modal.
     *
     * @return Collection<int, Brand>
     */
    public function searchBrands(string $query, int $limit = 20): Collection
    {
        return $this->brandRepository->searchBrands($query, $limit);
    }

    /**
     * Resolve max featured count from configuration.
     */
    private function maxFeatured(): int
    {
        return (int) config('brand.max_featured', 20);
    }
}
