<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if ($activeTab === 'social') {
            $request->merge([
                'website' => $this->normalizeUrl($request->input('website')),
                'instagram' => $this->normalizeUrl($request->input('instagram')),
                'tiktok' => $this->normalizeUrl($request->input('tiktok')),
                'facebook' => $this->normalizeUrl($request->input('facebook')),
                'x' => $this->normalizeUrl($request->input('x')),
                'youtube' => $this->normalizeUrl($request->input('youtube')),
                'linkedin' => $this->normalizeUrl($request->input('linkedin')),
            ]);
        }

        $rulesByTab = [
            'details' => [
                'brand_name' => 'required|string|max:255',
                'industry' => 'nullable|string|max:150',
                'bio' => 'nullable|string|max:500',
                'address_line' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:30',
                'phone' => 'nullable|string|max:30',
            ],
            'social' => [
                'website' => ['nullable', 'url', 'max:255'],
                'instagram' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['instagram.com'])],
                'tiktok' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['tiktok.com'])],
                'facebook' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['facebook.com', 'fb.com'])],
                'x' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['x.com', 'twitter.com'])],
                'youtube' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['youtube.com', 'youtu.be'])],
                'linkedin' => ['nullable', 'url', 'max:255', $this->platformUrlRule(['linkedin.com'])],
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
            $brand->industry = $validated['industry'] ?? null;
            $brand->save();

            // Update user fields (city, country, postal_code, phone, bio)
            $user->address_line = $validated['address_line'] ?? null;
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
                $this->deleteStoredAsset($user->profile_image_path);
            }

            $user->profile_image_path = $this->storeUploadedAsset($request->file('profile_image'), 'brand-profile-images');
            $user->save();
        }

        // Handle cover image upload (store on user)
        if ($activeTab === 'images' && $request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
                $this->deleteStoredAsset($user->cover_image_path);
            }

            $user->cover_image_path = $this->storeUploadedAsset($request->file('cover_image'), 'brand-cover-images');
            $user->save();
        }

        $messages = [
            'details' => 'Profile details updated successfully.',
            'social' => 'Social links updated successfully.',
            'images' => 'Profile images updated successfully.',
        ];

        $message = $messages[$activeTab] ?? 'Profile updated successfully.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'active_tab' => $activeTab,
            ]);
        }

        return redirect()->route('dashboard.brand.profile.edit', ['slug' => $slug])->with('success', $message);
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
                'user:id,name,slug,address_line,city,country,postal_code,phone,bio,profile_image_path,cover_image_path,is_active',
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

    private function normalizeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $url)) {
            $url = 'https://' . ltrim($url, '/');
        }

        return rtrim($url, '/');
    }

    private function platformUrlRule(array $allowedHosts): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) use ($allowedHosts): void {
            if (empty($value)) {
                return;
            }

            $host = strtolower((string) parse_url((string) $value, PHP_URL_HOST));
            if ($host === '') {
                $fail('The ' . str_replace('_', ' ', $attribute) . ' field must be a valid URL.');
                return;
            }

            foreach ($allowedHosts as $allowedHost) {
                $allowedHost = strtolower($allowedHost);
                if ($host === $allowedHost || str_ends_with($host, '.' . $allowedHost)) {
                    return;
                }
            }

            $fail('The ' . str_replace('_', ' ', $attribute) . ' URL is not a valid platform link.');
        };
    }

    /**
     * Persist uploaded file to public storage and return its path.
     * Returns path without 'storage/' prefix for proper Storage::url() usage in views.
     */
    private function storeUploadedAsset($file, string $directory): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    /**
     * Delete stored asset from public disk.
     * Handles paths with or without 'storage/' prefix for backward compatibility.
     */
    private function deleteStoredAsset(?string $path): void
    {
        if (!$path) {
            return;
        }

        // Remove 'storage/' prefix if present (for backward compatibility)
        $cleanPath = str_starts_with($path, 'storage/') ? \Illuminate\Support\Str::after($path, 'storage/') : $path;

        if (Storage::disk('public')->exists($cleanPath)) {
            Storage::disk('public')->delete($cleanPath);
        }
    }
}
