@extends('frontend.layouts.app')

@section('content')
@php
    $canManageBillingAndPayment = in_array($user->user_type, ['brand', 'creator'], true);
@endphp
<div class="max-w-5xl mx-auto"
    x-data="accountPageData({
        initialTab: @js(request('tab', session('tab', 'details'))),
        availableTabs: @js($canManageBillingAndPayment ? ['details', 'billing', 'payment', 'password', 'security'] : ['details', 'password', 'security']),
        stripePublicKey: @js(config('stripe.public_key')),
        paymentStoreUrl: @js(route('dashboard.payment-methods.store')),
        csrfToken: @js(csrf_token())
    })">
    
    <div>
        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">My Account</h1>

        <!-- Tab Navigation -->
        <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto mt-8"> 
            <button @click="tab = 'details'" 
                    :class="{ 'border-b-2 border-black dark:border-white text-black dark:text-white': tab === 'details', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': tab !== 'details' }"
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Details
            </button>
            
            @if($canManageBillingAndPayment)
            <button @click="tab = 'billing'" 
                    :class="{ 'border-b-2 border-black dark:border-white text-black dark:text-white': tab === 'billing', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': tab !== 'billing' }"
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Billing
            </button>
            @endif

            @if($canManageBillingAndPayment)
            <button @click="tab = 'payment'" 
                    :class="{ 'border-b-2 border-black dark:border-white text-black dark:text-white': tab === 'payment', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': tab !== 'payment' }"
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Payment
            </button>
            @endif

            <button @click="tab = 'password'" 
                    :class="{ 'border-b-2 border-black dark:border-white text-black dark:text-white': tab === 'password', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': tab !== 'password' }"
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Update Password
            </button>

            <button @click="tab = 'security'" 
                    :class="{ 'border-b-2 border-black dark:border-white text-black dark:text-white': tab === 'security', 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300': tab !== 'security' }"
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Security
            </button>

        </div>

        <!-- Details Tab -->
        <div x-show="tab === 'details'" x-cloak class="space-y-8 animate-in fade-in duration-300">
            <form action="{{ route('dashboard.account.details.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="tab" :value="tab">

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Full Name <span class="text-error-500"> *</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" 
                            required />
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Email Address <span class="text-error-500"> *</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" 
                            required />
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800 my-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Location</h3>

                    <!-- Address Line -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Street Address</label>
                        <input type="text" name="address_line" value="{{ old('address_line', $user->address_line) }}" placeholder="Street address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('address_line')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" placeholder="City name"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="country" value="{{ old('country', $user->country) }}" placeholder="Country"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Postal/ZIP Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="Postal or ZIP code"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="bg-[#222] shadow-theme-xs h-14 hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Save Changes
                </button>
            </form>
        </div>

    @if($canManageBillingAndPayment)
    <!-- Billing Tab -->
        <div x-show="tab === 'billing'" x-cloak class="space-y-8 animate-in fade-in duration-300">
            <form action="{{ route('dashboard.account.billing.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-8">
                @csrf
                <input type="hidden" name="tab" :value="tab">

                <div class="space-y-6">
                    @php
                        $profileOwner = match($user->user_type) {
                            'brand' => $user->brand,
                            'creator' => $user->creator,
                            default => null
                        };
                        $billingProfile = optional($profileOwner)->billingProfiles->first();
                    @endphp

                    <!-- Legal Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Legal Company Name</label>
                        <input type="text" name="legal_company_name" value="{{ old('legal_company_name', $billingProfile?->legal_company_name ?? '') }}" placeholder="Company Name for Invoicing"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('legal_company_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VAT ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">VAT ID</label>
                        <input type="text" name="vat_id" value="{{ old('vat_id', $billingProfile?->vat_id ?? '') }}" placeholder="Optional"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('vat_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-gray-200 dark:border-gray-800 my-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Billing Address</h3>

                    <!-- Billing Address -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Street Address</label>
                        <input type="text" name="billing_address" value="{{ old('billing_address', $billingProfile?->billing_address ?? '') }}" placeholder="Street address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="billing_city" value="{{ old('billing_city', $billingProfile?->billing_city ?? '') }}" placeholder="City Name"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="billing_country" value="{{ old('billing_country', $billingProfile?->billing_country ?? '') }}" placeholder="Country"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Postal/ZIP Code</label>
                        <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $billingProfile?->billing_postal_code ?? '') }}" placeholder="The postal or ZIP code for your billing address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="bg-[#222] shadow-theme-xs h-14 hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Save Billing Information
                </button>
            </form>
        </div>
    @endif


        <!-- Password Tab -->
        <div x-show="tab === 'password'" x-cloak class="space-y-8 animate-in fade-in duration-300" x-data="{ showPass: { old: false, new: false, confirm: false } }">
            <form action="{{ route('dashboard.account.password.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="tab" :value="tab">

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Current Password <span class="text-error-500"> *</span></label>
                    <div class="relative">
                        <input :type="showPass.old ? 'text' : 'password'" name="current_password" placeholder="Enter your current password"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" 
                            required />
                        <button type="button" @click="showPass.old = !showPass.old" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">New Password <span class="text-error-500"> *</span></label>
                    <div class="relative">
                        <input :type="showPass.new ? 'text' : 'password'" name="password" placeholder="Enter new password"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" 
                            required />
                        <button type="button" @click="showPass.new = !showPass.new" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">At least 8 characters with uppercase, lowercase, number, and symbol</p>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Confirm Password <span class="text-error-500"> *</span></label>
                    <div class="relative">
                        <input :type="showPass.confirm ? 'text' : 'password'" name="password_confirmation" placeholder="Confirm new password"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" 
                            required />
                        <button type="button" @click="showPass.confirm = !showPass.confirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-[#222] shadow-theme-xs h-14 hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Update Password
                </button>
            </form>
        </div>

        <!-- Security Tab -->
        <div x-show="tab === 'security'" x-cloak class="space-y-12 animate-in fade-in duration-300">
            <!-- Account Status -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Account Status</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Status: <span class="font-medium {{ $user->is_active ? 'text-green-500' : 'text-red-500' }}">
                                @if ($user->is_active)
                                    ✓ Active
                                @else
                                    ✗ Inactive
                                @endif
                            </span>
                        </p>
                    </div>
                    <form action="{{ route('dashboard.account.toggle-status', ['slug' => $user->slug]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="tab" value="security">
                        <button type="submit" class="px-4 py-2 rounded-lg font-medium text-sm transition {{ $user->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                            {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Logout -->
            <div class="border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log Out</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sign out from your account</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-6 py-2 rounded-lg font-medium text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="border-2 border-red-200 dark:border-red-900 rounded-xl p-6 bg-red-50 dark:bg-red-950/20" x-data="{ deleteOpen: false }">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-red-700 dark:text-red-300">Delete Account</h3>
                        <p class="text-sm text-red-600 dark:text-red-400 mt-1">Permanently delete your account and all data</p>
                    </div>
                    <button @click="deleteOpen = !deleteOpen" class="px-4 py-2 rounded-lg font-medium text-sm bg-red-100 text-red-700 hover:bg-red-200 transition">
                        Delete
                    </button>
                </div>

                <div x-show="deleteOpen" x-cloak class="mt-4 p-4 bg-white dark:bg-gray-900 rounded-lg border border-red-200 dark:border-red-800">
                    <p class="text-sm text-gray-800 dark:text-gray-300 mb-4 font-medium">
                        ⚠️ This action cannot be undone. Please enter your password to confirm deletion.
                    </p>
                    <form method="POST"
                        action="{{ route('dashboard.account.destroy', ['slug' => $user->slug]) }}"
                        class="js-confirmable"
                        data-confirm-title="Delete Account"
                        data-confirm-message="Are you absolutely sure? All your data will be permanently deleted."
                        data-confirm-button="Delete Account"
                        data-confirm-variant="danger">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tab" value="security">
                        <div class="flex gap-3 flex-wrap">
                            <input type="password" name="password" placeholder="Enter your password" 
                                class="flex-1 min-w-[200px] h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-red-400 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-800 transition outline-none"
                                required>
                            <button type="submit" class="px-6 py-2 rounded-lg font-medium text-sm bg-red-500 text-white hover:bg-red-600 transition active:scale-95">
                                Delete Account
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </form>
                </div>
            </div>
        </div>

        @if($canManageBillingAndPayment)
        <!-- Payment Tab -->
        <div x-show="tab === 'payment'"
            x-cloak
            class="space-y-8 animate-in fade-in duration-300">
            
            <!-- Add Payment Card Button -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Payment Methods</h3>
                <button type="button" @click="openPaymentModal()"
                        class="px-6 py-3 bg-[#222] dark:bg-white text-white dark:text-black rounded-lg font-medium text-sm hover:opacity-90 transition active:scale-95">
                    + Add Payment Card
                </button>
            </div>

            <!-- Saved Cards List -->
            <div class="space-y-4">
                <h4 class="text-base font-semibold text-gray-900 dark:text-white">Your Cards</h4>
                
                @if($paymentMethods && $paymentMethods->count() > 0)
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-800 rounded-lg bg-gray-50 dark:bg-gray-900/50 hover:border-gray-300 dark:hover:border-gray-700 transition">
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Card Icon -->
                                <div class="w-12 h-8 rounded flex items-center justify-center flex-shrink-0 bg-gray-200 dark:bg-gray-700">
                                    <svg class="w-6 h-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                    </svg>
                                </div>

                                <!-- Card Details -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-semibold text-gray-900 dark:text-white capitalize">
                                            {{ strtoupper($method->brand) }} •••• {{ $method->last4 }}
                                        </p>
                                        @if($method->is_default)
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded">
                                            Default
                                        </span>
                                        @endif
                                        @if($method->isExpired())
                                        <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-medium rounded">
                                            Expired
                                        </span>
                                        @elseif($method->isExpiringSoon())
                                        <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 text-xs font-medium rounded">
                                            Expiring Soon
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Expires {{ $method->formatted_expiry }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                @if(!$method->is_default)
                                <form action="{{ route('dashboard.payment-methods.set-default', $method->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="tab" value="payment">
                                    <button type="submit" 
                                            class="px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition">
                                        Set Default
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('dashboard.payment-methods.destroy', $method->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="tab" value="payment">
                                    <button type="submit"
                                            class="js-confirmable px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition"
                                            data-confirm-title="Delete Payment Method"
                                            data-confirm-message="Are you sure you want to delete this card?"
                                            data-confirm-button="Delete"
                                            data-confirm-variant="danger"
                                            >
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 border border-gray-200 dark:border-gray-800 rounded-lg text-center">
                        <p class="text-gray-600 dark:text-gray-400">No payment methods added yet.</p>
                        <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">Add your first card to get started.</p>
                    </div>
                @endif
            </div>

            <x-payment-modal />
        </div>
        @endif

    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
