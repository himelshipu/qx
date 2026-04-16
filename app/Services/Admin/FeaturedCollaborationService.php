<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\FeaturedCollaboration;
use App\Repositories\Contracts\FeaturedCollaborationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

final class FeaturedCollaborationService
{
    public function __construct(
        private readonly FeaturedCollaborationRepositoryInterface $repository
    ) {}

    /**
     * @return array{collaborations:LengthAwarePaginator,stats:array{total:int,published:int,unpublished:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status, string $assetType = 'all'): array
    {
        $perPage = (int) config('featured-collaboration.per_page', 15);

        return [
            'collaborations' => $this->repository->paginateForDashboard($search, $status, $assetType, $perPage),
            'stats' => $this->repository->getStats(),
            'search' => $search,
            'status' => $status,
            'assetType' => $assetType,
        ];
    }

    public function getNextSortOrder(): int
    {
        return $this->repository->getNextSortOrder();
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function createCollaboration(array $validated, ?UploadedFile $image, ?UploadedFile $video, ?UploadedFile $thumbnail): FeaturedCollaboration
    {
        $this->ensureDirectories();

        return $this->repository->create([
            'brand_name' => $validated['brand_name'],
            'asset_type' => $validated['asset_type'],
            'image_path' => $validated['asset_type'] === 'image' ? $this->storePublicFile($image, 'collaborations/images') : null,
            'video_path' => $validated['asset_type'] === 'video' ? $this->storePublicFile($video, 'collaborations/videos') : null,
            'thumbnail_path' => $validated['asset_type'] === 'video' ? $this->storePublicFile($thumbnail, 'collaborations/thumbnails') : null,
            'sort_order' => $validated['sort_order'] ?? $this->repository->getNextSortOrder(),
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ]);
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function updateCollaboration(
        FeaturedCollaboration $collaboration,
        array $validated,
        ?UploadedFile $image,
        ?UploadedFile $video,
        ?UploadedFile $thumbnail,
        bool $deleteImage,
        bool $deleteVideo,
        bool $deleteThumbnail
    ): FeaturedCollaboration {
        $this->ensureDirectories();

        $imagePath = $collaboration->image_path;
        $videoPath = $collaboration->video_path;
        $thumbnailPath = $collaboration->thumbnail_path;

        if ($validated['asset_type'] === 'image') {
            $this->deleteFromPublicDisk($videoPath);
            $this->deleteFromPublicDisk($thumbnailPath);
            $videoPath = null;
            $thumbnailPath = null;
        }

        if ($validated['asset_type'] === 'video') {
            $this->deleteFromPublicDisk($imagePath);
            $imagePath = null;
        }

        if ($deleteImage) {
            $this->deleteFromPublicDisk($imagePath);
            $imagePath = null;
        }

        if ($deleteVideo) {
            $this->deleteFromPublicDisk($videoPath);
            $videoPath = null;
        }

        if ($deleteThumbnail || $deleteVideo) {
            $this->deleteFromPublicDisk($thumbnailPath);
            $thumbnailPath = null;
        }

        if ($image) {
            $this->deleteFromPublicDisk($imagePath);
            $imagePath = $this->storePublicFile($image, 'collaborations/images');
        }

        if ($video) {
            $this->deleteFromPublicDisk($videoPath);
            $videoPath = $this->storePublicFile($video, 'collaborations/videos');
        }

        if ($thumbnail && $validated['asset_type'] === 'video') {
            $this->deleteFromPublicDisk($thumbnailPath);
            $thumbnailPath = $this->storePublicFile($thumbnail, 'collaborations/thumbnails');
        }

        return $this->repository->update($collaboration, [
            'brand_name' => $validated['brand_name'],
            'asset_type' => $validated['asset_type'],
            'image_path' => $validated['asset_type'] === 'image' ? $imagePath : null,
            'video_path' => $validated['asset_type'] === 'video' ? $videoPath : null,
            'thumbnail_path' => $validated['asset_type'] === 'video' ? $thumbnailPath : null,
            'sort_order' => $validated['sort_order'] ?? $collaboration->sort_order,
            'is_published' => (bool) ($validated['is_published'] ?? false),
        ]);
    }

    public function deleteCollaboration(FeaturedCollaboration $collaboration): bool
    {
        $this->deleteFromPublicDisk($collaboration->image_path);
        $this->deleteFromPublicDisk($collaboration->video_path);
        $this->deleteFromPublicDisk($collaboration->thumbnail_path);

        return $this->repository->delete($collaboration);
    }

    public function toggleStatus(FeaturedCollaboration $collaboration): FeaturedCollaboration
    {
        return $this->repository->toggleStatus($collaboration);
    }

    /**
     * @param array<int> $ids
     */
    public function reorderCollaborations(array $ids): void
    {
        $normalizedIds = array_values(array_filter(
            array_unique(array_map('intval', $ids)),
            fn (int $id): bool => $id > 0
        ));

        $this->repository->updateSortOrder($normalizedIds);
    }

    private function ensureDirectories(): void
    {
        $disk = Storage::disk('public');

        foreach (['collaborations/images', 'collaborations/videos', 'collaborations/thumbnails'] as $directory) {
            if (!$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }
        }
    }

    private function storePublicFile(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    private function deleteFromPublicDisk(?string $path): void
    {
        if (!$path || $path === '0') {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
