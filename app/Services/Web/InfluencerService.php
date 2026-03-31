<?php

declare (strict_types = 1);

namespace App\Services\Web;

use App\Models\Creator;
use App\Models\CreatorPlatformStat;
use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        'other'
    ];

    /**
     * Canonical platform config for labels and slugs.
     *
     * @var array<string, array{label:string,slug:string}>
     */
    private const PLATFORM_CONFIG = [
        'featured'  => ['label' => 'Featured', 'slug' => 'featured'],
        'facebook'  => ['label' => 'Facebook', 'slug' => 'facebook'],
        'instagram' => ['label' => 'Instagram', 'slug' => 'instagram'],
        'ugc'       => ['label' => 'User Generated Content', 'slug' => 'user-generated-content'],
        'tiktok'    => ['label' => 'TikTok', 'slug' => 'tiktok'],
        'youtube'   => ['label' => 'YouTube', 'slug' => 'youtube'],
        'linkedin'  => ['label' => 'LinkedIn', 'slug' => 'linkedin'],
        'x'         => ['label' => 'X', 'slug' => 'x'],
        'other'     => ['label' => 'Other', 'slug' => 'other']
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
            'featured'               => 'featured',
            'ugc'                    => 'ugc',
            'user-generated-content' => 'ugc',
            'twitter'                => 'x',
            'twitter-x'              => 'x'
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
        $discoveredPlatforms = CreatorPlatformStat::query()
            ->where('is_active', true)
            ->select('platform')
            ->distinct()
            ->pluck('platform')
            ->map(fn($value): string => $this->normalizePlatformKey((string) $value))
            ->filter(fn(string $value): bool => $value !== '')
            ->values();

        $orderedPlatforms = collect(self::PLATFORM_PRIORITY)
            ->merge($discoveredPlatforms)
            ->unique()
            ->values();

        return $orderedPlatforms
            ->map(fn(string $platformKey): array=> $this->platformMeta($platformKey))
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
     * Get paginated influencers, optionally filtered by a platform key.
     *
     * @return LengthAwarePaginator<int, array<string, mixed>>
     */
    /**
     * Get top featured influencers for the homepage section (not paginated).
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getFeaturedInfluencers(int $limit = 4): Collection
    {
        $creators = Creator::query()
            ->with([
                'user:id,name,slug,city,country,profile_image_path,is_active',
                'platformStats' => fn($q) => $q->where('is_active', true)->orderByDesc('follower_count')
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderByRaw('featured_priority IS NULL')
            ->orderBy('featured_priority')
            ->limit($limit)
            ->get();

        $creatorIds = $creators->pluck('id')->all();

        $reviewsByCreator = Review::query()
            ->whereIn('creator_id', $creatorIds)
            ->selectRaw('creator_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('creator_id')
            ->get()
            ->keyBy('creator_id');

        return $creators
            ->map(fn(Creator $creator): ?array=> $this->normalizeCreatorCard($creator, $reviewsByCreator))
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
        $paginator = Creator::query()
            ->with([
                'user:id,name,slug,city,country,profile_image_path,is_active',
                'platformStats' => fn($q) => $q->where('is_active', true)->orderByDesc('follower_count')
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderByRaw('featured_priority IS NULL')
            ->orderBy('featured_priority')
            ->paginate($perPage)
            ->withQueryString();

        $creatorIds = $paginator->getCollection()->pluck('id')->unique()->values()->all();

        $reviewsByCreator = Review::query()
            ->whereIn('creator_id', $creatorIds)
            ->selectRaw('creator_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('creator_id')
            ->get()
            ->keyBy('creator_id');

        $mapped = $paginator->getCollection()
            ->map(fn(Creator $creator): ?array=> $this->normalizeCreatorCard($creator, $reviewsByCreator))
            ->filter()
            ->values();

        $paginator->setCollection($mapped);

        return $paginator;
    }

    public function paginateInfluencers(?string $platformKey, int $perPage = 20): LengthAwarePaginator
    {
        $normalizedPlatformKey = $platformKey !== null
        ? $this->normalizePlatformKey($platformKey)
        : null;

        if ($normalizedPlatformKey === 'featured') {
            return $this->paginateFeaturedInfluencers($perPage);
        }

        $paginator = CreatorPlatformStat::query()
            ->with([
                'creator:id,user_id,display_name,title_name,is_active',
                'creator.user:id,name,slug,city,country,bio,profile_image_path,is_active'
            ])
            ->where('is_active', true)
            ->whereHas('creator', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('user', fn($userQuery) => $userQuery->where('is_active', true));
            })
            ->when($normalizedPlatformKey !== null, fn($query) => $query->where('platform', $normalizedPlatformKey))
            ->orderByDesc('follower_count')
            ->paginate($perPage)
            ->withQueryString();

        $creatorIds = $paginator->getCollection()
            ->pluck('creator_id')
            ->unique()
            ->values()
            ->all();

        $reviewsByCreator = Review::query()
            ->whereIn('creator_id', $creatorIds)
            ->selectRaw('creator_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('creator_id')
            ->get()
            ->keyBy('creator_id');

        $influencers = $paginator->getCollection()
            ->map(function (CreatorPlatformStat $stat) use ($reviewsByCreator): ?array {
                $creator = $stat->creator;

                if (!$creator || !$creator->user) {
                    return null;
                }

                $platformKey  = $this->normalizePlatformKey((string) $stat->platform);
                $platformMeta = $this->platformMeta($platformKey);

                $reviewSummary = $reviewsByCreator->get($creator->id);
                $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
                ? (float) $reviewSummary->average_rating
                : null;

                return [
                    'id'               => $creator->id,
                    'slug'             => $creator->user->slug,
                    'name'             => $this->resolveCreatorName($creator),
                    'title'            => $this->resolveCreatorTitle($creator),
                    'location'         => $this->resolveCreatorLocation($creator),
                    'image_url'        => image_url($creator->user->profile_image_path),
                    'platform'         => $platformKey,
                    'platform_label'   => $platformMeta['label'],
                    'platform_slug'    => $platformMeta['slug'],
                    'handle'           => $this->resolveHandle($stat->handle),
                    'followers_label'  => $this->formatFollowers($stat->follower_count),
                    'engagement_label' => $this->formatPercentage($stat->engagement_rate),
                    'rating_label'     => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
                    'reviews_count'    => $reviewSummary ? (int) $reviewSummary->reviews_count : 0
                ];
            })
            ->filter()
            ->values();

        $paginator->setCollection($influencers);

        return $paginator;
    }

    /**
     * Normalize a Creator model (with eager-loaded platformStats + user) to a card array.
     * Used by both getFeaturedInfluencers and paginateFeaturedInfluencers.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>        $reviewsByCreator
     * @return array<string,                       mixed>|null
     */
    private function normalizeCreatorCard(Creator $creator, \Illuminate\Support\Collection $reviewsByCreator): ?array
    {
        if (!$creator->user) {
            return null;
        }

        $stat        = $creator->platformStats->first();
        $platformKey = $stat !== null
        ? $this->normalizePlatformKey((string) $stat->platform)
        : 'other';
        $platformMeta = $this->platformMeta($platformKey);

        $reviewSummary = $reviewsByCreator->get($creator->id);
        $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
        ? (float) $reviewSummary->average_rating
        : null;

        return [
            'id'               => $creator->id,
            'slug'             => $creator->user->slug,
            'name'             => $this->resolveCreatorName($creator),
            'title'            => $this->resolveCreatorTitle($creator),
            'location'         => $this->resolveCreatorLocation($creator),
            'image_url'        => image_url($creator->user->profile_image_path),
            'platform'         => $platformKey,
            'platform_label'   => $platformMeta['label'],
            'platform_slug'    => $platformMeta['slug'],
            'handle'           => $this->resolveHandle($stat?->handle),
            'followers_label'  => $this->formatFollowers($stat?->follower_count),
            'engagement_label' => $this->formatPercentage($stat?->engagement_rate),
            'rating_label'     => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
            'reviews_count'    => $reviewSummary ? (int) $reviewSummary->reviews_count : 0
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
        $configured    = self::PLATFORM_CONFIG[$normalizedKey] ?? null;

        if ($configured !== null) {
            return [
                'key'   => $normalizedKey,
                'label' => $configured['label'],
                'slug'  => $configured['slug']
            ];
        }

        $label = Str::headline($normalizedKey);

        return [
            'key'   => $normalizedKey,
            'label' => $label,
            'slug'  => Str::slug($label)
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
     * Resolve creator display name.
     */
    private function resolveCreatorName(Creator $creator): string
    {
        $displayName = trim((string) ($creator->display_name ?? ''));

        if ($displayName !== '') {
            return $displayName;
        }

        return trim((string) $creator->user?->name) !== ''
        ? (string) $creator->user?->name
        : 'Creator';
    }

    /**
     * Resolve creator title text for cards.
     */
    private function resolveCreatorTitle(Creator $creator): string
    {
        $title = trim((string) ($creator->title_name ?? ''));
        if ($title !== '') {
            return $title;
        }

        $bio = trim((string) ($creator->user?->bio ?? ''));
        if ($bio !== '') {
            return Str::limit($bio, 56);
        }

        return 'Content Creator';
    }

    /**
     * Resolve creator location text.
     */
    private function resolveCreatorLocation(Creator $creator): string
    {
        $parts = array_values(array_filter([
            trim((string) ($creator->user?->city ?? '')),
            trim((string) ($creator->user?->country ?? ''))
        ]));

        return $parts !== [] ? implode(', ', $parts) : 'Location not provided';
    }

    /**
     * Normalize social handle output.
     */
    private function resolveHandle(?string $handle): string
    {
        $normalized = trim((string) $handle);

        if ($normalized === '') {
            return '@creator';
        }

        return str_starts_with($normalized, '@') ? $normalized : '@' . $normalized;
    }

    /**
     * Format follower count for compact display.
     */
    private function formatFollowers(?int $count): string
    {
        $value = max(0, (int) $count);

        if ($value >= 1000000) {
            return number_format($value / 1000000, 1) . 'M';
        }

        if ($value >= 1000) {
            return number_format($value / 1000, 1) . 'K';
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

        return number_format((float) $value, 1) . '%';
    }
}
