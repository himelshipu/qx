<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BrandProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        return view('frontend.pages.brand-edit-profile', [
            'user' => $user,
            'brand' => $brand
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        $validated = $request->validate([
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'categories' => 'nullable|array',
            'website' => 'nullable|url|max:255',
            'instagram' => 'nullable|string|max:255',
            'tiktok' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        // Prepare setup data
        $setupData = $brand->setup_data ?? [];
        
        $setupData['location'] = $request->location;
        $setupData['description'] = $request->description;
        $setupData['categories'] = $request->categories ?? [];
        $setupData['social'] = [
            'website' => $request->website,
            'instagram' => $request->instagram,
            'tiktok' => $request->tiktok,
            'youtube' => $request->youtube,
        ];

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            if (!empty($setupData['profile_image'])) {
                Storage::disk('public')->delete($setupData['profile_image']);
            }
            $path = $request->file('profile_image')->store('brands/profile', 'public');
            $setupData['profile_image'] = $path;
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            if (!empty($setupData['cover_image'])) {
                Storage::disk('public')->delete($setupData['cover_image']);
            }
            $path = $request->file('cover_image')->store('brands/cover', 'public');
            $setupData['cover_image'] = $path;
        }

        $brand->update(['setup_data' => $setupData]);

        return redirect()->route('brand.profile.edit')->with('status', 'profile-updated');
    }
}