@extends('layouts.general.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-16" 
     x-data="{ tab: 'details' }">
    
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-10">Account</h1>

        <!-- Tab Navigation -->
        <div class="flex gap-10 border-b border-gray-100 dark:border-gray-800 mb-10">
            <button @click="tab = 'details'" 
                    :class="tab === 'details' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" 
                    class="pb-4 text-lg font-bold transition-all">
                Details
            </button>
            <button @click="tab = 'payment'" 
                    :class="tab === 'payment' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" 
                    class="pb-4 text-lg font-bold transition-all">
                Payment
            </button>
            <button @click="tab = 'settings'" 
                    :class="tab === 'settings' ? 'border-b-2 border-black dark:border-white text-black dark:text-white' : 'text-gray-400 hover:text-gray-600'" 
                    class="pb-4 text-lg font-bold transition-all">
                Settings
            </button>
        </div>

        <!-- 4. Tab Content Area -->
        <div class="mt-8">

            <!-- TAB: DETAILS -->
            <div x-show="tab === 'details'" x-cloak class="space-y-8 animate-in fade-in duration-300">
                <form action="#" method="POST" class="space-y-8">
                    <!-- Email -->
                    <div>
                        <label class="mb-2 block text-sm font-bold text-gray-800 dark:text-white">Email</label>
                        <input type="email" value="fahmanchy@gmail.com" readonly
                            class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 px-4 text-sm text-gray-500 focus:outline-none cursor-not-allowed" />
                    </div>

                    <!-- Password Section -->
                    <div class="space-y-3 flex flex-col gap-4" x-data="{ showPass1: false, showPass2: false }">
                        <label class="block text-sm font-bold text-gray-800 dark:text-white">Password</label>
                        
                        <!-- New Pass -->
                        <div class="relative">
                            <input :type="showPass1 ? 'text' : 'password'" placeholder="Enter New Password"
                                class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 pr-12 text-sm text-gray-800 dark:text-white focus:border-purple-400 focus:ring-0 outline-none placeholder:text-gray-300" />
                            <button type="button" @click="showPass1 = !showPass1" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>

                        <!-- Confirm Pass -->
                        <div class="relative">
                            <input :type="showPass2 ? 'text' : 'password'" placeholder="Confirm New Password"
                                class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 pr-12 text-sm text-gray-800 dark:text-white focus:border-purple-400 focus:ring-0 outline-none placeholder:text-gray-300" />
                            <button type="button" @click="showPass2 = !showPass2" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Legal Company Name -->
                    <div>
                        <label class="mb-2 block text-sm font-bold text-gray-800 dark:text-white">Legal Company Name</label>
                        <input type="text" placeholder="Legal Company Name" class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 px-4 text-sm outline-none focus:border-purple-400 placeholder:text-gray-300 dark:text-white bg-transparent" />
                    </div>

                    <!-- VAT/Tax ID -->
                    <div>
                        <label class="mb-2 block text-sm font-bold text-gray-800 dark:text-white">VAT/Tax ID</label>
                        <input type="text" placeholder="VAT/Tax ID number"
                            class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-4 text-sm text-gray-800 dark:text-white focus:border-purple-400 focus:ring-0 outline-none placeholder:text-gray-300" />
                    </div>

                    <!-- Billing Address -->
                    <div class="space-y-3 flex flex-col gap-4">
                        <label class="block text-sm font-bold text-gray-800 dark:text-white">Billing Address</label>
                        <div class="relative w-full" x-data="locationPicker" @click.away="showDropdown = false">    
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
                        <input type="text" placeholder="Zip/Postal Code" class="h-12 w-full rounded-xl border border-gray-200 dark:border-gray-800 px-4 text-sm outline-none focus:border-purple-400 placeholder:text-gray-300 dark:text-white bg-transparent" />
                    </div>

                    <!-- Save Button -->
                    <a href="#" class="flex w-full items-center justify-center py-5 border border-gray-900 dark:border-purple-400/50 rounded-xl font-bold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-purple-400/10 transition shadow-sm active:scale-[0.99]">
                        Save Changes
                    </a>
                </form>
            </div>

            <!-- TAB: PAYMENT -->
            <div x-show="tab === 'payment'" 
                x-cloak 
                x-data="{ showAddFundsModal: false }" 
                class="space-y-12 animate-in fade-in duration-300 text-start">
                
                <!-- Subscription Section -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Subscription</h3>
                        <!-- Link updated to purple-400 -->
                        <a href="#" class="text-sm font-bold text-purple-400 hover:underline">Upgrade</a>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-base leading-relaxed">
                        You have no subscription. Upgrade to post campaigns and have 550,000+ creators come to you.
                    </p>
                </div>

                <!-- Balance Section -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Balance</h3>
                        <!-- Trigger for Modal - Link updated to purple-400 -->
                        <a href="#" @click.prevent="showAddFundsModal = true" class="text-sm font-bold text-purple-400 hover:underline">Add Funds</a>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">$0.00</p>
                </div>

                <!-- Modal-->
                <div x-show="showAddFundsModal" 
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden" 
                    x-cloak>
                    
                    <!-- Backdrop with Blur -->
                    <div x-show="showAddFundsModal" 
                        x-transition.opacity 
                        @click="showAddFundsModal = false" 
                        class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                    <!-- Modal Card -->
                    <div x-show="showAddFundsModal"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="relative w-full max-w-xl bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden p-8 md:p-14 text-center">
                        
                        <!-- Close Button -->
                        <button @click="showAddFundsModal = false" class="absolute top-8 right-10 text-gray-400 hover:text-black dark:hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <!-- Modal Content -->
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-10">How Much Do You Want to Add?</h2>

                        <div class="mb-10">
                            <input type="number" 
                                placeholder="Amount (USD)" 
                                class="w-full h-14 rounded-xl border border-gray-200 dark:border-gray-800 bg-transparent px-6 text-lg font-medium focus:border-purple-400 focus:ring-0 dark:text-white placeholder:text-gray-400 transition-colors">
                        </div>

                        <button @click="showAddFundsModal = false" class="w-full bg-[#1A1A1A] hover:bg-purple-400 text-white font-bold py-4 rounded-xl text-lg transition shadow-lg active:scale-95 uppercase tracking-widest">
                            Continue
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB: SETTINGS -->
            <div x-show="tab === 'settings'" x-cloak class="space-y-12 animate-in fade-in duration-300 text-start">
                <!-- Logout Section -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Log Out</h3>
                    <button class="bg-[#1A1A1A] hover:bg-purple-400 text-white w-64 py-4 rounded-xl font-bold text-sm transition shadow-lg active:scale-95 uppercase tracking-widest">
                        Sign Out
                    </button>
                </div>

                <!-- Delete Account -->
                <div class="pt-6">
                    <a href="#" class="text-sm font-medium text-gray-400 hover:text-red-500 transition-colors">
                        Delete Account
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
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