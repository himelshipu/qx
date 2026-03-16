<?php

declare (strict_types = 1);

namespace App\Services\Admin;

use App\Models\Campaign;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class CampaignService
 *
 * Handles business rules for dashboard campaign management.
 */
final class CampaignService
{
    /**
     * @var array<string, string>
     */
    private const COUNTRY_OPTIONS = [
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'BD' => 'Bangladesh',
        'AE' => 'United Arab Emirates',
        'IN' => 'India',
        'CA' => 'Canada',
        'DE' => 'Germany',
        'AU' => 'Australia',
        'FR' => 'France',
        'IT' => 'Italy',
        'ES' => 'Spain',
        'NL' => 'Netherlands',
        'SE' => 'Sweden',
        'NO' => 'Norway',
        'DK' => 'Denmark',
        'FI' => 'Finland',
        'IE' => 'Ireland',
        'NZ' => 'New Zealand',
        'JP' => 'Japan',
        'KR' => 'South Korea',
        'SG' => 'Singapore',
        'MY' => 'Malaysia',
        'TH' => 'Thailand',
        'ID' => 'Indonesia',
        'PK' => 'Pakistan',
        'BR' => 'Brazil',
        'MX' => 'Mexico',
        'ZA' => 'South Africa',
        'SA' => 'Saudi Arabia',
        'TR' => 'Turkey'
    ];

    /**
     * @var array<int, string>
     */
    private const TYPE_VALUES = ['instagram', 'tiktok', 'ugc', 'youtube', 'twitch', 'other'];

    /**
     * @var array<int, string>
     */
    private const STATUS_VALUES = ['draft', 'published', 'paused', 'closed', 'archived'];

    /**
     * @var array<int, string>
     */
    private const TARGET_GENDER_VALUES = ['any', 'male', 'female', 'other'];

    public function __construct(
        private readonly CampaignRepositoryInterface $campaignRepository
    ) {}

    /**
     * Build campaign listing payload for dashboard index page.
     *
     * @return array{campaigns:\Illuminate\Contracts\Pagination\LengthAwarePaginator,stats:array{total:int,published:int,draft:int,active:int},search:string,status:string,type:string,statusOptions:array<int, array{value:string,label:string}>,typeOptions:array<int, array{value:string,label:string}>}
     */
    public function getListingPayload(string $search, string $status, string $type): array
    {
        return [
            'campaigns'     => $this->campaignRepository->paginateForDashboard($search, $status, $type),
            'stats'         => $this->campaignRepository->getStats(),
            'search'        => $search,
            'status'        => $status,
            'type'          => $type,
            'statusOptions' => $this->getStatusOptions(includeAll: true),
            'typeOptions'   => $this->getTypeOptions(includeAll: true)
        ];
    }

    /**
     * Build form payload for create/edit pages.
     *
     * @return array{categoryOptions:\Illuminate\Support\Collection<int, array{id:int,name:string}>,followerRangeOptions:\Illuminate\Support\Collection<int, array{id:int,label:string}>,campaignTypeOptions:array<int, array{value:string,label:string}>,statusOptions:array<int, array{value:string,label:string}>,genderOptions:array<int, array{value:string,label:string}>,countryOptions:array<int, array{code:string,name:string}>}
     */
    public function getFormPayload(): array
    {
        return [
            'categoryOptions'      => $this->campaignRepository->getCategoryOptions(),
            'followerRangeOptions' => $this->campaignRepository->getFollowerRangeOptions(),
            'campaignTypeOptions'  => $this->getTypeOptions(),
            'statusOptions'        => $this->getStatusOptions(),
            'genderOptions'        => $this->getGenderOptions(),
            'countryOptions'       => $this->getCountryOptions()
        ];
    }

