@extends('backend.layouts.app')

@section('title', 'Creators')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Creators" />

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
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Categorized</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['categorized'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Creator Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage creator accounts, niches, and activity status.</p>
				</div>
				<a href="{{ route('dashboard.creators.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Creator
				</a>
			</div>

			<div class="p-5">
				<form method="GET" action="{{ route('dashboard.creators.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5">
					<div class="md:col-span-3">
						<label for="q"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, display name, email, or category"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<label for="status"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
							<option value="featured" {{ $status === 'featured' ? 'selected' : '' }}>Featured</option>
						</select>
					</div>
					<div class="flex items-end gap-2">
						<button type="submit"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Apply
						</button>
						<a href="{{ route('dashboard.creators.index') }}"
							class="h-10 w-full rounded-lg border border-gray-200 px-3 text-center text-sm font-medium leading-10 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Reset
						</a>
					</div>
				</form>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Creator</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Categories</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Email</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Usage</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Featured</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Updated</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($creators as $creator)
								@php
									$previewPath = $creator->user?->profile_image_path ?: $creator->user?->cover_image_path;
									$previewUrl = null;

									if (!empty($previewPath)) {
									    $isExternal = str_starts_with($previewPath, 'http://') || str_starts_with($previewPath, 'https://');
									    $previewUrl = $isExternal ? $previewPath : asset($previewPath);
									}
								@endphp
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3">
										<div class="flex items-center gap-3">
											<div
												class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
												@if ($previewUrl)
													<img src="{{ $previewUrl }}" alt="{{ $creator->display_name ?: $creator->user?->name }}"
														class="h-10 w-10 object-cover">
												@else
													<span
														class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($creator->display_name ?: $creator->user?->name ?? 'C', 0, 1)) }}</span>
												@endif
											</div>
											<div>
												<p class="text-sm font-semibold text-gray-900 dark:text-white">
													{{ $creator->display_name ?: $creator->user?->name ?? 'Unnamed' }}</p>
												<p class="text-xs text-gray-500 dark:text-gray-400">{{ $creator->title_name ?: 'No title' }}</p>
											</div>
										</div>
									</td>
									<td class="px-4 py-3">
										<div class="flex flex-wrap gap-1">
											@forelse ($creator->categories as $category)
												<span
													class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $category->name }}</span>
											@empty
												<span class="text-xs text-gray-500 dark:text-gray-400">Uncategorized</span>
											@endforelse
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ $creator->user?->email ?? 'N/A' }}
									</td>
									<td class="px-4 py-3">
										<div class="text-xs text-gray-600 dark:text-gray-300">
											<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Applications:
												{{ $creator->campaign_applications_count }}</span>
											<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Orders:
												{{ $creator->order_items_count }}</span>
										</div>
									</td>
									<td class="px-4 py-3">
										@if ($creator->is_featured)
											<span
												class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
												&#9733; Featured
												@if ($creator->featured_priority)
													<span class="ml-1 text-[10px] font-normal opacity-70">#{{ $creator->featured_priority }}</span>
												@endif
											</span>
										@else
											<span class="text-xs text-gray-400 dark:text-gray-600">&mdash;</span>
										@endif
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $creator->is_active ? 'checked' : '' }} onchange="toggleCreatorStatus({{ $creator->id }}, this)"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
												</div>
											</label>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $creator->updated_at?->format('M d, Y') }}
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('dashboard.creators.view', $creator) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="View creator">
												<x-icons.eye class="h-4 w-4" />
											</a>
											<a href="{{ route('dashboard.creators.edit', $creator) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="Edit creator">
												<x-icons.edit class="h-4 w-4" />
											</a>
											<form action="{{ route('dashboard.creators.destroy', $creator) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="submit" onclick="return confirm('Delete this creator? This action cannot be undone.')"
													title="Delete creator"
													class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
													<x-icons.trash class="h-4 w-4" />
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="px-4 py-12 text-center">
										<p class="text-sm text-gray-500 dark:text-gray-400">No creators found for the current filters.</p>
										<a href="{{ route('dashboard.creators.create') }}"
											class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
											<x-icons.plus class="h-4 w-4" />
											Create First Creator
										</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($creators->hasPages())
					<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
						{{ $creators->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function toggleCreatorStatus(creatorId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				const urlTemplate = @json(route('dashboard.creators.toggle-status', ['creator' => '__ID__']));
				const url = urlTemplate.replace('__ID__', String(creatorId));

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

							const message = data.message || 'Creator status updated successfully.';
							if (window.toast) {
								window.toast.success(message);
							}

							return;
						}

						throw new Error(data.message || 'Failed to update creator status.');
					})
					.catch((error) => {
						console.error(error);
						const message = error?.message || 'Unable to update creator status right now.';
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
