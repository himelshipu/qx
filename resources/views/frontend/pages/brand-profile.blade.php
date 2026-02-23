@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-20" 
     x-data="{ 
        tab: 'details',
        selectedCats: {{ json_encode(old('categories', $brand->setup_data['categories'] ?? [])) }},
        
        profileFile: null,
        profilePreview: null,
        
        isDraggingCover: false,
        coverFile: null,
        coverPreview: null,

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
            this.$refs.profileInput.value = '';
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
            this.$refs.coverInput.value = '';
        }
     }'
     @profile-preview-updated.window="profilePreview = $event.detail"
     @cover-preview-updated.window="coverPreview = $event.detail">
    
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-start">
            <a href="/brand-profile/" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-200 transition-colors">
                <x-icons.arrow-left class="w-4 h-4" />
                Back to Profile
            </a>
        </div>

        <h1 class="text-4xl font-medium text-gray-900 dark:text-white mb-8 text-start">Edit Profile</h1>

        @if (session('status') === 'profile-updated')
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-lg">
                Profile updated successfully.
            </div>
        @endif

        <div class="flex gap-10 border-b border-gray-100 dark:border-gray-800 mb-8">
            <button @click="tab = 'details'" :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Details</button>
            <button @click="tab = 'social'" :class="tab === 'social' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Social Media</button>
            <button @click="tab = 'images'" :class="tab === 'images' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" class="pb-4 text-base font-medium transition-all">Images</button>
        </div>

        <form action="{{ route('dashboard.brand.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="categories" x-bind:value="JSON.stringify(selectedCats)">
            
            <div x-show="tab === 'details'" x-cloak class="space-y-8 text-start">
                <div class="relative w-full" x-data="locationPicker" @click.away="showDropdown = false">
                    <label class="mb-2 block text-base font-medium text-gray-800 dark:text-white">Location</label>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            name="location"
                            x-model="search"
                            value="{{ old('location', $brand->setup_data['location'] ?? '') }}"
                            @input.debounce.500ms="fetchLocations()"
                            @focus="if(results.length > 0) showDropdown = true"
                            placeholder="Search for a city (e.g. USA)..."
                            class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent pl-4 pr-10 text-sm text-gray-800 dark:text-white focus:border-gray-400 focus:ring-0 outline-none" 
                        />

                        <button x-show="search.length > 0" @click="search = ''; results = []; showDropdown = false;" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                            <x-icons.x class="h-5 w-5" />
                        </button>
                    </div>

                    <div 
                        x-show="showDropdown && results.length > 0" 
                        x-cloak
                        x-transition
                        class="absolute left-0 right-0 top-full z-50 mt-1 max-h-60 overflow-y-auto rounded-xl border border-gray-100 bg-white shadow-2xl dark:border-gray-800 dark:bg-gray-900"
                    >
                        <template x-for="(loc, index) in results" :key="index">
                            <div 
                                @click="select(loc)"
                                class="cursor-pointer border-b border-gray-50 px-4 py-3 last:border-none hover:bg-blue-50/50 dark:border-gray-800 dark:hover:bg-gray-800 transition-colors"
                            >
                                <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="loc.city"></span>
                                <span class="ml-1 text-sm text-gray-400" x-text="loc.country"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-base font-medium text-gray-800 dark:text-white">Description</label>
                    <textarea name="description" rows="6" placeholder="What do you sell? What is your mission? Be specific as this is how influencers learn more about your brand."
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent p-4 text-sm text-gray-800 dark:text-white focus:border-gray-400 focus:ring-0 focus:outline-none placeholder:text-gray-400">{{ old('description', $brand->setup_data['description'] ?? '') }}</textarea>
                </div>

                <div>
                    <label class="mb-4 block text-base font-medium text-gray-800 dark:text-white">Categories</label>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $cats = ['Beauty', 'Fashion', 'Travel', 'Health & Fitness', 'Food & Drink', 'Comedy & Entertainment', 'Art & Photography', 'Family & Children', 'Music & Dance', 'Entrepreneur & Business', 'Education', 'Animals & Pets', 'Gaming', 'Technology', 'Athlete & Sports', 'Adventure & Outdoors', 'Healthcare', 'Automotive', 'Skilled Trades', 'Cannabis'];
                        @endphp
                        @foreach($cats as $cat)
                        <button type="button" @click="toggleCat('{{ $cat }}')"
                                :class="selectedCats.includes('{{ $cat }}') ? 'bg-black text-white border-transparent' : 'bg-white text-gray-600 border-gray-200 dark:bg-transparent dark:border-gray-800 dark:text-gray-400'"
                                class="px-4 py-1.5 rounded-lg border text-[13px] font-medium transition-all hover:bg-purple-400 hover:text-white hover:border-transparent">
                            {{ $cat }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div x-show="tab === 'social'" x-cloak class="space-y-8 text-start">
                <h5 class="text-base font-medium text-gray-800 dark:text-white">Social Links</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-400 mb-2">Website</label>
                        <input type="text" name="website" value="{{ old('website', $brand->setup_data['social']['website'] ?? '') }}" placeholder="https://yourwebsite.com" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white focus:border-gray-400 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-400 mb-2">Instagram</label>
                        <input type="text" name="instagram" value="{{ old('instagram', $brand->setup_data['social']['instagram'] ?? '') }}" placeholder="https://instagram.com/yourprofile" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white focus:border-gray-400 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-400 mb-2">Tiktok</label>
                        <input type="text" name="tiktok" value="{{ old('tiktok', $brand->setup_data['social']['tiktok'] ?? '') }}" placeholder="https://tiktok.com/@yourprofile" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white focus:border-gray-400 focus:ring-0 focus:outline-none">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-gray-400 mb-2">YouTube</label>
                        <input type="text" name="youtube" value="{{ old('youtube', $brand->setup_data['social']['youtube'] ?? '') }}" placeholder="https://youtube.com/c/yourchannel" class="h-11 w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-4 text-sm dark:text-white focus:border-gray-400 focus:ring-0 focus:outline-none">
                    </div>
                </div>
            </div>

            <div x-show="tab === 'images'" x-cloak class="space-y-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="relative group">
                        <div class="relative w-28 h-28 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-4 border-white shadow-md overflow-hidden cursor-pointer transition hover:opacity-90" @click="$refs.profileInput.click()">
                            <input x-ref="profileInput" type="file" name="profile_image" class="hidden" @change="handleProfileUpload($event)" accept="image/*">
                            <template x-if="profilePreview">
                                <img :src="profilePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview && '{{ $brand->setup_data['profile_image'] ?? '' }}'">
                                <img src="{{ isset($brand->setup_data['profile_image']) ? Storage::url($brand->setup_data['profile_image']) : '' }}" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!profilePreview && !'{{ $brand->setup_data['profile_image'] ?? '' }}'">
                                <x-icons.user-circle class="w-12 h-12 text-gray-300" />
                            </template>
                        </div>

                        <div x-show="!profilePreview && !'{{ $brand->setup_data['profile_image'] ?? '' }}'" class="absolute -bottom-6 left-1/2 -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-opacity bg-black text-white text-[10px] font-bold px-2 py-1 rounded whitespace-nowrap z-20">
                            No file chosen
                        </div>

                        <button x-show="profilePreview" @click.stop="removeProfile" type="button" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 transition">
                            <x-icons.x class="w-4 h-4" stroke-width="3" />
                        </button>
                    </div>
                </div>

                <div class="relative w-full">
                    <div class="absolute top-4 left-4 z-20">
                        <span class="bg-white/90 dark:bg-gray-800 px-2 py-1 rounded text-[10px] font-bold text-gray-500 border border-gray-100 dark:border-gray-700 uppercase">Cover Photo</span>
                    </div>

                    <div @click="$refs.coverInput.click()"
                         :class="isDraggingCover ? 'border-purple-400 bg-purple-50' : 'border-gray-200 bg-gray-50 dark:bg-gray-900/30'"
                         class="relative w-full aspect-video rounded-[2.5rem] border-2 border-dashed flex flex-col items-center justify-center cursor-pointer transition-all overflow-hidden hover:bg-gray-100">
                        
                        <input x-ref="coverInput" type="file" name="cover_image" class="hidden" @change="handleCoverFiles($event.target.files)" accept="image/*">
                        
                        <template x-if="coverPreview">
                            <img :src="coverPreview" class="absolute inset-0 w-full h-full object-cover rounded-[2.5rem] z-0 transition-opacity">
                        </template>
                        
                        <template x-if="!coverPreview && '{{ $brand->setup_data['cover_image'] ?? '' }}'">
                            <img src="{{ isset($brand->setup_data['cover_image']) ? Storage::url($brand->setup_data['cover_image']) : '' }}" class="absolute inset-0 w-full h-full object-cover rounded-[2.5rem] z-0 transition-opacity">
                        </template>

                        <div x-show="!coverPreview && !'{{ $brand->setup_data['cover_image'] ?? '' }}'" class="flex flex-col items-center z-10">
                            <h3 class="text-2xl md:text-4xl font-bold text-gray-300 dark:text-gray-700 mb-2">Optional Cover Photo</h3>
                            <div class="bg-black text-white text-[10px] font-bold px-2 py-1 rounded mb-8" x-text="coverFile ? coverFile.name : 'No file chosen'"></div>
                            <div class="w-32 h-32 text-gray-200 dark:text-gray-800 mb-6">
                                <x-icons.photo class="w-full h-full" />
                            </div>
                            <p class="text-xl font-bold text-gray-300">Click to upload</p>
                        </div>
                    </div>

                    <template x-if="coverPreview || '{{ $brand->setup_data['cover_image'] ?? '' }}'">
                        <button @click.stop="removeCover" type="button" class="absolute top-4 right-4 z-30 flex items-center gap-2 bg-red-500 text-white px-4 py-2 rounded-xl font-bold text-xs shadow-xl hover:bg-red-600 transition-all active:scale-95">
                            <x-icons.trash class="w-4 h-4" stroke-width="2.5" />
                            Remove Photo
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex justify-end mt-16">
                <button type="submit" class="bg-[#1A1A1A] hover:bg-purple-400 w-full sm:w-56 py-4 rounded-2xl text-sm font-medium text-white transition shadow-xl active:scale-95 uppercase">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('locationPicker', () => ({
        search: '{{ old('location', $brand->setup_data['location'] ?? '') }}',
        results: [],
        showDropdown: false,

        async fetchLocations() {
            if (this.search.length < 3) {
                this.results = [];
                this.showDropdown = false;
                return;
            }

            try {
                let url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.search)}&addressdetails=1&limit=5`;
                let response = await fetch(url, {
                    headers: { 'User-Agent': 'QX-Influencer-Platform' }
                });
                let data = await response.json();

                this.results = data.map(item => ({
                    city: item.address.city || item.address.town || item.address.village || item.display_name.split(',')[0],
                    country: item.address.country
                }));

                this.showDropdown = true;
            } catch (error) {
                console.error('Error:', error);
            }
        },

        select(loc) {
            this.search = `${loc.city}, ${loc.country}`;
            this.showDropdown = false;
            this.results = [];
        }
    }))
})
</script>