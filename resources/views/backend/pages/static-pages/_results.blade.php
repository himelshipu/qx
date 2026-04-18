<div id="static-pages-results" class="p-5" data-table-container>
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Title</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Slug</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
				@forelse ($pages as $page)
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3">
							<div>
								<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $page->title }}</p>
								<p class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($page->content_summary ?? 'No description', 55) }}</p>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
							<code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">{{ $page->slug }}</code>
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input
										type="checkbox"
										class="peer sr-only"
										data-status-toggle
										data-id="{{ $page->id }}"
										{{ $page->is_active ? 'checked' : '' }}>
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $page->updated_at?->format('M d, Y') }}</td>
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
		<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800" data-pagination-container>
			{{ $pages->links() }}
		</div>
	@endif
</div>
