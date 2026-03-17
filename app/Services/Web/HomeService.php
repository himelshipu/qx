<?php

declare (strict_types = 1);

namespace App\Services\Web;

use App\DTOs\HomeDataDTO;
use App\Models\Creator;
use App\Models\CreatorPlatformStat;
use App\Models\FaqItem;
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
     * @return Collection<int, array{key:string,label:string,creators:Collection<int, array<string, mixed>>}>
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

        $stats = CreatorPlatformStat::query()
            ->with([
                'creator:id,user_id,display_name,title_name,description,location,city,country,profile_image_path,is_active',
                'creator.user:id,name,slug,is_active'
            ])
            ->where('is_active', true)
            ->whereHas('creator', function ($query) {
                $query->where('is_active', true)
                    ->whereHas('user', fn($userQuery) => $userQuery->where('is_active', true));
            })
            ->orderByDesc('follower_count')
            ->get();

        $creatorIds = $stats->pluck('creator_id')->unique()->values()->all();

        $reviewsByCreator = Review::query()
            ->whereIn('creator_id', $creatorIds)
            ->selectRaw('creator_id, AVG(rating) as average_rating, COUNT(*) as reviews_count')
            ->groupBy('creator_id')
            ->get()
            ->keyBy('creator_id');

        $statsByPlatform = $stats->groupBy('platform');

        $orderedPlatforms = collect($platformPriority)
            ->merge($statsByPlatform->keys())
            ->unique()
            ->values();

        return $orderedPlatforms
            ->map(function (string $platform) use ($statsByPlatform, $limitPerPlatform, $reviewsByCreator): array {
                $platformStats = $statsByPlatform->get($platform, collect())
                    ->take($limitPerPlatform)
                    ->values();

                $creators = $platformStats
                    ->map(function (CreatorPlatformStat $stat) use ($reviewsByCreator, $platform): ?array {
                        $creator = $stat->creator;

                        if (!$creator || !$creator->user) {
                            return null;
                        }

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
                            'image_url'        => $this->resolveCreatorImageUrl($creator->profile_image_path),
                            'platform'         => $platform,
                            'platform_label'   => $this->humanizePlatform($platform),
                            'handle'           => $this->resolveHandle($stat->handle),
                            'followers_label'  => $this->formatFollowers($stat->follower_count),
                            'engagement_label' => $this->formatPercentage($stat->engagement_rate),
                            'rating_label'     => $averageRating !== null ? number_format($averageRating, 1) : 'N/A',
                            'reviews_count'    => $reviewSummary ? (int) $reviewSummary->reviews_count : 0
                        ];
                    })
                    ->filter()
                    ->values();

                return [
                    'key'      => $platform,
                    'label'    => $this->humanizePlatform($platform),
                    'creators' => $creators
                ];
            })
            ->filter(fn(array $group): bool =>
                $group['creators']->isNotEmpty() || in_array($group['key'], $mandatoryPlatforms, true)
            )
            ->values();
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

        $description = trim((string) ($creator->description ?? ''));
        if ($description !== '') {
            return Str::limit($description, 56);
        }

        return 'Content Creator';
    }

    /**
     * Resolve creator location text.
     */
    private function resolveCreatorLocation(Creator $creator): string
    {
        $location = trim((string) ($creator->location ?? ''));
        if ($location !== '') {
            return $location;
        }

        $parts = array_values(array_filter([
            trim((string) ($creator->city ?? '')),
            trim((string) ($creator->country ?? ''))
        ]));

        return $parts !== [] ? implode(', ', $parts) : 'Location not provided';
    }

    /**
     * Resolve creator profile image URL with storage fallbacks.
     */
    private function resolveCreatorImageUrl(?string $path): string
    {
        return image_url($path);
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

    /**
     * Get active FAQ items for the homepage (audience: all or creator).
     *
     * @return Collection<int, FaqItem>
     */
    public function getHomeFaqItems(): Collection
    {
        return FaqItem::query()
            ->where('is_active', true)
            ->whereHas('section', fn($q) => $q->where('is_active', true)
                    ->whereIn('audience_type', ['all', 'creator']))
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
