@extends('frontend.layouts.app')

@section('content')

<!-- Wrap the whole section in a x-data to manage the cart state -->
<div x-data="{ isCartOpen: false }" class="relative">


    <!-- ========================= CART SIDEBAR MODAL ========================= -->
    <div x-show="isCartOpen" 
         x-cloak 
         class="fixed inset-0 z-[100] overflow-hidden" 
         role="dialog" aria-modal="true">
        
        <!-- Backdrop Blur/Darken -->
        <div x-show="isCartOpen" 
             x-transition.opacity 
             @click="isCartOpen = false" 
             class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-full">
            <!-- Sidebar Panel Container -->
            <div x-show="isCartOpen" 
                 x-transition:enter="transform transition ease-in-out duration-500" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in-out duration-500" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full" 
                 class="w-screen max-w-5xl flex shadow-2xl">
                
                <!-- LEFT PANEL (Estimated Results - Dark) -->
                <div class="hidden md:flex flex-col w-[38%] bg-black p-12 text-white justify-between">
                    <div>
                        <h2 class="text-3xl font-bold mb-6 tracking-tight">Estimated Results</h2>
                        <p class="text-sm text-gray-400 leading-relaxed mb-16">
                            Not all influencers will accept your order. Here is a projection of your actual outcome based on acceptance rates.
                        </p>

                        <div class="space-y-12">
                            <!-- Progress Bar: Influencers -->
                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <span class="text-xl font-bold">0 Influencers</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-600 w-0 transition-all duration-1000"></div>
                                </div>
                                <div class="text-right mt-3 text-xs font-bold text-gray-500 uppercase tracking-widest">0 Influencers</div>
                            </div>

                            <!-- Progress Bar: Spend -->
                            <div>
                                <div class="flex justify-between items-end mb-3">
                                    <span class="text-xl font-bold">$0 Spend</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-600 w-0 transition-all duration-1000"></div>
                                </div>
                                <div class="text-right mt-3 text-xs font-bold text-gray-500 uppercase tracking-widest">$0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Protection Info -->
                    <div class="flex items-start gap-4">
                        <div class="pt-1 text-gray-500">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight leading-4">
                            Payment Protection <br>
                            <span class="text-gray-600 font-medium normal-case tracking-normal">If an order is declined, funds will be refunded.</span>
                        </p>
                    </div>
                </div>

                <!-- RIGHT PANEL (Cart Details - White) -->
                <div class="flex-1 flex flex-col bg-white p-12 justify-between">
                    <div class="flex items-center justify-between">
                        <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Cart</h2>
                        <button @click="isCartOpen = false" class="p-2 text-gray-300 hover:text-black transition-all hover:rotate-90">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>

                    <!-- Empty State Content -->
                    <div class="flex flex-col items-center justify-center text-center py-20">
                        <div class="relative mb-8">
                             <svg class="w-28 h-28 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-400 font-medium max-w-[280px] leading-relaxed">
                            Start adding influencers by clicking the button below
                        </p>
                    </div>

                    <!-- Footer Action Button -->
                    <button @click="isCartOpen = false" class="w-full bg-[#222222] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-purple-500 transition-all shadow-xl active:scale-95">
                        Discover Influencers
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>



