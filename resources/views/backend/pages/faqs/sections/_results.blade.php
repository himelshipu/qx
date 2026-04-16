<div id="faq-sections-results" data-table-container>
	<table class="w-full">
		<thead class="border-b border-gray-200 dark:border-gray-800">
			<tr class="bg-gray-50 dark:bg-gray-800/50">
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Title</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Code</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Audience</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Items</th>
				<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
				<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
			</tr>
		</thead>
		<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
			@forelse($sections as $section)
				<tr>
					<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $section->section_title }}</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $section->section_code }}</td>
					<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $section->audience_type }}</td>
					<td class="px-4 py-3 text-sm">
						<div class="flex items-center gap-2">
							<span class="inline-flex items-center justify-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">{{ $section->items_count }}</span>
							<a href="{{ route('dashboard.faqs.items.index', $section) }}"
								class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 px-2.5 py-1 text-xs text-gray-500 transition hover:bg-gray-50 hover:text-gray-700">
								<x-icons.navigator class="h-3.5 w-3.5" />
								View Items
							</a>
						</div>
					</td>
					<td class="px-4 py-3 text-sm">
						<label class="relative inline-flex cursor-pointer items-center">
							<input type="checkbox" class="peer sr-only" data-status-toggle data-id="{{ $section->id }}" {{ $section->is_active ? 'checked' : '' }}>
							<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
						</label>
					</td>
					<td class="px-4 py-3 text-right">
						<div class="flex items-center justify-end gap-2">
							<a href="{{ route('dashboard.faqs.items.create', $section) }}"
								class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-emerald-600 transition hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
								title="Add FAQ item">
								<x-icons.plus class="h-4 w-4" />
								<span>Add FAQ</span>
							</a>
							<a href="{{ route('dashboard.faqs.sections.edit', $section) }}"
								class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
								<x-icons.edit class="h-4 w-4" />
							</a>
							<form action="{{ route('dashboard.faqs.sections.destroy', $section) }}" method="POST" class="inline js-confirmable"
								data-confirm-title="Delete FAQ Section"
								data-confirm-message="This will delete all FAQ items in this section. Are you sure?"
								data-confirm-button="Delete Section"
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
					<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
						No sections found. <a href="{{ route('dashboard.faqs.sections.create') }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
					</td>
				</tr>
			@endforelse
		</tbody>
	</table>

	@if ($sections->hasPages())
		<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800" data-pagination-container>
			{{ $sections->links() }}
		</div>
	@endif
</div>
