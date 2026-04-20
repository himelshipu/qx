@extends('backend.layouts.app')

@section('title', 'Orders')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Orders" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Orders</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/40 dark:bg-yellow-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Pending</p>
				<p class="mt-2 text-2xl font-semibold text-yellow-700 dark:text-yellow-200">{{ $stats['pending'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Completed</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['completed'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Revenue</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">${{ number_format($stats['revenue'], 2) }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Management</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track and review live order records.</p>
			</div>

			<div class="p-5">
				<form id="orders-filters-form" method="GET" action="{{ route('dashboard.orders.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-6">
					<div class="md:col-span-3">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by order #, buyer, brand, campaign or item"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<select id="type" name="type"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
							<option value="campaign" {{ $type === 'campaign' ? 'selected' : '' }}>Campaign</option>
							<option value="package" {{ $type === 'package' ? 'selected' : '' }}>Package</option>
						</select>
					</div>
					<div>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
							@foreach (['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'] as $state)
								<option value="{{ $state }}" {{ $status === $state ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $state)) }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end gap-2">
						<a href="{{ route('dashboard.orders.index') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.orders._results')
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('orders-filters-form');
			const resultsId = 'orders-results';
			const searchInput = document.getElementById('q');
			const statusSelect = document.getElementById('status');
			const typeSelect = document.getElementById('type');
			let debounceTimer;
			let activeRequestController = null;

			if (!form) {
				return;
			}

			const buildQueryString = () => {
				const params = new URLSearchParams(new FormData(form));
				if (!params.get('q')) params.delete('q');
				if (!params.get('status') || params.get('status') === 'all') params.delete('status');
				if (!params.get('type') || params.get('type') === 'all') params.delete('type');
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

					const newResults = doc.getElementById(resultsId);
					const currentResults = document.getElementById(resultsId);

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

			searchInput?.addEventListener('input', function() {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(() => applyFilters(), 350);
			});

			statusSelect?.addEventListener('change', () => applyFilters());
			typeSelect?.addEventListener('change', () => applyFilters());

			document.addEventListener('click', function(event) {
				const link = event.target.closest(`#${resultsId} a[href*="page="]`);
				if (!link) return;

				event.preventDefault();
				const href = link.getAttribute('href');
				if (href) {
					applyFilters(href);
				}
			});
		});
	</script>
@endpush

