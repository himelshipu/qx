<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Influencer;
use App\Models\InfluencerPortfolio;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class InfluencerProfileController extends Controller
{
    private function getDashboardInfluencerBySlug(string $slug): ?Influencer
    {
        $user = Auth::user();

        if (!$user || $user->slug !== $slug) {
            return null;
        }

        return $user->influencer;
    }

    /**
     * Show public influencer profile
     */
    public function show(string $slug)
    {
        $influencer = Influencer::query()
            ->with([
                'user',
                'socialLinks',
                'categories:id,name',
                'platformStats' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('follower_count'),
                'badges'        => fn($query)        => $query
                    ->wherePivot('is_active', true)
                    ->select('badge_definitions.id', 'badge_definitions.code', 'badge_definitions.name', 'badge_definitions.description')
            ])
            ->whereHas('user', function ($query) use ($slug): void {
                $query->where('slug', $slug)->where('user_type', 'influencer');
            })
            ->firstOrFail();

        $portfolioBaseQuery = InfluencerPortfolio::query()
            ->where('influencer_id', $influencer->id)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $portfolioMedia = (clone $portfolioBaseQuery)
            ->get(['id', 'media_type', 'file_path', 'title', 'description', 'created_at']);

        $portfolioTotalCount = $portfolioMedia->count();

        $portfolioMediaItems = $portfolioMedia
            ->map(fn(InfluencerPortfolio $portfolioItem): array => $this->toPortfolioMediaCard($portfolioItem))
            ->values();

        $portfolioTopImageIds = $portfolioMediaItems
            ->where('media_type', 'image')
            ->take(3)
            ->pluck('id')
            ->values();

        $portfolioTopImages = $portfolioMediaItems
            ->whereIn('id', $portfolioTopImageIds)
            ->values();

        $portfolioRemainingMedia = $portfolioMediaItems
            ->reject(fn(array $portfolioItem): bool => $portfolioTopImageIds->contains($portfolioItem['id']))
            ->values();

        $reviewsBaseQuery = Review::query()
            ->where('influencer_id', $influencer->id)
            ->where('reviewee_type', 'influencer')
            ->where('is_public', true);

        $reviewSummary = (clone $reviewsBaseQuery)
            ->selectRaw('COUNT(*) as total_reviews, AVG(rating) as avg_rating')
            ->first();

        $reviewDistribution = (clone $reviewsBaseQuery)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $reviewsPage = (clone $reviewsBaseQuery)
            ->with([
                'brand:id,brand_name,user_id',
                'brand.user:id,slug',
                'orderItem:id,order_id,title,package_id',
                'orderItem.order:id,order_number',
                'orderItem.package:id,name',
                'subOrder:id,order_id',
                'subOrder.order:id,order_number,campaign_id',
                'subOrder.order.campaign:id,title'
            ])
            ->orderByDesc('created_at')
            ->simplePaginate(8, ['id', 'order_item_id', 'sub_order_id', 'brand_id', 'rating', 'title', 'comment', 'created_at'], 'reviews_page')
            ->withQueryString();

        $packages = Package::query()
            ->where('influencer_id', $influencer->id)
            ->where('is_active', true)
            ->orderBy('platform')
            ->orderBy('name')
            ->get([
                'id',
                'platform',
                'name',
                'description',
                'base_price',
                'currency'
            ]);

        $influencerCategoryIds = $influencer->categories->pluck('id')->filter()->values();

        $similarInfluencersQuery = Influencer::query()
            ->with([
                'user:id,name,slug,profile_image_path,city,country',
                'platformStats' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('follower_count'),
                'badges' => fn($query) => $query
                    ->wherePivot('is_active', true)
                    ->select('badge_definitions.id', 'badge_definitions.code')
            ])
            ->where('id', '!=', $influencer->id)
            ->where('is_active', true)
            ->whereHas('user', function ($query): void {
                $query->where('user_type', 'influencer');
            })
            ->when($influencerCategoryIds->isNotEmpty(), function ($query) use ($influencerCategoryIds): void {
                $query->whereHas('categories', function ($categoryQuery) use ($influencerCategoryIds): void {
                    $categoryQuery->whereIn('categories.id', $influencerCategoryIds->all());
                });
            })
            ->limit(7);

        $similarInfluencers = $similarInfluencersQuery->get();

        if ($similarInfluencers->count() < 7) {
            $excludeIds = $similarInfluencers->pluck('id')->push($influencer->id)->values();

            $fallbackInfluencers = Influencer::query()
                ->with([
                    'user:id,name,slug,profile_image_path,city,country',
                    'platformStats' => fn($query) => $query
                        ->where('is_active', true)
                        ->orderByDesc('follower_count'),
                    'badges' => fn($query) => $query
                        ->wherePivot('is_active', true)
                        ->select('badge_definitions.id', 'badge_definitions.code')
                ])
                ->whereNotIn('id', $excludeIds->all())
                ->where('is_active', true)
                ->whereHas('user', function ($query): void {
                    $query->where('user_type', 'influencer');
                })
                ->limit(7 - $similarInfluencers->count())
                ->get();

            $similarInfluencers = $similarInfluencers->concat($fallbackInfluencers)->take(7)->values();
        }

        $similarInfluencerReviewStats = Review::query()
            ->whereIn('influencer_id', $similarInfluencers->pluck('id')->all())
            ->where('reviewee_type', 'influencer')
            ->where('is_public', true)
            ->selectRaw('influencer_id, COUNT(*) as total_reviews, AVG(rating) as avg_rating')
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $similarInfluencerCards = $similarInfluencers
            ->map(fn(Influencer $candidate): array => $this->toSimilarInfluencerCard($candidate, $similarInfluencerReviewStats))
            ->values();

        $similarInfluencerRegionLabel = trim((string) ($influencer->display_name ?? '')) !== ''
            ? (string) $influencer->display_name
            : (string) ($influencer->user->name ?? 'this creator');

        return view('frontend.pages.influencer-profile', [
            'influencer'           => $influencer,
            'packages'             => $packages,
            'portfolioTopImages'   => $portfolioTopImages,
            'portfolioRemainingMedia' => $portfolioRemainingMedia,
            'portfolioMediaItems'  => $portfolioMediaItems,
            'portfolioTotalCount'  => $portfolioTotalCount,
            'reviewsPage'          => $reviewsPage,
            'reviewsTotalCount'    => (int) ($reviewSummary?->total_reviews ?? 0),
            'reviewsAverageRating' => $reviewSummary?->avg_rating !== null ? round((float) $reviewSummary->avg_rating, 1) : null,
            'reviewDistribution'   => $reviewDistribution,
            'similarInfluencers'   => $similarInfluencerCards,
            'similarRegionLabel'   => $similarInfluencerRegionLabel,
            'title'                => $influencer->user->name . ' — Influencer'
        ]);
    }

    private function toPortfolioMediaCard(InfluencerPortfolio $portfolio): array
    {
        $mediaUrl = ImageHelper::url($portfolio->file_path);

        return [
            'id' => (int) $portfolio->id,
            'media_type' => (string) $portfolio->media_type,
            'url' => $mediaUrl,
            'poster_url' => null,
            'title' => trim((string) ($portfolio->title ?? '')),
            'description' => trim((string) ($portfolio->description ?? '')),
        ];
    }

    /**
     * Build similar influencer card payload for profile page carousel.
     *
     * @param  Collection<int, mixed>  $reviewStatsByInfluencer
     */
    private function toSimilarInfluencerCard(Influencer $influencer, Collection $reviewStatsByInfluencer): array
    {
        $topStat = $influencer->platformStats
            ->sortByDesc(fn($stat) => (int) ($stat->follower_count ?? 0))
            ->first();

        $platformKey = strtolower(trim((string) ($topStat?->platform ?? 'other')));

        $platformLabel = match ($platformKey) {
            'ugc' => 'UGC',
            'x' => 'X',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            default => ucfirst($platformKey),
        };

        $reviews = $reviewStatsByInfluencer->get($influencer->id);
        $avgRating = $reviews?->avg_rating !== null ? (float) $reviews->avg_rating : 0.0;

        $displayName = trim((string) ($influencer->display_name ?? '')) !== ''
            ? (string) $influencer->display_name
            : (string) ($influencer->user?->name ?? 'Influencer');

        $locationParts = array_values(array_filter([
            trim((string) ($influencer->user?->city ?? '')),
            trim((string) ($influencer->user?->country ?? '')),
        ]));

        $badges = $influencer->badges->keyBy('code');

        return [
            'id' => (int) $influencer->id,
            'slug' => (string) ($influencer->user?->slug ?? ''),
            'name' => $displayName,
            'title' => trim((string) ($influencer->title_name ?? '')),
            'image_url' => $influencer->user?->profile_image_path,
            'location' => $locationParts !== [] ? implode(', ', $locationParts) : 'N/A',
            'platform' => $platformKey,
            'platform_label' => $platformLabel,
            'followers_label' => $this->formatFollowers((int) ($topStat?->follower_count ?? 0)),
            'rating_label' => number_format($avgRating, 1),
            'has_top_influencer' => $badges->has('top_influencer'),
            'has_responses_fast' => $badges->has('responds_fast'),
        ];
    }

    private function formatFollowers(?int $count): string
    {
        $followers = max(0, (int) $count);

        if ($followers >= 1000000) {
            return number_format($followers / 1000000, 1) . 'M';
        }

        if ($followers >= 1000) {
            return number_format($followers / 1000, 1) . 'K';
        }

        return (string) $followers;
    }

    /**
     * Show influencer profile edit form
     */
    public function edit(string $slug)
    {
        $user       = Auth::user();
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            abort(404);
        }

        $influencer->load(['user', 'socialLinks', 'portfolios']);

        $portfolioMediaItems = InfluencerPortfolio::query()
            ->where('influencer_id', $influencer->id)
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['id', 'media_type', 'file_path', 'title', 'description', 'created_at'])
            ->map(fn(InfluencerPortfolio $portfolioItem): array => $this->toPortfolioMediaCard($portfolioItem))
            ->values();

        return view('frontend.pages.influencer-edit-profile', [
            'user'                     => $influencer->user,
            'influencer'               => $influencer,
            'brand'                    => $influencer,
            'slug'                     => $slug,
            'portfolioMediaItems'      => $portfolioMediaItems,
            'portfolioPreviewMedia'    => $portfolioMediaItems->take(5)->values(),
            'portfolioOverflowCount'   => max($portfolioMediaItems->count() - 5, 0),
        ]);
    }

    /**
     * Update influencer profile information
     */
    public function update(Request $request, string $slug)
    {
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            return redirect()->route('influencer.profile.edit', ['slug' => Auth::user()->slug])->with('error', 'Influencer profile not found');
        }

        $user = $influencer->user;

        $activeTab = $request->string('active_tab')->toString() ?: 'details';

        $rulesByTab = [
            'details' => [
                'display_name'       => ['nullable', 'string', 'max:255'],
                'title_name'         => ['nullable', 'string', 'max:255'],
                'audience'           => ['nullable', 'string'],
                'brands_worked_with' => ['nullable', 'string'],
                'gender'             => ['nullable', 'string', 'in:male,female,other'],
                'date_of_birth'      => ['nullable', 'date_format:Y-m-d'],
                'phone'              => ['nullable', 'string', 'max:20'],
                'address_line'       => ['nullable', 'string', 'max:255'],
                'city'               => ['nullable', 'string', 'max:255'],
                'country'            => ['nullable', 'string', 'max:255'],
                'postal_code'        => ['nullable', 'string', 'max:20'],
                'bio'                => ['nullable', 'string', 'max:500']
            ],
            'social'  => [
                'instagram_url' => ['nullable', 'url', 'max:255'],
                'tiktok_url'    => ['nullable', 'url', 'max:255'],
                'facebook_url'  => ['nullable', 'url', 'max:255'],
                'x_url'         => ['nullable', 'url', 'max:255'],
                'youtube_url'   => ['nullable', 'url', 'max:255'],
                'linkedin_url'  => ['nullable', 'url', 'max:255'],
                'other_url'     => ['nullable', 'url', 'max:255']
            ],
            'images'  => [
                'profile_image'      => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
                'cover_image'        => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:4096'],
                'portfolio_images'   => ['nullable', 'array'],
                    'portfolio_images.*' => ['nullable', 'file', 'mimes:jpeg,png,webp,jpg,mp4,mov,webm,avi', 'max:51200']
            ]
        ];

        if (!array_key_exists($activeTab, $rulesByTab)) {
            $activeTab = 'details';
        }

        $validated = $request->validate($rulesByTab[$activeTab]);

        if ($activeTab === 'details') {
            $influencer->display_name       = $validated['display_name'] ?? $influencer->display_name;
            $influencer->title_name         = $validated['title_name'] ?? $influencer->title_name;
            $influencer->audience           = $validated['audience'] ?? $influencer->audience;
            $influencer->brands_worked_with = $validated['brands_worked_with'] ?? $influencer->brands_worked_with;
            $influencer->save();

            $user->city         = $validated['city'] ?? $user->city;
            $user->country      = $validated['country'] ?? $user->country;
            $user->postal_code  = $validated['postal_code'] ?? $user->postal_code;
            $user->bio          = $validated['bio'] ?? $user->bio;
            $user->phone        = $validated['phone'] ?? $user->phone;
            $user->address_line = $validated['address_line'] ?? $user->address_line;
            $user->gender       = $validated['gender'] ?? $user->gender;
            if (array_key_exists('date_of_birth', $validated)) {
                $user->date_of_birth = $validated['date_of_birth'];
            }
            $user->save();
        }

        if ($activeTab === 'social' && Schema::hasTable('influencer_social_links')) {
            $influencer->socialLinks()->updateOrCreate(
                ['influencer_id' => $influencer->id],
                [
                    'instagram_url' => $validated['instagram_url'] ?? null,
                    'tiktok_url'    => $validated['tiktok_url'] ?? null,
                    'facebook_url'  => $validated['facebook_url'] ?? null,
                    'x_url'         => $validated['x_url'] ?? null,
                    'youtube_url'   => $validated['youtube_url'] ?? null,
                    'linkedin_url'  => $validated['linkedin_url'] ?? null,
                    'other_url'     => $validated['other_url'] ?? null
                ]
            );
        }

        if ($activeTab === 'images') {
            if ($request->hasFile('profile_image')) {
                try {
                    if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
                        Storage::disk('public')->delete($user->profile_image_path);
                    }
                    $path                     = $request->file('profile_image')->store('creators/profile', 'public');
                    $user->profile_image_path = $path;
                    $saved                    = $user->save();
                } catch (\Exception $e) {
                    Log::error('Profile image upload error: ' . $e->getMessage());

                    return redirect()->back()->with('error', 'Failed to upload profile image')->with('active_tab', $activeTab);
                }
            }

            if ($request->hasFile('cover_image')) {
                try {
                    if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
                        Storage::disk('public')->delete($user->cover_image_path);
                    }
                    $path                   = $request->file('cover_image')->store('creators/cover', 'public');
                    $user->cover_image_path = $path;
                    $saved                  = $user->save();
                } catch (\Exception $e) {
                    Log::error('Cover image upload error: ' . $e->getMessage());

                    return redirect()->back()->with('error', 'Failed to upload cover image')->with('active_tab', $activeTab);
                }
            }

            if ($request->hasFile('portfolio_images')) {
                try {
                    foreach ($request->file('portfolio_images', []) as $portfolioImage) {
                        $path = $portfolioImage->store('creators/portfolio', 'public');
                            $mimeType = (string) $portfolioImage->getMimeType();
                            $mediaType = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                        $influencer->portfolios()->create([
                                'media_type' => $mediaType,
                            'file_path'  => $path,
                                'title'      => $mediaType === 'video'
                                    ? 'Portfolio Video ' . ($influencer->portfolios()->max('sort_order') + 1)
                                    : 'Portfolio Image ' . ($influencer->portfolios()->max('sort_order') + 1),
                            'sort_order' => $influencer->portfolios()->max('sort_order') + 1,
                            'is_active'  => true
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Portfolio image upload error: ' . $e->getMessage());

                    return redirect()->back()->with('error', 'Failed to upload portfolio images')->with('active_tab', $activeTab);
                }
            }
        }

        $messages = [
            'details' => 'Profile details updated successfully.',
            'social'  => 'Social links updated successfully.',
            'images'  => 'Profile images updated successfully.'
        ];

        $message = $messages[$activeTab] ?? 'Profile updated successfully.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message'       => $message,
                'active_tab'    => $activeTab,
                'profile_image' => ImageHelper::url($user->profile_image_path),
                'cover_image'   => ImageHelper::url($user->cover_image_path)
            ]);
        }

        return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('success', $message)->with('active_tab', $activeTab);
    }

    /**
     * Delete influencer profile image
     */
    public function deleteProfileImage(Request $request, string $slug)
    {
        $user       = Auth::user();
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            return response()->json(['error' => 'Influencer not found'], 404);
        }

        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        $user->update(['profile_image_path' => null]);

        return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('status', 'profile-image-deleted');
    }

    /**
     * Delete influencer cover image (kept for backward compatibility)
     */
    public function deleteCoverImage(Request $request, string $slug)
    {
        $user       = Auth::user();
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            return response()->json(['error' => 'Influencer not found'], 404);
        }

        // Delete cover image if exists
        if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
            Storage::disk('public')->delete($user->cover_image_path);
        }

        $user->update(['cover_image_path' => null]);

        return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('status', 'cover-image-deleted');
    }

    /**
     * Delete influencer portfolio image
     */
    public function deletePortfolioImage(Request $request, string $slug, int $portfolio)
    {
        $user       = Auth::user();
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('error', 'Influencer not found');
        }

        $portfolioItem = InfluencerPortfolio::where('influencer_id', $influencer->id)->where('id', $portfolio)->first();

        if (!$portfolioItem) {
            return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('error', 'Portfolio item not found');
        }

        // Delete file from storage
        if ($portfolioItem->file_path && Storage::disk('public')->exists($portfolioItem->file_path)) {
            Storage::disk('public')->delete($portfolioItem->file_path);
        }

        // Delete portfolio record
        $portfolioItem->delete();

        return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with('success', 'Portfolio image deleted successfully.');
    }

    public function toggleStatus(Request $request, string $slug)
    {
        $user       = Auth::user();
        $influencer = $this->getDashboardInfluencerBySlug($slug);

        if (!$influencer) {
            return response()->json(['error' => 'Influencer not found'], 404);
        }

        $influencer->update(['is_active' => !$influencer->is_active]);

        return redirect()->route('influencer.profile.edit', ['slug' => $slug])->with(
            'status',
            $influencer->is_active ? 'influencer-activated' : 'influencer-deactivated'
        );
    }
}
