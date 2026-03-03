@extends('frontend.layouts.app')

@section('content')
<section class="min-h-screen" 
     x-data="{ 
        openDropdown: false, 
        selectedPackage: '1 Instagram Photo Feed Post',
        activeTab: 'All',
        price: '$200',
        
        // Comprehensive Package List
        packages: [
            { name: '1 Instagram Photo Feed Post', icon: 'instagram', category: 'Instagram' },
            { name: '1 Instagram Reel (60 Seconds)', icon: 'instagram', category: 'Instagram' },
            { name: '1 Instagram Story', icon: 'instagram', category: 'Instagram' },
            { name: '1 Instagram Live (60 Seconds)', icon: 'instagram', category: 'Instagram' },
            { name: '1 TikTok Video (60 Seconds)', icon: 'tiktok', category: 'TikTok' },
            { name: '1 TikTok Story', icon: 'tiktok', category: 'TikTok' },
            { name: '1 TikTok Live (60 Seconds)', icon: 'tiktok', category: 'TikTok' },
            { name: '1 UGC Product Video (60 Seconds)', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Product Photo', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Video Ad (60 Seconds)', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Photo Ad', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Tutorial (60 Seconds)', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Testimonial/Review (60 Seconds)', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Unboxing (60 Seconds)', icon: 'camera', category: 'UGC' },
            { name: '1 UGC Blog (60 Seconds)', icon: 'group', category: 'Others' }
        ],

        // Filter Logic for Tabs
        get filteredPackages() {
            if (this.activeTab === 'All') return this.packages;
            return this.packages.filter(p => p.category === this.activeTab);
        },

        // Sync Function
        selectPackage(name) {
            this.selectedPackage = name;
            this.openDropdown = false;
        }
    }">
    
    <main class="max-w-screen-2xl mx-auto">
        
        <!-- 1. TOP CATEGORIES & EDIT -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-wrap gap-2 text-xl font-semibold text-gray-800 dark:text-gray-300 tracking-tight">
                <span>Tech,</span>
                <span>Tesla,</span>
                <span>Health & Fitness,</span>
                <span>Car,</span>
                <span>Pet</span>
            </div>
            @auth
                @if(optional(Auth::user()->creator)->id === optional($creator)->id)
                    <a href="{{ route('dashboard.creator.profile.edit') }}" class="flex items-center gap-2 px-5 py-2 border border-gray-200 dark:border-gray-800 rounded-lg text-sm font-bold text-[#222] hover:bg-purple-50 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit
                    </a>
                @endif
            @endauth
        </div>

        <!-- 2. PORTRAIT IMAGE GRID -->
        <div class="grid grid-cols-12 gap-4 mb-16 h-[450px] md:h-[600px]">
            <div class="col-span-4 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800">
                <img src="{{ asset('images/creator/creator-profile-01.webp') }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
            </div>
            <div class="col-span-4 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800">
                <img src="{{ asset('images/creator/creator-profile-02.webp') }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
            </div>
            <div class="col-span-4 rounded-xl overflow-hidden relative border border-gray-100 dark:border-gray-800">
                <img src="{{ asset('images/creator/creator-profile-03.webp') }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                <!-- Show All Photos Overlay -->
                <button class="absolute bottom-6 right-6 flex items-center gap-2 bg-white/90 backdrop-blur-md px-5 py-2.5 rounded-2xl text-sm font-bold text-gray-900 border border-gray-100 shadow-xl hover:bg-white transition active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M10 7C10 8.65685 8.65685 10 7 10C5.34315 10 4 8.65685 4 7C4 5.34315 5.34315 4 7 4C8.65685 4 10 5.34315 10 7Z" stroke="#28303F" stroke-width="1.5"/>
                    <path d="M20 17C20 18.6569 18.6569 20 17 20C15.3431 20 14 18.6569 14 17C14 15.3431 15.3431 14 17 14C18.6569 14 20 15.3431 20 17Z" stroke="#28303F" stroke-width="1.5"/>
                    <path d="M14 6C14 4.89543 14.8954 4 16 4H18C19.1046 4 20 4.89543 20 6V8C20 9.10457 19.1046 10 18 10H16C14.8954 10 14 9.10457 14 8V6Z" stroke="#28303F" stroke-width="1.5"/>
                    <path d="M4 16C4 14.8954 4.89543 14 6 14H8C9.10457 14 10 14.8954 10 16V18C10 19.1046 9.10457 20 8 20H6C4.89543 20 4 19.1046 4 18V16Z" stroke="#28303F" stroke-width="1.5"/>
                    </svg>
                    Show All Photos
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-16">
            <!-- LEFT COLUMN: CREATOR INFO -->
            <div class="flex-1 space-y-6">
                
                <!-- Profile Identity -->
                <div class="flex items-center gap-6">
                    <img src="{{ asset('images/creator/creator-profile.webp') }}" class="w-24 h-24 rounded-full border-2 border-gray-50 shadow-md object-cover" alt="Avatar">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-300 tracking-tight leading-none mb-2">Zahidangeless</h1>
                        <p class="text-sm text-gray-400 font-medium mb-4">Los Angeles, CA, United States</p>
                        <div class="flex gap-3">
                            <span class="px-4 py-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-full text-[12px] font-medium text-gray-500 flex items-center gap-2 shadow-sm">
                                <x-icons.tiktok class="w-5 h-5 text-[#28303F] dark:text-white" />
                                81.5k Followers
                            </span>
                            <span class="px-4 py-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-full text-[12px] font-medium text-gray-500 flex items-center gap-2 shadow-sm">
                                <x-icons.instagram class="w-5 h-5 text-[#28303F] dark:text-white" />
                                2.3M Followers
                            </span>
                        </div>
                    </div>
                </div>

                <!-- BADGES SYSTEM -->
                <div class="space-y-10">
                    <div class="flex items-start gap-6 group">
                        <div class="w-12 h-12 shrink-0 text-gray-300 transition-colors group-hover:text-purple-300">
                            <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-300">Top Creator</h3>
                                <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-semibold uppercase tracking-widest border border-red-100">Not Earned</span>
                            </div>
                            <p class="text-sm font-normal text-gray-800 dark:text-gray-300 max-w-md">To earn this badge, you must complete multiple orders and have a high rating from brands.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 group">
                        <div class="w-12 h-12 shrink-0 text-gray-300 transition-colors group-hover:text-purple-300">
                             <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-300">Responds Fast</h3>
                                <span class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-semibold uppercase tracking-widest border border-red-100">Not Earned</span>
                            </div>
                            <p class="text-sm font-normal text-gray-800 dark:text-gray-300 max-w-md">To earn this badge, you must respond to requests within 12 hours.</p>
                        </div>
                    </div>
                </div>

                <!-- DESCRIPTION BIO -->
                <div class="text-gray-600 dark:text-gray-300 text-base max-w-4xl font-normal opacity-90">
                    As a passionate blogger focusing on the intersection of technology, Tesla innovations, health and fitness, automotive insights, and pet care, I strive to create authentic content that resonates with my audience. My mission is to share my genuine experiences and recommendations, collaborating only with brands that align with my interests and values...
                </div>

                <!-- 3. NEW PACKAGES SECTION -->
                <div class="packages mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 tracking-tight">Packages</h2>
                    
                    <!-- Tabs -->
                    <div class="flex gap-8 border-b border-gray-100 dark:border-gray-800 mb-8">
                        <template x-for="tabName in ['All', 'Instagram', 'TikTok', 'UGC', 'Others']">
                            <button @click="activeTab = tabName" 
                                    :class="activeTab === tabName ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" 
                                    class="pb-4 text-sm font-medium transition-all" x-text="tabName">
                            </button>
                        </template>
                    </div>

                    <!-- List of Packages -->
                    <div class="space-y-3">
                        <template x-for="p in filteredPackages" :key="p.name">
                            <div @click="selectPackage(p.name)" 
                                 :class="selectedPackage === p.name ? 'border-gray-500 bg-purple-50/20 dark:bg-gray-900/10 shadow-sm' : 'border-gray-100 dark:border-gray-800 hover:border-gray-200'"
                                 class="flex items-center justify-between p-5 border rounded-2xl cursor-pointer bg-white dark:bg-transparent transition-all group">
                                <div class="flex items-center gap-5">
                                    <div class="text-gray-800 dark:text-white">
                                        <template x-if="p.icon === 'instagram'"><x-icons.instagram class="w-6 h-6" /></template>
                                        <template x-if="p.icon === 'tiktok'"><x-icons.tiktok class="w-6 h-6" /></template>
                                        <template x-if="p.icon === 'camera'"><x-icons.camera class="w-6 h-6" /></template>
                                        <template x-if="p.icon === 'group'"><x-icons.group class="w-6 h-6" /></template>
                                    </div>
                                    <span class="text-base font-bold text-gray-800 dark:text-gray-200" x-text="p.name"></span>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">$200</span>
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all" 
                                         :class="selectedPackage === p.name ? 'border-black bg-black dark:border-white dark:bg-white' : 'border-gray-200'">
                                        <div x-show="selectedPackage === p.name" class="w-2 h-2 rounded-full" :class="selectedPackage === p.name ? 'bg-white dark:bg-black' : ''"></div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: PRICING CARD (Synced) -->
                <div class="w-2/5">
                    <div class="sticky top-24 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-[2.5rem] p-10 shadow-2xl shadow-purple-900/5">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-4xl font-bold text-gray-800 dark:text-gray-300 tracking-tighter" x-text="price"></span>
                        </div>

                        <!-- CUSTOM DYNAMIC DROPDOWN -->
                        <div class="relative mb-6">
                            <button @click="openDropdown = !openDropdown" 
                                    class="w-full flex items-center justify-between px-5 py-4 border-2 border-purple-100 dark:border-gray-700 rounded-2xl text-base font-bold text-gray-800 dark:text-gray-300 bg-white dark:bg-transparent transition-all hover:border-purple-200">
                                <div class="flex items-center gap-4">
                                    <span class="text-gray-800 dark:text-purple-400">
                                        <template x-if="selectedPackage.includes('Instagram')"><x-icons.instagram class="w-5 h-5" /></template>
                                        <template x-if="selectedPackage.includes('TikTok')"><x-icons.tiktok class="w-5 h-5" /></template>
                                        <template x-if="selectedPackage.includes('UGC')"><x-icons.camera class="w-5 h-5" /></template>
                                    </span>
                                    <span x-text="selectedPackage"></span>
                                </div>
                                <svg class="w-5 h-5 transition-transform" :class="openDropdown ? 'rotate-180 text-gray-800' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="openDropdown" x-cloak @click.away="openDropdown = false" class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl z-50 overflow-y-auto max-h-96">
                                <template x-for="p in packages" :key="p.name">
                                    <div @click="selectPackage(p.name)" 
                                        class="px-6 py-4 cursor-pointer text-sm font-medium transition-colors"
                                        :class="selectedPackage === p.name ? 'bg-gray-100 text-gray-800' : 'text-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/40'"
                                        x-text="p.name">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Show only Brand Users -->
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-300 mb-2">Package Details</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 max-w-md">
                                This package includes 1 Instagram Photo Feed Post. The content will be created in collaboration with the brand, ensuring it aligns with my authentic style and resonates with my audience.
                            </p>
                        </div>

                        <!-- Show only Brand Users -->
                        <button class="bg-[#1A1A1A] hover:bg-purple-400 flex w-full items-center justify-center rounded-xl px-4 py-4 text-sm font-bold text-white transition active:scale-[0.98]">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </main>
</section>
@endsection