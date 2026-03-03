@extends('backend.layouts.app')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Create Campaign" />

    <div class="max-w-6xl px-2 py-2 transition-colors duration-300" 
         x-data="campaignWizard()">

        <!-- Header Progress -->
        <div class="flex items-center gap-12 mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
            <div class="flex items-center gap-3">
                <!-- Step 1 Circle: -->
                <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold text-base"
                      :class="step === 1 ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'bg-gray-200 dark:bg-gray-800 text-gray-500'">1</span>
                <span class="text-base font-bold text-gray-800 dark:text-gray-400">Set Campaign Targeting</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-bold text-base transition-all"
                      :class="step === 2 ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'border-gray-300 dark:border-gray-800 text-gray-400'">2</span>
                <span class="text-base font-bold text-gray-400 dark:text-gray-600"
                      :class="step === 2 && 'dark:text-gray-400 text-gray-800'">Enter Campaign Details</span>
            </div>
        </div>

        <!--  STEP 1: TARGETING -->
        <div x-show="step === 1" class="grid grid-cols-1 md:grid-cols-[1fr_380px] gap-8 items-start" x-cloak x-transition>
            
            <!-- Left Side: Form -->
            <div class="space-y-6">
                <header>
                    <h1 class="text-2xl font-medium text-gray-800 dark:text-white">Let's set your targeting details.</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">Provide some details on influencers you're looking to target.</p>
                </header>

                <div class="space-y-6">
                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What type of campaign do you want to run?</label>
                        <select x-model="campaignType" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            <option value="instagram">Instagram</option>
                            <option value="tiktok">TikTok</option>
                            <option value="ugc">UGC</option>
                            <option value="youtube">Youtube</option>
                            <option value="twitch">Twitch</option>
                            <option value="tiktok">TikTok</option>
                            <option value="ugc">Others</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">How many influencers do you want to hire for this campaign?
                        <span class="text-gray-400 font-normal text-sm">(optional)</span>
                        </label>
                        <input type="number" x-model="influencerCount" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>

                    <!-- Niches Selection -->
                    <div class="relative" x-data="{ 
                        showDropdown: false,
                        nicheOptions: [
                            'All', 'Lifestyle', 'Beauty', 'Fashion', 'Travel', 'Health & Fitness', 
                            'Food & Drink', 'Comedy', 'Art', 'Family', 'Music', 'Education', 
                            'Gaming', 'Tech', 'Sports', 'Outdoors', 'Healthcare', 'Automotive', 
                            'Trades', 'Cannabis', 'Model', 'Pets'
                        ] 
                    }" @click.away="showDropdown = false">
                        
                        <label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
                            What niches do you want to target? <span class="text-gray-400 font-normal text-sm">(optional)</span>
                        </label>

                        <!-- The 'Input' Trigger Area -->
                        <div @click="showDropdown = !showDropdown" 
                            class="flex flex-row gap-2 dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            
                            <!-- Placeholder when nothing is selected -->
                            <template x-if="selectedNiches.length === 0">
                                <span class="text-gray-400 text-sm">Select niches...</span>
                            </template>

                            <!-- Selected Niche Tags -->
                            <template x-for="niche in selectedNiches" :key="niche">
                                <span class="bg-purple-400 w-fit text-white dark:bg-purple-400 dark:text-gray-800 px-3 py-1 rounded-md text-sm font-normal flex items-center gap-4">
                                    <span x-text="niche"></span>
                                    <svg @click.stop="toggleSelection('selectedNiches', niche)" class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </span>
                            </template>
                        </div>

                        <!-- The Dropdown Menu -->
                        <div x-show="showDropdown" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-900 border border-purple-100 dark:border-gray-800 rounded-lg shadow-md z-50 overflow-hidden overflew-y-auto custom-scrollbar max-h-32">
                            
                            <div class="p-4 flex flex-wrap gap-4">
                                <template x-for="niche in nicheOptions" :key="niche">
                                    <button type="button"
                                            @click="toggleSelection('selectedNiches', niche)"
                                            :class="selectedNiches.includes(niche) 
                                                ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' 
                                                : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                            class="px-2 py-2 rounded-md text-sm font-medium transition-all text-center leading-tight">
                                        <span x-text="niche"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Follower Ranges Selection -->
                    <div class="relative" x-data="{ 
                        showFollowerDropdown: false,
                        followerOptions: [
                            'All', '0-1k', '1k-5k', '5k-10k', '10k-50k', '50k-100k', 
                            '100k-250k', '250k-500k', '500k-1m', '1m-5m', '5m-10m', '10m+'
                        ] 
                    }" @click.away="showFollowerDropdown = false">
                        
                        <label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
                            What follower ranges do you want to target? <span class="text-gray-400 font-normal text-sm">(optional)</span>
                        </label>

                        <!-- The 'Input' Trigger Area -->
                        <div @click="showFollowerDropdown = !showFollowerDropdown" 
                            class="flex flex-row flex-wrap gap-2 dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 cursor-pointer min-h-[46px]">
                            
                            <!-- Placeholder when nothing is selected -->
                            <template x-if="selectedFollowers.length === 0">
                                <span class="text-gray-400 text-sm py-1">Select follower ranges...</span>
                            </template>

                            <!-- Selected Follower Tags -->
                            <template x-for="range in selectedFollowers" :key="range">
                                <span class="bg-purple-400 w-fit text-white dark:bg-purple-400 dark:text-gray-800 px-3 py-1 rounded-md text-sm font-normal flex items-center gap-3">
                                    <span x-text="range"></span>
                                    <svg @click.stop="toggleSelection('selectedFollowers', range)" class="w-3 h-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </span>
                            </template>
                        </div>

                        <!-- The Dropdown Menu -->
                        <div x-show="showFollowerDropdown" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-900 border border-purple-100 dark:border-gray-800 rounded-lg shadow-md z-50 overflow-hidden overflow-y-auto custom-scrollbar max-h-40">
                            
                            <div class="p-4 flex flex-wrap gap-3">
                                <template x-for="range in followerOptions" :key="range">
                                    <button type="button"
                                            @click="toggleSelection('selectedFollowers', range)"
                                            :class="selectedFollowers.includes(range) 
                                                ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' 
                                                : 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                            class="px-3 py-2 rounded-md text-sm font-medium transition-all text-center leading-tight">
                                        <span x-text="range"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What countries do you want to target?
                        <span class="text-gray-400 font-normal text-sm">(optional)</span>
                        </label>
                        <input type="text" x-model="influencerCountry" placeholder="e.g. United States, Canada" class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>

                    <!-- Advanced Toggle -->
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                        <button @click="isAdvancedOpen = !isAdvancedOpen" class="flex items-center justify-between w-full group">
                            <span class="text-base font-medium text-gray-800 dark:text-gray-400 uppercase tracking-widest">Advanced Filters</span>
                            <svg class="w-5 h-5 transition-transform text-purple-500" :class="isAdvancedOpen ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4"/></svg>
                        </button>
                        <div x-show="isAdvancedOpen" x-collapse class="mt-6 space-y-6">
                             <input type="text" placeholder="Target Language" class="w-full h-12 rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 text-sm dark:text-white outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Sticky Estimated Results -->
            <div class="md:sticky md:top-24 space-y-6">
                <div class="bg-[#EBF7F0] dark:bg-green-950/20 rounded-[2.5rem] p-8 border border-green-100 dark:border-green-900/30 shadow-xl shadow-green-900/5">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-green-400 mb-8 uppercase tracking-widest">Estimated Results</h3>
                    
                    <div class="space-y-10">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-green-600 shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-800 dark:text-white leading-none" x-text="getEstimate().influencers"></p>
                                <p class="text-[10px] font-medium uppercase text-gray-500 mt-1 tracking-widest">Influencers Match</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-green-600 shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-2xl font-black text-gray-800 dark:text-white leading-none" x-text="getEstimate().reach"></p>
                                <p class="text-[10px] font-medium uppercase text-gray-500 mt-1 tracking-widest">Followers Reached</p>
                            </div>
                        </div>
                    </div>

                    <button @click="step = 2" class="w-full mt-12 bg-[#1A1A1A] hover:bg-black text-white py-5 rounded-3xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl transition active:scale-95">
                        Continue
                    </button>
                </div>
                
                <p class="text-[11px] text-center text-gray-400 px-6 leading-relaxed">Adjust your targeting to see real-time updates on potential reach and influencer matches.</p>
            </div>
        </div>

        <!-- ========================= STEP 2: CAMPAIGN DETAILS ========================= -->
        <div x-show="step === 2" x-cloak x-transition class="space-y-12 max-w-3xl">
            <header>
                <h1 class="text-3xl font-black text-gray-800 dark:text-white tracking-tighter">Campaign Details</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Define the specifics of your campaign content.</p>
            </header>

            <div class="space-y-8">
                <div>
                    <label class="block text-sm font-medium dark:text-gray-400 mb-3">Campaign Title</label>
                    <input type="text" placeholder="Summer 2026 Influencer Push" class="w-full h-14 rounded-2xl border border-gray-200 dark:border-gray-800 bg-transparent px-5 dark:text-white outline-none focus:border-purple-400">
                </div>

                <div>
                    <label class="block text-sm font-medium dark:text-gray-400 mb-3">Product Description & Instructions</label>
                    <textarea rows="6" placeholder="Describe your product and what you want creators to do..." class="w-full rounded-2xl border border-gray-200 dark:border-gray-800 bg-transparent p-5 dark:text-white outline-none focus:border-purple-400"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Selected Type</p>
                        <p class="text-xl font-medium text-gray-800 dark:text-purple-400 capitalize" x-text="campaignType"></p>
                    </div>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-black uppercase text-gray-400 mb-2 tracking-widest">Hiring Limit</p>
                        <p class="text-xl font-medium text-gray-800 dark:text-purple-400"><span x-text="influencerCount"></span> Creators</p>
                    </div>
                </div>

                <div class="pt-10 flex flex-col gap-4">
                    <button class="w-full bg-[#1A1A1A] dark:bg-purple-500 text-white dark:text-gray-800 py-6 rounded-3xl font-black text-sm uppercase tracking-[0.2em] shadow-2xl transition hover:opacity-90 active:scale-95">
                        Publish Campaign
                    </button>
                    <button @click="step = 1" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition">Back to Edit Targeting</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function campaignWizard() {
            return {
                step: 1,
                campaignType: 'instagram',
                influencerCount: 1,
                influencerCountry: '',
                isAdvancedOpen: false,

                nicheOptions: ['Lifestyle', 'Beauty', 'Fashion', 'Travel', 'Food & Drink', 'Gaming'],
                selectedNiches: ['Lifestyle', 'Beauty'],

                followerOptions: ['0-1k', '10k-50k', '100k-500k', '1m+'],
                selectedFollowers: ['10k-50k'],

                toggleSelection(array, item) {
                    if (this[array].includes(item)) {
                        this[array] = this[array].filter(i => i !== item);
                    } else {
                        this[array].push(item);
                    }
                },

                getEstimate() {
                    let count = parseInt(this.influencerCount) || 1;
                    return {
                        influencers: (850 * count) + '-' + (1100 * count),
                        reach: (120 * count) + 'M+'
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Clean scrollbar styling for the dropdown */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #374151;
        }
    </style>
@endsection