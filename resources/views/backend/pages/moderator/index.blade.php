@extends('backend.layouts.app')

@section('title', 'Moderators')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Moderators" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
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
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Moderator Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage moderation team accounts and access status.</p>
				</div>
				<a href="{{ route('dashboard.moderators.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Moderator
				</a>
			</div>

			<div class="p-5">
				<form method="GET" action="{{ route('dashboard.moderators.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5">
					<div class="md:col-span-3">
						<label for="q"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, email, or phone"
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
						</select>
					</div>
					<div class="flex items-end gap-2">
						<button type="submit"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Apply
						</button>
						<a href="{{ route('dashboard.moderators.index') }}"
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
									Moderator</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Email</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Phone</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Created</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($moderators as $moderator)
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3">
										<div class="flex items-center gap-3">
											<div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
												<span
													class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($moderator->name, 0, 1)) }}</span>
											</div>
											<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $moderator->name }}</p>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $moderator->email }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $moderator->phone ?: 'N/A' }}</td>
									<td class="px-4 py-3">
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $moderator->is_active ? 'checked' : '' }} onchange="toggleModeratorStatus({{ $moderator->id }}, this)"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
												</div>
											</label>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $moderator->created_at?->format('M d, Y') }}
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('dashboard.moderators.show', $moderator) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="View moderator">
												<x-icons.eye class="h-4 w-4" />
											</a>
											<a href="{{ route('dashboard.moderators.edit', $moderator) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="Edit moderator">
												<x-icons.edit class="h-4 w-4" />
											</a>
											<form action="{{ route('dashboard.moderators.destroy', $moderator) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="submit" onclick="return confirm('Delete this moderator? This action cannot be undone.')"
													title="Delete moderator"
													class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
													<x-icons.trash class="h-4 w-4" />
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="px-4 py-12 text-center">
										<p class="text-sm text-gray-500 dark:text-gray-400">No moderators found for the current filters.</p>
										<a href="{{ route('dashboard.moderators.create') }}"
											class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
											<x-icons.plus class="h-4 w-4" />
											Create First Moderator
										</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($moderators->hasPages())
					<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
						{{ $moderators->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function toggleModeratorStatus(moderatorId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				const urlTemplate = @json(route('dashboard.moderators.toggle-status', ['moderator' => '__ID__']));
				const url = urlTemplate.replace('__ID__', String(moderatorId));

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

							const message = data.message || 'Moderator status updated successfully.';
							if (window.toast) {
								window.toast.success(message);
							}

							return;
						}

						throw new Error(data.message || 'Failed to update moderator status.');
					})
					.catch((error) => {
						console.error(error);
						const message = error?.message || 'Unable to update moderator status right now.';
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
