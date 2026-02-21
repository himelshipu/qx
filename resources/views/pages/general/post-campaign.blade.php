@extends('layouts.general.app')

@section('content')
<!-- Include the locationPicker data component logic here if not globally available -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('campaignWizard', () => ({
            step: 1,
            campaignType: 'instagram',
            
            // Step 2 Form Data
            hireCount: 1,
            selectedNiches: ['Beauty'],
            selectedFollowers: ['0-1k'],
            selectedCountry: 'USA',
            citySearch: '',
            showNicheDropdown: false,
            showFollowerDropdown: false,
            isAdvancedVisible: false,

            // Options
            nicheOptions: ['Beauty', 'Fashion', 'Health & Fitness', 'Travel', 'Food & Drink', 'Gaming', 'Technology'],
            followerOptions: ['0-1k', '1k-10k', '10k-50k', '50k-100k', '100k-500k', '500k-1M+'],

            toggleSelection(array, item) {
                if (this[array].includes(item)) {
                    this[array] = this[array].filter(i => i !== item);
                } else {
                    this[array].push(item);
                }
            },

            prevStep() {
                if (this.step === 1) window.history.back();
                else this.step--;
            }
        }));
    });
</script>

<section class="w-full bg-white dark:bg-gray-950 py-20 min-h-screen" x-data="campaignWizard()">

    <!-- 1. HEADER & PROGRESS BAR -->
    <div class="mb-16 w-full px-12">
        <div class="flex items-center justify-between mb-8 px-2">
            <button @click="prevStep()" class="flex items-center gap-2 px-6 py-3 border border-gray-200 dark:border-purple-800 rounded-xl text-sm text-[#1F2937] dark:text-gray-100 hover:bg-purple-500 hover:text-white font-medium transition active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                Back
            </button>

            <div class="flex items-center gap-12">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-black text-sm transition-all duration-500" 
                        :class="step >= 1 ? 'border-black bg-purple-500 hover:bg-purple-600 text-white' dark:text-white : 'border-gray-200 text-gray-400 dark:text-gray-400'">1</span>
                    <span class="text-sm font-medium" :class="step >= 1 ? 'text-[#1F2937]  dark:text-purple-100' : 'text-gray-400'">Set Campaign Targeting</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-full border-2 font-black text-sm transition-all duration-500" 
                        :class="step >= 2 ? 'border-black bg-purple-500 hover:bg-purple-600 text-white' dark:text-white : 'border-gray-200 text-gray-400'">2</span>
                    <span class="text-sm font-medium" :class="step >= 2 ? 'text-[#1F2937]  dark:text-purple-100' : 'text-gray-400'">Enter Campaign Details</span>
                </div>
            </div>
            <div class="w-24 hidden md:block"></div>
        </div>

        <!-- Progress Bar -->
        <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
            <div class="h-full bg-purple-400 dark:bg-purple-400 transition-all duration-700 ease-in-out" 
                :style="step === 1 ? 'width: 50%' : 'width: 100%'"></div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">
        <!-- STEP 1: SELECT CAMPAIGN TYPE -->
        <div x-show="step === 1" x-transition.opacity.duration.500ms class="space-y-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-10">What type of campaign do you want to run?</h2>
            
            <div class="grid grid-cols-1 gap-6">
                @php
                    $types = [
                        ['id' => 'instagram', 'name' => 'Instagram', 'desc' => 'Collaborate with Instagram creators and have them post photos, reels, stories, and livestreams to their Instagram audience.'],
                        ['id' => 'tiktok', 'name' => 'TikTok', 'desc' => 'Collaborate with TikTok creators and have them post videos, stories, and livestreams to their TikTok audience.'],
                        ['id' => 'ugc', 'name' => 'User Generated Content', 'desc' => 'Get high-quality content without creators posting on their social media. Get thumb-stopping videos for your own use.'],
                    ];
                @endphp

                @foreach($types as $type)
                <div @click="campaignType = '{{ $type['id'] }}'; step = 2" 
                     class="flex flex-row items-stretch p-0 border border-gray-100 dark:border-gray-800 rounded-4xl cursor-pointer transition-all hover:shadow-md hover:border-purple-400 group bg-white dark:bg-white/[0.03] overflow-hidden min-h-[240px]">
                
                    <div class="w-56 md:w-60 h-full shrink-0 bg-gray-50 dark:bg-gray-900 border-r border-gray-50 dark:border-gray-800">
                        <img src="{{ asset('gif/' . $type['id'] . '.gif') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    </div>

                    <div class="flex-1 px-12 md:px-16 py-10 flex flex-col justify-center">
                        <div class="flex items-center gap-4 mb-4">
                             <div class="text-[#1F2937] dark:text-gray-100">
                                @if($type['id'] == 'instagram') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" stroke-width="2.5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01" stroke-width="2.5"/></svg> @endif
                                @if($type['id'] == 'tiktok') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12a4 4 0 104 4V4a5 5 0 005 5" stroke-width="2.5"/></svg> @endif
                                @if($type['id'] == 'ugc') <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2.5"/><circle cx="12" cy="13" r="3" stroke-width="2.5"/></svg> @endif
                             </div>
                             <span class="text-3xl font-bold text-[#1F2937] dark:text-gray-100">{{ $type['name'] }}</span>
                        </div>
                        <p class="text-[#1F2937] dark:text-gray-300 font-medium text-sm leading-relaxed max-w-lg">{{ $type['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- STEP 2: TARGETING DETAILS -->
        <div x-show="step === 2" x-transition.opacity.duration.500ms class="max-w-4xl">
            <div class="mb-10">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">Let's set your targeting details.</h2>
                <p class="text-[#1F2937] dark:text-gray-100 text-sm font-medium">Provide some details on influencers you're looking to target. We'll collect campaign details like content requirements and product descriptions in the next step.</p>
            </div>

            <div class="space-y-10">
                <!-- 1. Campaign Type -->
                <div>
                    <label class="block text-lg font-bold text-[#1F2937] dark:text-gray-100 mb-6">What type of campaign?</label>
                    <select x-model="campaignType" class="w-full h-12 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-4 text-sm text-[#222] dark:text-gray-100 focus:border-purple-400 outline-none">
                        <option value="instagram">Instagram</option>
                        <option value="tiktok">TikTok</option>
                        <option value="ugc">UGC</option>
                        <option value="promotion">Promotion</option>

                    </select>
                </div>

                <!-- 2. Hire Count -->
                <div>
                    <label class="block text-lg font-bold text-[#1F2937]  dark:text-gray-100 mb-6">Number of Influencers</label>
                    <input type="number" x-model="hireCount" class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 text-sm text-[#1F2937] dark:text-gray-100 focus:border-purple-400 outline-none" placeholder="1">
                </div>

                <!-- 3. Niches (Multi-Select) -->
                <div>
                    <label class="block text-lg font-bold text-[#1F2937]  dark:text-gray-100 mb-6">What niches to target? <span class="text-gray-400 font-normal lowercase">(optional)</span></label>
                    <div class="relative" @click.away="showNicheDropdown = false">
                        <div @click="showNicheDropdown = !showNicheDropdown" class="w-full min-h-[52px] rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 py-2 flex flex-wrap gap-2 items-center cursor-pointer transition-all hover:border-purple-300">
                            <template x-if="selectedNiches.length === 0"><span class="text-gray-400 text-sm">Select niches</span></template>
                            <template x-for="n in selectedNiches">
                                <span class="bg-black text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                    <span x-text="n"></span>
                                    <svg @click.stop="toggleSelection('selectedNiches', n)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                                </span>
                            </template>
                        </div>
                        <div x-show="showNicheDropdown" class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-900 border border-purple-100 dark:border-gray-800 rounded-2xl shadow-2xl z-50 p-3 grid grid-cols-2 md:grid-cols-4 gap-1">
                            <template x-for="opt in nicheOptions">
                                <div @click="toggleSelection('selectedNiches', opt)" :class="selectedNiches.includes(opt) ? 'bg-purple-100 dark:hover:text-[#222] dark:hover:bg-purple-100 text-[#1F2937] ' : 'hover:bg-purple-50 text-[#1F2937] dark:hover:bg-purple-100 dark:hover:text-[#222] dark:text-gray-100 '" class="px-4 py-2 mx-2 rounded-xl text-sm font-bold cursor-pointer transition-colors" x-text="opt"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 4. Follower Ranges -->
                <div>
                    <label class="block text-lg font-bold text-[#1F2937]  dark:text-gray-100 mb-6">Follower ranges? <span class="text-gray-400 font-normal lowercase">(optional)</span></label>
                    <div class="relative" @click.away="showFollowerDropdown = false">
                        <div @click="showFollowerDropdown = !showFollowerDropdown" class="w-full min-h-[52px] rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 py-2 flex flex-wrap gap-2 items-center cursor-pointer transition-all hover:border-purple-300">
                            <template x-if="selectedFollowers.length === 0"><span class="text-gray-400 text-sm">Select ranges</span></template>
                            <template x-for="f in selectedFollowers">
                                <span class="bg-black text-white px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                    <span x-text="f"></span>
                                    <svg @click.stop="toggleSelection('selectedFollowers', f)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="3"/></svg>
                                </span>
                            </template>
                        </div>
                        <div x-show="showFollowerDropdown" class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-900 border border-purple-100 dark:border-gray-800 rounded-2xl shadow-2xl z-50 p-3 grid grid-cols-2 gap-1">
                            <template x-for="opt in followerOptions">
                                <div @click="toggleSelection('selectedFollowers', opt)" :class="selectedFollowers.includes(opt) ? 'bg-purple-100 dark:hover:text-[#222] dark:hover:bg-purple-100 text-[#1F2937]' : 'hover:bg-purple-50 text-[#1F2937] dark:hover:bg-purple-100 dark:hover:text-[#222] dark:text-gray-100'" class="px-4 mx-2 py-2 rounded-xl text-sm font-bold cursor-pointer transition-colors" x-text="opt"></div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 5. City Search with Dropdown -->
                <div class="relative w-full" x-data="locationPicker" @click.away="showDropdown = false">
                    <label class="block text-lg font-bold text-[#1F2937]  dark:text-gray-100 mb-6">Target Country / Cities <span class="text-gray-400 font-normal lowercase">(optional)</span> </label>
                    
                    <div class="relative">
                        <input 
                            type="text" 
                            x-model="search"
                            @input.debounce.500ms="fetchLocations()"
                            @focus="if(results.length > 0) showDropdown = true"
                            placeholder="Search for a country/city (e.g. USA)..."
                            class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent pl-4 pr-10 text-sm text-gray-800 dark:text-white focus:border-purple-400 focus:ring-0 outline-none" 
                        />

                        <!-- Clear Button -->
                        <button x-show="search.length > 0" @click="search = ''; results = []; showDropdown = false;" type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-purple-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown Menu -->
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

                <!-- Advanced Toggle -->
                <div @click="isAdvancedVisible = !isAdvancedVisible" class="pt-6 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between group cursor-pointer">
                    <span class="block text-lg font-bold text-[#1F2937]  dark:text-gray-100 mb-6">View Advanced Filters</span>
                    <svg class="w-6 h-6 transition-transform text-violet-500" :class="isAdvancedVisible ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="3" stroke-linecap="round"/></svg>
                </div>

                <!-- Estimated Results -->
                <div class="bg-gradient-to-r from-[#E3C4FF]/80 to-[#E7C9FF]/80 sticky bottom-2  dark:bg-green-950/20 border-2 border-dark-500 dark:border-pink-400/30 rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-8 mt-16">
                    <div class="flex items-center gap-12 text-[#1F2937]  dark:text-gray-100">
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-md flex items-center justify-center text-[#222] dark:text-gray-100">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" stroke-width="2"/></svg>
                            </div>
                            <div><p class="text-3xl font-black">100 - 200</p><p class="text-sm font-normal text-gray-800 dark:text-gray-100 mt-2">Influencers matched</p></div>
                        </div>
                        <div class="flex items-center gap-5">
                            <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl shadow-md flex items-center justify-center text-[#222] dark:text-gray-100">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" stroke-width="2"/></svg>
                            </div>
                            <div><p class="text-3xl font-black">70k - 80k+</p><p class="text-sm font-normal text-gray-800 dark:text-gray-100 mt-2">Total Reach</p></div>
                        </div>
                    </div>
                    <button class="w-full md:w-auto bg-[#222] hover:bg-purple-500 text-white px-14 py-5 rounded-2xl font-bold text-lg shadow-md transition active:scale-95">Continue</button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    [x-cloak] { display: none !important; }
    /* Hide number input arrows */
    input::-webkit-outer-spin-button, input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
</style>
@endsection


<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('locationPicker', () => ({
        search: '',
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
                    headers: { 'User-Agent': 'QX-Influencer-Platform' } // Good practice for this API
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