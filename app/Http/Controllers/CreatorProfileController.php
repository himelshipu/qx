<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\CreatorPortfolio;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class CreatorProfileController extends Controller
{
    private function getDashboardCreatorBySlug(string $slug): ?Creator
    {
        $user = Auth::user();

        if (!$user || $user->slug !== $slug) {
            return null;
        }

        return $user->creator;
    }

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
            ->where('creator_id', $creator->id)
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
    public function edit(string $slug)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            abort(404);
        }

        $creator->load(['user', 'socialLinks', 'portfolios']);

        return view('frontend.pages.creator-edit-profile', [
            'user' => $creator->user,
            'creator' => $creator,
            'brand' => $creator,
            'slug' => $slug,
        ]);
    }

    /**
     * Update creator profile information
     */
    public function update(Request $request, string $slug)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            return redirect()->route('dashboard.creator.profile.edit')->with('error', 'Creator profile not found');
        }

        $activeTab = $request->string('active_tab')->toString() ?: 'details';

        $rulesByTab = [
            'details' => [
                'display_name' => ['nullable', 'string', 'max:255'],
                'title_name' => ['nullable', 'string', 'max:255'],
                'audience' => ['nullable', 'string'],
                'brands_worked_with' => ['nullable', 'string'],
                'gender' => ['nullable', 'string', 'in:male,female,other'],
                'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address_line' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'country' => ['nullable', 'string', 'max:255'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'bio' => ['nullable', 'string', 'max:500'],
            ],
            'social' => [
                'instagram_url' => ['nullable', 'url', 'max:255'],
                'tiktok_url' => ['nullable', 'url', 'max:255'],
                'facebook_url' => ['nullable', 'url', 'max:255'],
                'x_url' => ['nullable', 'url', 'max:255'],
                'youtube_url' => ['nullable', 'url', 'max:255'],
                'linkedin_url' => ['nullable', 'url', 'max:255'],
                'other_url' => ['nullable', 'url', 'max:255'],
            ],
            'images' => [
                'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
                'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:4096'],
                'portfolio_images' => ['nullable', 'array'],
                'portfolio_images.*' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
            ],
        ];

        if (!array_key_exists($activeTab, $rulesByTab)) {
            $activeTab = 'details';
        }

        $validated = $request->validate($rulesByTab[$activeTab]);

        if ($activeTab === 'details') {
            $creator->display_name = $validated['display_name'] ?? $creator->display_name;
            $creator->title_name = $validated['title_name'] ?? $creator->title_name;
            $creator->audience = $validated['audience'] ?? $creator->audience;
            $creator->brands_worked_with = $validated['brands_worked_with'] ?? $creator->brands_worked_with;
            $creator->save();

            $user->city = $validated['city'] ?? $user->city;
            $user->country = $validated['country'] ?? $user->country;
            $user->postal_code = $validated['postal_code'] ?? $user->postal_code;
            $user->bio = $validated['bio'] ?? $user->bio;
            $user->phone = $validated['phone'] ?? $user->phone;
            $user->address_line = $validated['address_line'] ?? $user->address_line;
            $user->gender = $validated['gender'] ?? $user->gender;
            if (array_key_exists('date_of_birth', $validated)) {
                $user->date_of_birth = $validated['date_of_birth'];
            }
            $user->save();
        }

        if ($activeTab === 'social' && Schema::hasTable('creator_social_links')) {
            $creator->socialLinks()->updateOrCreate(
                ['creator_id' => $creator->id],
                [
                    'instagram_url' => $validated['instagram_url'] ?? null,
                    'tiktok_url' => $validated['tiktok_url'] ?? null,
                    'facebook_url' => $validated['facebook_url'] ?? null,
                    'x_url' => $validated['x_url'] ?? null,
                    'youtube_url' => $validated['youtube_url'] ?? null,
                    'linkedin_url' => $validated['linkedin_url'] ?? null,
                    'other_url' => $validated['other_url'] ?? null,
                ]
            );
        }

        if ($activeTab === 'images') {
            if ($request->hasFile('profile_image')) {
                try {
                    if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
                        Storage::disk('public')->delete($user->profile_image_path);
                    }
                    Storage::disk('public')->makeDirectory('creators/profile');
                    $path = $request->file('profile_image')->store('creators/profile', 'public');
                    $user->profile_image_path = $path;
                    $user->save();
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
                    Storage::disk('public')->makeDirectory('creators/cover');
                    $path = $request->file('cover_image')->store('creators/cover', 'public');
                    $user->cover_image_path = $path;
                    $user->save();
                } catch (\Exception $e) {
                    Log::error('Cover image upload error: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to upload cover image')->with('active_tab', $activeTab);
                }
            }

            if ($request->hasFile('portfolio_images')) {
                try {
                    Storage::disk('public')->makeDirectory('creators/portfolio');
                    foreach ($request->file('portfolio_images') as $index => $portfolioImage) {
                        $path = $portfolioImage->store('creators/portfolio', 'public');
                        $creator->portfolios()->create([
                            'media_type' => 'image',
                            'file_path' => $path,
                            'title' => 'Portfolio Image ' . ($creator->portfolios()->max('sort_order') + 1),
                            'sort_order' => $creator->portfolios()->max('sort_order') + 1,
                            'is_active' => true,
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
            'social' => 'Social links updated successfully.',
            'images' => 'Profile images updated successfully.',
        ];

        $message = $messages[$activeTab] ?? 'Profile updated successfully.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([ 'message' => $message, 'active_tab' => $activeTab ]);
        }

        return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('success', $message)->with('active_tab', $activeTab);
    }

    /**
     * Delete creator profile image
     */
    public function deleteProfileImage(Request $request, string $slug)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        $user->update(['profile_image_path' => null]);

        return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('status', 'profile-image-deleted');
    }

    /**
     * Delete creator cover image (kept for backward compatibility)
     */
    public function deleteCoverImage(Request $request, string $slug)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        // Delete cover image if exists
        if ($user->cover_image_path && Storage::disk('public')->exists($user->cover_image_path)) {
            Storage::disk('public')->delete($user->cover_image_path);
        }

        $user->update(['cover_image_path' => null]);

        return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('status', 'cover-image-deleted');
    }

    /**
     * Delete creator portfolio image
     */
    public function deletePortfolioImage(Request $request, string $slug, int $portfolio)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('error', 'Creator not found');
        }

        $portfolioItem = CreatorPortfolio::where('creator_id', $creator->id)->where('id', $portfolio)->first();

        if (!$portfolioItem) {
            return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('error', 'Portfolio item not found');
        }

        // Delete file from storage
        if ($portfolioItem->file_path && Storage::disk('public')->exists($portfolioItem->file_path)) {
            Storage::disk('public')->delete($portfolioItem->file_path);
        }

        // Delete portfolio record
        $portfolioItem->delete();

        return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with('success', 'Portfolio image deleted successfully.');
    }

    public function toggleStatus(Request $request, string $slug)
    {
        $user = Auth::user();
        $creator = $this->getDashboardCreatorBySlug($slug);

        if (!$creator) {
            return response()->json(['error' => 'Creator not found'], 404);
        }

        $creator->update(['is_active' => !$creator->is_active]);

        return redirect()->route('dashboard.creator.profile.edit', ['slug' => $slug])->with(
            'status',
            $creator->is_active ? 'creator-activated' : 'creator-deactivated'
        );
    }
}
