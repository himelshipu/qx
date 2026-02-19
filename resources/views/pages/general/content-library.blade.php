@extends('layouts.general.app')

@section('content')
<section class="w-full bg-white dark:bg-gray-950 p-8" 
                x-data="{ 
                    openFilter: null, 
                    startDate: '2026-02-19', 
                    endDate: '2026-03-25',
                    selectedCampaigns: [],
                    selectedPlatforms: [],
                    
                    // Dummy Data
                    campaigns: ['Summer Blast 2026', 'QX Launch Event', 'Fitness Glow Up', 'Tech Unboxing Series', 'Winter Fashion Week', 'Gaming Marathon'],
                    platforms: ['Instagram', 'TikTok', 'YouTube', 'Twitter / X', 'Twitch', 'UGC Content'],

                    toggleSelection(array, item) {
                        if (this[array].includes(item)) {
                            this[array] = this[array].filter(i => i !== item);
                        } else {
                            this[array].push(item);
                        }
                    },

                    resetFilters() {
                        this.openFilter = null;
                        this.selectedCampaigns = [];
                        this.selectedPlatforms = [];
                        this.startDate = '';
                        this.endDate = '';
                    }
                }" >
            
            <!-- Header Section -->
            <div class="mb-10">
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white mt-8 leading-[1.1] tracking-tight">Content Library</h1>
                <p class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg">See all your delivered content in one place</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-8">
                <div class="relative max-w-sm">
                    <input type="text" placeholder="Search anything in library..." 
                        class="w-full px-5 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-4 focus:ring-gray-100 transition-all placeholder-gray-400">
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-wrap items-center gap-4 mb-10">
                
                <!-- Filter: Campaign -->
                <div class="relative">
                    <button @click="openFilter = (openFilter === 'campaign' ? null : 'campaign')" 
                            class="flex items-center gap-3 px-5 py-2 border rounded-full bg-white dark:bg-transparent transition active:scale-95"
                            :class="selectedCampaigns.length > 0 ? 'border-black bg-gray-50' : 'border-gray-300'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span class="text-sm font-bold" x-text="selectedCampaigns.length > 0 ? 'Campaign (' + selectedCampaigns.length + ')' : 'Campaign'"></span>
                        <svg class="w-4 h-4 transition-transform" :class="openFilter === 'campaign' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
                    </button>

                    <!-- Dropdown Body -->
                    <div x-show="openFilter === 'campaign'" x-cloak @click.away="openFilter = null" 
                        class="absolute top-full left-0 mt-3 w-72 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 p-2 max-h-80 overflow-y-auto">
                        <template x-for="camp in campaigns" :key="camp">
                            <div @click="toggleSelection('selectedCampaigns', camp)" 
                                class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-gray-50 transition group">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black" x-text="camp"></span>
                                <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition"
                                    :class="selectedCampaigns.includes(camp) ? 'bg-black border-black' : 'border-gray-200'">
                                    <svg x-show="selectedCampaigns.includes(camp)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Filter: Platform -->
                <div class="relative">
                    <button @click="openFilter = (openFilter === 'platform' ? null : 'platform')" 
                            class="flex items-center gap-3 px-5 py-2 border rounded-full bg-white dark:bg-transparent transition active:scale-95"
                            :class="selectedPlatforms.length > 0 ? 'border-black bg-gray-50' : 'border-gray-300'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span class="text-sm font-bold" x-text="selectedPlatforms.length > 0 ? 'Platform (' + selectedPlatforms.length + ')' : 'Platform'"></span>
                        <svg class="w-4 h-4 transition-transform" :class="openFilter === 'platform' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
                    </button>

                    <!-- Dropdown Body -->
                    <div x-show="openFilter === 'platform'" x-cloak @click.away="openFilter = null" 
                        class="absolute top-full left-0 mt-3 w-64 bg-white border border-gray-100 rounded-2xl shadow-2xl z-50 p-2">
                        <template x-for="plat in platforms" :key="plat">
                            <div @click="toggleSelection('selectedPlatforms', plat)" 
                                class="flex items-center justify-between px-4 py-3 rounded-xl cursor-pointer hover:bg-gray-50 transition group">
                                <span class="text-sm font-medium text-gray-700 group-hover:text-black" x-text="plat"></span>
                                <div class="w-5 h-5 border-2 rounded flex items-center justify-center transition"
                                    :class="selectedPlatforms.includes(plat) ? 'bg-black border-black' : 'border-gray-200'">
                                    <svg x-show="selectedPlatforms.includes(plat)" class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Filter: Date -->
                <div class="relative" x-data="{ 
                    startDate: '2026-02-19', 
                    endDate: '2026-03-25',
                    selectDate(date) {
                        if (!this.startDate || (this.startDate && this.endDate)) {
                            this.startDate = date;
                            this.endDate = null;
                        } else {
                            if (date < this.startDate) {
                                this.endDate = this.startDate;
                                this.startDate = date;
                            } else {
                                this.endDate = date;
                            }
                        }
                    },
                    isDateInRange(date) {
                        return this.startDate && this.endDate && date >= this.startDate && date <= this.endDate;
                    },
                    isStart(date) { return date === this.startDate; },
                    isEnd(date) { return date === this.endDate; }
                }">
                    <!-- Trigger Button -->
                    <button @click="openFilter = (openFilter === 'date' ? null : 'date')" 
                            class="flex items-center gap-3 px-5 py-2 border border-gray-300 rounded-full dark:text-white bg-white dark:bg-transparent transition hover:bg-pink-50 active:scale-95 shadow-sm">
                        <svg class="w-5 h-5 text-gray-800 dark:text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="1.5"/></svg>
                        <span class="text-sm font-bold">Date: 
                            <span class="font-normal text-gray-600 dark:text-gray-300" x-text="startDate && endDate ? startDate + ' - ' + endDate : 'Select Range'"></span>
                        </span>
                        <svg class="w-4 h-4 transition-transform" :class="openFilter === 'date' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2.5"/></svg>
                    </button>
                    
                    <!-- Interactive Dropdown -->
                    <div x-show="openFilter === 'date'" x-cloak @click.away="openFilter = null" 
                        class="absolute top-full left-0 md:-left-40 mt-3 w-[700px] bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl shadow-[0_30px_60px_rgba(0,0,0,0.15)] z-50 overflow-hidden">
                        
                        <div class="p-8">
                            <!-- Dynamic Date Inputs -->
                            <div class="grid grid-cols-2 gap-6 mb-8">
                                <div class="relative">
                                    <label class="mb-2 block text-xs font-bold text-gray-400 uppercase tracking-wider">Start Date</label>
                                    <input type="text" readonly :value="startDate" class="h-12 w-full rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 text-sm font-bold outline-none dark:text-white">
                                </div>
                                <div class="relative">
                                    <label class="mb-2 block text-xs font-bold text-gray-400 uppercase tracking-wider">End Date</label>
                                    <input type="text" readonly :value="endDate" class="h-12 w-full rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 text-sm font-bold outline-none dark:text-white">
                                </div>
                            </div>

                            <!-- Calendars -->
                            <div class="flex justify-between gap-10">
                                <!-- Feb 2026 -->
                                <div class="flex-1">
                                    <div class="text-center font-bold text-sm mb-4 text-gray-900 dark:text-white">Feb 2026</div>
                                    <div class="grid grid-cols-7 text-center text-[10px] font-bold text-gray-400 mb-2 uppercase"><div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div></div>
                                    <div class="grid grid-cols-7 text-center text-xs gap-y-0.5">
                                        @foreach(range(1, 28) as $day)
                                            @php $d = "2026-02-".sprintf('%02d', $day); @endphp
                                            <div @click="selectDate('{{ $d }}')" 
                                                class="py-2 cursor-pointer transition-all duration-200"
                                                :class="{
                                                    'bg-pink-400 text-white': isDateInRange('{{ $d }}'),
                                                    'rounded-l-lg': isStart('{{ $d }}'),
                                                    'rounded-r-lg': isEnd('{{ $d }}'),
                                                    'text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-pink-900/30 rounded-md': !isDateInRange('{{ $d }}')
                                                }">
                                                {{ $day }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Mar 2026 -->
                                <div class="flex-1">
                                    <div class="text-center font-bold text-sm mb-4 text-gray-900 dark:text-white">Mar 2026</div>
                                    <div class="grid grid-cols-7 text-center text-[10px] font-bold text-gray-400 mb-2 uppercase"><div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div></div>
                                    <div class="grid grid-cols-7 text-center text-xs gap-y-0.5">
                                        @foreach(range(1, 31) as $day)
                                            @php $d = "2026-03-".sprintf('%02d', $day); @endphp
                                            <div @click="selectDate('{{ $d }}')" 
                                                class="py-2 cursor-pointer transition-all duration-200"
                                                :class="{
                                                    'bg-pink-400 text-white': isDateInRange('{{ $d }}'),
                                                    'rounded-l-lg': isStart('{{ $d }}'),
                                                    'rounded-r-lg': isEnd('{{ $d }}'),
                                                    'text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-pink-900/30 rounded-md': !isDateInRange('{{ $d }}')
                                                }">
                                                {{ $day }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                            <button @click="startDate = null; endDate = null" class="px-8 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-full text-xs font-bold transition hover:bg-pink-50 dark:hover:text-pink-300">Clear</button>
                            <button @click="openFilter = null" class="px-8 py-2 bg-pink-400 text-white rounded-full text-xs font-bold transition hover:bg-pink-500 shadow-md">Apply</button>
                        </div>
                    </div>
                </div>

                <!-- RESET FILTERS -->
                <button @click="resetFilters()" 
                        class="ml-auto px-5 py-2 border border-gray-300 rounded-xl text-[13px] font-bold text-gray-800 dark:text-white dark:hover:text-[#222] hover:bg-gray-50 transition active:scale-95 shadow-sm">
                    Reset Filters
                </button>
            </div>
           <div x-data="{
            transactions: [
                {
                    id: 1,
                    name: 'Bought PYPL',
                    image: '/images/user/user-03.jpg',
                    date: 'Nov 23, 01:00 PM',
                    price: '$2,567.88',
                    Campaign: 'Summer Blast 2026',
                    status: 'Success',
                },
                {
                    id: 2,
                    name: 'Bought AAPL',
                    image: '/images/user/user-02.jpg',
                    date: 'Nov 23, 01:00 PM',
                    price: '$2,567.88',
                    Campaign: 'QX Launch Event',
                    status: 'Pending',
                },
                {
                    id: 3,
                    name: 'Sell KKST',
                    image: '/images/user/user-01.jpg',
                    date: 'Nov 23, 01:00 PM',
                    price: '$2,567.88',
                    Campaign: 'Fitness Glow Up',
                    status: 'Success',
                },
                {
                    id: 4,
                    name: 'Bought FB',
                    image: '/images/user/user-04.jpg',
                    date: 'Nov 23, 01:00 PM',
                    price: '$2,567.88',
                    Campaign: 'Tech Unboxing Series',
                    status: 'Success',
                },
                {
                    id: 5,
                    name: 'Sell AMZN',
                    image: '/images/user/user-05.jpg',
                    date: 'Nov 23, 01:00 PM',
                    price: '$2,567.88',
                    Campaign: 'Winter Fashion Week',
                    status: 'Failed',
                },
                {
                    id: 6,
                    name: 'Bought MSFT',
                    image: '/images/user/user-06.jpg',
                    date: 'Nov 22, 01:00 PM',
                    price: '$1,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 7,
                    name: 'Bought GOOG',
                    image: '/images/user/user-07.jpg',
                    date: 'Nov 22, 01:00 PM',
                    price: '$3,567.88',
                    Campaign: 'Finance',
                    status: 'Pending',
                },
                {
                    id: 8,
                    name: 'Sell TSLA',
                    image: '/images/user/user-08.jpg',
                    date: 'Nov 22, 01:00 PM',
                    price: '$4,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 9,
                    name: 'Bought NVDA',
                    image: '/images/user/user-09.jpg',
                    date: 'Nov 22, 01:00 PM',
                    price: '$5,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 10,
                    name: 'Sell META',
                    image: '/images/user/user-10.jpg',
                    date: 'Nov 22, 01:00 PM',
                    price: '$6,567.88',
                    Campaign: 'Finance',
                    status: 'Failed',
                },
                {
                    id: 11,
                    name: 'Bought DIS',
                    image: '/images/user/user-04.jpg',
                    date: 'Nov 21, 01:00 PM',
                    price: '$7,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 12,
                    name: 'Bought NFLX',
                    image: '/images/user/user-05.jpg',
                    date: 'Nov 21, 01:00 PM',
                    price: '$8,567.88',
                    Campaign: 'Finance',
                    status: 'Pending',
                },
                {
                    id: 13,
                    name: 'Sell CRM',
                    image: '/images/user/user-06.jpg',
                    date: 'Nov 21, 01:00 PM',
                    price: '$9,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 14,
                    name: 'Bought TSLA',
                    image: '/images/user/user-02.jpg',
                    date: 'Nov 21, 01:00 PM',
                    price: '$10,567.88',
                    Campaign: 'Finance',
                    status: 'Success',
                },
                {
                    id: 15,
                    name: 'Sell AAPL',
                    image: '/images/user/user-01.jpg',
                    date: 'Nov 21, 01:00 PM',
                    price: '$11,567.88',
                    Campaign: 'Finance',
                    status: 'Failed',
                },
            ],
            itemsPerPage: 12,
            currentPage: 1,
            dropdownOpen: null,
            get totalPages() {
                return Math.ceil(this.transactions.length / this.itemsPerPage);
            },
            get paginatedTransactions() {
                const start = (this.currentPage - 1) * this.itemsPerPage;
                const end = start + this.itemsPerPage;
                return this.transactions.slice(start, end);
            },
            get displayedPages() {
                const range = [];
                for (let i = 1; i <= this.totalPages; i++) {
                    if (
                        i === 1 ||
                        i === this.totalPages ||
                        (i >= this.currentPage - 1 && i <= this.currentPage + 1)
                    ) {
                        range.push(i);
                    } else if (range[range.length - 1] !== '...') {
                        range.push('...');
                    }
                }
                return range;
            },
            prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                }
            },
            nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                }
            },
            goToPage(page) {
                if (typeof page === 'number' && page >= 1 && page <= this.totalPages) {
                    this.currentPage = page;
                }
            },
            getStatusClass(status) {
                const classes = {
                    'Success': 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500',
                    'Pending': 'bg-yellow-50 text-yellow-600 dark:bg-yellow-500/15 dark:text-orange-400',
                    'Failed': 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-500',
                };
                return classes[status] || '';
            },
            toggleDropdown(id) {
                this.dropdownOpen = this.dropdownOpen === id ? null : id;
            }
                }" class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white pt-4 gap-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <!-- Header -->
                <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <form>
                            <div class="relative">
                                <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                                    <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" fill=""/>
                                    </svg>
                                </button>
                                <input type="text" placeholder="Search..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden mt-6">
                    <div class="max-w-full px-5 overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-gray-200 border-y dark:border-gray-700">
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Campaign Name</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Brand Info</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Join at</th>
                                    <th scope="col" class="px-4 py-3 font-normal tracking-wider text-left text-gray-500 capitalize text-theme-sm">Price</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Status</th>
                                    <th scope="col" class="relative px-4 py-3 capitalize">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="transaction in paginatedTransactions" :key="transaction.id">
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="transaction.Campaign"></div>
                                        </td>`
                                        <td class="py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="shrink-0 w-8 h-8">
                                                    <img class="w-8 h-8 rounded-full" :src="transaction.image" alt="">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white" x-text="transaction.name"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="transaction.date"></div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400" x-text="transaction.price"></div>
                                        </td>
                                        
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusClass(transaction.status)" x-text="transaction.status"></span>
                                        </td>
                                        <td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
                                            <div class="flex justify-center relative">
                                                <x-common.table-dropdown>
                                                    <x-slot name="button">
                                                        <button type="button" id="options-menu" aria-haspopup="true" aria-expanded="true" class="text-gray-500 dark:text-gray-400'">
                                                            <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z" fill="currentColor" />
                                                            </svg>
                                                        </button>
                                                    </x-slot>
                
                                                    <x-slot name="content">
                                                        <a href="#" class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300" role="menuitem">
                                                            View More
                                                        </a>
                                                        <a href="#" class="flex w-full px-3 py-2 font-medium text-left text-gray-500 rounded-lg text-theme-xs hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300" role="menuitem">
                                                            Delete
                                                        </a>
                                                    </x-slot>
                                                </x-common.table-dropdown>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
                    <div class="flex items-center justify-between">
                        <button @click="prevPage" :disabled="currentPage === 1" :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z" fill="currentColor"/>
                            </svg>
                            <span class="hidden sm:inline">Previous</span>
                        </button>

                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">
                            Page <span x-text="currentPage"></span> of <span x-text="totalPages"></span>
                        </span>

                        <ul class="hidden items-center gap-0.5 sm:flex">
                            <template x-for="page in displayedPages" :key="page">
                                <li>
                                    <button x-show="page !== '...'" @click="goToPage(page)" :class="currentPage === page ? 'bg-pink-500 text-white' : 'text-gray-700 hover:bg-blue-500/[0.08] hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-500'" class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium" x-text="page"></button>
                                    <span x-show="page === '...'" class="flex h-10 w-10 items-center justify-center text-gray-500">...</span>
                                </li>
                            </template>
                        </ul>

                        <button @click="nextPage" :disabled="currentPage === totalPages" :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200 sm:px-3.5">
                            <span class="hidden sm:inline">Next</span>
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z" fill="currentColor"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
</section>

@endsection