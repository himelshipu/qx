@extends('backend.layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="mx-auto max-w-5xl space-y-6" x-data="{ tab: @js($initialTab) }">
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Account Settings</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your dashboard account, security, and billing settings.</p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 pt-4 dark:border-gray-700">
            <div class="flex gap-6 overflow-x-auto text-sm font-medium">
                <button @click="tab='details'" :class="tab === 'details' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 dark:text-gray-400'" class="pb-3">Details</button>
                @if ($canManageBilling)
                    <button @click="tab='billing'" :class="tab === 'billing' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 dark:text-gray-400'" class="pb-3">Billing</button>
                @endif
                <button @click="tab='password'" :class="tab === 'password' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 dark:text-gray-400'" class="pb-3">Password</button>
                <button @click="tab='security'" :class="tab === 'security' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500 dark:text-gray-400'" class="pb-3">Security</button>
            </div>
        </div>

        <div class="p-6">
            <div x-show="tab==='details'" x-cloak>
                <form action="{{ route('dashboard.account.details.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="tab" value="details">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name *</label>
                            <input name="name" type="text" value="{{ old('name', $user->name) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email *</label>
                            <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input name="phone" type="text" value="{{ old('phone', $user->phone) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                            <input name="city" type="text" value="{{ old('city', $user->city) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                            <textarea name="bio" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('bio', $user->bio) }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Save Details</button>
                    </div>
                </form>
            </div>

            @if ($canManageBilling)
                <div x-show="tab==='billing'" x-cloak>
                    <form action="{{ route('dashboard.account.billing.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-5">
                        @csrf
                        <input type="hidden" name="tab" value="billing">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Legal Company Name</label>
                                <input name="legal_company_name" type="text" value="{{ old('legal_company_name', $billingProfile?->legal_company_name) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">VAT ID</label>
                                <input name="vat_id" type="text" value="{{ old('vat_id', $billingProfile?->vat_id) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Address</label>
                                <input name="billing_address" type="text" value="{{ old('billing_address', $billingProfile?->billing_address) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                                <input name="billing_city" type="text" value="{{ old('billing_city', $billingProfile?->billing_city) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                                <input name="billing_country" type="text" value="{{ old('billing_country', $billingProfile?->billing_country) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Postal Code</label>
                                <input name="billing_postal_code" type="text" value="{{ old('billing_postal_code', $billingProfile?->billing_postal_code) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Save Billing</button>
                        </div>
                    </form>
                </div>
            @endif

            <div x-show="tab==='password'" x-cloak>
                <form action="{{ route('dashboard.account.password.update', ['slug' => $user->slug]) }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="tab" value="password">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password *</label>
                        <input name="current_password" type="password" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">New Password *</label>
                        <input name="password" type="password" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm New Password *</label>
                        <input name="password_confirmation" type="password" required class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Update Password</button>
                    </div>
                </form>
            </div>

            <div x-show="tab==='security'" x-cloak class="space-y-6">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Account Status</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current status: {{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <form action="{{ route('dashboard.account.toggle-status', ['slug' => $user->slug]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="tab" value="security">
                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium {{ $user->is_active ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' }}">{{ $user->is_active ? 'Deactivate' : 'Activate' }}</button>
                        </form>
                    </div>
                </div>

                <div class="rounded-lg border border-red-200 p-4 dark:border-red-900/40">
                    <h3 class="text-sm font-semibold text-red-700 dark:text-red-300">Delete Account</h3>
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">This action is permanent and cannot be undone.</p>
                    <form method="POST" action="{{ route('dashboard.account.destroy', ['slug' => $user->slug]) }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="tab" value="security">
                        <input type="password" name="password" placeholder="Confirm your password" required
                            class="h-10 flex-1 rounded-lg border border-red-300 bg-white px-3 text-sm dark:border-red-900 dark:bg-gray-800 dark:text-white" />
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete Account</button>
                    </form>
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
