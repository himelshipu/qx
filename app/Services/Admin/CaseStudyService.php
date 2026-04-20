<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Helpers\ImageHelper;
use App\Models\CaseStudy;
use App\Repositories\Contracts\CaseStudyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class CaseStudyService
{
    public function __construct(
        private readonly CaseStudyRepositoryInterface $caseStudyRepository
    ) {}

    /**
     * @return array{caseStudies:LengthAwarePaginator,stats:array{total:int,published:int,draft:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        $perPage = (int) config('case-study.per_page', 15);

        return [
            'caseStudies' => $this->caseStudyRepository->paginateForDashboard($search, $status, $perPage),
            'stats' => $this->caseStudyRepository->getStats(),
            'search' => $search,
            'status' => $status,
        ];
    }

    public function getNextSortOrder(): int
    {
        return $this->caseStudyRepository->getNextSortOrder();
    }

    public function buildCoverPreview(?CaseStudy $caseStudy): ?string
    {
        if (!$caseStudy?->cover_image_path) {
            return null;
        }

        return ImageHelper::url($caseStudy->cover_image_path);
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function createCaseStudy(array $validated, ?UploadedFile $coverImage): CaseStudy
    {
        $slugSeed = (string) ($validated['title'] ?? '');

        return $this->caseStudyRepository->create([
            'title' => $validated['title'],
            'slug' => $this->buildUniqueSlug($slugSeed),
            'summary' => $validated['summary'],
            'cover_image_path' => $this->storeCoverImage($coverImage),
            'external_url' => $this->nullableString($validated['external_url'] ?? null),
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'sort_order' => $validated['sort_order'] ?? $this->caseStudyRepository->getNextSortOrder(),
            'published_at' => ($validated['is_published'] ?? false) ? now() : null,
        ]);
    }

    /**
     * @param array<string,mixed> $validated
     */
    public function updateCaseStudy(CaseStudy $caseStudy, array $validated, ?UploadedFile $coverImage): CaseStudy
    {
        $payload = [
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'external_url' => $this->nullableString($validated['external_url'] ?? null),
            'is_published' => (bool) ($validated['is_published'] ?? false),
            'sort_order' => $validated['sort_order'] ?? $caseStudy->sort_order,
        ];

        if ($validated['title'] !== $caseStudy->title) {
            $payload['slug'] = $this->buildUniqueSlug((string) $validated['title'], (int) $caseStudy->id);
        }

        if ($payload['is_published'] && !$caseStudy->published_at) {
            $payload['published_at'] = now();
        }

        if (!$payload['is_published']) {
            $payload['published_at'] = null;
        }

        if ($coverImage) {
            $this->deleteStoredAsset($caseStudy->cover_image_path);
            $payload['cover_image_path'] = $this->storeCoverImage($coverImage);
        }

        return $this->caseStudyRepository->update($caseStudy, $payload);
    }

    public function deleteCaseStudy(CaseStudy $caseStudy): bool
    {
        $this->deleteStoredAsset($caseStudy->cover_image_path);

        return $this->caseStudyRepository->delete($caseStudy);
    }

    public function toggleStatus(CaseStudy $caseStudy): CaseStudy
    {
        return $this->caseStudyRepository->toggleStatus($caseStudy);
    }

    /**
     * @param array<int> $caseStudyIds
     */
    public function reorderCaseStudies(array $caseStudyIds): void
    {
        $ids = array_values(array_filter(
            array_unique(array_map('intval', $caseStudyIds)),
            fn (int $id): bool => $id > 0
        ));

        $this->caseStudyRepository->updateSortOrder($ids);
    }

    private function buildUniqueSlug(string $seed, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($seed);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'case-study';

        $slug = $baseSlug;
        $counter = 2;

        while ($this->caseStudyRepository->hasSlug($slug, $ignoreId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function storeCoverImage(?UploadedFile $coverImage): ?string
    {
        if (!$coverImage) {
            return null;
        }

        return $coverImage->store('case-studies', 'public');
    }

    private function deleteStoredAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        $cleanPath = str_starts_with($path, 'storage/') ? Str::after($path, 'storage/') : $path;

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
