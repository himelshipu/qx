<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandProfileController extends Controller
{
    /**
     * Show brand profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $brand = $user->brand;

        return view('frontend.pages.brand-edit-profile', [
            'user' => $user,
            'brand' => $brand
        ]);
    }

    /**
     * Update brand profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        if (!$brand) {
            return redirect()->route('dashboard.brand.profile.edit')->with('error', 'Brand not found');
        }

        // Handle categories - if it's a JSON string, decode it
        $categories = $request->input('categories', []);
        if (is_string($categories)) {
            $categories = json_decode($categories, true) ?? [];
        }
        if (!is_array($categories)) {
            $categories = [];
        }

        $validated = $request->validate([
            'brand_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'tiktok' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,webp|max:5120',
        ]);

        // Update basic brand info
        $brand->brand_name = $validated['brand_name'];
        $brand->description = $validated['description'] ?? null;
        $brand->location = $validated['location'] ?? null;
        $brand->city = $validated['city'] ?? null;
        $brand->country = $validated['country'] ?? null;
        $brand->postal_code = $validated['postal_code'] ?? null;
        $brand->phone = $validated['phone'] ?? null;
        $brand->email = $validated['email'] ?? null;
        $brand->website = $validated['website'] ?? null;
        $brand->categories = $categories;

        // Update social links
        $brand->social_links = [
            'website' => $validated['website'] ?? null,
            'instagram' => $validated['instagram'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'youtube' => $validated['youtube'] ?? null,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($brand->profile_image_path && Storage::disk('public')->exists($brand->profile_image_path)) {
                Storage::disk('public')->delete($brand->profile_image_path);
            }
            
            $path = $request->file('profile_image')->store('brands/profile', 'public');
            $brand->profile_image_path = $path;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover image if exists
            if ($brand->cover_image_path && Storage::disk('public')->exists($brand->cover_image_path)) {
                Storage::disk('public')->delete($brand->cover_image_path);
            }
            
            $path = $request->file('cover_image')->store('brands/cover', 'public');
            $brand->cover_image_path = $path;
        }

        // Also update setup_data for backward compatibility
        $brand->setup_data = [
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'categories' => $categories,
            'social' => [
                'website' => $validated['website'] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'youtube' => $validated['youtube'] ?? null,
            ],
            'profile_image' => $brand->profile_image_path,
            'cover_image' => $brand->cover_image_path,
        ];

        $brand->save();

        return redirect()->route('dashboard.brand.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete profile images
     */
    public function deleteProfileImage(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        if ($brand->profile_image_path && Storage::disk('public')->exists($brand->profile_image_path)) {
            Storage::disk('public')->delete($brand->profile_image_path);
        }

        $brand->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.brand.profile.edit')->with('status', 'profile-image-deleted');
    }

    /**
     * Delete cover image
     */
    public function deleteCoverImage(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        if ($brand->cover_image_path && Storage::disk('public')->exists($brand->cover_image_path)) {
            Storage::disk('public')->delete($brand->cover_image_path);
        }

        $brand->update(['cover_image_path' => null]);

        return redirect()->route('dashboard.brand.profile.edit')->with('status', 'cover-image-deleted');
    }

    /**
     * Toggle brand verification status
     */
    public function toggleVerification(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $brand->update(['is_verified' => !$brand->is_verified]);

        return redirect()->route('dashboard.brand.profile.edit')->with(
            'status',
            $brand->is_verified ? 'brand-verified' : 'brand-unverified'
        );
    }

    /**
     * Toggle brand active status
     */
    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        if (!$brand) {
            return response()->json(['error' => 'Brand not found'], 404);
        }

        $brand->update(['is_active' => !$brand->is_active]);

        return redirect()->route('dashboard.brand.profile.edit')->with(
            'status',
            $brand->is_active ? 'brand-activated' : 'brand-deactivated'
        );
    }
}