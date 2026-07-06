<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Influencer;
use App\Models\Brand;
use App\Models\Notification;
use App\Models\Order;
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

    /**
     * Package names seeded by Database\Seeders\PackageSeeder, grouped by platform.
     *
     * @var array<string, array<int, string>>
     */
    private const SEEDED_PACKAGE_NAMES_BY_PLATFORM = [
        'instagram' => [
            '1 Instagram Story',
            '2 Instagram Stories',
            '1 Instagram Reel (60 Seconds)',
            '2 Instagram Reels',
            '1 Instagram Photo Feed Post',
        ],
        'tiktok' => [
            '1 TikTok Video (30 Seconds)',
            '1 TikTok Video (60 Seconds)',
            '2 TikTok Videos',
            '3 TikTok Videos',
            '1 TikTok Stories',
            '1 TikTok Live (30 Minutes)',
        ],
        'youtube' => [
            '1 YouTube Short',
            '1 YouTube Video',
            '2 YouTube Videos',
        ],
    ];

    public function __construct(
        private readonly PackageRepositoryInterface $packageRepository
    ) {}

    /**
     * Build package listing payload for dashboard index page.
     *
     * @param array<string, mixed> $filters
     * @return array{packages:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,active:int,inactive:int,in_use:int},filters:array<string,mixed>,platformOptions:array<int, array{value:string,label:string}>,influencerOptions:array<int, array{value:string,label:string}>}
     */
    public function getIndexPayload(array $filters): array
    {
        $normalized = [
            'q' => trim((string) ($filters['q'] ?? '')),
            'status' => (string) ($filters['status'] ?? 'all'),
            'platform' => (string) ($filters['platform'] ?? 'all'),
            'influencer_id' => $this->normalizeInfluencerFilter($filters['influencer_id'] ?? 'all'),
        ];

        return [
            'packages'         => $this->packageRepository->paginateForDashboard($normalized),
            'stats'            => $this->packageRepository->getStats(),
            'filters'          => $normalized,
            'platformOptions'  => $this->getPlatformOptions(includeAll: true),
            'influencerOptions' => $this->getInfluencerOptions(),
        ];
    }

    /**
     * @param array<string, mixed> $filters
     * @return array{packages:\Illuminate\Contracts\Pagination\LengthAwarePaginator}
     */
    public function getTablePayload(array $filters): array
    {
        $normalized = [
            'q' => trim((string) ($filters['q'] ?? '')),
            'status' => (string) ($filters['status'] ?? 'all'),
            'platform' => (string) ($filters['platform'] ?? 'all'),
            'influencer_id' => $this->normalizeInfluencerFilter($filters['influencer_id'] ?? 'all'),
        ];

        return [
            'packages' => $this->packageRepository->paginateForDashboard($normalized),
        ];
    }

    /**
     * Normalize the influencer filter input.
     */
    private function normalizeInfluencerFilter(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'all';
        }

        return (string) $value;
    }

    /**
     * Build influencer options for the dashboard filter dropdown.
     *
     * @return array<int, array{value:string,label:string}>
     */
    private function getInfluencerOptions(): array
    {
        $options = [
            ['value' => 'all', 'label' => 'All Influencers'],
            ['value' => 'unassigned', 'label' => 'Unassigned'],
        ];

        $influencers = Influencer::query()
            ->with('user:id,name')
            ->orderBy('display_name')
            ->get(['id', 'user_id', 'display_name']);

        foreach ($influencers as $influencer) {
            $label = $influencer->display_name
                ?: ($influencer->user?->name ?: 'Influencer #' . $influencer->id);

            $options[] = [
                'value' => (string) $influencer->id,
                'label' => $label,
            ];
        }

        return $options;
    }

    /**
     * Build form payload for create/edit pages.
     *
     * @return array{platformOptions:array<int, array{value:string,label:string}>,isInfluencer:bool,influencers:?\Illuminate\Database\Eloquent\Collection,packageOptions:array<int, string>}
     */
    public function getFormPayload(): array
    {
        $user         = Auth::user();
        $isInfluencer = $user && $user->influencer()->exists();

        return [
            'platformOptions' => $this->getPlatformOptions(),
            'isInfluencer'    => $isInfluencer,
            'influencers'     => !$isInfluencer ? Influencer::query()->whereHas('user')->get() : null,
            'packageNamesByPlatform' => self::SEEDED_PACKAGE_NAMES_BY_PLATFORM,
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

        $package = $this->packageRepository->create([
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

        $this->notifyPackageOwner($package, 'Package created', sprintf('Your package "%s" was created in the dashboard.', $package->name));

        return $package;
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

        $updated = $this->packageRepository->update($package, $updateData);

        $this->notifyPackageOwner($updated, 'Package updated', sprintf('Your package "%s" was updated in the dashboard.', $updated->name));

        return $updated;
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

        $ownerUserId = (int) ($package->influencer?->user_id ?? 0);
        if ($ownerUserId > 0) {
            Notification::create([
                'user_id' => $ownerUserId,
                'type' => 'package',
                'title' => 'Package deleted',
                'body' => sprintf('Your package "%s" was removed from the dashboard.', $package->name),
                'data_json' => [
                    'package_id' => $package->id,
                ],
                'notifiable_type' => Package::class,
                'notifiable_id' => $package->id,
                'is_read' => false,
            ]);
        }

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
        $updated = $this->packageRepository->toggleStatus($package);

        $this->notifyPackageOwner(
            $updated,
            'Package status changed',
            sprintf('Your package "%s" is now %s.', $updated->name, $updated->is_active ? 'active' : 'inactive')
        );

        return $updated->is_active;
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
            ->select(['id', 'name', 'description', 'base_price', 'currency', 'platform', 'delivery_days', 'revisions_included', 'influencer_id'])
            ->with(['influencer:id,display_name', 'influencer.user:id,email,name'])
            ->orderByDesc('created_at')
            ->get();

        $brands = \App\Models\Brand::with('user:id,email,name')
            ->select(['id', 'user_id', 'brand_name'])
            ->orderBy('brand_name')
            ->get();

        $activeBrandsCount   = $brands->count();
        $activePackagesCount = $packages->count();

        // Get latest purchased packages
        $latestPurchases = \App\Models\Order::where('status', '!=', 'cancelled')
            ->select(['id', 'order_number', 'brand_id', 'buyer_user_id', 'status', 'total_amount', 'currency', 'placed_at'])
            ->with(['brand:id,brand_name', 'buyer:id,email,name'])
            ->orderByDesc('placed_at')
            ->limit(10)
            ->get();

        return compact('packages', 'brands', 'activeBrandsCount', 'activePackagesCount', 'latestPurchases');
    }

    /**
     * Handle package purchase for multiple brands.
     */
    public function purchasePackageForBrands(int $packageId, array $brandIds): int
    {
        $package   = Package::findOrFail($packageId);
        $purchased = 0;
        $packageOwnerUserId = (int) ($package->influencer?->user_id ?? 0);

        foreach ($brandIds as $brandId) {
            // Check if order already exists
            $existingOrder = \App\Models\Order::where('brand_id', $brandId)
                ->whereHas('items', function ($query) use ($packageId) {
                    $query->where('package_id', $packageId);
                })
                ->exists();

            if (!$existingOrder) {
                // Calculate 20% service fee
                $subtotal = $package->base_price;
                $serviceFee = $subtotal * 0.20;
                $totalAmount = $subtotal + $serviceFee;

                // Create order
                $order = \App\Models\Order::create([
                    'order_number'  => Order::generateOrderNumber(Order::SOURCE_PACKAGE),
                    'buyer_user_id' => Auth::id(),
                    'brand_id'      => $brandId,
                    'status'        => 'pending',
                    'subtotal'      => $subtotal,
                    'service_fee'   => $serviceFee,
                    'tax_amount'    => 0,
                    'total_amount'  => $totalAmount,
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

                $brandUserId = (int) (Brand::query()->where('id', $brandId)->value('user_id') ?? 0);
                if ($brandUserId > 0) {
                    Notification::create([
                        'user_id' => $brandUserId,
                        'type' => 'package',
                        'title' => 'Package purchase recorded',
                        'body' => sprintf('You purchased package "%s".', $package->name),
                        'data_json' => [
                            'action_url' => route('frontend.orders.show', $order),
                            'order_id' => $order->id,
                            'package_id' => $package->id,
                        ],
                        'notifiable_type' => Order::class,
                        'notifiable_id' => $order->id,
                        'is_read' => false,
                    ]);
                }

                if ($packageOwnerUserId > 0) {
                    Notification::create([
                        'user_id' => $packageOwnerUserId,
                        'type' => 'package',
                        'title' => 'Your package was purchased',
                        'body' => sprintf('Your package "%s" was purchased by a brand.', $package->name),
                        'data_json' => [
                            'action_url' => route('frontend.packages.show', $package),
                            'package_id' => $package->id,
                            'order_id' => $order->id,
                            'brand_id' => $brandId,
                        ],
                        'notifiable_type' => Package::class,
                        'notifiable_id' => $package->id,
                        'is_read' => false,
                    ]);
                }

                $purchased++;
            }
        }

        return $purchased;
    }

    private function notifyPackageOwner(Package $package, string $title, string $body): void
    {
        $ownerUserId = (int) ($package->influencer?->user_id ?? 0);

        if ($ownerUserId <= 0) {
            return;
        }

        Notification::create([
            'user_id' => $ownerUserId,
            'type' => 'package',
            'title' => $title,
            'body' => $body,
            'data_json' => [
                'action_url' => route('frontend.packages.show', $package),
                'package_id' => $package->id,
            ],
            'notifiable_type' => Package::class,
            'notifiable_id' => $package->id,
            'is_read' => false,
        ]);
    }
}