<section class="min-h-screen transition-colors duration-200" 
    x-data="{ 
        openFilter: null, 
        startDate: null, 
        endDate: null,
        selectedCampaigns: [],
        selectedPlatforms: [],
        
        // Calendar State
        viewMonth: new Date().getMonth(),
        viewYear: new Date().getFullYear(),
        minYear: 2026,
        maxYear: new Date().getFullYear(),

        // Dummy Data for Selects
        campaigns: ['Summer Blast 2026', 'QX Launch Event', 'Fitness Glow Up', 'Tech Unboxing Series', 'Winter Fashion Week', 'Gaming Marathon'],
        platforms: ['Instagram', 'TikTok', 'YouTube', 'Twitter / X', 'Twitch', 'UGC Content'],

        // Table Data
        transactions: [
            { id: 1, name: 'Wilson Gouse', image: 'https://i.pravatar.cc/150?u=1', date: 'Feb 19, 2026', price: '$2,500', campaign: 'Summer Blast 2026', status: 'Success' },
            { id: 2, name: 'Terry Franci', image: 'https://i.pravatar.cc/150?u=2', date: 'Feb 22, 2026', price: '$1,200', campaign: 'QX Launch Event', status: 'Pending' },
            { id: 3, name: 'Alena Franci', image: 'https://i.pravatar.cc/150?u=3', date: 'Mar 05, 2026', price: '$850', campaign: 'Fitness Glow Up', status: 'Success' },
            { id: 4, name: 'Jocelyn Kenter', image: 'https://i.pravatar.cc/150?u=4', date: 'Mar 10, 2026', price: '$3,100', campaign: 'Tech Unboxing', status: 'Failed' },
            { id: 5, name: 'Brandon Philips', image: 'https://i.pravatar.cc/150?u=5', date: 'Mar 15, 2026', price: '$1,800', campaign: 'Winter Fashion', status: 'Success' },
            { id: 6, name: 'James Lipshutz', image: 'https://i.pravatar.cc/150?u=6', date: 'Mar 20, 2026', price: '$900', campaign: 'Gaming Marathon', status: 'Success' }
        ],
        itemsPerPage: 5,
        currentPage: 1,

        // --- Logic Methods ---
        toggleSelection(array, item) {
            if (this[array].includes(item)) {
                this[array] = this[array].filter(i => i !== item);
            } else {
                this[array].push(item);
            }
        },

        formatDate(y, m, d) {
            return `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        },

        getDaysInMonth(y, m) { return new Date(y, m + 1, 0).getDate(); },
        getFirstDayOfMonth(y, m) { return new Date(y, m, 1).getDay(); },

        selectDate(dateStr) {
            if (!this.startDate || (this.startDate && this.endDate)) {
                this.startDate = dateStr;
                this.endDate = null;
            } else {
                if (dateStr < this.startDate) {
                    this.endDate = this.startDate;
                    this.startDate = dateStr;
                } else {
                    this.endDate = dateStr;
                }
            }
        },

        isDateInRange(dateStr) {
            if (!this.startDate || !this.endDate) return dateStr === this.startDate;
            return dateStr >= this.startDate && dateStr <= this.endDate;
        },

        hasFilters() {
            return this.selectedCampaigns.length > 0 || this.selectedPlatforms.length > 0 || (this.startDate && this.endDate);
        },

        resetFilters() {
            this.selectedCampaigns = [];
            this.selectedPlatforms = [];
            this.startDate = null;
            this.endDate = null;
            this.openFilter = null;
        },

        get paginatedTransactions() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            return this.transactions.slice(start, start + this.itemsPerPage);
        },

        get totalPages() { return Math.ceil(this.transactions.length / this.itemsPerPage); },

        getStatusClass(status) {
            const classes = {
                'Success': 'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/15 dark:text-green-500',
                'Pending': 'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-yellow-500/15 dark:text-orange-400',
                'Failed': 'bg-red-50 text-red-600 border border-red-100 dark:bg-red-500/15 dark:text-red-500',
            };
            return classes[status] || '';
        }
    }">

    <!-- Header Section -->
    <div class="mb-10">
        <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-2">Content Library</h1>
        <p class="text-sm text-gray-700 dark:text-gray-400">See all your delivered content in one place</p>
    </div>

    <!-- Filters Row --> 
    <div class="flex flex-wrap items-center gap-4 mb-10">
        
        <!-- Filter: Campaign -->
        <div class="relative">
            <button @click="openFilter = (openFilter === 'campaign' ? null : 'campaign')" 
                    class="flex items-center gap-3 px-5 py-2 border rounded-full transition active:scale-95"
                    :class="selectedCampaigns.length > 0 ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-300' : 'border-gray-300 bg-white dark:bg-transparent dark:text-white'">
                <span class="text-sm font-bold" x-text="selectedCampaigns.length > 0 ? 'Campaign (' + selectedCampaigns.length + ')' : 'Campaign'"></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
            </button>
            <div x-show="openFilter === 'campaign'" x-cloak @click.away="openFilter = null" 
                class="absolute top-full left-0 mt-3 w-72 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-2xl z-50 p-2 max-h-80 overflow-y-auto">
                <template x-for="camp in campaigns" :key="camp">
                    <div @click="toggleSelection('selectedCampaigns', camp)" 
                        class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/10 transition group">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-purple-600" x-text="camp"></span>
                        <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition"
                            :class="selectedCampaigns.includes(camp) ? 'bg-purple-400 border-purple-400' : 'border-gray-200 dark:border-gray-700'">
                            <svg x-show="selectedCampaigns.includes(camp)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Filter: Platform -->
        <div class="relative">
            <button @click="openFilter = (openFilter === 'platform' ? null : 'platform')" 
                    class="flex items-center gap-3 px-5 py-2 border rounded-full transition active:scale-95"
                    :class="selectedPlatforms.length > 0 ? 'border-purple-400 bg-purple-50 dark:bg-purple-900/20 dark:text-purple-300' : 'border-gray-300 bg-white dark:bg-transparent dark:text-white'">
                <span class="text-sm font-bold" x-text="selectedPlatforms.length > 0 ? 'Platform (' + selectedPlatforms.length + ')' : 'Platform'"></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
            </button>
            <div x-show="openFilter === 'platform'" x-cloak @click.away="openFilter = null" 
                class="absolute top-full left-0 mt-3 w-64 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-2xl z-50 p-2">
                <template x-for="plat in platforms" :key="plat">
                    <div @click="toggleSelection('selectedPlatforms', plat)" 
                        class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-purple-50 dark:hover:bg-purple-900/10 transition group">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-purple-600" x-text="plat"></span>
                        <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition"
                            :class="selectedPlatforms.includes(plat) ? 'bg-purple-400 border-purple-400' : 'border-gray-200 dark:border-gray-700'">
                            <svg x-show="selectedPlatforms.includes(plat)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round"/></svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Filter: Date (Dynamic Range Picker) -->
        <div class="relative">
            <button @click="openFilter = (openFilter === 'date' ? null : 'date')" 
                    class="flex items-center gap-3 px-5 py-2 border border-gray-300 rounded-full dark:text-white bg-white dark:bg-transparent transition hover:bg-purple-50 active:scale-95 shadow-sm">
                <svg class="w-5 h-5 text-gray-800 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5"/></svg>
                <span class="text-sm font-bold">Date: 
                    <span class="font-normal text-gray-600 dark:text-gray-400" x-text="startDate && endDate ? startDate + ' - ' + endDate : 'Select Range'"></span>
                </span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
            </button>
            
            <!-- Date Picker Dropdown -->
            <div x-show="openFilter === 'date'" x-cloak @click.away="openFilter = null" 
                class="absolute top-full left-0 md:-left-40 mt-3 w-[700px] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl shadow-2xl z-50 overflow-hidden">
                <div class="p-8">
                    <!-- Nav Header -->
                    <div class="flex items-center justify-between mb-8 px-2">
                        <button @click="if(viewMonth === 0) { viewMonth = 11; viewYear--; } else { viewMonth--; }" class="p-2 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-full transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5"/></svg></button>
                        <div class="flex gap-4">
                            <select x-model="viewMonth" class="bg-transparent font-bold text-gray-800 dark:text-white outline-none cursor-pointer">
                                <template x-for="(m, idx) in ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']"><option :value="idx" x-text="m"></option></template>
                            </select>
                            <select x-model="viewYear" class="bg-transparent font-bold text-gray-800 dark:text-white outline-none cursor-pointer">
                                <template x-for="y in Array.from({length: maxYear - minYear + 1}, (_, i) => maxYear - i)"><option :value="y" x-text="y"></option></template>
                            </select>
                        </div>
                        <button @click="if(viewMonth === 11) { viewMonth = 0; viewYear++; } else { viewMonth++; }" class="p-2 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-full transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5"/></svg></button>
                    </div>

                    <div class="flex justify-between gap-10">
                        <!-- Current Month Calendar -->
                        <div class="flex-1">
                            <div class="grid grid-cols-7 text-center text-[10px] font-bold text-gray-400 mb-4 uppercase tracking-widest"><div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div></div>
                            <div class="grid grid-cols-7 text-center text-sm font-medium gap-y-1">
                                <template x-for="i in getFirstDayOfMonth(viewYear, viewMonth)"><div class="py-2"></div></template>
                                <template x-for="day in getDaysInMonth(viewYear, viewMonth)">
                                    <div @click="selectDate(formatDate(viewYear, viewMonth, day))" 
                                         class="py-2 cursor-pointer transition-all duration-200"
                                         :class="isDateInRange(formatDate(viewYear, viewMonth, day)) ? 'bg-purple-400 text-white rounded-md' : 'text-gray-700 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 rounded-md'" x-text="day"></div>
                                </template>
                            </div>
                        </div>
                        <!-- Next Month Calendar -->
                        <div class="flex-1 hidden md:block">
                            <div class="grid grid-cols-7 text-center text-[10px] font-bold text-gray-400 mb-4 uppercase tracking-widest"><div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div></div>
                            <div class="grid grid-cols-7 text-center text-sm font-medium gap-y-1">
                                <template x-for="i in getFirstDayOfMonth(viewMonth == 11 ? viewYear + 1 : viewYear, (viewMonth + 1) % 12)"><div class="py-2"></div></template>
                                <template x-for="day in getDaysInMonth(viewMonth == 11 ? viewYear + 1 : viewYear, (viewMonth + 1) % 12)">
                                    <div @click="selectDate(formatDate(viewMonth == 11 ? viewYear + 1 : viewYear, (viewMonth + 1) % 12, day))" 
                                         class="py-2 cursor-pointer transition-all duration-200"
                                         :class="isDateInRange(formatDate(viewMonth == 11 ? viewYear + 1 : viewYear, (viewMonth + 1) % 12, day)) ? 'bg-purple-400 text-white rounded-md' : 'text-gray-700 dark:text-gray-400 hover:bg-purple-50 dark:hover:bg-purple-900/10 rounded-md'" x-text="day"></div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                    <button @click="startDate = null; endDate = null" class="px-8 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs font-bold transition hover:bg-purple-50">Clear</button>
                    <button @click="openFilter = null" class="px-8 py-2 bg-purple-400 text-white rounded-full text-xs font-bold hover:opacity-80">Apply</button>
                </div>
            </div>
        </div>

        <!-- RESET FILTERS (Smooth Fade) -->
        <div x-show="hasFilters()" x-transition:enter="transition duration-300 opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-200 opacity-0" class="ml-auto">
            <button @click="resetFilters()" class="px-5 py-2 border border-gray-300 rounded-lg text-[13px] font-bold text-gray-800 dark:text-white hover:bg-purple-50 transition active:scale-95 shadow-sm">
                Reset Filters
            </button>
        </div>
    </div>

    <!-- CONTENT TABLE -->
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
        <div class="flex flex-col gap-2 px-5 mb-4 items-end sm:px-6">
            <input type="text" placeholder="Search anything in library..." class="mt-1.5 dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-64 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <div class="overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50/50 dark:bg-gray-800/50 border-y border-gray-200 dark:border-gray-700">
                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-widest text-left">
                        <th class="px-6 py-4">Creator</th>
                        <th class="px-6 py-4">Campaign Name</th>
                        <th class="px-6 py-4">Date Ordered</th>
                        <th class="px-6 py-4">Price</th>
                        <th class="px-6 py-4">Order Status</th>
                        <th class="px-6 py-4 text-right pr-10">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    <template x-for="order in paginatedTransactions" :key="order.id">
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img :src="order.image" class="w-9 h-9 rounded-full">
                                    <span class="text-sm font-bold text-gray-800 dark:text-white" x-text="order.name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400" x-text="order.campaign"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="order.date"></td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-white" x-text="order.price"></td>
                            <td class="px-6 py-4">
                                <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider" x-text="order.status"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-purple-400 transition-colors">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
            <button @click="currentPage--" :disabled="currentPage === 1" class="text-sm font-bold text-gray-500 hover:text-purple-400 disabled:opacity-30 transition">Previous</button>
            <div class="flex gap-2">
                <template x-for="p in totalPages">
                    <button @click="currentPage = p" :class="currentPage === p ? 'bg-purple-400 text-white shadow-md' : 'text-gray-500 hover:bg-gray-100'" class="w-8 h-8 rounded-lg text-xs font-bold transition" x-text="p"></button>
                </template>
            </div>
            <button @click="currentPage++" :disabled="currentPage === totalPages" class="text-sm font-bold text-gray-500 hover:text-purple-400 disabled:opacity-30 transition">Next</button>
        </div>
    </div>


    <!-- ========================= CART SIDEBAR MODAL ========================= -->
    <div x-show="isCartOpen" x-cloak class="fixed inset-0 z-[100] overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Background Overlay -->
        <div x-show="isCartOpen" x-transition.opacity @click="isCartOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

        <div class="fixed inset-y-0 right-0 flex max-w-full">
            <!-- Sidebar Content -->
            <div x-show="isCartOpen" 
                 x-transition:enter="transform transition ease-in-out duration-500" 
                 x-transition:enter-start="translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transform transition ease-in-out duration-500" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="translate-x-full" 
                 class="w-screen max-w-4xl flex shadow-2xl">
                
                <!-- LEFT PANEL (Estimated Results - Dark) -->
                <div class="hidden md:flex flex-col w-1/3 bg-black p-10 text-white justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-4">Estimated Results</h2>
                        <p class="text-sm text-gray-400 leading-relaxed mb-12">
                            Not all influencers will accept your order. Here is a projection of your actual outcome based on acceptance rates.
                        </p>

                        <div class="space-y-10">
                            <!-- Progress Bar: Influencers -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-lg font-bold">0 Influencers</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-600 w-0"></div>
                                </div>
                                <div class="text-right mt-2 text-xs font-bold text-gray-500">0 Influencers</div>
                            </div>

                            <!-- Progress Bar: Spend -->
                            <div>
                                <div class="flex justify-between items-end mb-2">
                                    <span class="text-lg font-bold">$0 Spend</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-gray-600 w-0"></div>
                                </div>
                                <div class="text-right mt-2 text-xs font-bold text-gray-500">$0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Protection Info -->
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight">
                            Payment Protection <br>
                            <span class="text-gray-600 font-medium normal-case">If an order is declined, funds will be refunded.</span>
                        </p>
                    </div>
                </div>

                <!-- RIGHT PANEL (Cart Details - White) -->
                <div class="flex-1 flex flex-col bg-white p-10 justify-between">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Cart</h2>
                        <button @click="isCartOpen = false" class="text-gray-400 hover:text-gray-900 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
                        </button>
                    </div>

                    <!-- Empty State Content -->
                    <div class="flex flex-col items-center justify-center text-center">
                        <svg class="w-24 h-24 text-gray-900 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-500 max-w-[240px] leading-relaxed">
                            Start adding influencers by clicking the button below
                        </p>
                    </div>

                    <!-- Footer Action -->
                    <button @click="isCartOpen = false" class="w-full bg-[#1A1A1A] text-white py-4 rounded-xl font-bold text-sm tracking-wide hover:bg-black transition shadow-lg active:scale-[0.98]">
                        Discover Influencers
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>


@endsection