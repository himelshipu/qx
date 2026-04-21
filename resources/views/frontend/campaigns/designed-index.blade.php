@extends('frontend.layouts.app')

@section('title', 'Campaigns')

@section('content')
	<div x-data="campaignFilter({ search: @js($search ?? ''), status: @js($status ?? 'all'), type: @js($type ?? 'all'), campaigns: @js($campaignsData) })" class="mt-8 px-2">
	
		<!-- Header with Create Campaign Button -->
		<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
			
			@if ($userType === 'brand')
				<a href="{{ route('frontend.campaigns.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					<span>Create Campaign</span>
				</a>
			@endif
		</div>

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

		<!-- Results Counter -->
		<div class="mb-6 flex items-center justify-between px-1">
			<div class="text-xs uppercase text-gray-400 dark:text-gray-600">
				<span>Showing <span x-text="filteredCampaigns.length"></span> of {{ $campaigns->total() }}</span>
			</div>
			<div class="text-xs uppercase text-gray-400 dark:text-gray-600">
				<span>My Campaigns</span>
			</div>
		</div>

		<!-- Campaigns Grid -->
		<div class="mt-6 grid grid-cols-1 gap-6 pb-20 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
			<template x-for="campaign in filteredCampaigns" :key="campaign.id">
				<div
					class="group relative overflow-hidden rounded-[1.8rem] border border-gray-100 bg-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl dark:border-gray-800 dark:bg-gray-900"
					style="aspect-ratio: 4 / 3;">
					<a :href="`/campaigns/${campaign.id}`" class="absolute inset-0 z-10"
						:aria-label="`View ${campaign.title}`"></a>

					<img :src="campaign.image" alt="Campaign Preview"
						class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
						onerror="this.onerror=null;this.src='{{ asset('images/campaignApply.png') }}';">

					<div class="absolute inset-0 z-10 opacity-90" style="background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.30) 55%, transparent 100%);"></div>

					<div class="absolute left-5 top-5 z-20 flex items-center gap-2">
						<span :class="getStatusBadgeClass(campaign.status)"
							class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[10px] font-medium uppercase">
							<span x-text="capitalizeStatus(campaign.status)"></span>
						</span>
						<template x-if="!campaign.is_active">
							<span class="rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-medium uppercase text-white backdrop-blur">
								Inactive
							</span>
						</template>
					</div>

					<div class="absolute right-5 top-5 z-20 flex items-center gap-2">
						<a :href="`/campaigns/${campaign.id}`"
							class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
							title="View campaign">
							<x-icons.eye class="h-4 w-4" />
						</a>
						<template x-if="campaign.canEdit">
							<a :href="`/campaigns/${campaign.id}/edit`"
								class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
								title="Edit campaign">
								<x-icons.edit class="h-4 w-4" />
							</a>
							<form :action="`/campaigns/${campaign.id}`" method="POST" class="relative z-20"
								@submit.prevent="deleteCampaign($event, campaign.id)">
								@csrf
								@method('DELETE')
								<button type="submit" data-confirm-title="Delete Campaign"
									data-confirm-message="Delete this campaign? This action cannot be undone." data-confirm-button="Delete"
									data-confirm-variant="danger" title="Delete campaign"
									class="js-confirmable inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-red-500/80">
									<x-icons.trash class="h-4 w-4" />
								</button>
							</form>
						</template>
					</div>

					<div class="absolute bottom-0 left-0 right-0 z-20 p-8">
						<a :href="`/campaigns/${campaign.id}`">
							<h3 class="line-clamp-2 text-base font-bold leading-tight text-white md:text-lg group-hover:underline"
								x-text="campaign.title">
							</h3>
						</a>
						<p class="mt-1.5 text-[12px] font-semibold uppercase tracking-wider text-gray-300"
							x-text="capitalizeStatus(campaign.campaign_type)">
						</p>
						<div class="mt-3 flex flex-wrap items-center gap-2 text-[10px] uppercase tracking-[0.22em] text-white/70">
							<span x-text="`${campaign.applications_count} Applications`"></span>
							<span x-text="`${campaign.categories_count} Niches`"></span>
							<template x-if="campaign.targeting?.influencer_count">
								<span x-text="`${campaign.targeting.influencer_count} Influencers Target`"></span>
							</template>
						</div>
					</div>
				</div>
			</template>
		</div>

		<!-- Empty State -->
		<template x-if="filteredCampaigns.length === 0 && campaigns.length > 0">
			<div
				class="mt-6 rounded-[1.8rem] border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
				<p class="text-sm text-gray-500 dark:text-gray-400">No campaigns found for the current filters.</p>
				<button @click="resetFilters()"
					class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.filter class="h-4 w-4" />
					Clear Filters
				</button>
			</div>
		</template>

		<!-- No Results State -->
		<template x-if="campaigns.length === 0">
			<div
				class="mt-6 rounded-[1.8rem] border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
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
	</div>

	<style>
		[x-cloak] {
			display: none !important;
		}

		.line-clamp-2 {
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
		}
	</style>
@endsection
