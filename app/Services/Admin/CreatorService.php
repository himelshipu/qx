<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Creator;
use App\Repositories\Contracts\CreatorRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class CreatorService
 *
 * Handles business rules for dashboard creator management.
 */
final class CreatorService
{
    public function __construct(
        private readonly CreatorRepositoryInterface $creatorRepository
    ) {}

    /**
     * Build creator listing payload for dashboard index page.
     *
     * @return array{creators:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,categorized:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        return [
            'creators' => $this->creatorRepository->paginateForDashboard($search, $status),
            'stats'    => $this->creatorRepository->getStats(),
            'search'   => $search,
            'status'   => $status
        ];
    }

    /**
     * Build payload for create/edit forms.
     *
     * @return array{categoryOptions:\Illuminate\Support\Collection<int, array{id:int,name:string}>}
     */
    public function getFormPayload(): array
    {
        return [
            'categoryOptions' => $this->creatorRepository->getCategoryOptions()
        ];
    }

    /**
     * Build detail payload for a single creator.
     *
     * @return array{creator:Creator}
     */
    public function getDetailPayload(Creator $creator): array
    {
        $creator->load(['user:id,name,email,phone,gender,city,country,postal_code,address_line,is_active,created_at,profile_image_path,cover_image_path', 'categories:id,name'])
            ->loadCount(['campaignApplications', 'orderItems', 'cartItems', 'conversations']);

        return [
            'creator' => $creator
        ];
    }

