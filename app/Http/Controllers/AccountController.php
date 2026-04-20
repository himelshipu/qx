<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    private function getAuthenticatedUser(): User
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Unauthorized access');
        }

        return $user;
    }

    private function redirectToAccount(User $user, Request $request, string $fallbackTab)
    {
        $tab = $request->input('tab', $request->query('tab', $fallbackTab));

        return redirect()
            ->route('dashboard.account.edit', ['slug' => $user->slug, 'tab' => $tab])
            ->with('tab', $tab);
    }

    /**
     * Get user by slug and verify ownership
     */
    private function getUserBySlug($slug)
    {
        $user = User::where('slug', $slug)->first();

        if (! $user) {
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

        $canManageBilling = in_array($user->user_type, ['brand', 'influencer'], true);
        $profileOwner = match ($user->user_type) {
            'brand' => $user->brand,
            'influencer' => $user->influencer,
            default => null,
        };
        $billingProfile = $canManageBilling ? $profileOwner?->billingProfiles()->first() : null;

        return view('backend.pages.account.edit', [
            'user' => $user,
            'canManageBilling' => $canManageBilling,
            'billingProfile' => $billingProfile,
            'initialTab' => request('tab', session('tab', 'details')),
        ]);
    }

    public function profileEdit()
    {
        $user = $this->getAuthenticatedUser();

        return view('backend.pages.account.profile', [
            'user' => $user,
        ]);
    }

    public function profileUpdate(Request $request)
    {
        $user = $this->getAuthenticatedUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s\(\)]+$/'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('dashboard.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update user account details
     *
     * Handles user personal information including address fields as separate columns:
     * - address_line: Street address
     * - city: City/Municipality
     * - country: Country
     * - postal_code: ZIP/Postal code
     */
    public function updateDetails(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s\(\)]+$/'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        // Update user data - fields stored separately
        $user->update($validated);

        return $this->redirectToAccount($user, $request, 'details')
            ->with('success', 'Your details updated successfully.');
    }

    /**
     * Update user billing information
     *
     * Uses updateOrCreate to prevent duplicate billing profiles.
     * Supports both Brand and Influencer polymorphic relationships.
     */
    public function updateBilling(Request $request, $slug)
    {
        $user = $this->getUserBySlug($slug);

        $profileOwner = match ($user->user_type) {
            'brand' => $user->brand,
            'influencer' => $user->influencer,
            default => null
        };

        if (! $profileOwner) {
            return $this->redirectToAccount($user, $request, 'billing')
                ->with('error', 'Billing information is not available for this account.');
        }

        $validated = $request->validate([
            'legal_company_name' => ['nullable', 'string', 'max:255'],
            'vat_id' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:255'],
            'billing_country' => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        // Add user_type to validated data before saving
        $validated['user_type'] = $user->user_type;

        // Use updateOrCreate to prevent duplicate billing profiles
        $profileOwner->billingProfiles()->updateOrCreate(
            ['user_id' => $profileOwner->id],
            $validated
        );

        return $this->redirectToAccount($user, $request, 'billing')
            ->with('success', 'Billing information updated successfully.');
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

        return $this->redirectToAccount($user, $request, 'password')
            ->with('success', 'Password updated successfully.');
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

        if ($user->isSuperadmin()) {
            return $this->redirectToAccount($user, $request, 'security')
                ->with('error', 'Superadmin account is protected and cannot be deleted.');
        }

        try {
            DB::beginTransaction();

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

            $user->delete();
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            return $this->redirectToAccount($user, $request, 'security')
                ->with('error', 'Unable to delete your account at this time. Please contact support.');
        }

        Auth::logout();

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
        $user->update(['is_active' => ! $user->is_active]);

        return $this->redirectToAccount($user, $request, 'security')->with(
            'success',
            $user->is_active ? 'Account activated successfully.' : 'Account deactivated successfully.'
        );
    }
}
