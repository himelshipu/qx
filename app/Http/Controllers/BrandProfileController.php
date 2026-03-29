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
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return redirect()->route('dashboard.index')->with('error', 'Brand not found');
        }

        $activeTab = $request->string('active_tab')->toString();
        $rulesByTab = [
            'details' => [
                'brand_name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'location' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'categories' => 'nullable|array',
                'categories.*' => 'string|max:100',
            ],
            'social' => [
                'website' => 'nullable|url|max:255',
                'instagram' => 'nullable|url|max:255',
                'tiktok' => 'nullable|url|max:255',
                'facebook' => 'nullable|url|max:255',
                'twitter' => 'nullable|url|max:255',
                'youtube' => 'nullable|url|max:255',
                'others' => 'nullable|url|max:255',
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
        $hasCategoriesColumn = Schema::hasColumn('brands', 'categories');
        $hasSocialLinksColumn = Schema::hasColumn('brands', 'social_links');
        $hasSetupDataColumn = Schema::hasColumn('brands', 'setup_data');

        if ($activeTab === 'details') {
            // Handle categories, including compatibility with JSON payloads.
            $categories = $request->input('categories', $brand->categories ?? []);
            if (is_string($categories)) {
                $categories = json_decode($categories, true) ?? [];
            }
            if (!is_array($categories)) {
                $categories = [];
            }

            $brand->brand_name = $validated['brand_name'];
            $brand->description = $validated['description'] ?? null;
            $brand->location = $validated['location'] ?? null;
            $brand->city = $validated['city'] ?? null;
            $brand->country = $validated['country'] ?? null;
            $brand->postal_code = $validated['postal_code'] ?? null;
            $brand->phone = $validated['phone'] ?? null;
            $brand->email = $validated['email'] ?? null;
            if ($hasCategoriesColumn) {
                $brand->categories = $categories;
            }

            if ($hasSetupDataColumn) {
                $setupData = is_array($brand->setup_data) ? $brand->setup_data : [];
                $setupData['location'] = $brand->location;
                $setupData['description'] = $brand->description;
                $setupData['categories'] = $categories;
                $brand->setup_data = $setupData;
            }
        }

        if ($activeTab === 'social') {
            if (array_key_exists('website', $validated)) {
                $brand->website = $validated['website'] ?? null;
            }

            if (Schema::hasTable('brand_social_links')) {
                $brand->socialLinks()->updateOrCreate(
                    ['brand_id' => $brand->id],
                    [
                        'instagram_url' => $validated['instagram'] ?? null,
                        'tiktok_url' => $validated['tiktok'] ?? null,
                        'facebook_url' => $validated['facebook'] ?? null,
                        'x_url' => $validated['twitter'] ?? null,
                        'youtube_url' => $validated['youtube'] ?? null,
                        'other_url' => $validated['others'] ?? null,
                    ]
                );
            }

            if ($hasSocialLinksColumn) {
                $socialLinks = is_array($brand->social_links) ? $brand->social_links : [];
                foreach (['website', 'instagram', 'tiktok', 'facebook', 'twitter', 'youtube', 'others'] as $socialKey) {
                    if (array_key_exists($socialKey, $validated)) {
                        $socialLinks[$socialKey] = $validated[$socialKey] ?? null;
                    }
                }
                $brand->social_links = $socialLinks;

                if ($hasSetupDataColumn) {
                    $setupData = is_array($brand->setup_data) ? $brand->setup_data : [];
                    $setupData['social'] = $socialLinks;
                    $brand->setup_data = $setupData;
                }
            }
        }

        // Handle profile image upload
        if ($activeTab === 'images' && $request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($brand->profile_image_path && Storage::disk('public')->exists($brand->profile_image_path)) {
                Storage::disk('public')->delete($brand->profile_image_path);
            }

            $path = $request->file('profile_image')->store('brands/profile', 'public');
            $brand->profile_image_path = $path;
        }

        // Handle cover image upload
        if ($activeTab === 'images' && $request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($brand->cover_image_path && Storage::disk('public')->exists($brand->cover_image_path)) {
                Storage::disk('public')->delete($brand->cover_image_path);
            }

            $path = $request->file('cover_image')->store('brands/cover', 'public');
            $brand->cover_image_path = $path;
        }

        if ($activeTab === 'images' && $hasSetupDataColumn) {
            $setupData = is_array($brand->setup_data) ? $brand->setup_data : [];
            $setupData['profile_image'] = $brand->profile_image_path;
            $setupData['cover_image'] = $brand->cover_image_path;
            $brand->setup_data = $setupData;
        }

        $brand->save();

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', 'Profile updated successfully.');
    }

    /**
     * Delete profile images
     */
    public function deleteProfileImage(Request $request, string $slug)
    {
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        if ($brand->profile_image_path && Storage::disk('public')->exists($brand->profile_image_path)) {
            Storage::disk('public')->delete($brand->profile_image_path);
        }

        $brand->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', 'Profile image removed.');
    }

    /**
     * Delete cover image
     */
    public function deleteCoverImage(Request $request, string $slug)
    {
        $brand = $this->getDashboardBrandBySlug($slug);

        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        if ($brand->cover_image_path && Storage::disk('public')->exists($brand->cover_image_path)) {
            Storage::disk('public')->delete($brand->cover_image_path);
        }

        $brand->update(['cover_image_path' => null]);

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
        $brand = Brand::with('user')
            ->whereHas('user', fn($query) => $query->where('slug', $slug))
            ->firstOrFail();

        return view('frontend.pages.brand-profile', [
            'brand' => $brand,
            'title' => $brand->brand_name ?? $brand->user->name,
        ]);
    }
}
