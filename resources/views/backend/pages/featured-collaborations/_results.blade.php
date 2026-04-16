<div id="featured-collaborations-results">
	<div class="overflow-x-auto">
		<table class="w-full">
			<thead class="border-b border-gray-200 dark:border-gray-800">
				<tr class="bg-gray-50 dark:bg-gray-800/50">
					<th class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Drag</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Brand</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Type</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Preview</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Order</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
				</tr>
			</thead>
			<tbody id="featured-collaborations-sortable" class="divide-y divide-gray-200 dark:divide-gray-800">
				@forelse($collaborations as $collaboration)
					<tr class="cursor-move" data-collaboration-id="{{ $collaboration->id }}">
						<td class="px-4 py-3 text-gray-400">
							<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
								<path d="M8 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
							</svg>
						</td>
						<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $collaboration->brand_name }}</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $collaboration->asset_type }}</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
							@if ($collaboration->asset_type === 'image' && $collaboration->image_path)
								<img src="{{ image_url($collaboration->image_path) }}" alt="{{ $collaboration->brand_name }}" class="h-12 w-12 rounded-lg object-cover">
							@elseif($collaboration->asset_type === 'video' && $collaboration->thumbnail_path)
								<img src="{{ image_url($collaboration->thumbnail_path) }}" alt="{{ $collaboration->brand_name }}" class="h-12 w-12 rounded-lg object-cover">
							@elseif($collaboration->asset_type === 'video')
								<span class="text-xs text-gray-500 dark:text-gray-400">Video</span>
							@else
								-
							@endif
						</td>
						<td class="px-4 py-3 text-sm">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $collaboration->is_published ? 'checked' : '' }} class="peer sr-only js-featured-collaboration-status-toggle" data-collaboration-id="{{ $collaboration->id }}" />
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
								</label>
							</div>
						</td>
						<td class="js-sort-order-value px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $collaboration->sort_order }}</td>
						<td class="px-4 py-3 text-right">
							<div class="flex items-center justify-end gap-2">
								<a href="{{ route('dashboard.featured-collaborations.edit', $collaboration) }}" class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
									<x-icons.edit class="h-4 w-4" />
								</a>
								<form action="{{ route('dashboard.featured-collaborations.destroy', $collaboration) }}" method="POST" class="inline js-confirmable"
									data-confirm-title="Delete Collaboration"
									data-confirm-message="Are you sure you want to delete this collaboration?"
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
							No collaborations found. <a href="{{ route('dashboard.featured-collaborations.create') }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if ($collaborations->hasPages())
		<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
			{{ $collaborations->links() }}
		</div>
	@endif
</div>
