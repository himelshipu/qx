<div id="influencers-results">
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sn.</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencer</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usage</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Featured</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
				@forelse ($influencers as $influencer)
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">
							{{ (int) ($influencers->firstItem() ?? 1) + $loop->index }}
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center gap-3">
								<div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
									@if ($influencer->user?->profile_image_path || $influencer->user?->cover_image_path)
										<img src="{{ \App\Helpers\ImageHelper::url($influencer->user?->profile_image_path ?: $influencer->user?->cover_image_path) }}" alt="{{ $influencer->display_name ?: $influencer->user?->name }}" class="h-10 w-10 object-cover">
									@else
										<span class="text-sm font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($influencer->display_name ?: $influencer->user?->name ?? 'C', 0, 1)) }}</span>
									@endif
								</div>
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $influencer->display_name ?: $influencer->user?->name ?? 'Unnamed' }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">{{ $influencer->title_name ?: 'No title' }}</p>
								</div>
							</div>
						</td>
						<td class="px-4 py-3">
							<div class="flex flex-wrap gap-1">
								@forelse ($influencer->categories as $category)
									<span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $category->name }}</span>
								@empty
									<span class="text-xs text-gray-500 dark:text-gray-400">Uncategorized</span>
								@endforelse
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $influencer->user?->email ?? 'N/A' }}</td>
						<td class="px-4 py-3">
							<div class="text-xs text-gray-600 dark:text-gray-300">
								<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Applications: {{ $influencer->campaign_applications_count }}</span>
								<span class="inline-flex rounded bg-gray-100 px-2 py-1 dark:bg-gray-800">Orders: {{ $influencer->order_items_count }}</span>
							</div>
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $influencer->is_featured ? 'checked' : '' }} class="peer sr-only js-influencer-featured-toggle" data-influencer-id="{{ $influencer->id }}" />
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-amber-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-amber-300 dark:bg-gray-700 dark:peer-focus:ring-amber-800"></div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center">
								<label class="relative inline-flex cursor-pointer items-center">
									<input type="checkbox" {{ $influencer->is_active ? 'checked' : '' }} class="peer sr-only js-influencer-status-toggle" data-influencer-id="{{ $influencer->id }}" />
									<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
								</label>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $influencer->updated_at?->format('M d, Y') }}</td>
						<td class="px-4 py-3">
							<div class="flex items-center justify-end gap-2">
								<a href="{{ route('dashboard.influencers.view', $influencer) }}" class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" title="View influencer">
									<x-icons.eye class="h-4 w-4" />
								</a>
								<a href="{{ route('dashboard.influencers.edit', $influencer) }}" class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" title="Edit influencer">
									<x-icons.edit class="h-4 w-4" />
								</a>
								<form action="{{ route('dashboard.influencers.destroy', $influencer) }}" method="POST">
									@csrf
									@method('DELETE')
									<button type="submit" class="js-confirmable inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300" data-confirm-title="Delete Influencer" data-confirm-message="Delete this influencer? This action cannot be undone." data-confirm-button="Delete" data-confirm-variant="danger" title="Delete influencer">
										<x-icons.trash class="h-4 w-4" />
									</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="9" class="px-4 py-12 text-center">
							<p class="text-sm text-gray-500 dark:text-gray-400">No influencers found for the current filters.</p>
							<a href="{{ route('dashboard.influencers.create') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
								<x-icons.plus class="h-4 w-4" />
								Create First Influencer
							</a>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if ($influencers->hasPages())
		<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
			{{ $influencers->links() }}
		</div>
	@endif
</div>