    /**
     * Build detail payload for a single campaign.
     *
     * @return array{campaign:Campaign}
     */
    public function getDetailPayload(Campaign $campaign): array
    {
        $campaign->load([
            'targeting',
            'categories:id,name',
            'followerRanges:id,label',
            'targetCountries:id,campaign_id,country_code,country_name'
        ])->loadCount(['applications', 'assets', 'orders', 'orderItems', 'cartItems']);

        return [
            'campaign' => $campaign
        ];
    }

    /**
     * Create a campaign with relations.
     *
     * @param array<string, mixed> $validated
     */
    public function createCampaign(array $validated, bool $isActive): Campaign
    {
        return DB::transaction(function () use ($validated, $isActive): Campaign {
            $campaign = $this->campaignRepository->create($this->buildCampaignData(
                validated: $validated,
                isActive: $isActive,
                publishedAt: $this->resolvePublishedAt(status: (string) $validated['status'])
            ));

            $this->persistRelations($campaign, $validated);

            return $campaign->refresh();
        });
    }

    /**
     * Update a campaign with relations.
     *
     * @param array<string, mixed> $validated
     */
    public function updateCampaign(Campaign $campaign, array $validated, bool $isActive): Campaign
    {
        return DB::transaction(function () use ($campaign, $validated, $isActive): Campaign {
            $updatedCampaign = $this->campaignRepository->update(
                $campaign,
                $this->buildCampaignData(
                    validated: $validated,
                    isActive: $isActive,
                    publishedAt: $this->resolvePublishedAt(
                        status: (string) $validated['status'],
                        currentPublishedAt: $campaign->published_at
                    )
                )
            );

            $this->persistRelations($updatedCampaign, $validated);

            return $updatedCampaign;
        });
    }

    /**
     * Delete a campaign if no dependencies exist.
     *
     * @return array{deleted:bool,message:string}
     */
    public function deleteCampaign(Campaign $campaign): array
    {
        $dependencyCount = $this->campaignRepository->getDependencyCount($campaign);

        if ($dependencyCount > 0) {
            return [
                'deleted' => false,
                'message' => '⚠️ Cannot delete campaign. It has related applications, assets, orders, or cart items.'
            ];
        }

        $this->campaignRepository->delete($campaign);

        return [
            'deleted' => true,
            'message' => '🗑️ Campaign deleted successfully.'
        ];
    }

