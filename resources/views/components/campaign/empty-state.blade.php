@props(['userType'])

<!-- Empty State - No Results for Filters -->
<template x-if="filteredCampaigns.length === 0 && campaigns.length > 0">
    <div class="mt-6 rounded-[1.8rem] border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm text-gray-500 dark:text-gray-400">No campaigns found for the current filters.</p>
        <button @click="resetFilters()"
            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
            <x-icons.filter class="h-4 w-4" />
            Clear Filters
        </button>
    </div>
</template>

<!-- No Results State - No Campaigns -->
<template x-if="campaigns.length === 0">
    <div class="mt-6 rounded-[1.8rem] border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
        <p class="text-sm text-gray-500 dark:text-gray-400">No campaigns available.</p>
        @if ($userType === 'brand')
            <a href="{{ route('frontend.campaigns.create') }}"
                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                <x-icons.plus class="h-4 w-4" />
                Create First Campaign
            </a>
        @endif
    </div>
</template>
