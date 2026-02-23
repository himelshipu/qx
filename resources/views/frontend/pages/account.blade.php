@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-16" 
     x-data="{ tab: 'profile' }">
    
    <div class="max-w-5xl mx-auto">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-10">My Account</h1>

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
        <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 mb-10 overflow-x-auto pb-4 md:pb-0">
            <button @click="tab = 'profile'" 
                    :class="tab === 'profile' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Profile
            </button>
            <button @click="tab = 'billing'" 
                    :class="tab === 'billing' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Billing
            </button>
            <button @click="tab = 'password'" 
                    :class="tab === 'password' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Password
            </button>
           <button @click="tab = 'security'" 
                    :class="tab === 'security' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" 
                    class="pb-4 text-base font-medium transition-all whitespace-nowrap">
                Security
            </button>
        </div>

        <!-- Profile Tab -->
        <div x-show="tab === 'profile'" x-cloak class="space-y-8 animate-in fade-in duration-300">
            <form action="{{ route('dashboard.account.details.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Profile Picture Section -->
                <div class="flex flex-col items-center space-y-4">
                    <div class="relative group">
                        <div class="relative w-32 h-32 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 dark:from-purple-900 dark:to-blue-900 flex items-center justify-center border-4 border-white dark:border-gray-800 shadow-lg overflow-hidden cursor-pointer transition hover:shadow-xl" @click="(function(){ const fileInput = $el.closest('.group').querySelector('input[name=profile_image]'); if(fileInput) fileInput.click(); })()">
                            <input type="file" name="profile_image" class="hidden" accept="image/*" @change="$event.target.form.submit()">
                            @if ($user->profile_image_path)
                                <img src="{{ Storage::url($user->profile_image_path) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @endif
                        </div>
                        <div class="absolute bottom-0 right-0 bg-purple-400 text-white p-2 rounded-full shadow-lg hover:bg-purple-500 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Click to upload profile picture</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" 
                            required />
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" 
                            required />
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 123-4567"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('date_of_birth')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                        <select name="gender"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('company_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Job Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Title</label>
                        <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('job_title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="country" value="{{ old('country', $user->country) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                    <textarea name="bio" rows="4" placeholder="Tell us about yourself... (max 1000 characters)"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none resize-none">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full md:w-48 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-bold py-3 rounded-lg transition shadow-lg active:scale-95">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Billing Tab -->
        <div x-show="tab === 'billing'" x-cloak class="space-y-8 animate-in fade-in duration-300">
            <form action="{{ route('dashboard.account.billing.update') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Legal Company Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Legal Company Name</label>
                        <input type="text" name="legal_company_name" value="{{ old('legal_company_name', $brand->setup_data['billing']['legal_company_name'] ?? '') }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('legal_company_name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- VAT ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">VAT ID</label>
                        <input type="text" name="vat_id" value="{{ old('vat_id', $brand->setup_data['billing']['vat_id'] ?? '') }}" placeholder="Optional"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('vat_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Billing Address</label>
                        <input type="text" name="billing_address" value="{{ old('billing_address', $brand->setup_data['billing']['billing_address'] ?? '') }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('billing_address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing City -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City</label>
                        <input type="text" name="billing_city" value="{{ old('billing_city', $brand->setup_data['billing']['billing_city'] ?? '') }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('billing_city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="billing_country" value="{{ old('billing_country', $brand->setup_data['billing']['billing_country'] ?? '') }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('billing_country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Billing Postal Code -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Postal Code</label>
                        <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code', $brand->setup_data['billing']['billing_postal_code'] ?? '') }}"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" />
                        @error('billing_postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="w-full md:w-48 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-bold py-3 rounded-lg transition shadow-lg active:scale-95">
                    Save Billing Info
                </button>
            </form>
        </div>

        <!-- Password Tab -->
        <div x-show="tab === 'password'" x-cloak class="space-y-8 animate-in fade-in duration-300" x-data="{ showPass: { old: false, new: false, confirm: false } }">
            <form action="{{ route('dashboard.account.password.update') }}" method="POST" class="space-y-6 max-w-md">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Password *</label>
                    <div class="relative">
                        <input :type="showPass.old ? 'text' : 'password'" name="current_password" placeholder="Enter your current password"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 pr-12 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" 
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password *</label>
                    <div class="relative">
                        <input :type="showPass.new ? 'text' : 'password'" name="password" placeholder="Enter new password"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 pr-12 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" 
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password *</label>
                    <div class="relative">
                        <input :type="showPass.confirm ? 'text' : 'password'" name="password_confirmation" placeholder="Confirm new password"
                            class="w-full h-11 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 pr-12 text-sm text-gray-900 dark:text-white focus:border-purple-400 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-800 transition outline-none" 
                            required />
                        <button type="button" @click="showPass.confirm = !showPass.confirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full md:w-48 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-bold py-3 rounded-lg transition shadow-lg active:scale-95">
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
                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4 font-medium">
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
