@props(['statusOptions', 'typeOptions', 'search', 'status', 'type'])

<!-- Search and Filters in One Row -->
<div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
    <!-- Search Field -->
    <div class="relative max-w-xs">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
            <x-icons.search class="h-4 w-4" />
        </span>
        <input type="text" x-model="search" @keyup="filterCampaigns()" placeholder="Search campaigns..."
            class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-8 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
    </div>

    <!-- Filters Section -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-3 lg:ml-4">
        <!-- Status Filter -->
        <div class="relative flex-1 sm:flex-none sm:w-40">
            <select x-model="status" @change="filterCampaigns()"
                class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white appearance-none cursor-pointer pr-10">
                @foreach ($statusOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400 pointer-events-none"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>

        <!-- Type Filter -->
        <div class="relative flex-1 sm:flex-none sm:w-40">
            <select x-model="type" @change="filterCampaigns()"
                class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white appearance-none cursor-pointer pr-10">
                @foreach ($typeOptions as $option)
                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
            <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400 pointer-events-none"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>

        <!-- Reset Button -->
        <button @click="resetFilters()"
            class="h-12 px-6 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm transition-colors whitespace-nowrap">
            Reset
        </button>
    </div>
</div>
