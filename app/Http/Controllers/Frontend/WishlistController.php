<?php

declare (strict_types = 1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function page(Request $request): View
    {
        $wishlists = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->withCount('items')
            ->with(['items.influencer.user'])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(function (Wishlist $wishlist): array {
                return [
                    'id' => $wishlist->id,
                    'name' => $wishlist->name,
                    'is_default' => $wishlist->is_default,
                    'items_count' => $wishlist->items_count,
                    'items' => $wishlist->items->map(function ($item): array {
                        $influencer = $item->influencer;
                        $influencerUser = $influencer?->user;
                        $displayName = $influencer?->display_name ?: ($influencerUser?->name ?? 'N/A');
                        $titleName = $influencer?->title_name ?: 'Influencer';
                        $avatarPath = $influencerUser?->profile_image_path ?: ($influencer?->profile_image_path ?? '/default.webp');

                        return [
                            'id' => $item->id,
                            'influencer_id' => $influencer?->id,
                            'name' => $displayName,
                            'title' => $titleName,
                            'handle' => $influencerUser?->slug ? '@' . $influencerUser->slug : ($influencerUser?->name ? '@' . Str::slug($influencerUser->name) : 'N/A'),
                            'location' => trim((string) ($influencerUser?->city ?? '')) !== ''
                                ? trim((string) $influencerUser?->city)
                                : (trim((string) ($influencerUser?->country ?? '')) !== '' ? trim((string) $influencerUser?->country) : 'N/A'),
                            'image_url' => image_url($avatarPath),
                            'profile_url' => !empty($influencerUser?->slug)
                                ? route('influencer.profile', ['slug' => $influencerUser->slug])
                                : '#',
                            'audience' => $influencer?->audience ?: null,
                        ];
                    })->values(),
                ];
            })
            ->filter(fn (array $wishlist): bool => $wishlist['items_count'] > 0)
            ->values();

        return view('frontend.pages.wishlist', [
            'title' => 'Wishlist',
            'wishlists' => $wishlists,
        ]);
    }

    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'wishlisted_influencer_ids' => $this->wishlistedInfluencerIds($request->user()->id),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $influencerId = $request->integer('influencer_id');

        $wishlists = Wishlist::query()
            ->where('user_id', $request->user()->id)
            ->withCount('items')
            ->with(['items' => function ($query) use ($influencerId): void {
                $query->when($influencerId > 0, function ($itemQuery) use ($influencerId): void {
                    $itemQuery->where('influencer_id', $influencerId);
                });
            }])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get()
            ->map(function (Wishlist $wishlist) use ($influencerId): array {
                // Determine a preview image using the latest added item for the list
                $latestItem = $wishlist->items()->with('influencer.user')->orderByDesc('created_at')->first();

                $previewImage = null;
                if ($latestItem && ($influencer = $latestItem->influencer)) {
                    $influencerUser = $influencer->user ?? null;
                    $avatarPath = $influencerUser?->profile_image_path ?: ($influencer->profile_image_path ?? null);
                    if ($avatarPath) {
                        $previewImage = image_url($avatarPath);
                    }
                }

                return [
                    'id' => $wishlist->id,
                    'name' => $wishlist->name,
                    'is_default' => $wishlist->is_default,
                    'items_count' => $wishlist->items_count,
                    'contains_influencer' => $influencerId > 0 && $wishlist->items->isNotEmpty(),
                    'preview_image' => $previewImage,
                ];
            })
            ->values();

        return response()->json([
            'wishlists' => $wishlists,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'influencer_id' => ['nullable', 'integer', Rule::exists('influencers', 'id')],
        ]);

        $wishlist = Wishlist::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'is_default' => false,
        ]);

        if (!empty($validated['influencer_id'])) {
            $wishlist->items()->firstOrCreate([
                'influencer_id' => $validated['influencer_id'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wishlist created successfully.',
            'wishlist' => [
                'id' => $wishlist->id,
                'name' => $wishlist->name,
                'is_default' => $wishlist->is_default,
            ],
        ], 201);
    }

    public function storeItem(Request $request, Wishlist $wishlist): JsonResponse
    {
        $this->ensureOwnership($request, $wishlist);

        $validated = $request->validate([
            'influencer_id' => ['required', 'integer', Rule::exists('influencers', 'id')],
        ]);

        $wishlist->items()->firstOrCreate([
            'influencer_id' => $validated['influencer_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Influencer added to wishlist.',
        ]);
    }

    public function destroyItem(Request $request, Wishlist $wishlist, Influencer $influencer): JsonResponse
    {
        $this->ensureOwnership($request, $wishlist);

        $wishlist->items()->where('influencer_id', $influencer->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Influencer removed from wishlist.',
        ]);
    }

    public function destroyInfluencer(Request $request, Influencer $influencer): JsonResponse
    {
        $userId = $request->user()->id;

        Wishlist::query()
            ->where('user_id', $userId)
            ->whereHas('items', function ($query) use ($influencer): void {
                $query->where('influencer_id', $influencer->id);
            })
            ->get()
            ->each(function (Wishlist $wishlist) use ($influencer): void {
                $wishlist->items()->where('influencer_id', $influencer->id)->delete();
            });

        return response()->json([
            'success' => true,
            'message' => 'Influencer removed from wishlist.',
        ]);
    }

    private function wishlistedInfluencerIds(int $userId): array
    {
        return DB::table('wishlist_items')
            ->join('wishlists', 'wishlist_items.wishlist_id', '=', 'wishlists.id')
            ->where('wishlists.user_id', $userId)
            ->distinct()
            ->pluck('wishlist_items.influencer_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function ensureOwnership(Request $request, Wishlist $wishlist): void
    {
        abort_unless($wishlist->user_id === $request->user()->id, 403);
    }
}