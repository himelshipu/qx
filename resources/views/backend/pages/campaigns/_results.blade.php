<div id="campaigns-results">
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Campaign</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Type</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Status</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Budget</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Targeting</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Updated</th>
					<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
						Actions</th>
				</tr>
			</thead>
			<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
				@forelse ($campaigns as $campaign)
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3">
							<div>
								<a href="{{ route('dashboard.campaigns.view', $campaign) }}"
									class="text-sm font-semibold text-gray-900 transition hover:text-gray-700 dark:text-white dark:hover:text-gray-200">
									{{ $campaign->title }}
								</a>
								<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
									{{ $campaign->dashboard_description }}</p>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 capitalize dark:text-gray-300">{{ $campaign->campaign_type }}</td>
						<td class="px-4 py-3">
							<span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $campaign->dashboard_status_badge_class }}">
								{{ $campaign->dashboard_status_label }}
							</span>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
							{{ $campaign->budget_range_label }}
						</td>
						<td class="px-4 py-3">
							<div class="text-xs text-gray-600 dark:text-gray-300">
								<p>Influencers: {{ $campaign->targeting?->influencer_count ?? 'N/A' }}</p>
								<p>Categories: {{ $campaign->categories_count }}</p>
								<p>Applications: {{ $campaign->applications_count }}</p>
							</div>
						</td>
						<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $campaign->updated_at?->format('M d, Y') }}
						</td>
						<td class="px-4 py-3">
							<div class="flex items-center justify-end gap-2">
								<a href="{{ route('dashboard.campaigns.view', $campaign) }}"
									class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
									title="View campaign">
									<x-icons.eye class="h-4 w-4" />
								</a>
								<a href="{{ route('dashboard.campaigns.edit', $campaign) }}"
									class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
									title="Edit campaign">
									<x-icons.edit class="h-4 w-4" />
								</a>
								<form action="{{ route('dashboard.campaigns.destroy', $campaign) }}" method="POST">
									@csrf
									@method('DELETE')
									<button type="submit" {{ $campaign->dependency_count > 0 ? 'disabled' : '' }} data-confirm-title="Delete Campaign"
										data-confirm-message="Delete this campaign? This action cannot be undone." data-confirm-button="Delete"
										data-confirm-variant="danger"
										title="{{ $campaign->dependency_count > 0 ? 'Cannot delete: campaign has linked records' : 'Delete campaign' }}"
										class="js-confirmable inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
										<x-icons.trash class="h-4 w-4" />
									</button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="7" class="px-4 py-12 text-center">
							<p class="text-sm text-gray-500 dark:text-gray-400">No campaigns found for the current filters.</p>
							<a href="{{ route('dashboard.campaigns.standard.create') }}"
								class="mt-3 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
								<x-icons.plus class="h-4 w-4" />
								Create First Campaign
							</a>
						</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	@if ($campaigns->hasPages())
		<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
			{{ $campaigns->links() }}
		</div>
	@endif
</div>
