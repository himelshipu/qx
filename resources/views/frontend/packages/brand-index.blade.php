@extends('frontend.layouts.app')

@section('content')
	<section class="py-10" x-data="brandPackagesFilter()">
		<div class="max-w-screen-2xl mx-auto px-4">
			<!-- Header Section -->
			<div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-10">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Purchased Packages</h1>
					<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View all packages you've purchased from influencers</p>
				</div>
			</div>

			<!-- Search & Filter Section -->
			<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-3 mb-8">
				<div>
					<input type="text" x-model="search" placeholder="Search packages..." @keyup="filterPackages()"
						class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition" />
				</div>
				<div class="relative">
					<select x-model="platform" @change="filterPackages()"
						class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition appearance-none cursor-pointer pr-10">
						<option value="">All Platforms</option>
						<option value="facebook">Facebook</option>
						<option value="instagram">Instagram</option>
						<option value="tiktok">TikTok</option>
						<option value="linkedin">LinkedIn</option>
						<option value="x">X (Twitter)</option>
						<option value="youtube">YouTube</option>
						<option value="ugc">UGC</option>
						<option value="other">Other</option>
					</select>
					<svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400 pointer-events-none"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
					</svg>
				</div>
				<div class="relative">
					<select x-model="influencer" @change="filterPackages()"
						class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition appearance-none cursor-pointer pr-10">
						<option value="">All Influencers</option>
						<template x-for="influencerName in getUniqueInfluencers()" :key="influencerName">
							<option :value="influencerName" x-text="influencerName"></option>
						</template>
					</select>
					<svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400 pointer-events-none"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
					</svg>
				</div>
				<button @click="resetFilters()"
					class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm rounded-lg transition-colors">
					Reset
				</button>
			</div>

			<!-- Empty State (filtered none) -->
			<template x-if="filteredPackages.length === 0 && packages.length > 0">
				<div class="text-center py-16">
					<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
						<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No packages found</h3>
					<p class="text-gray-600 dark:text-gray-400 mb-6">No packages match your search or filter criteria. Try adjusting
						your filters.</p>
					<button @click="resetFilters()"
						class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-lg transition-colors">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
						</svg>
						Clear Filters
					</button>
				</div>
			</template>

			<!-- Empty State (no packages at all) -->
			<template x-if="packages.length === 0">
				<div class="text-center py-16">
					<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
						<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M20 7l-8-4-8 4m16 0l-8 4m0 0l-8-4m8 4v10l8-4v-10M4 7v10l8 4" />
						</svg>
					</div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Purchases Yet</h3>
					<p class="text-gray-600 dark:text-gray-400 mb-6">You haven't purchased any packages yet. Start exploring influencers
						and add packages to your cart.</p>
					<a href="{{ route('influencers') }}"
						class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-lg transition-colors">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
						Browse Influencers
					</a>
				</div>
			</template>

			<!-- Packages Grid -->
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5" x-show="filteredPackages.length > 0">
				<template x-for="pkg in filteredPackages" :key="pkg.id">
					<div
						class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300">
						<!-- Platform Badge -->
						<div class="h-1" :style="{ backgroundColor: getPlatformColor(pkg.platform) }"></div>

						<!-- Card Content -->
						<div class="p-5">
							<div class="flex items-start justify-between gap-3 mb-3">
								<div class="flex-1">
									<h3 class="text-base font-bold text-gray-900 dark:text-white line-clamp-2">
										<span x-text="pkg.name"></span>
									</h3>
									<p class="text-xs text-gray-600 dark:text-gray-400 mt-1" x-text="'by ' + pkg.influencer_name"></p>
								</div>
								<span class="inline-flex items-center px-2 py-1 text-xs font-semibold text-white rounded whitespace-nowrap"
									:style="{ backgroundColor: getPlatformColor(pkg.platform) }" x-text="getPlatformName(pkg.platform)">
								</span>
							</div>
							<p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-4" x-text="pkg.description"></p>

							<!-- Stats Grid -->
							<div class="grid grid-cols-2 gap-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4">
								<div class="text-center">
									<p class="text-xs text-gray-600 dark:text-gray-400">Price</p>
									<p class="text-sm font-bold text-purple-600 dark:text-purple-400">
										<span x-text="'$' + parseInt(pkg.base_price).toLocaleString()"></span>
									</p>
								</div>
								<div class="text-center border-l border-gray-200 dark:border-gray-600">
									<p class="text-xs text-gray-600 dark:text-gray-400">Delivery</p>
									<p class="text-sm font-bold text-gray-900 dark:text-white">
										<span x-text="pkg.delivery_days + ' days'"></span>
									</p>
								</div>
							</div>

							<!-- View Button -->
							<a :href="`/packages/${pkg.id}`"
								class="w-full inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-purple-600 dark:bg-purple-600 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-700 transition-colors">
								<svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
								</svg>
								View Details
							</a>
						</div>
					</div>
				</template>
			</div>
		</div>

		<!-- Alpine.js Script -->
		@php
			$packagesData = $packages
			    ->map(
			        fn($p) => [
			            'id' => $p->id,
			            'name' => $p->name,
			            'description' => $p->description,
			            'platform' => $p->platform,
			            'base_price' => $p->base_price,
			            'currency' => $p->currency,
			            'delivery_days' => $p->delivery_days,
			            'influencer_name' => $p->influencer?->user?->name ?? 'Unknown',
			        ],
			    )
			    ->values()
			    ->all();
		@endphp

		<script>
			function brandPackagesFilter() {
				return {
					search: '',
					platform: '',
					influencer: '',
					packages: @json($packagesData),
					filteredPackages: [],

					init() {
						this.filterPackages();
					},

					filterPackages() {
						const search = this.search.toLowerCase();
						const platform = this.platform;
						const influencer = this.influencer;

						this.filteredPackages = this.packages.filter(pkg => {
							const matchesSearch = !search ||
								pkg.name.toLowerCase().includes(search) ||
								pkg.description.toLowerCase().includes(search);

							const matchesPlatform = !platform || pkg.platform === platform;
							const matchesInfluencer = !influencer || pkg.influencer_name === influencer;

							return matchesSearch && matchesPlatform && matchesInfluencer;
						});
					},

					resetFilters() {
						this.search = '';
						this.platform = '';
						this.influencer = '';
						this.filterPackages();
					},

					getUniqueInfluencers() {
						return [...new Set(this.packages.map(pkg => pkg.influencer_name))].sort();
					},

					getPlatformColor(platform) {
						const colors = {
							'facebook': '#1877F2',
							'instagram': '#E1306C',
							'tiktok': '#000000',
							'linkedin': '#0A66C2',
							'x': '#000000',
							'youtube': '#FF0000',
							'ugc': '#A855F7',
							'other': '#6B7280'
						};
						return colors[platform] || '#6B7280';
					},

					getPlatformName(platform) {
						const names = {
							'facebook': 'Facebook',
							'instagram': 'Instagram',
							'tiktok': 'TikTok',
							'linkedin': 'LinkedIn',
							'x': 'X',
							'youtube': 'YouTube',
							'ugc': 'UGC',
							'other': 'Other'
						};
						return names[platform] || platform;
					}
				}
			}
		</script>
	</section>
@endsection