    /**
     * Create a new creator account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function createCreator(
        array         $validated,
        bool          $isActive,
        bool          $isFeatured,
        ?int          $featuredPriority,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Creator {
        return DB::transaction(function () use ($validated, $isActive, $isFeatured, $featuredPriority, $profileImageFile, $coverImageFile): Creator {
            $user = $this->creatorRepository->createUser([
                'name'              => $validated['full_name'],
                'email'             => $validated['email'],
                'password'          => $validated['password'],
                'phone'             => $this->nullableString($validated['phone'] ?? null),
                'gender'            => $validated['gender'] ?? null,
                'city'              => $this->nullableString($validated['city'] ?? null),
                'country'           => $this->nullableString($validated['country'] ?? null),
                'postal_code'       => $this->nullableString($validated['postal_code'] ?? null),
                'address_line'      => $this->nullableString($validated['location'] ?? null),
                'bio'               => $this->nullableString($validated['bio'] ?? null),
                'profile_image_path' => $this->storeUploadedAsset($profileImageFile, 'users/profile'),
                'cover_image_path'   => $this->storeUploadedAsset($coverImageFile, 'users/cover'),
                'user_type'         => 'creator',
                'is_active'         => $isActive,
                'email_verified_at' => now()
            ]);

            $creator = $this->creatorRepository->createCreator([
                'user_id'           => $user->id,
                'display_name'      => $this->nullableString($validated['display_name'] ?? null) ?? $validated['full_name'],
                'title_name'        => $this->nullableString($validated['title_name'] ?? null),
                'audience'          => $this->nullableString($validated['audience'] ?? null),
                'is_active'         => $isActive,
                'is_featured'       => $isFeatured,
                'featured_priority' => $isFeatured ? $featuredPriority : null
            ]);

            $this->creatorRepository->syncCategories($creator, $this->normalizeCategoryIds($validated['categories'] ?? []));

            return $creator;
        });
    }

    /**
     * Update a creator account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function updateCreator(
        Creator       $creator,
        array         $validated,
        bool          $isActive,
        bool          $isFeatured,
        ?int          $featuredPriority,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Creator {
        return DB::transaction(function () use ($creator, $validated, $isActive, $isFeatured, $featuredPriority, $profileImageFile, $coverImageFile): Creator {
            $profileImagePath = $creator->user?->profile_image_path;
            if ($profileImageFile) {
                $this->deleteStoredAsset($creator->user?->profile_image_path);
                $profileImagePath = $this->storeUploadedAsset($profileImageFile, 'users/profile');
            }

            $coverImagePath = $creator->user?->cover_image_path;
            if ($coverImageFile) {
                $this->deleteStoredAsset($creator->user?->cover_image_path);
                $coverImagePath = $this->storeUploadedAsset($coverImageFile, 'users/cover');
            }

            if ($creator->user) {
                $userData = [
                    'name'        => $validated['full_name'],
                    'email'       => $validated['email'],
                    'phone'       => $this->nullableString($validated['phone'] ?? null),
                    'gender'      => $validated['gender'] ?? null,
                    'city'        => $this->nullableString($validated['city'] ?? null),
                    'country'     => $this->nullableString($validated['country'] ?? null),
                    'postal_code' => $this->nullableString($validated['postal_code'] ?? null),
                    'address_line' => $this->nullableString($validated['location'] ?? null),
                    'bio'         => $this->nullableString($validated['bio'] ?? null),
                    'is_active'   => $isActive
                ];

                if ($profileImageFile) {
                    $userData['profile_image_path'] = $profileImagePath;
                }

                if ($coverImageFile) {
                    $userData['cover_image_path'] = $coverImagePath;
                }

                if (!empty($validated['password']) && is_string($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }

                $this->creatorRepository->updateUser($creator->user, $userData);
            }

            $creator = $this->creatorRepository->updateCreator($creator, [
                'display_name'      => $this->nullableString($validated['display_name'] ?? null) ?? $validated['full_name'],
                'title_name'        => $this->nullableString($validated['title_name'] ?? null),
                'audience'          => $this->nullableString($validated['audience'] ?? null),
                'is_active'         => $isActive,
                'is_featured'       => $isFeatured,
                'featured_priority' => $isFeatured ? $featuredPriority : null
            ]);

            $this->creatorRepository->syncCategories($creator, $this->normalizeCategoryIds($validated['categories'] ?? []));

            return $creator;
        });
    }

    /**
     * Delete a creator if no critical dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteCreator(Creator $creator): array
    {
        $dependencyCount = $this->creatorRepository->getDependencyCount($creator);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Creator cannot be deleted because it has related applications, orders, or activity records.'
            ];
        }

        return DB::transaction(function () use ($creator): array {
            $this->deleteStoredAsset($creator->user?->profile_image_path);
            $this->deleteStoredAsset($creator->user?->cover_image_path);

            if ($creator->user) {
                $this->creatorRepository->deleteUser($creator->user);
            } else {
                $this->creatorRepository->deleteCreator($creator);
            }

            return [
                'deleted' => true,
                'message' => 'Creator deleted successfully.'
            ];
        });
    }

    /**
     * Toggle active status and keep linked user in sync.
     */
    public function toggleStatus(Creator $creator): bool
    {
        $updatedCreator = $this->creatorRepository->toggleStatus($creator);

        if ($updatedCreator->user) {
            $this->creatorRepository->updateUser($updatedCreator->user, [
                'is_active' => $updatedCreator->is_active
            ]);
        }

        return $updatedCreator->is_active;
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Creator $creator): bool
    {
        $updatedCreator = $this->creatorRepository->toggleFeatured($creator);

        return $updatedCreator->is_featured;
    }

    /**
     * Persist uploaded file and return its relative path.
     * Returns path without 'storage/' prefix for proper Storage::url() usage in views.
     */
    private function storeUploadedAsset(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    /**
     * Delete public storage files only.
     * Handles paths with or without 'storage/' prefix for backward compatibility.
     */
    private function deleteStoredAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        // Remove 'storage/' prefix if present (for backward compatibility)
        $cleanPath = str_starts_with($path, 'storage/') ? Str::after($path, 'storage/') : $path;

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }

    /**
     * Normalize optional string inputs.
     */
    private function nullableString(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }

    /**
     * Normalize category IDs from request payload.
     *
     * @param  mixed      $value
     * @return array<int, int>
     */
    private function normalizeCategoryIds(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = array_values(array_unique(array_filter(array_map(static function ($item): int {
            return (int) $item;
        }, $value), static function (int $id): bool {
            return $id > 0;
        })));

        return $normalized;
    }
}
