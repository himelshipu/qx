<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BrandProfileController extends Controller
{
    private function getDashboardBrandBySlug(string $slug): ?Brand
    {
        $user = Auth::user();

        if (!$user || $user->slug !== $slug) {
            return null;
        }

        return $user->brand;
    }

    /**
     * Show brand profile edit form
     */
    public function edit(string $slug)
    {
        $user = Auth::user();
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            abort(404);
        }

        return view('frontend.pages.brand-edit-profile', [
            'user' => $user,
            'brand' => $brand,
            'slug' => $slug,
        ]);
    }

    /**
     * Update brand profile information
     */
    public function update(Request $request, string $slug)
    {
        $user = Auth::user();
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return redirect()->route('dashboard.index')->with('error', 'Brand not found');
        }

        $activeTab = $request->string('active_tab')->toString();
        $rulesByTab = [
            'details' => [
                'brand_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'industry' => 'nullable|string|max:150',
                'bio' => 'nullable|string|max:500',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|max:20',
            ],
            'social' => [
                'website' => 'nullable|url|max:255',
                'instagram' => 'nullable|url|max:255',
                'tiktok' => 'nullable|url|max:255',
                'facebook' => 'nullable|url|max:255',
                'x' => 'nullable|url|max:255',
                'youtube' => 'nullable|url|max:255',
                'linkedin' => 'nullable|url|max:255',
            ],
            'images' => [
                'profile_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
                'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
            ],
        ];

        if (!array_key_exists($activeTab, $rulesByTab)) {
            $activeTab = 'details';
        }

        $validated = $request->validate($rulesByTab[$activeTab]);

        // Update brand details tab
        if ($activeTab === 'details') {
            // Update brand fields
            $brand->brand_name = $validated['brand_name'];
            $brand->description = $validated['description'] ?? null;
            $brand->industry = $validated['industry'] ?? null;
            $brand->save();

            // Update user fields (city, country, postal_code, phone, bio)
            $user->city = $validated['city'] ?? null;
            $user->country = $validated['country'] ?? null;
            $user->postal_code = $validated['postal_code'] ?? null;
            $user->phone = $validated['phone'] ?? null;
            $user->bio = $validated['bio'] ?? null;
            $user->save();
        }

        // Update social tab
        if ($activeTab === 'social') {
            // Update website on brand
            if (array_key_exists('website', $validated)) {
                $brand->website = $validated['website'] ?? null;
                $brand->save();
            }

            // Update social links via brand_social_links table
            $brand->socialLinks()->updateOrCreate(
                ['brand_id' => $brand->id],
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

        // Handle profile image upload (store on user)
        if ($activeTab === 'images' && $request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
                Storage::disk('public')->delete($user->profile_image_path);
            }

            $path = $request->file('profile_image')->store('users/profile', 'public');
            $user->profile_image_path = $path;
            $user->save();
        }

        // Handle cover image upload (store on user)
        if ($activeTab === 'images' && $request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
                Storage::disk('public')->delete($user->cover_image_path);
            }

            $path = $request->file('cover_image')->store('users/cover', 'public');
            $user->cover_image_path = $path;
            $user->save();
        }

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete profile image
     */
    public function deleteProfileImage(Request $request, string $slug)
    {
        $user = Auth::user();
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        // Delete profile image from storage
        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        // Clear the profile_image_path from user
        $user->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', 'Profile image removed.');
    }

    /**
     * Delete cover image
     */
    public function deleteCoverImage(Request $request, string $slug)
    {
        $user = Auth::user();
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        // Delete cover image from storage
        if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
            Storage::disk('public')->delete($user->cover_image_path);
        }

        // Clear the cover_image_path from user
        $user->update(['cover_image_path' => null]);

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', 'Cover image removed.');
    }

    /**
     * Toggle brand verification status
     */
    public function toggleVerification(Request $request, string $slug)
    {
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $brand->update(['is_verified' => !$brand->is_verified]);

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with(
            'success',
            $brand->is_verified ? 'Brand marked as verified.' : 'Brand marked as unverified.'
        );
    }

    /**
     * Toggle brand active status
     */
    public function toggleStatus(Request $request, string $slug)
    {
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $brand->update(['is_active' => !$brand->is_active]);

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with(
            'success',
            $brand->is_active ? 'Brand activated.' : 'Brand deactivated.'
        );
    }

    /**
     * Show public brand profile
     */
    public function show(string $slug)
    {
        $brand = Brand::query()
            ->with([
                'user:id,name,slug,city,country,postal_code,phone,bio,profile_image_path,cover_image_path,is_active',
                'socialLinks:id,brand_id,instagram_url,tiktok_url,facebook_url,x_url,youtube_url,linkedin_url',
                'campaigns' => fn($query) => $query->where('is_active', true)->where('status', '!=', 'draft')->orderBy('published_at', 'desc')
            ])
            ->whereHas('user', fn($query) => $query->where('slug', $slug))
            ->firstOrFail();

        $isOwner = Auth::check() && Auth::user()->slug === $slug;
        if (!$isOwner && !$brand->user?->is_active) {
            abort(404);
        }

        return view('frontend.pages.brand-profile', [
            'brand' => $brand,
            'campaigns' => $brand->campaigns,
            'title' => $brand->brand_name ?? $brand->user->name,
        ]);
    }
}
