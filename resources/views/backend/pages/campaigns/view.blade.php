@extends('backend.layouts.app')

@section('title', 'Campaign Details')

@section('content')
	@php
		$statusClass = match ($campaign->status) {
		    'published' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
		    'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
		    'closed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
		    'archived' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
		    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
		};
		$minBudgetLabel =
		    $campaign->budget_min !== null
		        ? $campaign->currency . ' ' . number_format((float) $campaign->budget_min, 2)
		        : 'N/A';
		$maxBudgetLabel =
		    $campaign->budget_max !== null
		        ? $campaign->currency . ' ' . number_format((float) $campaign->budget_max, 2)
		        : 'N/A';
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Campaign Details" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $campaign->title }}</h2>
					<div class="mt-2 flex flex-wrap items-center gap-2">
						<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
							{{ \Illuminate\Support\Str::headline($campaign->status) }}
						</span>
						<span
							class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
							{{ \Illuminate\Support\Str::headline($campaign->campaign_type) }}
						</span>
						<span
							class="text-xs {{ $campaign->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
							{{ $campaign->is_active ? 'Active' : 'Inactive' }}
						</span>
					</div>
				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('dashboard.campaigns.edit', $campaign) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.edit class="h-4 w-4" />
						Edit Campaign
					</a>
					<a href="{{ route('dashboard.campaigns.standard') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back to List
					</a>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Applications</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $campaign->applications_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $campaign->categories->count() }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Follower Ranges</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $campaign->followerRanges->count() }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Linked Commerce Records
				</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ $campaign->orders_count + $campaign->order_items_count + $campaign->cart_items_count }}</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Campaign Overview</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Budget Range</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							@if ($campaign->budget_min === null && $campaign->budget_max === null)
								N/A
							@else
								{{ $minBudgetLabel }} - {{ $maxBudgetLabel }}
							@endif
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Start Date</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->start_date?->format('M d, Y') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">End Date</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->end_date?->format('M d, Y') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Published At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ $campaign->published_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Brand</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->brand?->brand_name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Created By</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->createdBy?->name ?? 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Targeting</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Influencer Count</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->targeting?->influencer_count ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Target Gender</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ \Illuminate\Support\Str::headline($campaign->targeting?->target_gender ?? 'any') }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Age Range</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							@if ($campaign->targeting?->age_min || $campaign->targeting?->age_max)
								{{ $campaign->targeting?->age_min ?? 'N/A' }} - {{ $campaign->targeting?->age_max ?? 'N/A' }}
							@else
								N/A
							@endif
						</dd>
					</div>
				</dl>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</h3>
			<div class="mt-3 flex flex-wrap gap-2">
				@forelse ($campaign->categories as $category)
					<span
						class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
						{{ $category->name }}
					</span>
				@empty
					<span class="text-sm text-gray-500 dark:text-gray-400">No categories assigned.</span>
				@endforelse
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Follower Ranges</h3>
			<div class="mt-3 flex flex-wrap gap-2">
				@forelse ($campaign->followerRanges as $range)
					<span
						class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
						{{ $range->label }}
					</span>
				@empty
					<span class="text-sm text-gray-500 dark:text-gray-400">No follower ranges selected.</span>
				@endforelse
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Target Countries</h3>
			<div class="mt-3 flex flex-wrap gap-2">
				@forelse ($campaign->targetCountries as $country)
					<span
						class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
						{{ $country->country_code }}
					</span>
				@empty
					<span class="text-sm text-gray-500 dark:text-gray-400">No countries selected.</span>
				@endforelse
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Description</h3>
			<p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
				{{ $campaign->description ?: 'No description provided.' }}</p>

			<h3 class="mt-6 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Instructions</h3>
			<p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">
				{{ $campaign->instructions ?: 'No instructions provided.' }}</p>
		</div>
	</div>
@endsection
