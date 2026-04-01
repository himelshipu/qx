@extends('frontend.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto"
     x-data="{
        tab: @js(old('active_tab', 'details')),
        errors: {},
        isSaving: false,
        profilePreview: null,
        coverPreview: null,
        init() {
        },
        handleProfileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.profilePreview = URL.createObjectURL(file);
            }
        },
        removeProfile() {
            this.profilePreview = null;
            if (this.$refs.profileInput) this.$refs.profileInput.value = '';
        },
        handleCoverUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.coverPreview = URL.createObjectURL(file);
            }
        },
        removeCover() {
            this.coverPreview = null;
            if (this.$refs.coverInput) this.$refs.coverInput.value = '';
        },
        fieldError(field) {
            const err = this.errors[field];
            if (!err) return '';
            return Array.isArray(err) ? err[0] : err;
        },
        normalizeUrl(value) {
            if (!value) return '';
            const trimmed = value.trim();
            if (!trimmed) return '';
            if (/^https?:\/\//i.test(trimmed)) return trimmed.replace(/\/$/, '');
            return ('https://' + trimmed.replace(/^\/+/, '')).replace(/\/$/, '');
        },
        normalizeSocialInput(event) {
            event.target.value = this.normalizeUrl(event.target.value);
        },
        async submitForm(event) {
            this.errors = {};

            const form = event.target;
            const source = new FormData(form);
            const payload = new FormData();
            payload.append('_token', source.get('_token'));
            payload.append('active_tab', this.tab);

            const fieldsByTab = {
                details: ['brand_name', 'industry', 'bio', 'address_line', 'city', 'country', 'postal_code', 'phone'],
                social: ['website', 'instagram', 'tiktok', 'facebook', 'x', 'youtube', 'linkedin'],
                images: ['profile_image', 'cover_image']
            };

            if (this.tab === 'details' && !String(source.get('brand_name') || '').trim()) {
                this.errors = { brand_name: ['Brand name is required.'] };
                window.toast?.error('Brand name is required.');
                return;
            }

            for (const field of (fieldsByTab[this.tab] || [])) {
                if (!source.has(field)) continue;
                const value = source.get(field);

                if (this.tab === 'social' && typeof value === 'string') {
                    const normalized = this.normalizeUrl(value);
                    const el = form.querySelector(`[name='${field}']`);
                    if (el) el.value = normalized;
                    payload.append(field, normalized);
                    continue;
                }

                if (value instanceof File) {
                    if (value.name) payload.append(field, value);
                } else {
                    payload.append(field, value ?? '');
                }
            }

            this.isSaving = true;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: payload
                });

                const data = await response.json();

                if (response.status === 422) {
                    this.errors = data.errors || {};
                    const firstError = Object.values(this.errors)[0]?.[0] || 'Validation failed.';
                    window.toast?.error(firstError);
                    return;
                }

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update profile.');
                }

                window.toast?.success(data.message || 'Profile updated successfully.');
            } catch (error) {
                window.toast?.error(error.message || 'Error updating profile.');
            } finally {
                this.isSaving = false;
            }
        }
     }"
     x-init="init()">

    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-start">
            <a href="{{ route('dashboard.brand.profile.edit', ['slug' => $slug]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full text-sm font-medium text-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
        </div>

        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">Edit Profile</h1>
        <p class="text-sm text-gray-800 dark:text-gray-400">Manage your brand information and make it stand out</p>

        <!-- Tab Navigation -->
        <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto mt-8">
            <button @click="tab = 'details'" :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Details</button>
            <button @click="tab = 'social'" :class="tab === 'social' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Social Media</button>
            <button @click="tab = 'images'" :class="tab === 'images' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Images</button>
        </div>

        <form action="{{ route('dashboard.brand.profile.update', ['slug' => $slug]) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitForm">
            @csrf
            <input type="hidden" name="active_tab" :value="tab">

            <!-- Details Tab -->
            <div x-show="tab === 'details'" x-cloak class="space-y-8 text-start animate-in fade-in duration-300">
                <!-- Brand Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Brand Name <span class="text-red-500">*</span></label>
                    <input type="text" name="brand_name" required value="{{ old('brand_name', $brand->brand_name ?? '') }}" placeholder="Enter your brand name"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('brand_name')" x-text="fieldError('brand_name')" class="mt-1 text-xs text-red-500"></p>
                    @error('brand_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Industry -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $brand->industry ?? '') }}" placeholder="E.g. Fashion, Technology, Finance"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('industry')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Bio</label>
                    <textarea name="bio" rows="3" placeholder="A short bio about your brand (max 500 characters)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Address Line</label>
                    <input type="text" name="address_line" value="{{ old('address_line', $user->address_line ?? '') }}" placeholder="E.g. 123 Main Street"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('address_line')" x-text="fieldError('address_line')" class="mt-1 text-xs text-red-500"></p>
                    @error('address_line')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}" placeholder="E.g. New York"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('city')" x-text="fieldError('city')" class="mt-1 text-xs text-red-500"></p>
                    @error('city')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country ?? '') }}" placeholder="E.g. United States"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('country')" x-text="fieldError('country')" class="mt-1 text-xs text-red-500"></p>
                    @error('country')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Postal Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Postal Code</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}" placeholder="E.g. 10001"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('postal_code')" x-text="fieldError('postal_code')" class="mt-1 text-xs text-red-500"></p>
                    @error('postal_code')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Phone</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="E.g. +1 (555) 123-4567"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('phone')" x-text="fieldError('phone')" class="mt-1 text-xs text-red-500"></p>
                    @error('phone')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Social Media Tab -->
            <div x-show="tab === 'social'" x-cloak class="space-y-8 text-start animate-in fade-in duration-300">
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Website</label>
                    <input type="url" name="website" @blur="normalizeSocialInput($event)" value="{{ old('website', $brand->website ?? '') }}" placeholder="https://yourwebsite.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('website')" x-text="fieldError('website')" class="mt-1 text-xs text-red-500"></p>
                    @error('website')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Instagram</label>
                    <input type="url" name="instagram" @blur="normalizeSocialInput($event)" value="{{ old('instagram', $brand?->socialLinks?->instagram_url ?? '') }}" placeholder="https://instagram.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('instagram')" x-text="fieldError('instagram')" class="mt-1 text-xs text-red-500"></p>
                    @error('instagram')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">TikTok</label>
                    <input type="url" name="tiktok" @blur="normalizeSocialInput($event)" value="{{ old('tiktok', $brand?->socialLinks?->tiktok_url ?? '') }}" placeholder="https://tiktok.com/@yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('tiktok')" x-text="fieldError('tiktok')" class="mt-1 text-xs text-red-500"></p>
                    @error('tiktok')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Facebook</label>
                    <input type="url" name="facebook" @blur="normalizeSocialInput($event)" value="{{ old('facebook', $brand?->socialLinks?->facebook_url ?? '') }}" placeholder="https://facebook.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('facebook')" x-text="fieldError('facebook')" class="mt-1 text-xs text-red-500"></p>
                    @error('facebook')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">X</label>
                    <input type="url" name="x" @blur="normalizeSocialInput($event)" value="{{ old('x', $brand?->socialLinks?->x_url ?? '') }}" placeholder="https://x.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('x')" x-text="fieldError('x')" class="mt-1 text-xs text-red-500"></p>
                    @error('x')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">YouTube</label>
                    <input type="url" name="youtube" @blur="normalizeSocialInput($event)" value="{{ old('youtube', $brand?->socialLinks?->youtube_url ?? '') }}" placeholder="https://youtube.com/c/yourchannel"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('youtube')" x-text="fieldError('youtube')" class="mt-1 text-xs text-red-500"></p>
                    @error('youtube')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">LinkedIn</label>
                    <input type="url" name="linkedin" @blur="normalizeSocialInput($event)" value="{{ old('linkedin', $brand?->socialLinks?->linkedin_url ?? '') }}" placeholder="https://linkedin.com/company/yourcompany"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    <p x-show="fieldError('linkedin')" x-text="fieldError('linkedin')" class="mt-1 text-xs text-red-500"></p>
                    @error('linkedin')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Images Tab -->
            <div x-show="tab === 'images'" x-cloak class="space-y-12 text-center animate-in fade-in duration-300">
                <!-- Profile Picture -->
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <div class="relative w-32 h-32 rounded-full bg-gradient-to-br from-purple-100 to-blue-100 dark:from-purple-900 dark:to-blue-900 flex items-center justify-center border-4 border-white dark:border-gray-800 shadow-lg overflow-hidden cursor-pointer transition hover:shadow-xl"  @click="$el.closest('.group').querySelector('input[name=profile_image]').click()">
                            <input x-ref="profileInput" type="file" name="profile_image" class="hidden" @change="handleProfileUpload($event)" accept="image/*">
                            <template x-if="profilePreview">
                                <img :src="profilePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview && {{ !is_null($user?->profile_image_path) ? 'true' : 'false' }}">
                                <img src="{{ $user?->profile_image_path ? \App\Helpers\ImageHelper::url($user->profile_image_path) : '' }}" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview && !{{ !is_null($user?->profile_image_path) ? 'true' : 'false' }}">
                                <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </template>
                        </div>
                        <div class="absolute bottom-0 right-0 bg-purple-400 text-white p-2 rounded-full shadow-lg hover:bg-purple-500 transition cursor-pointer">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                        </div>
                        <button x-show="profilePreview" @click.stop="removeProfile" type="button" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Profile Picture</p>
                </div>

                <!-- Cover Image -->
                <div class="relative w-full">
                    <div class="absolute top-4 left-4 z-20">
                        <span class="bg-white/90 dark:bg-gray-800 px-3 py-1 rounded-lg text-[11px] font-bold text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700 uppercase tracking-wide">Cover Photo</span>
                    </div>

                    <div @click="$el.querySelector('input[name=cover_image]').click()"
                         class="relative w-full aspect-video rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 flex flex-col items-center justify-center cursor-pointer transition-all hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 overflow-hidden">

                        <input x-ref="coverInput" type="file" name="cover_image" class="hidden" @change="handleCoverUpload($event)" accept="image/*">

                        <template x-if="coverPreview">
                            <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover rounded-2xl z-0">
                        </template>

                        <template x-if="!coverPreview && {{ !is_null($user?->cover_image_path) ? 'true' : 'false' }}">
                            <img src="{{ $user?->cover_image_path ? \App\Helpers\ImageHelper::url($user->cover_image_path) : '' }}" class="absolute inset-0 w-full h-full object-cover rounded-2xl z-0">
                        </template>

                        <div x-show="!coverPreview && !{{ !is_null($user?->cover_image_path) ? 'true' : 'false' }}" class="flex flex-col items-center z-10">
                            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                            </div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-400 mb-1">Click to upload cover photo</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Recommended size: 1200x630px</p>
                        </div>
                    </div>

                    <template x-if="coverPreview || {{ !is_null($user?->cover_image_path) ? 'true' : 'false' }}">
                        <button @click.stop="removeCover" type="button" class="absolute top-4 right-4 z-30 flex items-center gap-2 bg-red-500 text-white px-3 py-2 rounded-lg font-bold text-xs shadow-lg hover:bg-red-600 transition-all active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Remove
                        </button>
                    </template>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 mt-16 pt-8 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ url('/dashboard') }}" class="px-6 py-3 rounded-lg font-medium text-sm border border-gray-300 dark:border-gray-700 text-gray-800 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    Cancel
                </a>
                <button type="submit" :disabled="isSaving" class="bg-[#222] shadow-theme-xs hover:bg-purple-400 flex items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transitionactive:scale-95 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!isSaving">Save Changes</span>
                    <span x-show="isSaving">Saving...</span>
                </button>
            </div>
        </form>



    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
