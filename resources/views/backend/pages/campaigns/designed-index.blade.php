@extends('backend.layouts.app')

@section('title', 'Campaigns')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="All Campaigns" />

	<div x-data="{ showFilters: {{ $status !== 'all' || $type !== 'all' ? 'true' : 'false' }} }" class="mt-8 px-2">
		<form method="GET" action="{{ route('dashboard.campaigns.designed') }}" class="space-y-4">
			<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
				<div class="relative flex-1 max-w-md">
					<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
						<x-icons.search class="h-4 w-4" />
					</span>
					<input type="text" name="q" value="{{ $search }}" placeholder="Search campaigns by name or type..."
						class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-8 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
				</div>

				<div class="flex flex-wrap items-center gap-2">
					<button type="button" @click="showFilters = !showFilters"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
						<x-icons.filter class="h-4 w-4" />
						<span class="hidden sm:inline">Filter</span>
					</button>

					<a href="{{ route('dashboard.campaigns.standard') }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
						Switch to Standard View
					</a>

					<a href="{{ route('dashboard.campaigns.designed.create') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						<span>Add Campaign</span>
					</a>
				</div>
			</div>

			<div x-show="showFilters" x-cloak class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="grid grid-cols-1 gap-3 md:grid-cols-4">
					<div>
						<label for="status" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
						<select id="status" name="status"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($statusOptions as $option)
								<option value="{{ $option['value'] }}" {{ $status === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>

					<div>
						<label for="type" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</label>
						<select id="type" name="type"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($typeOptions as $option)
								<option value="{{ $option['value'] }}" {{ $type === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="md:col-span-2 flex items-end gap-2">
						<button type="submit"
							class="inline-flex h-11 items-center justify-center rounded-lg bg-gray-900 px-4 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Apply Filters
						</button>
						<a href="{{ route('dashboard.campaigns.designed') }}"
							class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-200 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Reset
						</a>
					</div>
				</div>
			</div>
		</form>

		<div class="mt-4 flex items-center justify-between px-1 text-xs uppercase tracking-[0.22em] text-gray-400 dark:text-gray-500">
			<span>Showing {{ $campaigns->count() }} of {{ $campaigns->total() }}</span>
			<span>Switch to Enhanced View</span>
		</div>

		<div class="mt-6 grid grid-cols-1 gap-6 pb-20 md:grid-cols-2 lg:grid-cols-3 lg:gap-8">
			@forelse ($campaigns as $campaign)
				@php
					$categoryImagePath = $campaign->categories->firstWhere('image_path', '!=', null)?->image_path;
					if (!$categoryImagePath) {
					    $categoryImagePath = $campaign->categories->first()?->image_path;
					}

					$resolvedImage = asset('images/campaignApply.png');
					if (!empty($categoryImagePath)) {
					    $isExternal = str_starts_with($categoryImagePath, 'http://') || str_starts_with($categoryImagePath, 'https://');
					    $resolvedImage = $isExternal ? $categoryImagePath : asset($categoryImagePath);
					}

					$dependencyCount = $campaign->applications_count + $campaign->orders_count + $campaign->order_items_count + $campaign->cart_items_count;
					$badgeClass = match ($campaign->status) {
					    'published' => 'bg-emerald-500 text-white',
					    'paused' => 'bg-amber-500 text-white',
					    'closed' => 'bg-red-500 text-white',
					    'archived' => 'bg-gray-500 text-white',
					    default => 'bg-pink-500 text-white',
					};
				@endphp

				<div class="group relative aspect-[4/3] overflow-hidden rounded-[1.8rem] border border-gray-100 bg-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl dark:border-gray-800 dark:bg-gray-900">
					<a href="{{ route('dashboard.campaigns.view', $campaign) }}" class="absolute inset-0 z-10" aria-label="View {{ $campaign->title }}"></a>

					<img src="{{ $resolvedImage }}" alt="Campaign Preview"
						class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
						onerror="this.onerror=null;this.src='{{ asset('images/campaignApply.png') }}';">

					<div class="absolute inset-0 z-10 bg-gradient-to-t from-black/95 via-black/30 to-transparent opacity-90"></div>

					<div class="absolute left-5 top-5 z-20 flex items-center gap-2">
						<span class="flex items-center gap-1.5 rounded-full px-3 py-1.5 text-[10px] font-medium uppercase {{ $badgeClass }}">
							{{ \Illuminate\Support\Str::headline($campaign->status) }}
						</span>
						@if (!$campaign->is_active)
							<span class="rounded-full bg-black/50 px-2.5 py-1 text-[10px] font-medium uppercase text-white backdrop-blur">
								Inactive
							</span>
						@endif
					</div>

					<div class="absolute right-5 top-5 z-20 flex items-center gap-2">
						<a href="{{ route('dashboard.campaigns.view', $campaign) }}"
							class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
							title="View campaign">
							<x-icons.eye class="h-4 w-4" />
						</a>
						<a href="{{ route('dashboard.campaigns.edit', $campaign) }}"
							class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-white/25"
							title="Edit campaign">
							<x-icons.edit class="h-4 w-4" />
						</a>
						<form action="{{ route('dashboard.campaigns.destroy', $campaign) }}" method="POST" class="relative z-20">
							@csrf
							@method('DELETE')
							<button type="submit" {{ $dependencyCount > 0 ? 'disabled' : '' }}
								onclick="return confirm('Delete this campaign? This action cannot be undone.')"
								title="{{ $dependencyCount > 0 ? 'Cannot delete: campaign has linked records' : 'Delete campaign' }}"
								class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition hover:bg-red-500/80 disabled:cursor-not-allowed disabled:opacity-40">
								<x-icons.trash class="h-4 w-4" />
							</button>
						</form>
					</div>

					<div class="absolute bottom-0 left-0 right-0 z-20 p-8">
						<a href="{{ route('dashboard.campaigns.view', $campaign) }}">

							<h3 class="line-clamp-2 text-base font-bold leading-tight text-white md:text-lg group-hover:underline">
								{{ $campaign->title }}
							</h3>
						</a>
						<p class="mt-1.5 text-[12px] font-semibold uppercase tracking-wider text-gray-300">
							{{ \Illuminate\Support\Str::headline($campaign->campaign_type) }}
						</p>
						<div class="mt-3 flex flex-wrap items-center gap-2 text-[10px] uppercase tracking-[0.22em] text-white/70">
							<span>{{ $campaign->applications_count }} Applications</span>
							<span>{{ $campaign->categories_count }} Niches</span>
							@if ($campaign->targeting?->influencer_count)
								<span>{{ $campaign->targeting->influencer_count }} Creators Target</span>
							@endif
						</div>
					</div>
				</div>
			@empty
				<div class="col-span-full rounded-[1.8rem] border border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-900">
					<p class="text-sm text-gray-500 dark:text-gray-400">No campaigns found for the current filters.</p>
					<a href="{{ route('dashboard.campaigns.designed.create') }}"
						class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						Create First Campaign
					</a>
				</div>
			@endforelse
		</div>

		@if ($campaigns->hasPages())
			<div class="mt-2 border-t border-gray-200 pt-4 dark:border-gray-800">
				{{ $campaigns->links() }}
			</div>
		@endif
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
