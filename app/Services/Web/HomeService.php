<?php

declare (strict_types = 1);

namespace App\Services\Web;

use App\DTOs\HomeDataDTO;
use App\Models\FaqItem;
use App\Models\Influencer;
use App\Models\InfluencerPlatformStat;
use App\Models\Review;
use App\Models\Testimonial;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class HomeService
 *
 * Handles business logic for the homepage.
 */
final class HomeService
{
    /**
     * Create a new service instance.
     *
     * @param  UserRepositoryInterface $userRepository
     * @return void
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get homepage data.
     *
     * @return HomeDataDTO
     */
    public function getHomePageData(): HomeDataDTO
    {
        $users = $this->getRecentUsers();

        return HomeDataDTO::fromUsersCollection($users);
    }

    /**
     * Get recent users from repository.
     *
     * @return Collection<int, \App\Models\User>
     */
    private function getRecentUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Get featured users for homepage.
     *
     * @param  int             $limit
     * @return Collection<int, \App\Models\User>
     */
    public function getFeaturedUsers(int $limit = 5): Collection
    {
        return $this->userRepository->getAll()->take($limit);
    }

    /**
     * Get influencers grouped by platform for homepage sections.
     *
     * @param  int             $limitPerPlatform
     * @return Collection<int, array{key:string,label:string,influencers:Collection<int, array<string, mixed>>
     */
    public function getInfluencersByPlatform(int $limitPerPlatform = 4): Collection
    {
        $platformPriority = [
            'facebook',
            'instagram',
            'ugc',
            'tiktok',
            'youtube',
            'linkedin',
            'x',
            'other'
        ];

        $mandatoryPlatforms = ['facebook', 'instagram', 'ugc'];

        $stats = InfluencerPlatformStat::query()
            ->with([
                'influencer:id,user_id,display_name,title_name,is_active',
                'influencer.user:id,name,slug,city,country,bio,profile_image_path,is_active'
            ])
            ->where('is_active', true)
            ->whereHas('influencer', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('user', fn($userQuery) => $userQuery->where('is_active', true));
            })
            ->orderByDesc('follower_count')
            ->get();

        $influencerIds = $stats->pluck('influencer_id')->unique()->values()->all();

        $reviewsByInfluencer = Review::query()
            ->whereIn('influencer_id', $influencerIds)
            ->selectRaw('influencer_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $statsByPlatform = $stats->groupBy('platform');

        $orderedPlatforms = collect($platformPriority)
            ->merge($statsByPlatform->keys())
            ->unique()
            ->values();

        return $orderedPlatforms
            ->map(function (string $platform) use ($statsByPlatform, $limitPerPlatform, $reviewsByInfluencer): array {
                $platformStats = $statsByPlatform->get($platform, collect())
                    ->take($limitPerPlatform)
                    ->values();

                $influencers = $platformStats
                    ->map(function (InfluencerPlatformStat $stat) use ($reviewsByInfluencer, $platform): ?array {
                        $influencer = $stat->influencer;

                        if (!$influencer || !$influencer->user) {
                            return null;
                        }

                        $reviewSummary = $reviewsByInfluencer->get($influencer->id);
                        $averageRating = $reviewSummary && $reviewSummary->average_rating !== null
                        ? (float) $reviewSummary->average_rating
                        : null;

                        return [
                            'id'               => $influencer->id,
                            'slug'             => $influencer->user->slug,
                            'name'             => $this->resolveInfluencerName($influencer),
                            'title'            => $this->resolveInfluencerTitle($influencer),
                            'location'         => $this->resolveInfluencerLocation($influencer),
                            'image_url'        => $influencer->user->profile_image_path,
                            'platform'         => $platform,
                            'platform_label'   => $this->humanizePlatform($platform),
                            'handle'           => $this->resolveHandle($stat->handle, $influencer->user->slug),
                            'followers_label'  => $this->formatFollowers($stat->follower_count),
                            'engagement_label' => $this->formatPercentage($stat->engagement_rate),
                            'rating_label'     => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
                            'reviews_count'    => $reviewSummary ? (int) $reviewSummary->reviews_count : 0
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'key'         => $platform,
                    'label'       => $this->humanizePlatform($platform),
                    'influencers' => $influencers
                ];
            })
            ->filter(fn(array $group): bool =>
                $group['influencers']->isNotEmpty() || in_array($group['key'], $mandatoryPlatforms, true)
            )
            ->values();
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
            trim((string) ($influencer->user?->country ?? ''))
        ]));

        return $parts !== [] ? implode(', ', $parts) : 'N/A';
    }

    /**
     * Format platform value for UI labels.
     */
    private function humanizePlatform(string $platform): string
    {
        return match ($platform) {
            'ugc'      => 'UGC',
            'x'        => 'X',
            'tiktok'   => 'TikTok',
            'youtube'  => 'YouTube',
            'linkedin' => 'LinkedIn',
            default    => Str::headline($platform)
        };
    }

    /**
     * Normalize social handle output.
     */
    private function resolveHandle(?string $handle, ?string $slug = null): string
    {
        $normalized = trim((string) $handle);

        if ($normalized !== '') {
            return str_starts_with($normalized, '@') ? $normalized : '@' . $normalized;
        }

        $normalizedSlug = trim((string) $slug);

        return $normalizedSlug !== '' ? '@' . ltrim($normalizedSlug, '@') : 'N/A';
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

    /**
     * Get active FAQ items for the homepage (audience: all or influencer).
     *
     * @return Collection<int, FaqItem>
     */
    public function getHomeFaqItems(): Collection
    {
        return FaqItem::query()
            ->where('is_active', true)
            ->whereHas('section', fn($q) => $q->where('is_active', true)
                    ->whereIn('audience_type', ['all', 'influencer']))
            ->orderBy('sort_order')
            ->get(['id', 'faq_section_id', 'question', 'answer', 'sort_order']);
    }

    /**
     * Get published testimonials for the homepage.
     *
     * @return Collection<int, Testimonial>
     */
    public function getTestimonials(): Collection
    {
        return Testimonial::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get(['id', 'author_name', 'author_role', 'company_name', 'quote', 'rating']);
    }
}
