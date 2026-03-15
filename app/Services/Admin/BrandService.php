<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class BrandService
 *
 * Handles business rules for dashboard brand management.
 */
final class BrandService
{
    public function __construct(
        private readonly BrandRepositoryInterface $brandRepository
    ) {}

    /**
     * Build brand listing payload for dashboard index page.
     *
     * @return array{brands:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,verified:int},search:string,status:string}
     */
    public function getListingPayload(string $search, string $status): array
    {
        return [
            'brands' => $this->brandRepository->paginateForDashboard($search, $status),
            'stats'  => $this->brandRepository->getStats(),
            'search' => $search,
            'status' => $status
        ];
    }

    /**
     * Build detail payload for a single brand.
     *
     * @return array{brand:Brand}
     */
    public function getDetailPayload(Brand $brand): array
    {
        $brand->load(['user:id,name,email,phone,is_active,created_at', 'socialLinks', 'billingProfile', 'onboardingProfile'])
            ->loadCount(['orders', 'reviews']);

        return [
            'brand' => $brand
        ];
    }

    /**
     * Create a new brand account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function createBrand(
        array         $validated,
        bool          $isActive,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Brand {
        return DB::transaction(function () use ($validated, $isActive, $profileImageFile, $coverImageFile): Brand {
            $user = $this->brandRepository->createUser([
                'name'              => $validated['contact_name'],
                'email'             => $validated['email'],
                'password'          => $validated['password'],
                'phone'             => $this->nullableString($validated['phone'] ?? null),
                'user_type'         => 'brand',
                'is_active'         => $isActive,
                'email_verified_at' => now()
            ]);

            return $this->brandRepository->createBrand([
                'user_id'            => $user->id,
                'brand_name'         => $validated['brand_name'],
                'description'        => $this->nullableString($validated['description'] ?? null),
                'industry'           => $this->nullableString($validated['industry'] ?? null),
                'phone'              => $this->nullableString($validated['phone'] ?? null),
                'email'              => $validated['email'],
                'website'            => $this->nullableString($validated['website'] ?? null),
                'location'           => $this->nullableString($validated['location'] ?? null),
                'city'               => $this->nullableString($validated['city'] ?? null),
                'country'            => $this->nullableString($validated['country'] ?? null),
                'postal_code'        => $this->nullableString($validated['postal_code'] ?? null),
                'profile_image_path' => $this->storeUploadedAsset($profileImageFile, 'brands/profile-images'),
                'cover_image_path'   => $this->storeUploadedAsset($coverImageFile, 'brands/cover-images'),
                'is_verified'        => (bool) ($validated['is_verified'] ?? false),
                'is_active'          => $isActive
            ]);
        });
    }

    /**
     * Update a brand account and profile.
     *
     * @param array<string, mixed> $validated
     */
    public function updateBrand(
        Brand         $brand,
        array         $validated,
        bool          $isActive,
        ?UploadedFile $profileImageFile,
        ?UploadedFile $coverImageFile
    ): Brand {
        return DB::transaction(function () use ($brand, $validated, $isActive, $profileImageFile, $coverImageFile): Brand {
            $profileImagePath = $brand->profile_image_path;
            if ($profileImageFile) {
                $this->deleteStoredAsset($brand->profile_image_path);
                $profileImagePath = $this->storeUploadedAsset($profileImageFile, 'brands/profile-images');
            }

            $coverImagePath = $brand->cover_image_path;
            if ($coverImageFile) {
                $this->deleteStoredAsset($brand->cover_image_path);
                $coverImagePath = $this->storeUploadedAsset($coverImageFile, 'brands/cover-images');
            }

            if ($brand->user) {
                $userData = [
                    'name'      => $validated['contact_name'],
                    'email'     => $validated['email'],
                    'phone'     => $this->nullableString($validated['phone'] ?? null),
                    'is_active' => $isActive
                ];

                if (!empty($validated['password']) && is_string($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }

                $this->brandRepository->updateUser($brand->user, $userData);
            }

            return $this->brandRepository->updateBrand($brand, [
                'brand_name'         => $validated['brand_name'],
                'description'        => $this->nullableString($validated['description'] ?? null),
                'industry'           => $this->nullableString($validated['industry'] ?? null),
                'phone'              => $this->nullableString($validated['phone'] ?? null),
                'email'              => $validated['email'],
                'website'            => $this->nullableString($validated['website'] ?? null),
                'location'           => $this->nullableString($validated['location'] ?? null),
                'city'               => $this->nullableString($validated['city'] ?? null),
                'country'            => $this->nullableString($validated['country'] ?? null),
                'postal_code'        => $this->nullableString($validated['postal_code'] ?? null),
                'profile_image_path' => $profileImagePath,
                'cover_image_path'   => $coverImagePath,
                'is_verified'        => (bool) ($validated['is_verified'] ?? false),
                'is_active'          => $isActive
            ]);
        });
    }

    /**
     * Delete a brand if no critical dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteBrand(Brand $brand): array
    {
        $dependencyCount = $this->brandRepository->getDependencyCount($brand);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Brand cannot be deleted because it already has related orders or reviews.'
            ];
        }

        return DB::transaction(function () use ($brand): array {
            $this->deleteStoredAsset($brand->profile_image_path);
            $this->deleteStoredAsset($brand->cover_image_path);

            if ($brand->user) {
                $this->brandRepository->deleteUser($brand->user);
            } else {
                $this->brandRepository->deleteBrand($brand);
            }

            return [
                'deleted' => true,
                'message' => 'Brand deleted successfully.'
            ];
        });
    }

    /**
     * Toggle active status and keep linked user in sync.
     */
    public function toggleStatus(Brand $brand): bool
    {
        $updatedBrand = $this->brandRepository->toggleStatus($brand);

        if ($updatedBrand->user) {
            $this->brandRepository->updateUser($updatedBrand->user, [
                'is_active' => $updatedBrand->is_active
            ]);
        }

        return $updatedBrand->is_active;
    }

    /**
     * Persist uploaded file and return its public path.
     */
    private function storeUploadedAsset(?UploadedFile $file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        $storedPath = $file->store($directory, 'public');

        return 'storage/' . $storedPath;
    }

    /**
     * Delete public storage files only.
     */
    private function deleteStoredAsset(?string $path): void
    {
        if (!$path || !str_starts_with($path, 'storage/')) {
            return;
        }

        Storage::disk('public')->delete(Str::after($path, 'storage/'));
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
}
