<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Influencer;
use App\Repositories\Contracts\InfluencerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class InfluencerService
 *
 * Handles business rules for dashboard influencer management.
 */
final class InfluencerService
{
    public function __construct(
        private readonly InfluencerRepositoryInterface $influencerRepository
    ) {}

    /**
     * Build influencer listing payload for dashboard index page.
     *
     * @return array{influencers:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,categorized:int,featured:int},search:string,status:string,featured:string}
     */
    public function getListingPayload(string $search, string $status, string $featured = 'all', int $perPage = 12): array
    {
        return [
            'influencers' => $this->influencerRepository->paginateForDashboard($search, $status, $featured, $perPage),
            'stats'       => $this->influencerRepository->getStats(),
            'search'      => $search,
            'status'      => $status,
            'featured'    => $featured,
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
            'categoryOptions' => $this->influencerRepository->getCategoryOptions()
        ];
    }

    /**
     * Build detail payload for a single influencer.
     *
     * @return array{ influencer:Influencer}
     */
    public function getDetailPayload(Influencer $influencer): array
    {
        $influencer->load(['user:id,name,email,phone,gender,city,country,postal_code,address_line,is_active,created_at,profile_image_path,cover_image_path', 'categories:id,name'])
            ->loadCount(['campaignApplications', 'orderItems', 'cartItems', 'conversations']);

        return [
            'influencer' => $influencer
        ];
    }

    /**
     * Create a new influencer account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function createInfluencer(
        array         $validated,
        bool          $isActive,
        bool          $isFeatured,
        ?int          $featuredPriority,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Influencer {
        return DB::transaction(function () use ($validated, $isActive, $isFeatured, $featuredPriority, $profileImageFile, $coverImageFile): Influencer {
            $user = $this->influencerRepository->createUser([
                'name'               => $validated['full_name'],
                'email'              => $validated['email'],
                'password'           => $validated['password'],
                'phone'              => $this->nullableString($validated['phone'] ?? null),
                'gender'             => $validated['gender'] ?? null,
                'city'               => $this->nullableString($validated['city'] ?? null),
                'country'            => $this->nullableString($validated['country'] ?? null),
                'postal_code'        => $this->nullableString($validated['postal_code'] ?? null),
                'address_line'       => $this->nullableString($validated['location'] ?? null),
                'bio'                => $this->nullableString($validated['bio'] ?? null),
                'profile_image_path' => $this->storeUploadedAsset($profileImageFile, 'users/profile'),
                'cover_image_path'   => $this->storeUploadedAsset($coverImageFile, 'users/cover'),
                'user_type'          => 'influencer',
                'is_active'          => $isActive,
                'email_verified_at'  => now()
            ]);

            $influencer = $this->influencerRepository->createInfluencer([
                'user_id'           => $user->id,
                'display_name'      => $this->nullableString($validated['display_name'] ?? null) ?? $validated['full_name'],
                'title_name'        => $this->nullableString($validated['title_name'] ?? null),
                'audience'          => $this->nullableString($validated['audience'] ?? null),
                'is_active'         => $isActive,
                'is_featured'       => $isFeatured,
                'featured_priority' => $isFeatured ? $featuredPriority : null
            ]);

            $this->influencerRepository->syncCategories($influencer, $this->normalizeCategoryIds($validated['categories'] ?? []));

            return $influencer;
        });
    }

    /**
     * Update an influencer account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function updateInfluencer(
        Influencer    $influencer,
        array         $validated,
        bool          $isActive,
        bool          $isFeatured,
        ?int          $featuredPriority,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Influencer {
        return DB::transaction(function () use ($influencer, $validated, $isActive, $isFeatured, $featuredPriority, $profileImageFile, $coverImageFile): Influencer {
            $profileImagePath = $influencer->user?->profile_image_path;
            if ($profileImageFile) {
                $this->deleteStoredAsset($influencer->user?->profile_image_path);
                $profileImagePath = $this->storeUploadedAsset($profileImageFile, 'users/profile');
            }

            $coverImagePath = $influencer->user?->cover_image_path;
            if ($coverImageFile) {
                $this->deleteStoredAsset($influencer->user?->cover_image_path);
                $coverImagePath = $this->storeUploadedAsset($coverImageFile, 'users/cover');
            }

            if ($influencer->user) {
                $userData = [
                    'name'         => $validated['full_name'],
                    'email'        => $validated['email'],
                    'phone'        => $this->nullableString($validated['phone'] ?? null),
                    'gender'       => $validated['gender'] ?? null,
                    'city'         => $this->nullableString($validated['city'] ?? null),
                    'country'      => $this->nullableString($validated['country'] ?? null),
                    'postal_code'  => $this->nullableString($validated['postal_code'] ?? null),
                    'address_line' => $this->nullableString($validated['location'] ?? null),
                    'bio'          => $this->nullableString($validated['bio'] ?? null),
                    'is_active'    => $isActive
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

                $this->influencerRepository->updateUser($influencer->user, $userData);
            }

            $influencer = $this->influencerRepository->updateInfluencer($influencer, [
                'display_name'      => $this->nullableString($validated['display_name'] ?? null) ?? $validated['full_name'],
                'title_name'        => $this->nullableString($validated['title_name'] ?? null),
                'audience'          => $this->nullableString($validated['audience'] ?? null),
                'is_active'         => $isActive,
                'is_featured'       => $isFeatured,
                'featured_priority' => $isFeatured ? $featuredPriority : null
            ]);

            $this->influencerRepository->syncCategories($influencer, $this->normalizeCategoryIds($validated['categories'] ?? []));

            return $influencer;
        });
    }

    /**
     * Delete an influencer if no critical dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteInfluencer(Influencer $influencer): array
    {
        $dependencyCount = $this->influencerRepository->getDependencyCount($influencer);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Influencer cannot be deleted because it has related applications, orders, or activity records.'
            ];
        }

        return DB::transaction(function () use ($influencer): array {
            $this->deleteStoredAsset($influencer->user?->profile_image_path);
            $this->deleteStoredAsset($influencer->user?->cover_image_path);

            if ($influencer->user) {
                $this->influencerRepository->deleteUser($influencer->user);
            } else {
                $this->influencerRepository->deleteInfluencer($influencer);
            }

            return [
                'deleted' => true,
                'message' => 'Influencer deleted successfully.'
            ];
        });
    }

    /**
     * Toggle active status and keep linked user in sync.
     */
    public function toggleStatus(Influencer $influencer): bool
    {
        $updatedInfluencer = $this->influencerRepository->toggleStatus($influencer);

        if ($updatedInfluencer->user) {
            $this->influencerRepository->updateUser($updatedInfluencer->user, [
                'is_active' => $updatedInfluencer->is_active
            ]);
        }

        return $updatedInfluencer->is_active;
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Influencer $influencer): bool
    {
        $updatedInfluencer = $this->influencerRepository->toggleFeatured($influencer);

        return $updatedInfluencer->is_featured;
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
