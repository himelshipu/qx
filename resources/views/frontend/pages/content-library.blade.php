@extends('frontend.layouts.app')

@section('content')
	<section class="min-h-screen transition-colors duration-200">
		<div class="mb-10">
			<h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-2">Content Library
			</h1>
			<p class="text-sm text-gray-700 dark:text-gray-400">See all your delivered content in one place</p>
		</div>

		@php
			$dateRangeLabel =
			    ($filters['date_from'] ?? null) && ($filters['date_to'] ?? null)
			        ? $filters['date_from'] . ' - ' . $filters['date_to']
			        : '';
		@endphp

		<form method="GET" action="{{ route('frontend.content-library') }}" class="flex flex-wrap items-center gap-4 mb-10"
			id="filters-form">
			<div class="relative">
				<select name="platform" id="platform-filter"
					class="appearance-none h-12 w-[220px] pr-10 px-4 rounded-lg border border-gray-300 bg-transparent text-sm font-bold text-gray-800 transition focus:border-gray-400 focus:ring-1 focus:ring-gray-400/20 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 dark:focus:ring-gray-500/20">
					<option value="all">Platform: All</option>
					@foreach ($platforms as $platform => $count)
						<option value="{{ $platform }}" {{ $filters['platform'] === $platform ? 'selected' : '' }}>
							{{ ucfirst($platform) }} ({{ $count }})
						</option>
					@endforeach
				</select>
				<x-icons.chevron-right
					class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-gray-500 dark:text-gray-400" />
			</div>

			<div class="relative">
				<select name="status" id="status-filter"
					class="appearance-none h-12 w-[220px] pr-10 px-4 rounded-lg border border-gray-300 bg-transparent text-sm font-bold text-gray-800 transition focus:border-gray-400 focus:ring-1 focus:ring-gray-400/20 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 dark:focus:ring-gray-500/20">
					<option value="all">Status: All</option>
					@foreach ($statuses as $status => $count)
						<option value="{{ $status }}" {{ $filters['status'] === $status ? 'selected' : '' }}>
							{{ ucfirst(str_replace('_', ' ', $status)) }} ({{ $count }})
						</option>
					@endforeach
				</select>
				<x-icons.chevron-right
					class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-gray-500 dark:text-gray-400" />
			</div>

			<div class="relative">
				<x-icons.clock
					class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500 dark:text-gray-400" />
				<input type="text" id="date-range-display" placeholder="Date: Select Range" value="{{ $dateRangeLabel }}"
					autocomplete="off"
					class="h-12 w-[260px] pl-12 pr-10 rounded-lg border border-gray-300 bg-transparent text-sm font-bold text-gray-800 transition focus:border-gray-400 focus:ring-1 focus:ring-gray-400/20 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 dark:focus:ring-gray-500/20">
				<x-icons.chevron-right
					class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 rotate-90 text-gray-500 dark:text-gray-400" />
				<input type="hidden" name="date_from" id="date-from" value="{{ $filters['date_from'] }}">
				<input type="hidden" name="date_to" id="date-to" value="{{ $filters['date_to'] }}">
			</div>

			<div class="ml-auto flex items-center gap-2">
				<input type="text" name="q" id="search-input" value="{{ $filters['search'] }}"
					placeholder="Search anything in library..."
					class="h-12 w-64 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 transition focus:border-gray-400 focus:ring-1 focus:ring-gray-400/20 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-500 dark:focus:ring-gray-500/20">
				<a href="{{ route('frontend.content-library') }}"
					class="h-12 inline-flex items-center px-5 border border-gray-300 rounded-lg text-[13px] font-bold text-gray-800 dark:text-white hover:bg-purple-50 transition">Reset</a>
			</div>
		</form>

		<div id="content-library-results"
			class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm">
			<div class="overflow-hidden">
				<table class="min-w-full">
					<thead class="bg-gray-50/50 dark:bg-gray-800/50 border-y border-gray-200 dark:border-gray-700">
						<tr class="text-xs font-bold text-gray-400 uppercase tracking-widest text-left">
							<th class="px-6 py-4">{{ $perspective === 'brand' ? 'Influencer' : 'Brand' }}</th>
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
								$personName =
								    $perspective === 'brand'
								        ? $entry->influencer?->user?->name ?? ($entry->influencer?->display_name ?? 'N/A')
								        : $entry->order?->brand?->brand_name ?? 'N/A';
								$avatarPath =
								    $perspective === 'brand'
								        ? $entry->influencer?->user?->profile_image_path ?? null
								        : $entry->order?->brand?->user?->profile_image_path ?? null;

								$statusClasses = [
								    'completed' =>
								        'bg-green-50 text-green-600 border border-green-100 dark:bg-green-500/15 dark:text-green-500',
								    'cancelled' => 'bg-red-50 text-red-600 border border-red-100 dark:bg-red-500/15 dark:text-red-500',
								    'refunded' => 'bg-red-50 text-red-600 border border-red-100 dark:bg-red-500/15 dark:text-red-500',
								    'in_progress' => 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/15 dark:text-blue-500',
								    'accepted' => 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/15 dark:text-blue-500',
								    'delivered' => 'bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/15 dark:text-blue-500',
								    'pending' =>
								        'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-yellow-500/15 dark:text-orange-400',
								];
								$statusClass =
								    $statusClasses[$entry->order?->status ?? 'pending'] ??
								    'bg-orange-50 text-orange-600 border border-orange-100 dark:bg-yellow-500/15 dark:text-orange-400';
							@endphp
							<tr class="hover:bg-gray-50/50 dark:hover:bg-white/5 transition-colors">
								<td class="px-6 py-4">
									<div class="flex items-center gap-3">
										@if ($avatarPath)
											<img src="{{ image_url($avatarPath) }}" class="w-9 h-9 rounded-full object-cover">
										@else
											<div
												class="w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
												{{ strtoupper(substr($personName, 0, 1)) }}
											</div>
										@endif
										<span class="text-sm font-bold text-gray-800 dark:text-white">{{ $personName }}</span>
									</div>
								</td>
								<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
									{{ $entry->order?->campaign?->title ?? ($entry->title ?? 'N/A') }}</td>
								<td class="px-6 py-4 text-sm text-gray-500">
									{{ optional($entry->accepted_at ?? $entry->created_at)?->format('M d, Y') }}</td>
								<td class="px-6 py-4 text-sm font-bold text-gray-700 dark:text-white">
									{{ strtoupper($entry->order?->currency ?? 'USD') }} {{ number_format((float) $entry->line_total, 2) }}</td>
								<td class="px-6 py-4">
									<span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
										{{ ucfirst(str_replace('_', ' ', $entry->order?->status ?? 'pending')) }}
									</span>
								</td>
								<td class="px-6 py-4 text-right">
									<a href=\"{{ route('frontend.orders.show', $entry->order_id) }}\"
										class="text-gray-400 hover:text-purple-400 transition-colors">
										<x-icons.eye class="w-5 h-5" />
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No content records
									found.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($entries->hasPages())
				@php
					$startPage = max(1, $entries->currentPage() - 2);
					$endPage = min($entries->lastPage(), $entries->currentPage() + 2);
				@endphp
				<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex justify-between items-center">
					@if ($entries->onFirstPage())
						<span class="text-sm font-bold text-gray-400">Previous</span>
					@else
						<a href="{{ $entries->appends(request()->query())->previousPageUrl() }}"
							class="js-library-page-link text-sm font-bold text-gray-500 hover:text-purple-400 transition">Previous</a>
					@endif

					<div class="flex gap-2">
						@for ($page = $startPage; $page <= $endPage; $page++)
							<a href="{{ $entries->appends(request()->query())->url($page) }}"
								class="js-library-page-link w-8 h-8 rounded-lg text-xs font-bold transition flex items-center justify-center {{ $entries->currentPage() === $page ? 'bg-purple-400 text-white shadow-md' : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
								{{ $page }}
							</a>
						@endfor
					</div>

					@if ($entries->hasMorePages())
						<a href="{{ $entries->appends(request()->query())->nextPageUrl() }}"
							class="js-library-page-link text-sm font-bold text-gray-500 hover:text-purple-400 transition">Next</a>
					@else
						<span class="text-sm font-bold text-gray-400">Next</span>
					@endif
				</div>
			@endif
		</div>
	</section>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.getElementById('filters-form');
				const platformFilter = document.getElementById('platform-filter');
				const statusFilter = document.getElementById('status-filter');
				const searchInput = document.getElementById('search-input');
				const dateDisplayInput = document.getElementById('date-range-display');
				const dateFromInput = document.getElementById('date-from');
				const dateToInput = document.getElementById('date-to');
				let debounceTimer;
				let activeRequestController = null;

				const buildQueryString = () => {
					const params = new URLSearchParams(new FormData(form));

					if (!params.get('platform') || params.get('platform') === 'all') params.delete('platform');
					if (!params.get('status') || params.get('status') === 'all') params.delete('status');
					if (!params.get('q')) params.delete('q');
					if (!params.get('date_from')) params.delete('date_from');
					if (!params.get('date_to')) params.delete('date_to');

					return params.toString();
				};

				const applyFilters = async (explicitUrl = null) => {
					const query = buildQueryString();
					const requestUrl = explicitUrl || `${form.action}${query ? `?${query}` : ''}`;

					if (activeRequestController) {
						activeRequestController.abort();
					}

					activeRequestController = new AbortController();

					try {
						const response = await fetch(requestUrl, {
							headers: {
								'X-Requested-With': 'XMLHttpRequest'
							},
							signal: activeRequestController.signal,
						});

						const html = await response.text();
						const parser = new DOMParser();
						const doc = parser.parseFromString(html, 'text/html');

						const newResults = doc.getElementById('content-library-results');
						const currentResults = document.getElementById('content-library-results');

						if (newResults && currentResults) {
							currentResults.outerHTML = newResults.outerHTML;
							window.history.replaceState({}, '', requestUrl);
						}
					} catch (error) {
						if (error.name !== 'AbortError') {
							window.location.href = requestUrl;
						}
					}
				};

				form?.addEventListener('submit', function(event) {
					event.preventDefault();
					applyFilters();
				});

				platformFilter?.addEventListener('change', () => applyFilters());
				statusFilter?.addEventListener('change', () => applyFilters());

				searchInput?.addEventListener('input', function() {
					clearTimeout(debounceTimer);
					debounceTimer = setTimeout(() => applyFilters(), 350);
				});

				document.addEventListener('click', function(event) {
					const link = event.target.closest('.js-library-page-link');
					if (!link) return;

					event.preventDefault();
					const href = link.getAttribute('href');
					if (href) {
						applyFilters(href);
					}
				});

				if (typeof flatpickr !== 'undefined' && dateDisplayInput) {
					flatpickr(dateDisplayInput, {
						mode: 'range',
						dateFormat: 'Y-m-d',
						defaultDate: [dateFromInput.value, dateToInput.value].filter(Boolean),
						onClose: function(selectedDates, dateStr) {
							if (selectedDates.length === 2) {
								const format = (date) => {
									const y = date.getFullYear();
									const m = String(date.getMonth() + 1).padStart(2, '0');
									const d = String(date.getDate()).padStart(2, '0');
									return `${y}-${m}-${d}`;
								};

								dateFromInput.value = format(selectedDates[0]);
								dateToInput.value = format(selectedDates[1]);
								dateDisplayInput.value = `${dateFromInput.value} - ${dateToInput.value}`;
							}

							if (selectedDates.length === 0) {
								dateFromInput.value = '';
								dateToInput.value = '';
								dateDisplayInput.value = '';
							}

							applyFilters();
						}
					});
				}
			});
		</script>
	@endpush
@endsection
