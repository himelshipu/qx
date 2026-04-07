@extends('backend.layouts.app')

@section('title', 'Users')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Users" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Brands + Influencers</p>
				<p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">{{ $stats['brands'] + $stats['influencers'] }}
				</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Moderators + Admins
				</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">
					{{ $stats['moderators'] + $stats['admins'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['active'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Directory</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All brands, influencers, moderators, and admins in one place.
					</p>
				</div>
			</div>

			<div class="p-5">
				<form id="users-filters-form" method="GET" action="{{ route('dashboard.users.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5">
					<div class="md:col-span-3">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, email, phone, city, country or type"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>
					<div class="flex items-end gap-2">
						<a href="{{ route('dashboard.users.index') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.users._results')
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.getElementById('users-filters-form');
				const resultsId = 'users-results';
				const searchInput = document.getElementById('q');
				const statusSelect = document.getElementById('status');
				let debounceTimer;
				let activeRequestController = null;

				if (!form) {
					return;
				}

				const buildQueryString = () => {
					const params = new URLSearchParams(new FormData(form));
					if (!params.get('q')) params.delete('q');
					if (!params.get('status') || params.get('status') === 'all') params.delete('status');
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

			function toggleUserStatus(userId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				const urlTemplate = @json(route('dashboard.users.toggle-status', ['user' => '__ID__']));
				const url = urlTemplate.replace('__ID__', String(userId));

				fetch(url, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': @json(csrf_token()),
							'Accept': 'application/json',
							'Content-Type': 'application/json'
						}
					})
					.then((response) => {
						if (!response.ok) {
							throw new Error('Failed to update status');
						}

						return response.json();
					})
					.then((data) => {
						if (data.success) {
							if (checkbox && typeof data.is_active !== 'undefined') {
								checkbox.checked = Boolean(data.is_active);
							}

							const message = data.message || 'User status updated successfully.';
							if (window.toast) {
								window.toast.success(message);
							}

							return;
						}

						throw new Error(data.message || 'Failed to update user status.');
					})
					.catch((error) => {
						console.error(error);
						const message = error?.message || 'Unable to update user status right now.';
						if (window.toast) {
							window.toast.error(message);
						}

						if (checkbox) {
							checkbox.checked = !checkbox.checked;
						}
					})
					.finally(() => {
						if (checkbox) {
							checkbox.disabled = false;
						}
					});
			}
		</script>
	@endpush
@endsection