    /**
     * @param  array<string, mixed>   $validated
     * @return array<string, mixed>
     */
    private function buildCampaignData(array $validated, bool $isActive, mixed $publishedAt): array
    {
        return [
            'title'         => $validated['title'],
            'campaign_type' => $validated['campaign_type'],
            'description'   => $this->nullableString($validated['description'] ?? null),
            'instructions'  => $this->nullableString($validated['instructions'] ?? null),
            'status'        => $validated['status'],
            'budget_min'    => $this->nullableDecimal($validated['budget_min'] ?? null),
            'budget_max'    => $this->nullableDecimal($validated['budget_max'] ?? null),
            'currency'      => strtoupper((string) $validated['currency']),
            'start_date'    => $validated['start_date'] ?? null,
            'end_date'      => $validated['end_date'] ?? null,
            'published_at'  => $publishedAt,
            'is_active'     => $isActive
        ];
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function persistRelations(Campaign $campaign, array $validated): void
    {
        $this->campaignRepository->upsertTargeting($campaign, [
            'influencer_count' => $this->nullableInteger($validated['influencer_count'] ?? null),
            'target_gender'    => $this->normalizeTargetGender($validated['target_gender'] ?? null),
            'age_min'          => $this->nullableInteger($validated['age_min'] ?? null),
            'age_max'          => $this->nullableInteger($validated['age_max'] ?? null),
            'notes'            => $this->nullableString($validated['targeting_notes'] ?? null)
        ]);

        $this->campaignRepository->syncCategories(
            $campaign,
            $this->normalizeIntegerIds($validated['categories'] ?? [])
        );

        $this->campaignRepository->syncFollowerRanges(
            $campaign,
            $this->normalizeIntegerIds($validated['follower_ranges'] ?? [])
        );

        $this->campaignRepository->syncTargetCountries(
            $campaign,
            $this->normalizeTargetCountries($validated['target_countries'] ?? [])
        );
    }

    /**
     * Build campaign type options for forms and filters.
     *
     * @return array<int, array{value:string,label:string}>
     */
    private function getTypeOptions(bool $includeAll = false): array
    {
        $options = [];

        if ($includeAll) {
            $options[] = [
                'value' => 'all',
                'label' => 'All Types'
            ];
        }

        foreach (self::TYPE_VALUES as $type) {
            $options[] = [
                'value' => $type,
                'label' => $this->humanizeOption($type)
            ];
        }

        return $options;
    }

    /**
     * Build status options for forms and filters.
     *
     * @return array<int, array{value:string,label:string}>
     */
    private function getStatusOptions(bool $includeAll = false): array
    {
        $options = [];

        if ($includeAll) {
            $options[] = [
                'value' => 'all',
                'label' => 'All Statuses'
            ];
        }

        foreach (self::STATUS_VALUES as $status) {
            $options[] = [
                'value' => $status,
                'label' => $this->humanizeOption($status)
            ];
        }

        return $options;
    }

    /**
     * Build gender options for campaign targeting.
     *
     * @return array<int, array{value:string,label:string}>
     */
    private function getGenderOptions(): array
    {
        $options = [];

        foreach (self::TARGET_GENDER_VALUES as $gender) {
            $options[] = [
                'value' => $gender,
                'label' => $this->humanizeOption($gender)
            ];
        }

        return $options;
    }

    /**
     * Build country options for multi-select input.
     *
     * @return array<int, array{code:string,name:string}>
     */
    private function getCountryOptions(): array
    {
        $options = [];

        foreach (self::COUNTRY_OPTIONS as $code => $name) {
            $options[] = [
                'code' => $code,
                'name' => $name
            ];
        }

        return $options;
    }

    /**
     * Resolve published timestamp from status transitions.
     */
    private function resolvePublishedAt(string $status, mixed $currentPublishedAt = null): mixed
    {
        if ($status !== 'published') {
            return null;
        }

        return $currentPublishedAt ?: now();
    }

    /**
     * Normalize an option key into a UI label.
     */
    private function humanizeOption(string $value): string
    {
        if ($value === 'ugc') {
            return 'UGC';
        }

        return Str::headline($value);
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
     * Normalize optional decimal values.
     */
    private function nullableDecimal(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return number_format((float) $value, 2, '.', '');
    }

    /**
     * Normalize optional integer values.
     */
    private function nullableInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Normalize target gender values.
     */
    private function normalizeTargetGender(mixed $value): string
    {
        $gender = is_string($value) ? strtolower(trim($value)) : 'any';

        return in_array($gender, self::TARGET_GENDER_VALUES, true) ? $gender : 'any';
    }

    /**
     * Normalize numeric IDs from request payload.
     *
     * @param  mixed      $value
     * @return array<int, int>
     */
    private function normalizeIntegerIds(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(static function ($item): int {
            return (int) $item;
        }, $value), static function (int $id): bool {
            return $id > 0;
        })));
    }

    /**
     * Normalize selected target countries from request payload.
     *
     * @param  mixed      $value
     * @return array<int, array{country_code:string,country_name:string}>
     */
    private function normalizeTargetCountries(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $countries = [];

        foreach ($value as $item) {
            if (!is_string($item)) {
                continue;
            }

            $code = strtoupper(trim($item));
            if (!preg_match('/^[A-Z]{2}$/', $code)) {
                continue;
            }

            $countries[$code] = [
                'country_code' => $code,
                'country_name' => self::COUNTRY_OPTIONS[$code] ?? $code
            ];
        }

        return array_values($countries);
    }
}
