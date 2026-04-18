<div id="brands-results">
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sn.</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Contact</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usage</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Featured</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
				@forelse ($brands as $brand)
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ (int) ($brands->firstItem() ?? 1) + $loop->index }}</td>
						<td class="px-4 py-3">
							<div class="flex items-center gap-3">
								<div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-800">
									@if ($brand->user?->profile_image_path || $brand->user?->cover_image_path)
										<img src="{{ \App\Helpers\ImageHelper::url($brand->user?->profile_image_path ?: $brand->user?->cover_image_path) }}" alt="{{ $brand->brand_name }}" class="h-10 w-10 object-cover">
									@else
										<span class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($brand->brand_name, 0, 1)) }}</span>
									@endif
								</div>
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $brand->brand_name }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">{{ $brand->industry ?: 'No industry' }}</p>
								</div>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $brand->user?->name ?? 'Unknown' }}</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $brand->user?->email ?? 'N/A' }}</td>
						<td class="px-4 py-3">
							<div class="text-xs text-gray-600 dark:text-gray-300">
								<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Orders: {{ $brand->orders_count }}</span>
								<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Reviews: {{ $brand->reviews_count }}</span>
							</div>
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $brand->is_featured ? 'checked' : '' }} class="peer sr-only js-brand-featured-toggle" data-brand-id="{{ $brand->id }}" />
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-indigo-300 dark:bg-gray-700 dark:peer-focus:ring-indigo-900"></div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $brand->user?->is_active ? 'checked' : '' }} class="peer sr-only js-brand-status-toggle" data-brand-id="{{ $brand->id }}" />
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $brand->updated_at?->format('M d, Y') }}</td>
						<td class="px-4 py-3">
							<div class="flex items-center justify-end gap-2">
								<a href="{{ route('dashboard.brands.view', $brand) }}" class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" title="View brand">
									<x-icons.eye class="h-4 w-4" />
								</a>
								<a href="{{ route('dashboard.brands.edit', $brand) }}" class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" title="Edit brand">
									<x-icons.edit class="h-4 w-4" />
								</a>
								<form action="{{ route('dashboard.brands.destroy', $brand) }}" method="POST">
									@csrf
									@method('DELETE')
									<button type="submit" data-confirm-title="Delete Brand" data-confirm-message="Delete this brand? This action cannot be undone." data-confirm-button="Delete" data-confirm-variant="danger" title="Delete brand" class="js-confirmable inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
										<x-icons.trash class="h-4 w-4" />
									</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="9" class="px-4 py-12 text-center">
							<p class="text-sm text-gray-500 dark:text-gray-400">No brands found for the current filters.</p>
							<a href="{{ route('dashboard.brands.create') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
								<x-icons.plus class="h-4 w-4" />
								Create First Brand
							</a>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if ($brands->hasPages())
		<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
			{{ $brands->links() }}
		</div>
	@endif
</div>
