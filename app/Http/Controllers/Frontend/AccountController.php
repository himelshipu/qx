<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Show the account edit page for the authenticated user.
     */
    public function edit(string $slug): View
    {
        $user       = Auth::user();
        $brand      = $user->brand;
        $influencer = $user->influencer;

        // Determine if user can manage billing and payment
        $canManageBillingAndPayment = in_array($user->user_type, ['brand', 'influencer'], true);

        // Get payment methods for the user
        $paymentMethods = $user->paymentMethods;

        return view('frontend.pages.account', [
            'user'                       => $user,
            'brand'                      => $brand,
            'influencer'                 => $influencer,
            'slug'                       => $slug,
            'tab'                        => request()->query('tab', 'details'),
            'canManageBillingAndPayment' => $canManageBillingAndPayment,
            'paymentMethods'             => $paymentMethods
        ]);
    }

    /**
     * Update user details (name, email, phone, address, location).
     */
    public function updateDetails(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone'        => 'nullable|string|max:20',
            'bio'          => 'nullable|string|max:1000',
            'address_line' => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'country'      => 'nullable|string|max:100',
            'postal_code'  => 'nullable|string|max:20'
        ]);

        $user->update($validated);

        return redirect()
            ->route('frontend.account.edit', ['slug' => $slug, 'tab' => 'details'])
            ->with('success', 'Profile details updated successfully.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::defaults()]
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('frontend.account.edit', ['slug' => $slug, 'tab' => 'password'])
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Update user billing information.
     */
    public function updateBilling(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();

        // Get the profile owner (brand or influencer)
        $profileOwner = match ($user->user_type) {
            'brand'   => $user->brand,
            'creator' => $user->influencer,
            default   => null
        };

        if (!$profileOwner) {
            return redirect()
                ->route('frontend.account.edit', ['slug' => $slug, 'tab' => 'billing'])
                ->with('error', 'Billing information is not available for this account.');
        }

        $validated = $request->validate([
            'legal_company_name'  => ['nullable', 'string', 'max:255'],
            'vat_id'              => ['nullable', 'string', 'max:50'],
            'billing_address'     => ['nullable', 'string', 'max:255'],
            'billing_city'        => ['nullable', 'string', 'max:255'],
            'billing_country'     => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'max:20']
        ]);

        // Use updateOrCreate to prevent duplicate billing profiles
        $profileOwner->billingProfiles()->updateOrCreate(
            [],
            $validated
        );

        return redirect()
            ->route('frontend.account.edit', ['slug' => $slug, 'tab' => 'billing'])
            ->with('success', 'Billing information updated successfully.');
    }

    /**
     * Toggle account status (activate/deactivate).
     */
    public function toggleStatus(Request $request, string $slug): RedirectResponse
    {
        $user = Auth::user();

        // Toggle the is_active status
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $message = $user->is_active
        ? 'Account activated successfully.'
        : 'Account deactivated successfully.';

        return redirect()
            ->route('frontend.account.edit', ['slug' => $slug, 'tab' => 'security'])
            ->with('success', $message);
    }
}
