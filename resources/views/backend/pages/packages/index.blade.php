@extends('backend.layouts.app')

@section('title', 'Packages')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Packages" />

    <div data-packages-dashboard data-table-url="{{ route('dashboard.packages.table') }}" class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
                <p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Inactive</p>
                <p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">{{ $stats['inactive'] }}</p>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">In Use</p>
                <p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['in_use'] }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Package Management</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage global packages used across carts and order items.</p>
                </div>
                <a href="{{ route('dashboard.packages.create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <x-icons.plus class="h-4 w-4" />
                    New Package
                </a>
            </div>

            <div class="p-5">
                <form data-packages-filter-form method="GET" action="{{ route('dashboard.packages.index') }}" class="mb-5 flex flex-wrap items-end gap-3">
                    <div class="min-w-60 flex-1">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <x-icons.search class="h-4 w-4" />
                            </span>
                            <input name="q" type="text" value="{{ $filters['q'] ?? '' }}" placeholder="Search by name, platform, or currency"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div class="min-w-56" data-influencer-combobox>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Influencer</label>
                        <input type="hidden" name="influencer_id" value="{{ $filters['influencer_id'] ?? 'all' }}" data-influencer-value>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <x-icons.search class="h-4 w-4" />
                            </span>
                            <input
                                type="text"
                                autocomplete="off"
                                spellcheck="false"
                                placeholder="Search influencer..."
                                data-influencer-input
                                aria-autocomplete="list"
                                aria-expanded="false"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-9 pr-9 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                            >
                            <button
                                type="button"
                                data-influencer-clear
                                aria-label="Clear selection"
                                class="absolute inset-y-0 right-0 hidden items-center px-2 text-gray-400 transition hover:text-gray-700 dark:hover:text-gray-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <ul
                                data-influencer-list
                                role="listbox"
                                class="absolute left-0 right-0 z-30 mt-1 hidden max-h-60 overflow-y-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800">
                                @foreach ($influencerOptions as $option)
                                    <li
                                        data-influencer-option
                                        data-value="{{ $option['value'] }}"
                                        data-label="{{ $option['label'] }}"
                                        role="option"
                                        aria-selected="{{ (string) ($filters['influencer_id'] ?? 'all') === $option['value'] ? 'true' : 'false' }}"
                                        class="influencer-combobox-option cursor-pointer px-3 py-2 text-sm text-gray-700 transition aria-selected:bg-gray-100 aria-selected:font-medium hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 dark:aria-selected:bg-gray-700">
                                        {{ $option['label'] }}
                                    </li>
                                @endforeach
                                <li data-influencer-empty class="hidden px-3 py-2 text-sm text-gray-500 dark:text-gray-400">No matches found</li>
                            </ul>
                        </div>
                    </div>
                    <div class="min-w-44">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Platform</label>
                        <select name="platform"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @foreach ($platformOptions as $option)
                                <option value="{{ $option['value'] }}" @selected(($filters['platform'] ?? 'all') === $option['value'])>
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-44">
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>All</option>
                            <option value="active" @selected(($filters['status'] ?? 'all') === 'active')>Active</option>
                            <option value="inactive" @selected(($filters['status'] ?? 'all') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" data-packages-reset
                            class="h-10 rounded-lg bg-gray-200 px-4 text-sm font-medium text-gray-900 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                            Reset
                        </button>
                    </div>
                </form>

                <div data-packages-results>
                    @include('backend.pages.packages._results', ['packages' => $packages])
                </div>
            </div>
        </div>
    </div>
@endsection
