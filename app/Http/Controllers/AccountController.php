<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    public function updateDetails(Request $request)
    {
        $user = Auth::user();
        $brand = $user->brand;
        
        $validated = $request->validate([
            'billing_address' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
        ]);

        $setupData = $brand->setup_data ?? [];
        $setupData['billing'] = [
            'legal_company_name' => $request->legal_company_name,
            'vat_id' => $request->vat_id,
            'billing_address' => $request->billing_address,
            'zip_code' => $request->zip_code,
        ];

        $brand->update(['setup_data' => $setupData]);

        return redirect()->route('account.edit')->with('status', 'details-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('account.edit')->with('status', 'password-updated');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        Auth::logout();
        
        if ($user->brand) {
            $user->brand->delete();
        }
        
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}