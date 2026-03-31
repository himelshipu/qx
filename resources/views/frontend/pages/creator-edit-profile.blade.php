@extends('frontend.layouts.app')

@section('content')

<div class="min-h-screen" 
     x-data="{ 
        tab: 'details',
        
        // Profile Image State
        profileFile: null,
        profilePreview: null,
        
        // Multiple Cover Photos State (Array of objects)
        coverPhotos: [
            // Dummy data based on your ref image
            { id: 1, preview: '{{ asset('images/creator/creator-profile-01.webp') }}', name: 'img1.png' },
            { id: 2, preview: '{{ asset('images/creator/creator-profile-02.webp') }}', name: 'img2.png' },
            { id: 3, preview: '{{ asset('images/creator/creator-profile-03.webp') }}', name: 'img3.png' },
        ],

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
            this.$refs.profileInput.value = '';
        },

        handleMultipleCoverUpload(e) {
            const files = Array.from(e.target.files);
            const remainingSlots = 6 - this.coverPhotos.length;
            
            files.slice(0, remainingSlots).forEach(file => {
                this.coverPhotos.push({
                    id: Date.now() + Math.random(),
                    file: file,
                    preview: URL.createObjectURL(file),
                    name: file.name
                });
            });
            e.target.value = ''; // Reset input
        },

        removeCoverPhoto(index) {
            this.coverPhotos.splice(index, 1);
        }
    }">
    
    <div class="max-w-4xl mx-auto">
        <!-- 1. Back Button -->
         <div class="mb-6 text-start">
            <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full text-sm font-medium text-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Back
            </a>
        </div>

        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-6">Edit Profile</h1>

        <!-- 2. Tab Navigation -->
        <div class="flex gap-10 border-b border-gray-100 dark:border-gray-800 mb-8">
            <button @click="tab = 'details'" :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Details</button>
            <button @click="tab = 'social'" :class="tab === 'social' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Social Media</button>
            <button @click="tab = 'images'" :class="tab === 'images' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Images</button>
        </div>

        <form action="{{ route('dashboard.creator.profile.update', ['slug' => $slug]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- TAB 1: DETAILS -->
            <div x-show="tab === 'details'" x-cloak class="space-y-6 text-start">

                <!-- Display Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Display Name</label>
                    <input type="text" name="display_name" value="{{ old('display_name', $creator->display_name ?? '') }}" placeholder="Enter your display name"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('display_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                 <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Location</label>
                    <input type="text" name="location" value="" placeholder="E.g. New York, NY"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('location')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Title Name</label>
                    <input type="text" name="title_name" value="{{ old('title_name', $creator->title_name ?? '') }}" placeholder="Enter your title (e.g. Beauty Influencer, Fitness Coach, etc.)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('title_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Description</label>
                    <textarea name="description" rows="5" placeholder="Tell us about yourself, your content, and what you do... (max 1000 characters)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('description', $creator->description ?? ''   ) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Audience -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Who is your audience?</label>
                    <textarea name="audience" rows="5" placeholder="Describe your target audience (max 1000 characters)"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('audience', $creator->audience ?? ''   ) }}</textarea>
                    @error('audience')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Brands work with -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">What brands have you worked with?</label>
                    <input type="text" name="brands_worked_with" value="{{ old('brands_worked_with', $creator->brands_worked_with ?? '') }}" placeholder="Enter brands you've worked with"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('brands_worked_with')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Gender</label>
                    <select name="gender"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- TAB 2: SOCIAL MEDIA -->
            <div x-show="tab === 'social'" x-cloak class="space-y-8 text-start animate-in fade-in duration-300">
                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Website</label>
                    <input type="url" name="website" value="{{ old('website', $creator->user->website ?? '') }}" placeholder="https://yourwebsite.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('website')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Instagram</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $creator->socialLinks?->instagram_url ?? '') }}" placeholder="https://instagram.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('instagram_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">TikTok</label>
                    <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $creator->socialLinks?->tiktok_url ?? '') }}" placeholder="https://tiktok.com/@yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('tiktok_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Facebook</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $creator->socialLinks?->facebook_url ?? '') }}" placeholder="https://facebook.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('facebook_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">X (Twitter)</label>
                    <input type="url" name="x_url" value="{{ old('x_url', $creator->socialLinks?->x_url ?? '') }}" placeholder="https://x.com/yourprofile"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('x_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">YouTube</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $creator->socialLinks?->youtube_url ?? '') }}" placeholder="https://youtube.com/c/yourchannel"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('youtube_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Other Website</label>
                    <input type="url" name="other_url" value="{{ old('other_url', $creator->socialLinks?->other_url ?? '') }}" placeholder="https://yourwebsite.com"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('other_url')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- TAB 3: IMAGES -->
            <div x-show="tab === 'images'" x-cloak class="space-y-12 text-center">
                
                <!-- Profile Image Area -->
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <div class="relative w-28 h-28 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-4 border-white shadow-md overflow-hidden cursor-pointer transition hover:opacity-90" @click="$refs.profileInput.click()">
                            <input x-ref="profileInput" type="file" class="hidden" @change="handleProfileUpload($event)" accept="image/*">
                            
                            <template x-if="profilePreview">
                                <img :src="profilePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview">
                                <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </template>
                        </div>

                        <!-- Tooltip -->
                        <div x-show="!profilePreview" class="absolute -bottom-8 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-black text-white text-[10px] font-bold px-2 py-1 rounded whitespace-nowrap z-20">
                            No file chosen
                        </div>

                        <!-- Remove Profile Icon -->
                        <button x-show="profilePreview" @click.stop="removeProfile" type="button" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Multi-Cover Grid Area -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- Loop through uploaded cover photos -->
                    <template x-for="(photo, index) in coverPhotos" :key="photo.id">
                        <div class="relative aspect-[3/4] rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 group shadow-sm">
                            <img :src="photo.preview" class="w-full h-full object-cover">
                            
                            <!-- Label Badge -->
                            <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm border border-gray-100 px-2 py-0.5 rounded text-[9px] font-bold text-gray-600 uppercase">
                                Cover Photo <span x-text="index + 1"></span>
                            </div>

                            <!-- Delete Menu Toggle -->
                            <button @click="removeCoverPhoto(index)" class="absolute top-3 right-3 bg-black/40 hover:bg-red-500 text-white p-1 rounded-full transition-colors backdrop-blur-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>

                    <!-- Empty Upload Slot -->
                    <template x-if="coverPhotos.length < 6">
                        <div class="relative aspect-[3/4] rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30 flex flex-col items-center justify-center group cursor-pointer hover:bg-gray-100 transition-all" 
                            @click="$refs.multiCoverInput.click()">
                            
                            <input x-ref="multiCoverInput" type="file" class="hidden" multiple @change="handleMultipleCoverUpload($event)" accept="image/*">

                            <div class="flex flex-col items-center gap-4 relative">
                                <button type="button" class="flex items-center gap-2 bg-[#222] text-white px-4 py-2 rounded-lg font-bold text-xs shadow-lg hover:bg-black transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"/></svg>
                                    Upload Photos
                                </button>
                                
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -bottom-8 bg-black text-white text-[10px] font-bold px-2 py-1 rounded whitespace-nowrap z-20">
                                    No file chosen
                                </div>

                                <div class="w-20 h-20 text-gray-200 mt-4">
                                    <svg class="w-full h-full" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-[11px] font-black uppercase text-gray-300 tracking-widest">Click to upload</p>
                            </div>
                        </div>
                    </template>

                </div>

            </div>

            <!-- Global Save Button -->
            <div class="flex justify-end mt-16">
                <button type="submit" class="bg-[#1A1A1A] hover:bg-purple-400 w-full sm:w-56 py-4 rounded-xl text-sm font-medium text-white transition shadow-xl active:scale-95 uppercase">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
