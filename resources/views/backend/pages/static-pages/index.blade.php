@extends('backend.layouts.app')

@section('title', 'Static Pages')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Static Pages" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
			</div>
			<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/40 dark:bg-yellow-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-yellow-700 dark:text-yellow-200">{{ $stats['draft'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Page Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create and manage static pages for your platform.</p>
				</div>
				<a href="{{ route('dashboard.static-pages.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Page
				</a>
			</div>

			<div class="p-5">
				<div id="pages-results">
					<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Title</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Slug</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Updated</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($pages as $page)
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3">
										<div>
											<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $page->title }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">
												{{ \Illuminate\Support\Str::limit($page->content_summary ?? 'No description', 55) }}</p>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										<code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">{{ $page->slug }}</code>
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $page->is_active ? 'checked' : '' }} onchange="togglePageStatus({{ $page->id }}, this)"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800">
												</div>
											</label>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $page->updated_at?->format('M d, Y') }}
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center justify-end gap-2">
											<a href="{{ route('dashboard.static-pages.show', $page) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="View page">
												<x-icons.eye class="h-4 w-4" />
											</a>
											<a href="{{ route('dashboard.static-pages.edit', $page) }}"
												class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
												title="Edit page">
												<x-icons.edit class="h-4 w-4" />
											</a>
											<form action="{{ route('dashboard.static-pages.destroy', $page) }}" method="POST">
												@csrf
												@method('DELETE')
												<button type="submit"
													data-confirm-title="Delete Page"
													data-confirm-message="Delete this page? This action cannot be undone."
													data-confirm-button="Delete"
													data-confirm-variant="danger"
													title="Delete page"
													class="js-confirmable inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
													<x-icons.trash class="h-4 w-4" />
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-4 py-12 text-center">
										<p class="text-sm text-gray-500 dark:text-gray-400">No pages found.</p>
										<a href="{{ route('dashboard.static-pages.create') }}"
											class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
											<x-icons.plus class="h-4 w-4" />
											Create First Page
										</a>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
					</div>

					@if ($pages->hasPages())
						<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
							{{ $pages->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function togglePageStatus(pageId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				try {
					const urlTemplate = @json(route('dashboard.static-pages.toggle-status', ['staticPage' => ':id']));
					const url = urlTemplate.replace(':id', String(pageId));
					
					console.log('Toggling page status:', { pageId, url });

					fetch(url, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': @json(csrf_token()),
							'Accept': 'application/json',
							'Content-Type': 'application/json',
							'X-Requested-With': 'XMLHttpRequest'
						},
						body: JSON.stringify({})
					})
					.then((response) => {
						console.log('Response status:', response.status);
						console.log('Response headers:', response.headers);
						
						if (!response.ok) {
							return response.text().then(text => {
								console.error('Error response text:', text);
								throw new Error(`HTTP ${response.status}: ${text || 'Unknown error'}`);
							});
						}
						return response.json();
					})
					.then((data) => {
						console.log('Response data:', data);
						
						if (data.success) {
							if (checkbox && typeof data.is_active !== 'undefined') {
								checkbox.checked = Boolean(data.is_active);
							}

							const message = data.message || 'Page status updated successfully.';
							if (window.toast) {
								window.toast.success(message);
							}
							return;
						}

						throw new Error(data.message || 'Failed to update page status.');
					})
					.catch((error) => {
						console.error('Toggle status error:', error);
						const message = error?.message || 'Unable to update page status right now.';
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
				} catch (error) {
					console.error('Script error:', error);
					if (window.toast) {
						window.toast.error('Script error: ' + error.message);
					}
					if (checkbox) {
						checkbox.disabled = false;
						checkbox.checked = !checkbox.checked;
					}
				}
			}
		</script>
	@endpush
@endsection
