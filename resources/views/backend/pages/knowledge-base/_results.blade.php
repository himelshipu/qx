<div id="knowledge-base-results" class="min-h-60" data-table-container>
	<table class="w-full">
		<thead class="border-b border-gray-200 dark:border-gray-800">
			<tr class="bg-gray-50 dark:bg-gray-800/50">
				<th class="w-10 px-2 py-3"></th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Title</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Badge</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Read Time</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Published</th>
				<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
			</tr>
		</thead>
		<tbody id="knowledge-base-sortable" class="divide-y divide-gray-200 dark:divide-gray-800">
			@forelse($articles as $article)
				<tr data-id="{{ $article->id }}">
					<td class="px-2 py-3 align-middle text-gray-400">
						<button type="button" class="cursor-grab p-1 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200" title="Drag to reorder">
							<x-icons.menu class="h-4 w-4" />
						</button>
					</td>
					<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
						{{ $article->title }}
						@if ($article->is_featured)
							<span class="ml-2 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Featured</span>
						@endif
					</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $article->badge ?? '-' }}</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $article->read_time_minutes }} min</td>
					<td class="px-4 py-3 text-sm">
						<label class="relative inline-flex cursor-pointer items-center">
							<input type="checkbox"
								class="peer sr-only"
								data-status-toggle
								data-id="{{ $article->id }}"
								{{ $article->is_published ? 'checked' : '' }}>
							<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
						</label>
					</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400" data-published-at="{{ $article->id }}">{{ $article->published_at?->format('M d, Y') ?? '-' }}</td>
					<td class="px-4 py-3 text-right">
						<div class="flex items-center justify-end gap-2">
							<a href="{{ route('dashboard.knowledge-base.edit', $article) }}"
								class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
								<x-icons.edit class="h-4 w-4" />
							</a>
							<form action="{{ route('dashboard.knowledge-base.destroy', $article) }}" method="POST" class="inline js-confirmable"
								data-confirm-title="Delete Article"
								data-confirm-message="Are you sure you want to delete this article?"
								data-confirm-button="Delete"
								data-confirm-variant="danger">
								@csrf
								@method('DELETE')
								<button type="submit" class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:hover:bg-red-900/20">
									<x-icons.trash class="h-4 w-4" />
								</button>
							</form>
						</div>
					</td>
				</tr>
			@empty
				<tr>
					<td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
						No articles found. <a href="{{ route('dashboard.knowledge-base.create') }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>

	@if ($articles->hasPages())
		<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800" data-pagination-container>
			{{ $articles->links($paginationView ?? 'pagination::tailwind') }}
		</div>
	@endif
</div>
