<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Get user by slug and verify ownership
     */
    private function getUserBySlug($slug)
    {
        $user = User::where('slug', $slug)->first();
        
        if (!$user) {
            abort(404, 'User not found');
        }
        
        // Verify that the logged-in user owns this account
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized access');
        }
        
        return $user;
    }

    public function edit($slug)
    {
        $user = $this->getUserBySlug($slug);
        $brand = $user->brand;
        
        return view('frontend.pages.account', [
            'user' => $user,
            'brand' => $brand
        ]);
    }

    /**
     * Update user account details
     */
    public function updateDetails(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'address_line' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update user data
        $user->update($validated);

        return redirect()->route('dashboard.account.edit', ['slug' => $user->slug])->with('success', 'Your details updated successfully.');
    }

    /**
     * Update user billing information
     */
    public function updateBilling(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);
        $brand = $user->brand;
        
        if (!$brand) {
            return redirect()->route('dashboard.account.edit', ['slug' => $user->slug])
                ->with('error', 'Billing information is only available for brand accounts.');
        }
        
        $validated = $request->validate([
            'legal_company_name' => 'nullable|string|max:255',
            'vat_id' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_country' => 'nullable|string|max:255',
            'billing_postal_code' => 'nullable|string|max:20',
        ]);

        // Get or create billing profile
        $billingProfile = $brand->billingProfile;
        
        if ($billingProfile) {
            $billingProfile->update($validated);
        } else {
            $brand->billingProfile()->create($validated);
        }

        return redirect()->route('dashboard.account.edit', ['slug' => $user->slug])->with('success', 'Billing information updated successfully.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('dashboard.account.edit', ['slug' => $user->slug])->with('success', 'Password updated successfully.');
    }

    /**
     * Delete user account with cascade deletion
     */
    public function destroy(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);
        
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Delete user's profile image if exists
        if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
            Storage::disk('public')->delete($user->profile_image_path);
        }

        // Delete related brand with cascade
        if ($user->brand) {
            // Delete brand images
            if ($user->brand->profile_image_path && Storage::disk('public')->exists($user->brand->profile_image_path)) {
                Storage::disk('public')->delete($user->brand->profile_image_path);
            }
            if ($user->brand->cover_image_path && Storage::disk('public')->exists($user->brand->cover_image_path)) {
                Storage::disk('public')->delete($user->brand->cover_image_path);
            }
            $user->brand->delete();
        }
        
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
    }

    /**
     * Toggle account active status
     */
    public function toggleStatus(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('dashboard.account.edit', ['slug' => $user->slug])->with(
            'status',
            $user->is_active ? 'account-activated' : 'account-deactivated'
        );
    }
}