<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Influencer;
use App\Models\Package;
use App\Repositories\Contracts\PackageRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

/**
 * Class PackageService
 *
 * Handles business rules for dashboard package management.
 */
final class PackageService
{
    /**
     * @var array<int, string>
     */
    private const PLATFORM_VALUES = ['facebook', 'instagram', 'tiktok', 'linkedin', 'x', 'youtube', 'ugc', 'other'];

    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository
    ) {}

    /**
     * Build package listing payload for dashboard index page.
     *
     * @return array{packages:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,in_use:int},search:string,status:string,platform:string,platformOptions:array<int, array{value:string,label:string}>}
     */
    public function getListingPayload(string $search, string $status, string $platform): array
    {
        return [
            'packages'        => $this->packageRepository->paginateForDashboard($search, $status, $platform),
            'stats'           => $this->packageRepository->getStats(),
            'search'          => $search,
            'status'          => $status,
            'platform'        => $platform,
            'platformOptions' => $this->getPlatformOptions(includeAll: true)
        ];
    }

    /**
     * Build form payload for create/edit pages.
     *
     * @return array{platformOptions:array<int, array{value:string,label:string}>,isInfluencer:bool,influencers:?\Illuminate\Database\Eloquent\Collection}
     */
    public function getFormPayload(): array
    {
        $user         = Auth::user();
        $isInfluencer = $user && $user->influencer()->exists();

        return [
            'platformOptions' => $this->getPlatformOptions(),
            'isInfluencer'    => $isInfluencer,
            'influencers'     => !$isInfluencer ? Influencer::query()->whereHas('user')->get() : null
        ];
    }

    /**
     * Create a package.
     *
     * @param array<string, mixed> $validated
     */
    public function createPackage(array $validated, bool $isActive): Package
    {
        $createdByUserId = $this->resolveAuthenticatedUserId();
        $user            = Auth::user();

        // Determine influencer_id based on user type
        // If user is an influencer, they are creating a package for themselves
        // If user is admin/moderator, they are creating a package for a selected influencer
        $influencerId = null;

        if ($user && $user->influencer()->exists()) {
            // User is an influencer, set influencer_id to their influencer id
            $influencerId = $user->influencer->id;
        } elseif (isset($validated['created_for']) && (int) $validated['created_for'] > 0) {
            // Admin/moderator creating package for a specific influencer
            $influencerId = (int) $validated['created_for'];
        }

        return $this->packageRepository->create([
            'platform'           => $validated['platform'],
            'name'               => $validated['name'],
            'description'        => $this->nullableString($validated['description'] ?? null),
            'base_price'         => $this->normalizePrice($validated['base_price']),
            'currency'           => $this->normalizeCurrency((string) $validated['currency']),
            'delivery_days'      => $this->nullableInteger($validated['delivery_days'] ?? null),
            'revisions_included' => $this->nullableInteger($validated['revisions_included'] ?? null),
            'influencer_id'      => $influencerId,
            'created_by'         => $createdByUserId,
            'is_active'          => $isActive
        ]);
    }

    /**
     * Update a package.
     *
     * @param array<string, mixed> $validated
     */
    public function updatePackage(Package $package, array $validated, bool $isActive): Package
    {
        $user       = Auth::user();
        $updateData = [
            'platform'           => $validated['platform'],
            'name'               => $validated['name'],
            'description'        => $this->nullableString($validated['description'] ?? null),
            'base_price'         => $this->normalizePrice($validated['base_price']),
            'currency'           => $this->normalizeCurrency((string) $validated['currency']),
            'delivery_days'      => $this->nullableInteger($validated['delivery_days'] ?? null),
            'revisions_included' => $this->nullableInteger($validated['revisions_included'] ?? null),
            'is_active'          => $isActive
        ];

        // Only allow updating influencer_id if user is admin/moderator and created_for is provided
        if ($user && !$user->influencer()->exists() && isset($validated['created_for']) && (int) $validated['created_for'] > 0) {
            $updateData['influencer_id'] = (int) $validated['created_for'];
        }

        return $this->packageRepository->update($package, $updateData);
    }

    /**
     * Delete a package if no dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deletePackage(Package $package): array
    {
        $dependencyCount = $this->packageRepository->getDependencyCount($package);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => 'Package cannot be deleted because it is already linked to cart or order items.'
            ];
        }

        $this->packageRepository->delete($package);

        return [
            'deleted' => true,
            'message' => 'Package deleted successfully.'
        ];
    }

    /**
     * Toggle package active status.
     */
    public function toggleStatus(Package $package): bool
    {
        return $this->packageRepository->toggleStatus($package)->is_active;
    }

    /**
     * Build package platform options.
     *
     * @return array<int, array{value:string,label:string}>
     */
    public function getPlatformOptions(bool $includeAll = false): array
    {
        $options = [];

        if ($includeAll) {
            $options[] = [
                'value' => 'all',
                'label' => 'All Platforms'
            ];
        }

        foreach (self::PLATFORM_VALUES as $platform) {
            $options[] = [
                'value' => $platform,
                'label' => $this->humanizeOption($platform)
            ];
        }

        return $options;
    }

    /**
     * Convert snake/kebab values to readable labels.
     */
    private function humanizeOption(string $value): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $value));
    }

    /**
     * Resolve the authenticated user id for package ownership.
     */
    private function resolveAuthenticatedUserId(): int
    {
        $authId = Auth::id();

        if (is_int($authId)) {
            return $authId;
        }

        if (is_string($authId) && ctype_digit($authId)) {
            return (int) $authId;
        }

        throw new RuntimeException('Authenticated user is required to create packages.');
    }

    /**
     * Normalize decimal input for storage.
     */
    private function normalizePrice(mixed $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }

    /**
     * Normalize currency code.
     */
    private function normalizeCurrency(string $value): string
    {
        $currency = strtoupper(trim($value));

        return $currency !== '' ? $currency : 'USD';
    }

    /**
     * Normalize nullable string inputs.
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
     * Normalize nullable integer inputs.
     */
    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * Get payload for package purchase page.
     */
    public function getPurchasePayload(): array
    {
        $packages = Package::where('is_active', true)
            ->with(['influencer:id,display_name', 'influencer.user:id,email,name'])
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'description', 'base_price', 'currency', 'platform', 'delivery_days', 'revisions_included', 'influencer_id']);

        $brands = \App\Models\Brand::with('user:id,email,name')
            ->orderBy('brand_name')
            ->get(['id', 'user_id', 'brand_name']);

        $activeBrandsCount   = $brands->count();
        $activePackagesCount = $packages->count();

        // Get latest purchased packages
        $latestPurchases = \App\Models\Order::where('status', '!=', 'cancelled')
            ->with(['items', 'brand:id,brand_name', 'buyer:id,email,name'])
            ->orderByDesc('placed_at')
            ->limit(10)
            ->get(['id', 'order_number', 'brand_id', 'buyer_user_id', 'status', 'total_amount', 'currency', 'placed_at']);

        return compact('packages', 'brands', 'activeBrandsCount', 'activePackagesCount', 'latestPurchases');
    }

    /**
     * Handle package purchase for multiple brands.
     */
    public function purchasePackageForBrands(int $packageId, array $brandIds): int
    {
        $package   = Package::findOrFail($packageId);
        $purchased = 0;

        foreach ($brandIds as $brandId) {
            // Check if order already exists
            $existingOrder = \App\Models\Order::where('brand_id', $brandId)
                ->whereHas('items', function ($query) use ($packageId) {
                    $query->where('package_id', $packageId);
                })
                ->exists();

            if (!$existingOrder) {
                // Create order
                $order = \App\Models\Order::create([
                    'order_number'  => 'ORD-' . strtoupper(uniqid()),
                    'buyer_user_id' => Auth::id(),
                    'brand_id'      => $brandId,
                    'status'        => 'pending',
                    'subtotal'      => $package->base_price,
                    'service_fee'   => 0,
                    'tax_amount'    => 0,
                    'total_amount'  => $package->base_price,
                    'currency'      => $package->currency,
                    'placed_at'     => now()
                ]);

                // Create order item
                \App\Models\OrderItem::create([
                    'order_id'      => $order->id,
                    'influencer_id' => $package->influencer_id,
                    'package_id'    => $packageId,
                    'title'         => $package->name,
                    'description'   => $package->description,
                    'quantity'      => 1,
                    'unit_price'    => $package->base_price,
                    'line_total'    => $package->base_price,
                    'status'        => 'pending',
                    'due_date'      => $package->delivery_days ? now()->addDays($package->delivery_days)->toDateString() : null
                ]);

                $purchased++;
            }
        }

        return $purchased;
    }
}
