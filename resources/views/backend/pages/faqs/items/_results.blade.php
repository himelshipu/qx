<div id="faq-items-results" data-table-container>
	<table class="w-full">
		<thead class="border-b border-gray-200 dark:border-gray-800">
			<tr class="bg-gray-50 dark:bg-gray-800/50">
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Question</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Order</th>
				<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
			</tr>
		</thead>
		<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
			@forelse($items as $item)
				<tr>
					<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->question }}</td>
					<td class="px-4 py-3 text-sm">
						<label class="relative inline-flex cursor-pointer items-center">
							<input type="checkbox" class="peer sr-only" data-status-toggle data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
							<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
						</label>
					</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->sort_order }}</td>
					<td class="px-4 py-3 text-right">
						<div class="flex items-center justify-end gap-2">
							<a href="{{ route('dashboard.faqs.items.edit', [$section, $item]) }}"
								class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
								<x-icons.edit class="h-4 w-4" />
							</a>
							<form action="{{ route('dashboard.faqs.items.destroy', [$section, $item]) }}" method="POST" class="inline js-confirmable"
								data-confirm-title="Delete FAQ Item"
								data-confirm-message="Are you sure you want to delete this FAQ item?"
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
					<td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
						No FAQ items found. <a href="{{ route('dashboard.faqs.items.create', $section) }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>

	@if ($items->hasPages())
		<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800" data-pagination-container>
			{{ $items->links() }}
		</div>
	@endif
</div>
