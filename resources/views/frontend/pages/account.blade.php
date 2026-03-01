@extends('frontend.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" 
     x-data="{ tab: 'details' }">
    
    <div>
        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">My Account</h1>

        <!-- Status Messages -->
        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Profile updated successfully.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Password updated successfully.
            </div>
        @endif

        @if (session('status') === 'billing-updated')
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Billing information updated successfully.
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-xl">
                <div class="font-bold mb-2">Please fix the following errors:</div>
                <ul class="list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto mt-8"> 
            <button @click="tab = 'details'" 
                    :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Details
            </button>
            
            <button @click="tab = 'password'" 
                    :class="tab === 'password' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Update Password
            </button>
           <button @click="tab = 'security'" 
                    :class="tab === 'security' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Security
            </button>
        </div>

        <!-- Details Tab -->
        <div x-show="tab === 'details'" x-cloak class="space-y-8 animate-in fade-in duration-300">
            <form action="{{ route('dashboard.account.details.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-2 gap-6">
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

                    <!-- Legal Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Legal Company Name</label>
                        <input type="text" name="legal_company_name" value="{{ old('legal_company_name', $brand->setup_data['details']['legal_company_name'] ?? '') }}" placeholder="The official name of your company for billing purposes"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('legal_company_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VAT ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">VAT ID</label>
                        <input type="text" name="vat_id" value="{{ old('vat_id', $brand->setup_data['details']['vat_id'] ?? '') }}" placeholder="Optional"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('vat_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Billing Address</label>
                        <input type="text" name="billing_address" value="{{ old('billing_address', $brand->setup_data['details']['billing_address'] ?? '') }}" placeholder="The street address for billing purposes"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="billing_city" value="{{ old('billing_city', $brand->setup_data['details']['billing_city'] ?? '') }}" placeholder="The city for your billing address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="billing_country" value="{{ old('billing_country', $brand->setup_data['details']['billing_country'] ?? '') }}" placeholder="The country for your billing address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Postal Code</label>
                        <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $brand->setup_data['details']['billing_postal_code'] ?? '') }}" placeholder="The postal or ZIP code for your billing address"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('billing_postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="bg-[#222] shadow-theme-xs h-14 hover:bg-purple-400 flex w-full items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Save Billing Info
                </button>
            </form>
        </div>


        <!-- Password Tab -->
        <div x-show="tab === 'password'" x-cloak class="space-y-8 animate-in fade-in duration-300" x-data="{ showPass: { old: false, new: false, confirm: false } }">
            <form action="{{ route('dashboard.account.password.update') }}" method="POST" class="space-y-6">
                @csrf

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
                    <form action="{{ route('dashboard.account.toggle-status') }}" method="POST">
                        @csrf
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
                    <form method="POST" action="{{ route('dashboard.account.destroy') }}" @submit="if(!confirm('Are you absolutely sure? All your data will be permanently deleted.')) $event.preventDefault();">
                        @csrf
                        @method('DELETE')
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
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
