<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreatorProfileController extends Controller
{
    /**
     * Show public creator profile
     */
    public function show($id)
    {
        $creator = Creator::with('user')->findOrFail($id);

        return view('frontend.pages.creator-profile', [
            'creator' => $creator,
            'title' => $creator->user->name . ' — Creator',
        ]);
    }

    /**
     * Show creator profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $creator = $user->creator;

        // Some views expect $brand variable; to minimize view changes we'll pass creator as brand when needed
        return view('frontend.pages.creator-edit-profile', [
            'user' => $user,
            'creator' => $creator,
            'brand' => $creator, // compatibility for fields referenced as $brand in the blade
        ]);
    }

    /**
     * Update creator profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return redirect()->route('dashboard.creator.profile.edit')->with('error', 'Creator profile not found');
        }

        $validated = $request->validate([
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        // Map validated fields to creator model where appropriate
        if (array_key_exists('display_name', $validated)) {
            $creator->display_name = $validated['display_name'];
        }
        $creator->description = $validated['description'] ?? $creator->description ?? null;
        $creator->location = $validated['location'] ?? $creator->location ?? null;
        $creator->city = $validated['city'] ?? $creator->city ?? null;
        $creator->country = $validated['country'] ?? $creator->country ?? null;
        $creator->postal_code = $validated['postal_code'] ?? $creator->postal_code ?? null;

        $creator->social_links = [
            'website' => $validated['website'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'twitter' => $validated['twitter'] ?? null,
        ];

        if ($request->hasFile('profile_image')) {
            if ($creator->profile_image_path && Storage::disk('public')->exists($creator->profile_image_path)) {
                Storage::disk('public')->delete($creator->profile_image_path);
            }
            $path = $request->file('profile_image')->store('creators/profile', 'public');
            $creator->profile_image_path = $path;
        }

        if ($request->hasFile('cover_image')) {
            if ($creator->cover_image_path && Storage::disk('public')->exists($creator->cover_image_path)) {
                Storage::disk('public')->delete($creator->cover_image_path);
            }
            $path = $request->file('cover_image')->store('creators/cover', 'public');
            $creator->cover_image_path = $path;
        }

        $creator->save();

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'profile-updated');
    }

    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        if ($creator->profile_image_path && Storage::disk('public')->exists($creator->profile_image_path)) {
            Storage::disk('public')->delete($creator->profile_image_path);
        }

        $creator->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'profile-image-deleted');
    }

    public function deleteCoverImage(Request $request)
    {
        $user = Auth::user();
        $creator = $user->creator;

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        if ($creator->cover_image_path && Storage::disk('public')->exists($creator->cover_image_path)) {
            Storage::disk('public')->delete($creator->cover_image_path);
        }

        $creator->update(['cover_image_path' => null]);

        return redirect()->route('dashboard.creator.profile.edit')->with('status', 'cover-image-deleted');
    }

    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
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
