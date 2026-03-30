<?php

declare (strict_types = 1);

namespace App\Services\Admin;

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
     * @return array{platformOptions:array<int, array{value:string,label:string}>}
     */
    public function getFormPayload(): array
    {
        return [
            'platformOptions' => $this->getPlatformOptions()
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

        return $this->packageRepository->create([
            'platform'           => $validated['platform'],
            'name'               => $validated['name'],
            'description'        => $this->nullableString($validated['description'] ?? null),
            'base_price'         => $this->normalizePrice($validated['base_price']),
            'currency'           => $this->normalizeCurrency((string) $validated['currency']),
            'delivery_days'      => $this->nullableInteger($validated['delivery_days'] ?? null),
            'revisions_included' => $this->nullableInteger($validated['revisions_included'] ?? null),
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
        return $this->packageRepository->update($package, [
            'platform'           => $validated['platform'],
            'name'               => $validated['name'],
            'description'        => $this->nullableString($validated['description'] ?? null),
            'base_price'         => $this->normalizePrice($validated['base_price']),
            'currency'           => $this->normalizeCurrency((string) $validated['currency']),
            'delivery_days'      => $this->nullableInteger($validated['delivery_days'] ?? null),
            'revisions_included' => $this->nullableInteger($validated['revisions_included'] ?? null),
            'is_active'          => $isActive
        ]);
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
    private function getPlatformOptions(bool $includeAll = false): array
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
}
