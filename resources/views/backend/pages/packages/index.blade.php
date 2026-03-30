@extends('backend.layouts.app')

@section('title', 'Packages')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Packages" />

	<div class="space-y-6">
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
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Package Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage global packages used across carts and order items.
					</p>
				</div>
				<a href="{{ route('dashboard.packages.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Package
				</a>
			</div>

			<div class="p-5">
				<form id="package-filters-form" method="GET" action="{{ route('dashboard.packages.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-7">
					<div class="md:col-span-3">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, platform, or currency"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<select id="platform" name="platform"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($platformOptions as $option)
								<option value="{{ $option['value'] }}" {{ $platform === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					<div>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>
					<div class="flex items-end gap-2 md:col-span-2">
						<a href="{{ route('dashboard.packages.index') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				<div id="packages-results">
					<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Package</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Platform</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Price</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Delivery</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Revisions</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Usage</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Updated</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($packages as $package)
								@php
									$usageCount = $package->cart_items_count + $package->order_items_count;
								@endphp
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3">
										<div>
											<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $package->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">
												{{ \Illuminate\Support\Str::limit($package->description ?? 'No description', 70) }}
											</p>
											<p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
												By: {{ $package->createdBy?->name ?? 'System' }}
											</p>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										<span class="inline-flex rounded bg-gray-100 px-2 py-1 text-xs font-medium uppercase dark:bg-gray-800">
											{{ $package->platform }}
										</span>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ strtoupper($package->currency) }} {{ number_format((float) $package->base_price, 2) }}
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ $package->delivery_days ? $package->delivery_days . ' days' : '-' }}
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ $package->revisions_included !== null ? $package->revisions_included : '-' }}
									</td>
									<td class="px-4 py-3">
										<div class="text-xs text-gray-600 dark:text-gray-300">
											<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Cart:
												{{ $package->cart_items_count }}</span>
											<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Order:
												{{ $package->order_items_count }}</span>
										</div>
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $package->is_active ? 'checked' : '' }} onchange="togglePackageStatus({{ $package->id }}, this)"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
												</div>
											</label>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $package->updated_at?->format('M d, Y') }}
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('dashboard.packages.edit', $package) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="Edit package">
												<x-icons.edit class="h-4 w-4" />
											</a>
											<form action="{{ route('dashboard.packages.destroy', $package) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="submit" {{ $usageCount > 0 ? 'disabled' : '' }}
													onclick="return confirm('Delete this package? This action cannot be undone.')"
													title="{{ $usageCount > 0 ? 'Cannot delete: package has linked records' : 'Delete package' }}"
													class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
													<x-icons.trash class="h-4 w-4" />
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="9" class="px-4 py-12 text-center">
										<p class="text-sm text-gray-500 dark:text-gray-400">No packages found for the current filters.</p>
										<a href="{{ route('dashboard.packages.create') }}"
											class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
											<x-icons.plus class="h-4 w-4" />
											Create First Package
										</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					</div>

					@if ($packages->hasPages())
						<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
							{{ $packages->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const form = document.getElementById('package-filters-form');
				const resultsId = 'packages-results';
				const searchInput = document.getElementById('q');
				const statusSelect = document.getElementById('status');
				const platformSelect = document.getElementById('platform');
				let debounceTimer;
				let activeRequestController = null;

				if (!form) {
					return;
				}

				const buildQueryString = () => {
					const params = new URLSearchParams(new FormData(form));
					if (!params.get('q')) params.delete('q');
					if (!params.get('platform') || params.get('platform') === 'all') params.delete('platform');
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
				platformSelect?.addEventListener('change', () => applyFilters());

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

			function togglePackageStatus(packageId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				const urlTemplate = @json(route('dashboard.packages.toggle-status', ['package' => '__ID__']));
				const url = urlTemplate.replace('__ID__', String(packageId));

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

							const message = data.message || 'Package status updated successfully.';
							if (window.toast) {
								window.toast.success(message);
							}

							return;
						}

						throw new Error(data.message || 'Failed to update package status.');
					})
					.catch((error) => {
						console.error(error);
						const message = error?.message || 'Unable to update package status right now.';
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
