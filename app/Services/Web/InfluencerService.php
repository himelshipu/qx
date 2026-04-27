<?php

declare(strict_types=1);

namespace App\Services\Web;

use App\Models\Influencer;
use App\Models\InfluencerPlatformStat;
use App\Models\Review;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class InfluencerService
 *
 * Handles business logic for influencer listing pages.
 */
final class InfluencerService
{
    /**
     * Canonical platform ordering for UI lists.
     *
     * @var array<int, string>
     */
    private const PLATFORM_PRIORITY = [
        'facebook',
        'instagram',
        'ugc',
        'tiktok',
        'youtube',
        'linkedin',
        'x',
        'other',
    ];

    /**
     * Canonical platform config for labels and slugs.
     *
     * @var array<string, array{label:string,slug:string}>
     */
    private const PLATFORM_CONFIG = [
        'featured' => ['label' => 'Featured', 'slug' => 'featured'],
        'facebook' => ['label' => 'Facebook', 'slug' => 'facebook'],
        'instagram' => ['label' => 'Instagram', 'slug' => 'instagram'],
        'ugc' => ['label' => 'User Generated Content', 'slug' => 'user-generated-content'],
        'tiktok' => ['label' => 'TikTok', 'slug' => 'tiktok'],
        'youtube' => ['label' => 'YouTube', 'slug' => 'youtube'],
        'linkedin' => ['label' => 'LinkedIn', 'slug' => 'linkedin'],
        'x' => ['label' => 'X', 'slug' => 'x'],
        'other' => ['label' => 'Other', 'slug' => 'other'],
    ];

    /**
     * Resolve canonical platform key from incoming URL slug.
     */
    public function resolvePlatformKeyFromSlug(?string $platformSlug): ?string
    {
        if ($platformSlug === null || trim($platformSlug) === '') {
            return null;
        }

        $slug = Str::of($platformSlug)->lower()->trim()->value();

        $aliasMap = [
            'featured' => 'featured',
            'ugc' => 'ugc',
            'user-generated-content' => 'ugc',
            'twitter' => 'x',
            'twitter-x' => 'x',
        ];

        if (array_key_exists($slug, $aliasMap)) {
            return $aliasMap[$slug];
        }

        foreach ($this->getPlatformFilters() as $platform) {
            if ($platform['slug'] === $slug) {
                return $platform['key'];
            }
        }

        return null;
    }

    /**
     * Get normalized platform metadata for filter controls.
     *
     * @return Collection<int, array{key:string,label:string,slug:string}>
     */
    public function getPlatformFilters(): Collection
    {
        $discoveredPlatforms = InfluencerPlatformStat::query()
            ->where('is_active', true)
            ->select('platform')
            ->distinct()
            ->pluck('platform')
            ->map(fn ($value): string => $this->normalizePlatformKey((string) $value))
            ->filter(fn (string $value): bool => $value !== '')
            ->values();

        $orderedPlatforms = collect(self::PLATFORM_PRIORITY)
            ->merge($discoveredPlatforms)
            ->unique()
            ->values();

        return $orderedPlatforms
            ->map(fn (string $platformKey): array => $this->platformMeta($platformKey))
            ->values();
    }

    /**
     * Get platform metadata by key.
     *
     * @return array{key:string,label:string,slug:string}
     */
    public function getPlatformMeta(string $platformKey): array
    {
        return $this->platformMeta($platformKey);
    }

