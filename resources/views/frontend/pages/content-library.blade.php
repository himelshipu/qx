@extends('frontend.layouts.app')

@section('content')
<section class="min-h-screen transition-colors duration-200">
	<div class="mb-10">
		<h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-2">Content Library</h1>
		<p class="text-sm text-gray-700 dark:text-gray-400">See all your delivered content in one place</p>
	</div>

	<form method="GET" action="{{ route('dashboard.content-library') }}" class="flex flex-wrap items-center gap-4 mb-10">
		<div class="relative">
			<select name="status"
				class="appearance-none pr-10 flex items-center gap-3 px-5 py-2 border border-gray-300 rounded-full dark:text-white bg-white dark:bg-transparent transition active:scale-95 text-sm font-bold">
				<option value="all" {{ $status === 'all' ? 'selected' : '' }}>Status: All</option>
				@foreach (['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'] as $state)
					<option value="{{ $state }}" {{ $status === $state ? 'selected' : '' }}>Status: {{ ucfirst(str_replace('_', ' ', $state)) }}</option>
				@endforeach
			</select>
			<svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path d="M19 9l-7 7-7-7" stroke-width="2.5" />
			</svg>
		</div>

		<div class="ml-auto flex items-center gap-2">
			<input type="text" name="q" value="{{ $search }}" placeholder="Search anything in library..."
				class="mt-1.5 dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-64 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
			<button type="submit" class="px-5 py-2 border border-gray-300 rounded-lg text-[13px] font-bold text-gray-800 dark:text-white hover:bg-purple-50 transition active:scale-95 shadow-sm">Apply</button>
			<a href="{{ route('dashboard.content-library') }}" class="px-5 py-2 border border-gray-300 rounded-lg text-[13px] font-bold text-gray-800 dark:text-white hover:bg-purple-50 transition active:scale-95 shadow-sm">Reset</a>
		</div>
	</form>

	<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
		<div class="overflow-hidden">
			<table class="min-w-full">
				<thead class="bg-gray-50/50 dark:bg-gray-800/50 border-y border-gray-200 dark:border-gray-700">
					<tr class="text-xs font-bold text-gray-400 uppercase tracking-widest text-left">
						<th class="px-6 py-4">{{ $perspective === 'brand' ? 'Creator' : 'Brand' }}</th>
						<th class="px-6 py-4">Campaign Name</th>
						<th class="px-6 py-4">{{ $perspective === 'brand' ? 'Date Ordered' : 'Date Accepted' }}</th>
						<th class="px-6 py-4">Price</th>
						<th class="px-6 py-4">Order Status</th>
						<th class="px-6 py-4 text-right pr-10">Action</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
					@forelse ($entries as $entry)
						@php
							$personName = $perspective === 'brand'
								? ($entry->creator?->user?->name ?? $entry->creator?->display_name ?? 'N/A')
								: ($entry->order?->brand?->brand_name ?? 'N/A');
							$avatarPath = $perspective === 'brand'
								? ($entry->creator?->user?->profile_image_path ?? null)
								: ($entry->order?->brand?->user?->profile_image_path ?? null);
							$statusClass = match ($entry->order?->status) {
								'completed' => 'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/15 dark:text-green-500',
								'cancelled', 'refunded' => 'bg-red-50 text-red-600 border border-red-100 dark:bg-red-500/15 dark:text-red-500',
								'in_progress', 'accepted', 'delivered' => 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/15 dark:text-blue-500',
								default => 'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-yellow-500/15 dark:text-orange-400',
							};
						@endphp
						<tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
							<td class="px-6 py-4">
								<div class="flex items-center gap-3">
									@if ($avatarPath)
										<img src="{{ image_url($avatarPath) }}" alt="{{ $personName }}" class="w-9 h-9 rounded-full object-cover">
									@else
										<div class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
											{{ strtoupper(substr($personName, 0, 1)) }}
										</div>
									@endif
									<span class="text-sm font-bold text-gray-800 dark:text-white">{{ $personName }}</span>
								</div>
							</td>
							<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $entry->order?->campaign?->title ?? $entry->title ?? 'N/A' }}</td>
							<td class="px-6 py-4 text-sm text-gray-500">{{ optional($entry->accepted_at ?? $entry->created_at)?->format('M d, Y') }}</td>
							<td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-white">{{ strtoupper($entry->order?->currency ?? 'USD') }} {{ number_format((float) $entry->line_total, 2) }}</td>
							<td class="px-6 py-4">
								<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $entry->order?->status ?? 'pending')) }}</span>
							</td>
							<td class="px-6 py-4 text-right">
								<a href="{{ route('dashboard.orders.show', $entry->order_id) }}" class="text-gray-400 hover:text-purple-400 transition-colors inline-flex">
									<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 10c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 12c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
								</a>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No content records found.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($entries->hasPages())
			<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
				@if ($entries->onFirstPage())
					<span class="text-sm font-bold text-gray-400">Previous</span>
				@else
					<a href="{{ $entries->previousPageUrl() }}" class="text-sm font-bold text-gray-500 hover:text-purple-400 transition">Previous</a>
				@endif

				<div class="text-xs font-bold text-gray-500 dark:text-gray-400">
					Page {{ $entries->currentPage() }} of {{ $entries->lastPage() }}
				</div>

				@if ($entries->hasMorePages())
					<a href="{{ $entries->nextPageUrl() }}" class="text-sm font-bold text-gray-500 hover:text-purple-400 transition">Next</a>
				@else
					<span class="text-sm font-bold text-gray-400">Next</span>
				@endif
			</div>
		@endif
	</div>
</section>


@endsection
