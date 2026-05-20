@extends('frontend.layouts.app')

@section('title', 'Wishlist')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-950 px-4 py-6 sm:px-6 lg:px-8" data-wishlist-page>
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">My Wishlist</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Your curated collection of influencers across different lists</p>
        </div>

        @if ($wishlists->isNotEmpty())
            <div class="space-y-10">
                @foreach ($wishlists as $wishlist)
                    <div class="wishlist-list-section rounded-2xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden" data-wishlist-list-section data-wishlist-list-id="{{ $wishlist['id'] }}">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-200 dark:border-gray-800 bg-linear-to-r from-gray-50 to-white dark:from-gray-900 dark:to-gray-900 px-6 py-5">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $wishlist['name'] }}</h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span data-wishlist-list-count>{{ $wishlist['items_count'] }}</span> influencer(s) saved
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center rounded-full bg-purple-50 dark:bg-purple-950/50 px-3 py-1 text-xs font-medium text-purple-700 dark:text-purple-300">
                                    List
                                </span>
                            </div>
                        </div>

                        @if (!empty($wishlist['items']))
                            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                @foreach ($wishlist['items'] as $item)
                                    <div class="wishlist-item-card group relative flex flex-col overflow-hidden rounded-xl bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 transition-all duration-200 hover:shadow-lg hover:ring-2 hover:ring-purple-500 dark:hover:ring-purple-500" data-wishlist-item-card data-influencer-id="{{ $item['influencer_id'] }}">
                                        <a href="{{ $item['profile_url'] }}" class="block overflow-hidden">
                                            <div class="relative aspect-4/3 overflow-hidden bg-gray-100 dark:bg-gray-800">
                                                <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-110">
                                                <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent"></div>
                                                
                                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                                    <p class="text-lg font-bold text-white leading-tight">{{ $item['name'] }}</p>
                                                    <p class="text-sm text-white/90 mt-0.5 line-clamp-1">{{ $item['title'] }}</p>
                                                </div>
                                            </div>
                                        </a>

                                        <div class="flex flex-col flex-1 p-4 space-y-3">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $item['handle'] }}</p>
                                                <div class="flex items-center gap-1 mt-1">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item['location'] }}</p>
                                                </div>
                                            </div>

                                            <div class="flex items-center justify-between gap-3 pt-2 border-t border-gray-100 dark:border-gray-800">
                                                <a href="{{ $item['profile_url'] }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
                                                    View Profile
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                    </svg>
                                                </a>
                                                <button
                                                    type="button"
                                                    class="js-wishlist-remove inline-flex items-center gap-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors"
                                                    data-remove-url="{{ route('frontend.wishlists.items.destroy', [$wishlist['id'], $item['influencer_id']]) }}"
                                                    data-influencer-id="{{ $item['influencer_id'] }}"
                                                    data-list-id="{{ $wishlist['id'] }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="wishlist-list-empty px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No influencers in this list yet</p>
                                <a href="{{ route('influencers') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700">
                                    Browse influencers to add
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="hidden" data-wishlist-page-empty>
                <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white px-6 py-16 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:px-10">
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-linear-to-b from-purple-100/70 via-transparent to-transparent dark:from-purple-500/10"></div>
                    <div class="relative mx-auto flex max-w-xl flex-col items-center">
                        <div class="mb-6 flex h-18 w-18 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 shadow-sm ring-1 ring-purple-100 dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20">
                            <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>

                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-purple-600 dark:text-purple-300">Wishlist</p>
                        <h3 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Your wishlist is empty</h3>
                        <p class="mt-3 max-w-lg text-base leading-7 text-gray-600 dark:text-gray-400">Start building your collection by adding influencers from our directory. You can organize them into named lists anytime.</p>

                        <a href="{{ route('influencers') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-purple-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-purple-600/20 transition-all hover:-translate-y-0.5 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Influencers
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="relative overflow-hidden rounded-3xl border border-gray-200 bg-white px-6 py-16 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:px-10" data-wishlist-page-empty>
                <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-linear-to-b from-purple-100/70 via-transparent to-transparent dark:from-purple-500/10"></div>
                <div class="relative mx-auto flex max-w-xl flex-col items-center">
                    <div class="mb-6 flex h-18 w-18 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 shadow-sm ring-1 ring-purple-100 dark:bg-purple-500/10 dark:text-purple-300 dark:ring-purple-500/20">
                        <svg class="h-9 w-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>

                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-purple-600 dark:text-purple-300">Wishlist</p>
                    <h3 class="mt-3 text-2xl font-bold text-gray-900 dark:text-white sm:text-3xl">Your wishlist is empty</h3>
                    <p class="mt-3 max-w-lg text-base leading-7 text-gray-600 dark:text-gray-400">Start building your collection by adding influencers from our directory. You can organize them into named lists anytime.</p>

                    <a href="{{ route('influencers') }}" class="mt-8 inline-flex items-center gap-2 rounded-xl bg-purple-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-purple-600/20 transition-all hover:-translate-y-0.5 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Browse Influencers
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection