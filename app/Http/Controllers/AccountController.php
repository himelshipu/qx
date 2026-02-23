<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        return view('frontend.pages.account', [
            'user' => $user,
            'brand' => $brand
        ]);
    }

    /**
     * Update user account details
     */
    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'company_name' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path)) {
                Storage::disk('public')->delete($user->profile_image_path);
            }
            
            $path = $request->file('profile_image')->store('users/profile', 'public');
            $validated['profile_image_path'] = $path;
        }

        $user->update($validated);

        return redirect()->route('dashboard.account.edit')->with('status', 'details-updated');
    }

    /**
     * Update user billing information
     */
    public function updateBilling(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        $validated = $request->validate([
            'legal_company_name' => 'nullable|string|max:255',
            'vat_id' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_country' => 'nullable|string|max:255',
            'billing_postal_code' => 'nullable|string|max:20',
        ]);

        if ($brand) {
            $setupData = $brand->setup_data ?? [];
            $setupData['billing'] = $validated;
            $brand->update(['setup_data' => $setupData]);
        }

        return redirect()->route('dashboard.account.edit')->with('status', 'billing-updated');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('dashboard.account.edit')->with('status', 'password-updated');
    }

    /**
     * Delete user account with cascade deletion
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
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
    public function toggleStatus(Request $request)
    {
        $user = Auth::user();
        $user->update(['is_active' => !$user->is_active]);

        return redirect()->route('dashboard.account.edit')->with(
            'status',
            $user->is_active ? 'account-activated' : 'account-deactivated'
        );
    }
}