    /**
     * Get distinct active regions from influencer profiles.
     *
     * @return Collection<int, string>
     */
    public function getRegionFilters(int $limit = 24): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->whereHas('influencer', fn (Builder $query): Builder => $query->where('is_active', true))
            ->select('country')
            ->distinct()
            ->orderBy('country')
            ->limit($limit)
            ->pluck('country')
            ->map(fn ($country): string => trim((string) $country))
            ->filter(fn (string $country): bool => $country !== '')
            ->values();
    }

    /**
     * Get distinct active gender filters from influencer profiles.
     *
     * @return Collection<int, array{value:string,label:string}>
     */
    public function getGenderFilters(): Collection
    {
        $discovered = User::query()
            ->where('is_active', true)
            ->whereNotNull('gender')
            ->where('gender', '!=', '')
            ->whereHas('influencer', fn (Builder $query): Builder => $query->where('is_active', true))
            ->select('gender')
            ->distinct()
            ->pluck('gender')
            ->map(fn ($gender): string => Str::of((string) $gender)->lower()->trim()->value())
            ->filter(fn (string $gender): bool => in_array($gender, ['male', 'female', 'other'], true))
            ->unique()
            ->values();

        $ordered = collect(['male', 'female', 'other'])
            ->filter(fn (string $gender): bool => $discovered->contains($gender));

        if ($ordered->isEmpty()) {
            $ordered = collect(['male', 'female', 'other']);
        }

        return $ordered
            ->map(fn (string $gender): array => [
                'value' => $gender,
                'label' => Str::headline($gender),
            ])
            ->values();
    }

    /**
     * Get follower range filters with only ranges that have active influencers.
     *
     * @return Collection<int, array{value:string,label:string}>
     */
    public function getFollowerRangeFilters(): Collection
    {
        $ranges = [
            ['value' => '0-10000', 'label' => '0 - 10K', 'min' => 0, 'max' => 10000],
            ['value' => '10001-50000', 'label' => '10K - 50K', 'min' => 10001, 'max' => 50000],
            ['value' => '50001-100000', 'label' => '50K - 100K', 'min' => 50001, 'max' => 100000],
            ['value' => '100001-500000', 'label' => '100K - 500K', 'min' => 100001, 'max' => 500000],
            ['value' => '500001+', 'label' => '500K+', 'min' => 500001, 'max' => null],
        ];

        $available = collect($ranges)
            ->filter(function (array $range): bool {
                $query = InfluencerPlatformStat::query()->where('is_active', true);

                $query->where('follower_count', '>=', $range['min']);

                if ($range['max'] !== null) {
                    $query->where('follower_count', '<=', $range['max']);
                }

                return $query->exists();
            })
            ->map(fn (array $range): array => [
                'value' => $range['value'],
                'label' => $range['label'],
            ])
            ->values();

        return $available->isNotEmpty()
            ? $available
            : collect($ranges)->map(fn (array $range): array => [
                'value' => $range['value'],
                'label' => $range['label'],
            ])->values();
    }

    /**
     * Get paginated influencers, optionally filtered by a platform key.
     *
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    /**
     * Get top featured influencers for the homepage section (not paginated).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getFeaturedInfluencers(int $limit = 4): Collection
    {
        $influencers = Influencer::query()
            ->with([
                'user:id,name,slug,city,country,profile_image_path,is_active',
                'platformStats' => fn ($q) => $q->where('is_active', true)->orderByDesc('follower_count'),
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->orderByRaw('featured_priority IS NULL')
            ->orderBy('featured_priority')
            ->limit($limit)
            ->get();

        $influencerIds = $influencers->pluck('id')->all();

        $reviewsByInfluencer = Review::query()
            ->whereIn('influencer_id', $influencerIds)
            ->selectRaw('influencer_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        return $influencers
            ->map(fn (Influencer $influencer): ?array => $this->normalizeInfluencerCard($influencer, $reviewsByInfluencer))
            ->filter()
            ->values();
    }

    /**
     * Get paginated featured influencers for the /influencer/featured page.
     *
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    public function paginateFeaturedInfluencers(int $perPage = 20): LengthAwarePaginator
    {
        $paginator = Influencer::query()
            ->with([
                'user:id,name,slug,city,country,profile_image_path,is_active',
                'platformStats' => fn ($q) => $q->where('is_active', true)->orderByDesc('follower_count'),
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->whereHas('user', fn ($q) => $q->where('is_active', true))
            ->orderByRaw('featured_priority IS NULL')
            ->orderBy('featured_priority')
            ->paginate($perPage)
            ->withQueryString();

        $influencerIds = $paginator->getCollection()->pluck('id')->unique()->values()->all();

        $reviewsByInfluencer = Review::query()
            ->whereIn('influencer_id', $influencerIds)
            ->selectRaw('influencer_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $mapped = $paginator->getCollection()
            ->map(fn (Influencer $influencer): ?array => $this->normalizeInfluencerCard($influencer, $reviewsByInfluencer))
            ->filter()
            ->values();

        $paginator->setCollection($mapped);

        return $paginator;
    }

    /**
     * @param  array{categories?:array<int, int|string>,sort?:string,gender?:string,region?:string,followers?:string}  $filters
     */
    public function paginateInfluencers(?string $platformKey, int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $normalizedPlatformKey = $platformKey !== null
        ? $this->normalizePlatformKey($platformKey)
        : null;
        $selectedCategoryIds = array_values(array_filter(array_map(
            fn ($value): int => (int) $value,
            $filters['categories'] ?? []
        ), fn (int $id): bool => $id > 0));
        $sort = trim((string) ($filters['sort'] ?? 'followers_desc'));
        $gender = Str::of((string) ($filters['gender'] ?? ''))->lower()->trim()->value();
        $gender = in_array($gender, ['male', 'female', 'other'], true) ? $gender : '';
        $region = trim((string) ($filters['region'] ?? ''));
        [$minFollowers, $maxFollowers] = $this->resolveFollowerRange((string) ($filters['followers'] ?? ''));

        if ($normalizedPlatformKey === 'featured') {
            return $this->paginateFeaturedInfluencers($perPage);
        }

        $query = InfluencerPlatformStat::query()
            ->with([
                'influencer:id,user_id,display_name,title_name,is_active',
                'influencer.user:id,name,slug,city,country,bio,profile_image_path,is_active',
            ])
            ->where('is_active', true)
            ->whereHas('influencer', function ($query) use ($selectedCategoryIds) {
                $query->where('is_active', true)
                    ->whereHas('user', fn ($userQuery) => $userQuery->where('is_active', true));

                if ($selectedCategoryIds !== []) {
                    $query->whereHas('categories', fn ($categoryQuery) => $categoryQuery->whereIn('categories.id', $selectedCategoryIds));
                }
            })
            ->whereHas('influencer.user', function (Builder $query) use ($gender, $region): void {
                if ($gender !== '') {
                    $query->where('gender', $gender);
                }

                if ($region !== '') {
                    $query->where(function (Builder $regionQuery) use ($region): void {
                        $regionQuery
                            ->where('country', 'like', "%{$region}%")
                            ->orWhere('city', 'like', "%{$region}%");
                    });
                }
            })
            ->when($minFollowers !== null, fn (Builder $query): Builder => $query->where('follower_count', '>=', $minFollowers))
            ->when($maxFollowers !== null, fn (Builder $query): Builder => $query->where('follower_count', '<=', $maxFollowers))
            ->when($normalizedPlatformKey !== null, fn ($query) => $query->where('platform', $normalizedPlatformKey));

        $this->applySorting($query, $sort);

        $paginator = $query->paginate($perPage)->withQueryString();

        $influencerIds = $paginator->getCollection()
            ->pluck('influencer_id')
            ->unique()
            ->values()
            ->all();

        $reviewsByInfluencer = Review::query()
            ->whereIn('influencer_id', $influencerIds)
            ->selectRaw('influencer_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $influencers = $paginator->getCollection()
            ->map(function (InfluencerPlatformStat $stat) use ($reviewsByInfluencer): ?array {
                $influencer = $stat->influencer;

                if (! $influencer || ! $influencer->user) {
                    return null;
                }

                $platformKey = $this->normalizePlatformKey((string) $stat->platform);
                $platformMeta = $this->platformMeta($platformKey);

                $reviewSummary = $reviewsByInfluencer->get($influencer->id);
                $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
                ? (float) $reviewSummary->average_rating
                : null;

                return [
                    'id' => $influencer->id,
                    'slug' => $influencer->user->slug,
                    'name' => $this->resolveInfluencerName($influencer),
                    'title' => $this->resolveInfluencerTitle($influencer),
                    'location' => $this->resolveInfluencerLocation($influencer),
                    'image_url' => $influencer->user->profile_image_path,
                    'platform' => $platformKey,
                    'platform_label' => $platformMeta['label'],
                    'platform_slug' => $platformMeta['slug'],
                    'handle' => $this->resolveHandle($stat->handle, $influencer->user->slug),
                    'followers_label' => $this->formatFollowers($stat->follower_count),
                    'rating_label' => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
                    'reviews_count' => $reviewSummary ? (int) $reviewSummary->reviews_count : 0,
                ];
            })
            ->filter()
            ->values();

        $paginator->setCollection($influencers);

        return $paginator;
    }

    /**
     * Normalize an Influencer model (with eager-loaded platformStats + user) to a card array.
     * Used by both getFeaturedInfluencers and paginateFeaturedInfluencers.
     *
     * @param  Collection<int, mixed>  $reviewsByInfluencer
     * @return array<string,                       mixed>|null
     */
    private function normalizeInfluencerCard(Influencer $influencer, Collection $reviewsByInfluencer): ?array
    {
        if (! $influencer->user) {
            return null;
        }

        $stat = $influencer->platformStats->first();
        $platformKey = $stat !== null
        ? $this->normalizePlatformKey((string) $stat->platform)
        : 'other';
        $platformMeta = $this->platformMeta($platformKey);

        $reviewSummary = $reviewsByInfluencer->get($influencer->id);
        $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
        ? (float) $reviewSummary->average_rating
        : null;

        return [
            'id' => $influencer->id,
            'slug' => $influencer->user->slug,
            'name' => $this->resolveInfluencerName($influencer),
            'title' => $this->resolveInfluencerTitle($influencer),
            'location' => $this->resolveInfluencerLocation($influencer),
            'image_url' => $influencer->user->profile_image_path,
            'platform' => $platformKey,
            'platform_label' => $platformMeta['label'],
            'platform_slug' => $platformMeta['slug'],
            'handle' => $this->resolveHandle($stat?->handle, $influencer->user->slug),
            'followers_label' => $this->formatFollowers($stat?->follower_count),
            'rating_label' => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
            'reviews_count' => $reviewSummary ? (int) $reviewSummary->reviews_count : 0,
        ];
    }

    /**
     * Build platform metadata from key.
     *
     * @return array{key:string,label:string,slug:string}
     */
    private function platformMeta(string $platformKey): array
    {
        $normalizedKey = $this->normalizePlatformKey($platformKey);
        $configured = self::PLATFORM_CONFIG[$normalizedKey] ?? null;

        if ($configured !== null) {
            return [
                'key' => $normalizedKey,
                'label' => $configured['label'],
                'slug' => $configured['slug'],
            ];
        }

        $label = Str::headline($normalizedKey);

        return [
            'key' => $normalizedKey,
            'label' => $label,
            'slug' => Str::slug($label),
        ];
    }

    /**
     * Normalize platform values.
     */
    private function normalizePlatformKey(string $platformKey): string
    {
        return Str::of($platformKey)
            ->lower()
            ->trim()
            ->value();
    }

    /**
     * Resolve influencer display name.
     */
    private function resolveInfluencerName(Influencer $influencer): string
    {
        $displayName = trim((string) ($influencer->display_name ?? ''));

        if ($displayName !== '') {
            return $displayName;
        }

        return trim((string) $influencer->user?->name) !== ''
        ? (string) $influencer->user?->name
        : ($influencer->user?->slug ?? 'N/A');
    }

    /**
     * Resolve influencer title text for cards.
     */
    private function resolveInfluencerTitle(Influencer $influencer): string
    {
        $title = trim((string) ($influencer->title_name ?? ''));
        if ($title !== '') {
            return $title;
        }

        $bio = trim((string) ($influencer->user?->bio ?? ''));
        if ($bio !== '') {
            return Str::limit($bio, 56);
        }

        return 'N/A';
    }

    /**
     * Resolve influencer location text.
     */
    private function resolveInfluencerLocation(Influencer $influencer): string
    {
        $parts = array_values(array_filter([
            trim((string) ($influencer->user?->city ?? '')),
            trim((string) ($influencer->user?->country ?? '')),
        ]));

        return $parts !== [] ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Normalize social handle output.
     */
    private function resolveHandle(?string $handle, ?string $slug = null): string
    {
        $normalized = trim((string) $handle);

        if ($normalized !== '') {
            return str_starts_with($normalized, '@') ? $normalized : '@'.$normalized;
        }

        $normalizedSlug = trim((string) $slug);

        return $normalizedSlug !== '' ? '@'.ltrim($normalizedSlug, '@') : 'N/A';
    }

    /**
     * Apply supported influencer listing sort options.
     */
    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'followers_asc' => $query->orderBy('follower_count'),
           'recent' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('follower_count')
        };
    }

    /**
     * Resolve follower range filter values to min/max bounds.
     *
     * @return array{0:?int,1:?int}
     */
    private function resolveFollowerRange(string $value): array
    {
        $normalized = trim($value);

        $presets = [
            '0-10000' => [0, 10000],
            '10001-50000' => [10001, 50000],
            '50001-100000' => [50001, 100000],
            '100001-500000' => [100001, 500000],
            '500001+' => [500001, null],
        ];

        if (array_key_exists($normalized, $presets)) {
            return $presets[$normalized];
        }

        if (preg_match('/^(\d+)\-(\d+)$/', $normalized, $matches) === 1) {
            $min = max(0, (int) $matches[1]);
            $max = max($min, (int) $matches[2]);

            return [$min, $max];
        }

        if (preg_match('/^(\d+)\+$/', $normalized, $matches) === 1) {
            return [max(0, (int) $matches[1]), null];
        }

        return [null, null];
    }

    /**
     * Format follower count for compact display.
     */
    private function formatFollowers(?int $count): string
    {
        $value = max(0, (int) $count);

        if ($value >= 1000000) {
            return number_format($value / 1000000, 1).'M';
        }

        if ($value >= 1000) {
            return number_format($value / 1000, 1).'K';
        }

        return (string) $value;
    }

    /**
     * Format nullable decimal percentage values.
     */
    private function formatPercentage(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return number_format((float) $value, 1).'%';
    }
}
