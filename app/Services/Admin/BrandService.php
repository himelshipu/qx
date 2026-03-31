<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Category;
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
     * @return array{brand:Brand,onboardingData:array<string,mixed>}
     */
    public function getDetailPayload(Brand $brand): array
    {
        $brand->load([
            'user:id,slug,name,email,user_type,phone,city,country,postal_code,address_line,bio,profile_image_path,cover_image_path,is_active,email_verified_at,last_login_at,stripe_customer_id,created_at',
            'socialLinks',
            'billingProfiles',
            'onboardingProfile.categories:id,name'
        ])->loadCount(['campaigns', 'orders', 'reviews']);

        return [
            'brand'          => $brand,
            'onboardingData' => $this->buildOnboardingData($brand)
        ];
    }

    /**
     * Build normalized onboarding data from canonical profile with setup_data fallback.
     *
     * @return array<string,mixed>
     */
    private function buildOnboardingData(Brand $brand): array
    {
    $profile = $brand->onboardingProfile;
    $rawSetup = $brand->getAttribute('setup_data');
    $setup   = is_array($rawSetup) ? $rawSetup : [];

        $industrySlugs = array_values(array_filter(array_map(
            fn($value): string => trim((string) $value),
            is_array($setup['influencer-type'] ?? null) ? $setup['influencer-type'] : []
        ), fn(string $value): bool => $value !== ''));

        $setupIndustryLabels = [];
        if ($industrySlugs !== []) {
            $dbCategories = Category::query()
                ->whereIn('slug', $industrySlugs)
                ->pluck('name', 'slug');

            foreach ($industrySlugs as $slug) {
                $setupIndustryLabels[] = $dbCategories[$slug] ?? str_replace('-', ' ', ucfirst($slug));
            }
        }

        $objectiveMap = [
            'one-time-campaign' => 'Find influencers for a one-time campaign',
            'ongoing-content'   => 'Get ongoing influencer content',
            'exploring'         => "I'm not sure yet, just exploring"
        ];

        $budgetMap = [
            'under-1000' => 'Under $1,000',
            '1000-5000'  => '$1,000 - $5,000',
            '5000-10000' => '$5,000 - $10,000',
            '10000-25000' => '$10,000 - $25,000',
            '25000-50000' => '$25,000 - $50,000',
            '50000-plus' => '$50,000+'
        ];

        $businessTypeMap = [
            'agency'    => 'Agency',
            'ecommerce' => 'E-commerce',
            'saas'      => 'SaaS/Software',
            'local'     => 'Local Business',
            'other'     => 'Other'
        ];

        $companySizeMap = [
            'just-me'  => 'Just me',
            '2-10'     => '2-10 people',
            '11-50'    => '11-50 people',
            '51-200'   => '51-200 people',
            '201-500'  => '201-500 people',
            '500-plus' => '500+ people'
        ];

        $resolvedObjectiveRaw = $profile?->objective ?: ($setup['objective'] ?? null);
        $resolvedBudgetRaw = $profile?->budget_range ?: ($setup['budget'] ?? null);
        $resolvedBusinessTypeRaw = $profile?->business_type ?: ($setup['business-type'] ?? null);
        $resolvedCompanySizeRaw = $profile?->company_size ?: ($setup['company-size'] ?? null);

        $profileCategoryNames = $profile
            ? $profile->categories->pluck('name')->filter()->values()->all()
            : [];

        $industries = $profileCategoryNames !== [] ? $profileCategoryNames : $setupIndustryLabels;

        return [
            'objective'       => $objectiveMap[(string) $resolvedObjectiveRaw] ?? (is_string($resolvedObjectiveRaw) && trim($resolvedObjectiveRaw) !== '' ? $resolvedObjectiveRaw : null),
            'budget_range'    => $budgetMap[(string) $resolvedBudgetRaw] ?? (is_string($resolvedBudgetRaw) && trim($resolvedBudgetRaw) !== '' ? $resolvedBudgetRaw : null),
            'business_type'   => $businessTypeMap[(string) $resolvedBusinessTypeRaw] ?? (is_string($resolvedBusinessTypeRaw) && trim($resolvedBusinessTypeRaw) !== '' ? $resolvedBusinessTypeRaw : null),
            'company_size'    => $companySizeMap[(string) $resolvedCompanySizeRaw] ?? (is_string($resolvedCompanySizeRaw) && trim($resolvedCompanySizeRaw) !== '' ? $resolvedCompanySizeRaw : null),
            'is_completed'    => (bool) ($profile?->is_completed ?? !empty($setup)),
            'completed_at'    => $profile?->completed_at,
            'industries'      => $industries,
            'has_any_data'    => ($resolvedObjectiveRaw !== null || $resolvedBudgetRaw !== null || $resolvedBusinessTypeRaw !== null || $resolvedCompanySizeRaw !== null || $industries !== []),
            'source'          => $profile ? 'onboarding_profile' : (!empty($setup) ? 'setup_data' : null)
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
                'city'              => $this->nullableString($validated['city'] ?? null),
                'country'           => $this->nullableString($validated['country'] ?? null),
                'postal_code'       => $this->nullableString($validated['postal_code'] ?? null),
                'address_line'      => $this->nullableString($validated['location'] ?? null),
                'bio'               => $this->nullableString($validated['bio'] ?? null),
                'profile_image_path' => $this->storeUploadedAsset($profileImageFile, 'brands/profile-images'),
                'cover_image_path'   => $this->storeUploadedAsset($coverImageFile, 'brands/cover-images'),
                'user_type'         => 'brand',
                'is_active'         => $isActive,
                'email_verified_at' => now()
            ]);

            return $this->brandRepository->createBrand([
                'user_id'     => $user->id,
                'brand_name'  => $validated['brand_name'],
                'industry'    => $this->nullableString($validated['industry'] ?? null),
                'website'     => $this->nullableString($validated['website'] ?? null),
                'is_verified' => (bool) ($validated['is_verified'] ?? false)
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
            $profileImagePath = $brand->user?->profile_image_path;
            if ($profileImageFile) {
                $this->deleteStoredAsset($brand->user?->profile_image_path);
                $profileImagePath = $this->storeUploadedAsset($profileImageFile, 'brands/profile-images');
            }

            $coverImagePath = $brand->user?->cover_image_path;
            if ($coverImageFile) {
                $this->deleteStoredAsset($brand->user?->cover_image_path);
                $coverImagePath = $this->storeUploadedAsset($coverImageFile, 'brands/cover-images');
            }

            if ($brand->user) {
                $userData = [
                    'name'               => $validated['contact_name'],
                    'email'              => $validated['email'],
                    'phone'              => $this->nullableString($validated['phone'] ?? null),
                    'city'               => $this->nullableString($validated['city'] ?? null),
                    'country'            => $this->nullableString($validated['country'] ?? null),
                    'postal_code'        => $this->nullableString($validated['postal_code'] ?? null),
                    'address_line'       => $this->nullableString($validated['location'] ?? null),
                    'bio'                => $this->nullableString($validated['bio'] ?? null),
                    'profile_image_path' => $profileImagePath,
                    'cover_image_path'   => $coverImagePath,
                    'is_active'          => $isActive
                ];

                if (!empty($validated['password']) && is_string($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }

                $this->brandRepository->updateUser($brand->user, $userData);
            }

            return $this->brandRepository->updateBrand($brand, [
                'brand_name'  => $validated['brand_name'],
                'industry'    => $this->nullableString($validated['industry'] ?? null),
                'website'     => $this->nullableString($validated['website'] ?? null),
                'is_verified' => (bool) ($validated['is_verified'] ?? false)
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
            $this->deleteStoredAsset($brand->user?->profile_image_path);
            $this->deleteStoredAsset($brand->user?->cover_image_path);

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

        return (bool) ($updatedBrand->user?->is_active ?? false);
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
