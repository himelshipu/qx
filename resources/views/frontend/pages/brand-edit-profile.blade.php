@extends('frontend.layouts.app')

@section('content')
<div class="max-w-5xl mx-auto" 
     x-data="{
        tab: 'details',
        selectedCats: @json(old('categories') ?? $brand?->categories ?? []),
        profileFile: null,
        profilePreview: null,
        isDraggingCover: false,
        coverFile: null,
        coverPreview: null,
        init() {
            // Ensure selectedCats is always an array
            if (!Array.isArray(this.selectedCats)) {
                this.selectedCats = [];
            }
        },
        toggleCat(cat) {
            if (this.selectedCats.includes(cat)) {
                this.selectedCats = this.selectedCats.filter(i => i !== cat);
            } else {
                this.selectedCats.push(cat);
            }
        },
        handleProfileUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.profileFile = file;
                this.profilePreview = URL.createObjectURL(file);
            }
        },
        removeProfile() {
            this.profileFile = null;
            this.profilePreview = null;
            if (this.$refs.profileInput) this.$refs.profileInput.value = '';
        },
        handleCoverFiles(files) {
            const file = files[0];
            if (file && ['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                this.coverFile = file;
                this.coverPreview = URL.createObjectURL(file);
            }
        },
        removeCover() {
            this.coverFile = null;
            this.coverPreview = null;
            if (this.$refs.coverInput) this.$refs.coverInput.value = '';
        }
     }"
     @load="init()">
    
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-start">
            <a href="/dashboard/brand-profile/edit" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full text-sm font-medium text-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
        </div>

        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">Edit Brand Profile</h1>
        <p class="text-sm text-gray-800 dark:text-gray-400">Manage your brand information and make it stand out</p>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-xl font-medium flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Profile updated successfully!
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
        <div class="flex gap-8 border-b border-gray-200 dark:border-gray-800 mb-8 overflow-x-auto pb-4 mt-8">
            <button @click="tab = 'details'" :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Details</button>
            <button @click="tab = 'social'" :class="tab === 'social' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Social Media</button>
            <button @click="tab = 'images'" :class="tab === 'images' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-300'" class="pb-4 text-base font-medium transition-all whitespace-nowrap">Images</button>
        </div>

        <form action="{{ route('dashboard.brand.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <template x-for="cat in selectedCats" :key="cat">
                <input type="hidden" name="categories[]" :value="cat">
            </template>
            
            <!-- Details Tab -->
            <div x-show="tab === 'details'" x-cloak class="space-y-8 text-start animate-in fade-in duration-300">
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Brand Name <span class="text-error-500"> *</span></label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $brand->brand_name ?? '') }}" required
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('brand_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Location</label>
                    <input type="text" name="location" value="{{ old('location', $brand->location ?? '') }}" placeholder="E.g. New York, NY"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('location')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">City</label>
                        <input type="text" name="city" value="{{ old('city', $brand->city ?? '') }}" placeholder="E.g. New York"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('city')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Country</label>
                        <input type="text" name="country" value="{{ old('country', $brand->country ?? '') }}" placeholder="E.g. United States"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('country')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $brand->phone ?? '') }}" placeholder="+1 (555) 123-4567"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $brand->email ?? '') }}" placeholder="E.g. brand@example.com"
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Description</label>
                    <textarea name="description" rows="5" placeholder="Tell us about your brand, mission, and what you do... (max 1000 characters)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('description', $brand->description ?? ''   ) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-4">Categories</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $cats = ['Beauty', 'Fashion', 'Travel', 'Health & Fitness', 'Food & Drink', 'Comedy & Entertainment', 'Art & Photography', 'Family & Children', 'Music & Dance', 'Entrepreneur & Business', 'Education', 'Animals & Pets', 'Gaming', 'Technology', 'Athlete & Sports', 'Adventure & Outdoors', 'Healthcare', 'Automotive', 'Skilled Trades', 'Cannabis'];
                        @endphp
                        @foreach($cats as $cat)
                        <button type="button" @click="toggleCat('{{ $cat }}')"
                                :class="selectedCats.includes('{{ $cat }}') ? 'bg-purple-500 text-white border-transparent' : 'bg-white text-gray-800 border-gray-300 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400'"
                                class="px-4 py-2 rounded-lg border text-sm font-medium transition-all hover:bg-purple-500 hover:text-white hover:border-transparent active:scale-95">
                            {{ $cat }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Social Media Tab -->
            <div x-show="tab === 'social'" x-cloak class="space-y-8 text-start animate-in fade-in duration-300">
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website', $brand->website ?? '') }}" placeholder="https://yourwebsite.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('website')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Instagram</label>
                    <input type="url" name="instagram" value="{{ old('instagram', $brand?->social_links['instagram'] ?? '') }}" placeholder="https://instagram.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('instagram')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">TikTok</label>
                    <input type="url" name="tiktok" value="{{ old('tiktok', $brand?->social_links['tiktok'] ?? '') }}" placeholder="https://tiktok.com/@yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('tiktok')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">YouTube</label>
                    <input type="url" name="youtube" value="{{ old('youtube', $brand?->social_links['youtube'] ?? '') }}" placeholder="https://youtube.com/c/yourchannel"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('youtube')
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
                            <template x-if="!profilePreview && {{ !is_null($brand?->profile_image_path) ? 'true' : 'false' }}">
                                <img src="{{ $brand?->profile_image_path ? Storage::url($brand->profile_image_path) : '' }}" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview && !{{ !is_null($brand?->profile_image_path) ? 'true' : 'false' }}">
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
                         :class="isDraggingCover ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-300 dark:border-gray-700'"
                         class="relative w-full aspect-video rounded-2xl border-2 border-dashed flex flex-col items-center justify-center cursor-pointer transition-all hover:border-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 overflow-hidden">
                        
                        <input x-ref="coverInput" type="file" name="cover_image" class="hidden" @change="handleCoverFiles($event.target.files)" accept="image/*">
                        
                        <template x-if="coverPreview">
                            <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover rounded-2xl z-0">
                        </template>
                        
                        <template x-if="!coverPreview && {{ !is_null($brand?->cover_image_path) ? 'true' : 'false' }}">
                            <img src="{{ $brand?->cover_image_path ? Storage::url($brand->cover_image_path) : '' }}" class="absolute inset-0 w-full h-full object-cover rounded-2xl z-0">
                        </template>

                        <div x-show="!coverPreview && !{{ !is_null($brand?->cover_image_path) ? 'true' : 'false' }}" class="flex flex-col items-center z-10">
                            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                            </div>
                            <p class="text-lg font-bold text-gray-800 dark:text-gray-400 mb-1">Click to upload cover photo</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Recommended size: 1200x630px</p>
                        </div>
                    </div>

                    <template x-if="coverPreview || {{ !is_null($brand?->cover_image_path) ? 'true' : 'false' }}">
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
                <button type="submit" class="bg-[#222] shadow-theme-xs hover:bg-purple-400 flex items-center justify-center rounded-lg px-4 py-3 text-sm font-medium text-white transitionactive:scale-95">
                    Save Changes
                </button>
            </div>
        </form>

        <!-- Brand Status Section -->
        <div class="mt-20 pt-10 border-t border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Brand Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Verification Status -->
                {{-- <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Brand Verification</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $brand->is_verified ? '✓ Verified' : '○ Not Verified' }}</p>
                        </div>
                        <div class="text-4xl {{ $brand->is_verified ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $brand->is_verified ? '✓' : '○' }}
                        </div>
                    </div>
                </div> --}}

                <!-- Active Status -->
                {{-- <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Brand Status</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $brand->is_active ? '✓ Active' : '✗ Inactive' }}</p>
                        </div>
                        <div class="text-4xl {{ $brand->is_active ? 'text-green-500' : 'text-red-500' }}">
                            {{ $brand->is_active ? '✓' : '✗' }}
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
