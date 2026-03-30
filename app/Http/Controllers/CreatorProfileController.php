<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CreatorProfileController extends Controller
{
    /**
     * Show public creator profile
     */
    public function show(string $slug)
    {
        $creator = Creator::query()
            ->with([
                'user',
                'categories:id,name',
                'portfolios'    => fn($query)    => $query->where('is_active', true)->orderBy('sort_order'),
                'platformStats' => fn($query) => $query
                    ->where('is_active', true)
                    ->orderByDesc('follower_count')
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereHas('user', function ($query) use ($slug): void {
                $query->where('slug', $slug)->where('user_type', 'creator');
            })
            ->firstOrFail();

        $packages = Package::query()
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

        return view('frontend.pages.creator-profile', [
            'creator'  => $creator,
            'packages' => $packages,
            'title'    => $creator->user->name . ' — Creator'
        ]);
    }

    /**
     * Show creator profile edit form
     */
    public function edit()
    {
        $user    = Auth::user();
        $creator = $user->creator;

        // Some views expect $brand variable; to minimize view changes we'll pass creator as brand when needed

        return view('frontend.pages.creator-edit-profile', [
            'user'    => $user,
            'creator' => $creator,
            'brand'   => $creator // compatibility for fields referenced as $brand in the blade
        ]);
    }

    /**
     * Update creator profile information
     */
    public function update(Request $request)
    {
        $user    = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('dashboard.creator.profile.edit')->with('error', 'Creator profile not found');
        }

        $validated = $request->validate([
            'display_name'  => 'nullable|string|max:255',
            'title_name'    => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'audience'      => 'nullable|string',
            'brands_worked_with' => 'nullable|string',
            'city'          => 'nullable|string|max:255',
            'country'       => 'nullable|string|max:255',
            'postal_code'   => 'nullable|string|max:20',
            'bio'           => 'nullable|string|max:500',
            'phone'         => 'nullable|string|max:20',
            'website'       => 'nullable|url|max:255',
            'instagram'     => 'nullable|url|max:255',
            'tiktok'        => 'nullable|url|max:255',
            'facebook'      => 'nullable|url|max:255',
            'x'             => 'nullable|url|max:255',
            'youtube'       => 'nullable|url|max:255',
            'linkedin'      => 'nullable|url|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        // Update creator fields
        $creator->display_name = $validated['display_name'] ?? $creator->display_name;
        $creator->title_name = $validated['title_name'] ?? $creator->title_name;
        $creator->description = $validated['description'] ?? $creator->description;
        $creator->audience = $validated['audience'] ?? $creator->audience;
        $creator->brands_worked_with = $validated['brands_worked_with'] ?? $creator->brands_worked_with;
        $creator->save();

        // Update user fields
        $user->city = $validated['city'] ?? $user->city;
        $user->country = $validated['country'] ?? $user->country;
        $user->postal_code = $validated['postal_code'] ?? $user->postal_code;
        $user->bio = $validated['bio'] ?? $user->bio;
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();

        // Handle social links via creator_social_links table
        if (Schema::hasTable('creator_social_links')) {
            $creator->socialLinks()->updateOrCreate(
                ['creator_id' => $creator->id],
                [
                    'instagram_url' => $validated['instagram'] ?? null,
                    'tiktok_url' => $validated['tiktok'] ?? null,
                    'facebook_url' => $validated['facebook'] ?? null,
                    'x_url' => $validated['x'] ?? null,
                    'youtube_url' => $validated['youtube'] ?? null,
                    'linkedin_url' => $validated['linkedin'] ?? null,
                ]
            );
        }

        // Handle profile image upload (on user model)
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
                Storage::disk('public')->delete($user->profile_image_path);
            }
            $path = $request->file('profile_image')->store('users/profile', 'public');
            $user->profile_image_path = $path;
            $user->save();
        }

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete creator profile image
     */
    public function deleteProfileImage(Request $request)
    {
        $user    = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        $user->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'profile-image-deleted');
    }

    /**
     * Delete creator cover image (kept for backward compatibility)
     */
    public function deleteCoverImage(Request $request)
    {
        $user    = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'cover-image-message');
    }

    public function toggleStatus(Request $request)
    {
        $user    = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        $creator->update(['is_active' => !$creator->is_active]);

        return redirect()->route('dashboard.creator.profile.edit')->with(
            'status',
            $creator->is_active ? 'creator-activated' : 'creator-deactivated'
        );
    }
}
