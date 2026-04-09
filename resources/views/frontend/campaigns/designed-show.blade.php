@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-white dark:bg-gray-900 px-4 py-6 sm:px-6 lg:px-8">
		<div class="max-w-7xl mx-auto">
			<!-- Back Button (above first card) -->
			<div class="mb-4">
				<a href="{{ route('frontend.campaigns.index') }}"
					class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
					<x-icons.chevron-right class="w-4 h-4 -rotate-180" />
					Back to Campaigns
				</a>
			</div>

			<!-- First Card: Title, Platform, Status, Budget, Duration, Actions -->
			<div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm p-5 mb-6">

				<!-- Row 1 -->
				<div class="flex items-center justify-between gap-4">

					<!-- Title + Status -->
					<div class="flex items-center gap-3 flex-wrap">
						<h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white">
							{{ $campaign->title }}
						</h1>

						@if ($campaign->status === 'published')
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full  text-emerald-700  dark:text-emerald-300">
								● Active
							</span>
						@elseif ($campaign->status === 'paused')
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full  text-amber-700 dark:text-amber-300">
								● Paused
							</span>
						@elseif ($campaign->status === 'closed')
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full  text-red-700 dark:text-red-300">
								● Closed
							</span>
						@else
							<span
								class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full  text-gray-700 dark:text-gray-300">
								● Draft
							</span>
						@endif
					</div>

					<!-- Actions -->
					@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
						<div class="flex items-center gap-2">
							<a href="{{ route('frontend.campaigns.edit', $campaign) }}"
								class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
								<x-icons.edit class="w-4 h-4" />
								Edit
							</a>

							<form action="{{ route('frontend.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.');">
								@csrf @method('DELETE')
								<button type="submit"
									class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border border-red-300 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 transition">
									<x-icons.trash class="w-4 h-4" />
									Delete
								</button>
							</form>
						</div>
					@endif
				</div>

				<!-- Row 2 -->
				<div class="grid grid-cols-3 items-center mt-4 text-sm">

					<!-- Campaign Type -->
					<div class="text-left">
						<span
							class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
							{{ \Illuminate\Support\Str::headline($campaign->campaign_type ?? 'Campaign') }}
						</span>
					</div>

					<!-- Budget -->
					<div class="flex justify-center items-center gap-2 text-gray-700 dark:text-gray-300">
						<div class="p-1.5 rounded-md bg-emerald-100 dark:bg-emerald-900/30">
							<x-icons.dollar-sign class="w-4 h-4 text-emerald-600 dark:text-emerald-300" />
						</div>
						<span class="font-medium">
							${{ number_format($campaign->budget_min, 0) }}
							<span class="text-gray-400">—</span>
							${{ number_format($campaign->budget_max, 0) }}
						</span>
					</div>

					<!-- Dates -->
					<div class="flex justify-end items-center gap-2 text-gray-700 dark:text-gray-300">
						<div class="p-1.5 rounded-md bg-indigo-100 dark:bg-indigo-900/30">
							<x-icons.calendar class="w-4 h-4 text-indigo-600 dark:text-indigo-300" />
						</div>
						<span class="font-medium text-right">
							{{ $campaign->start_date?->format('M d') ?? '—' }}
							@if ($campaign->end_date)
								<span class="text-gray-400 mx-1">→</span>
								{{ $campaign->end_date->format('M d') }}
							@endif
						</span>
					</div>

				</div>

			</div>

			<!-- Stats Row: Total, Approved, Pending, Rejected -->
			<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
				<div
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-4 shadow-sm flex items-center gap-3">
					<div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-2">
						<x-icons.users class="w-5 h-5 text-blue-600 dark:text-blue-400" />
					</div>
					<div>
						<p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</p>
						<p class="text-xl font-bold text-gray-900 dark:text-white">{{ $campaign->applications->count() }}</p>
					</div>
				</div>
				<div
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-4 shadow-sm flex items-center gap-3">
					<div class="rounded-full bg-emerald-100 dark:bg-emerald-900/30 p-2">
						<x-icons.check class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
					</div>
					<div>
						<p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Approved</p>
						<p class="text-xl font-bold text-gray-900 dark:text-white">{{ $applicationStats['approved'] ?? 0 }}</p>
					</div>
				</div>
				<div
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-4 shadow-sm flex items-center gap-3">
					<div class="rounded-full bg-amber-100 dark:bg-amber-900/30 p-2">
						<x-icons.clock class="w-5 h-5 text-amber-600 dark:text-amber-400" />
					</div>
					<div>
						<p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pending</p>
						<p class="text-xl font-bold text-gray-900 dark:text-white">
							{{ $campaign->applications->whereIn('status', ['invited', 'applied'])->count() }}</p>
					</div>
				</div>
				<div
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 p-4 shadow-sm flex items-center gap-3">
					<div class="rounded-full bg-red-100 dark:bg-red-900/30 p-2">
						<x-icons.x class="w-5 h-5 text-red-600 dark:text-red-400" />
					</div>
					<div>
						<p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rejected</p>
						<p class="text-xl font-bold text-gray-900 dark:text-white">{{ $applicationStats['rejected'] ?? 0 }}</p>
					</div>
				</div>
			</div>

			<!-- Two collapsible cards: Description/Instructions & Target Audience -->
			<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
				<!-- Left: Description & Instructions -->
				<details
					class="group rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
					<summary
						class="flex items-center justify-between cursor-pointer px-5 py-4 font-semibold text-gray-900 dark:text-white text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50">
						<span class="flex items-center gap-2">
							<x-icons.file-text class="w-4 h-4" />
							Description & Instructions
						</span>
						<x-icons.chevron-down class="w-4 h-4 transition-transform group-open:rotate-180" />
					</summary>
					<div class="border-t border-gray-200 dark:border-gray-800 px-5 py-4 space-y-4">
						@if ($campaign->description)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Description</p>
								<p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $campaign->description }}</p>
							</div>
						@endif
						@if ($campaign->instructions)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Instructions</p>
								<p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $campaign->instructions }}</p>
							</div>
						@endif
						@if (!$campaign->description && !$campaign->instructions)
							<p class="text-sm text-gray-500 dark:text-gray-400 italic">No description or instructions provided.</p>
						@endif
					</div>
				</details>

				<!-- Right: Target Audience (Categories, Follower Ranges, Countries, Demographics) -->
				<details
					class="group rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
					<summary
						class="flex items-center justify-between cursor-pointer px-5 py-4 font-semibold text-gray-900 dark:text-white text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50">
						<span class="flex items-center gap-2">
							<x-icons.target class="w-4 h-4" />
							Target Audience
						</span>
						<x-icons.chevron-down class="w-4 h-4 transition-transform group-open:rotate-180" />
					</summary>
					<div class="border-t border-gray-200 dark:border-gray-800 px-5 py-4 space-y-4">
						@if ($campaign->categories->count() > 0)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Categories</p>
								<div class="flex flex-wrap gap-2">
									@foreach ($campaign->categories as $category)
										<span
											class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">{{ $category->name }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if ($campaign->followerRanges->count() > 0)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Follower Ranges
								</p>
								<div class="flex flex-wrap gap-2">
									@foreach ($campaign->followerRanges as $range)
										<span
											class="inline-flex rounded-full bg-purple-100 dark:bg-purple-900/30 px-2.5 py-1 text-xs font-medium text-purple-700 dark:text-purple-300">{{ $range->label }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if ($campaign->targetCountries->count() > 0)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Countries
									({{ $campaign->targetCountries->count() }})</p>
								<div class="flex flex-wrap gap-2">
									@foreach ($campaign->targetCountries as $country)
										<span
											class="inline-flex rounded-full bg-indigo-100 dark:bg-indigo-900/30 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300">{{ $country->country_code }}</span>
									@endforeach
								</div>
							</div>
						@endif

						@if ($campaign->targeting)
							<div>
								<p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Demographics</p>
								<div class="grid grid-cols-2 gap-2 text-sm">
									<div>
										<span class="text-gray-500 dark:text-gray-400">Gender:</span>
										<span
											class="font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Str::headline($campaign->targeting->target_gender ?? 'Any') }}</span>
									</div>
									<div>
										<span class="text-gray-500 dark:text-gray-400">Age:</span>
										<span class="font-medium text-gray-900 dark:text-white">
											@if ($campaign->targeting->age_min || $campaign->targeting->age_max)
												{{ $campaign->targeting->age_min ?? '0' }} – {{ $campaign->targeting->age_max ?? '99' }}
											@else
												Any
											@endif
										</span>
									</div>
								</div>
							</div>
						@endif
					</div>
				</details>
			</div>

			<!-- Influencer Applications Section (Full Width) -->
			<div
				class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
				<!-- Header with Search & Filter -->
				<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
					<div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
						<h2 class="text-lg font-bold text-gray-900 dark:text-white">Influencer Applications</h2>
						<span
							class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-sm font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
							{{ $campaign->applications->count() }}
						</span>
					</div>
					<div class="flex flex-col sm:flex-row gap-3 items-end">
						<div class="flex-[0_0_58%] relative">
							<input type="text" placeholder="Search..." id="js-table-search"
								class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition">
							<x-icons.search class="absolute right-3 top-2.5 w-4 h-4 text-gray-400 pointer-events-none" />
						</div>
						<select id="js-status-filter"
							class="flex-[0_0_30%] px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white transition">
							<option value="">All Status</option>
							<option value="invited">Invited</option>
							<option value="applied">Applied</option>
							<option value="approved">Approved</option>
							<option value="rejected">Rejected</option>
						</select>
						<button type="button" id="js-filter-reset"
							class="flex-[0_0_10%] px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-100 dark:bg-gray-700 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition font-medium">
							Reset
						</button>
					</div>
				</div>

				<!-- Batch Actions Bar -->
				@if ($campaign->applications->count() > 0)
					<div id="js-batch-actions"
						class="px-5 py-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-3"
						style="display: none;">
						<div class="text-sm text-gray-600 dark:text-gray-300">
							<span id="js-batch-count" class="font-semibold">0</span> selected
						</div>
						<div class="flex items-center gap-2">
							<button type="button" id="js-batch-approve"
								class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-2 text-xs sm:text-sm font-medium text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50 transition">
								<x-icons.check class="w-4 h-4" />
								Approve Selected
							</button>
							<button type="button" id="js-batch-reject"
								class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 px-3 py-2 text-xs sm:text-sm font-medium text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition">
								<x-icons.x class="w-4 h-4" />
								Reject Selected
							</button>
							<button type="button" id="js-batch-cancel"
								class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs sm:text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition">
								Cancel
							</button>
						</div>
					</div>
				@endif

				<!-- Table -->
				<div class="overflow-x-auto" style="max-height: 65vh; overflow-y: auto;">
					<table class="w-full text-sm" id="js-applications-table">
						<thead class="sticky top-0 bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-800">
							<tr>
								<th class="w-8 px-4 py-3 text-left"><input type="checkbox" id="js-select-all"
										class="w-4 h-4 rounded border-gray-300 dark:border-gray-600" /></th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Followers
								</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden lg:table-cell">Engagement
								</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Applied</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden sm:table-cell">Decided</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
							@forelse ($campaign->applications->sortByDesc('applied_at') as $application)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition js-table-row"
									data-status="{{ $application->status }}"
									data-search="{{ strtolower($application->influencer->user->name . ' ' . ($application->influencer->display_name ?? '')) }}"
									data-app-id="{{ $application->id }}">
									<td class="px-4 py-3">
										<input type="checkbox" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 js-row-checkbox"
											value="{{ $application->id }}" />
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center gap-3">
											<div
												class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
												<span
													class="text-xs font-bold text-white">{{ substr($application->influencer->user->name ?? 'N', 0, 1) }}</span>
											</div>
											<div class="min-w-0">
												<p class="font-medium text-gray-900 dark:text-white truncate">
													{{ $application->influencer->user->name ?? 'Unknown' }}</p>
												<p class="text-xs text-gray-500 dark:text-gray-400 truncate">
													{{ $application->influencer->display_name ?? '@unknown' }}</p>
											</div>
										</div>
									</td>
									<td class="px-4 py-3 hidden md:table-cell text-gray-700 dark:text-gray-300">
										@php $maxFollowers = $application->influencer->platformStats()->latest('follower_count')->first()?->follower_count ?? 0; @endphp
										{{ $maxFollowers ? number_format($maxFollowers) : 'N/A' }}
									</td>
									<td class="px-4 py-3 hidden lg:table-cell text-gray-700 dark:text-gray-300">
										@php $avgEngagement = $application->influencer->platformStats()->avg('engagement_rate') ?? 0; @endphp
										{{ $avgEngagement ? number_format($avgEngagement, 2) . '%' : '—' }}
									</td>
									<td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs whitespace-nowrap">
										{{ $application->applied_at?->format('M d, Y') ?? '—' }}
									</td>
									<td class="px-4 py-3">
									@if ($application->status === 'approved')
											<span
												class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Approved</span>
										@elseif ($application->status === 'rejected')
											<span
												class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-300">Rejected</span>
										@elseif ($application->status === 'applied')
											<span
												class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Applied</span>
										@else
											<span
												class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Invited</span>
										@endif
									</td>
									<td class="px-4 py-3 hidden sm:table-cell text-xs text-gray-600 dark:text-gray-400 whitespace-nowrap">
										@if ($application->decided_at)
											<span class="font-medium">{{ $application->decided_at->format('M d') }}</span>
										@else
											<span class="text-gray-400">—</span>
										@endif
									</td>
									<td class="px-4 py-3">
										<div class="flex items-center gap-1.5 js-action-buttons">
													@if ($application->status === 'approved')
												<button disabled
													class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 cursor-default opacity-60">
													<x-icons.check class="w-3 h-3" />
												</button>
											@elseif ($application->status === 'rejected')
												<button disabled
													class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 cursor-default opacity-60">
													<x-icons.x class="w-3 h-3" />
												</button>
											@else
												<form
													action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}"
													method="POST" class="inline js-approve-form">
													@csrf
													<input type="hidden" name="status" value="approved">
													<button type="submit"
														class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50 transition"
														title="Approve">
														<x-icons.check class="w-3 h-3" />
													</button>
												</form>
												<form
													action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}"
													method="POST" class="inline js-reject-form">
													@csrf
													<input type="hidden" name="status" value="rejected">
													<button type="submit"
														class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition"
														title="Reject">
														<x-icons.x class="w-3 h-3" />
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
										<p class="text-sm">📭 No influencer applications yet</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const table = document.getElementById('js-applications-table');
			const tbody = table?.querySelector('tbody');
			const rows = Array.from(tbody?.querySelectorAll('tr') || []);
			const selectAllCheckbox = document.getElementById('js-select-all');
			const rowCheckboxes = document.querySelectorAll('.js-row-checkbox');
			const batchActionsContainer = document.getElementById('js-batch-actions');
			const batchCountEl = document.getElementById('js-batch-count');
			const batchApproveBtn = document.getElementById('js-batch-approve');
			const batchRejectBtn = document.getElementById('js-batch-reject');
			const batchCancelBtn = document.getElementById('js-batch-cancel');
			const searchInput = document.getElementById('js-table-search');
			const statusFilter = document.getElementById('js-status-filter');
			const resetBtn = document.getElementById('js-filter-reset');

			// Reset button functionality
			resetBtn?.addEventListener('click', function() {
				searchInput.value = '';
				statusFilter.value = '';
				rows.forEach(row => {
					row.style.display = '';
				});
			});

			// Search functionality
			searchInput?.addEventListener('keyup', function() {
				const searchTerm = this.value.toLowerCase();
				rows.forEach(row => {
					const searchData = row.dataset.search || '';
					const matches = searchData.includes(searchTerm);
					row.style.display = matches ? '' : 'none';
				});
			});

			// Status filter
			statusFilter?.addEventListener('change', function() {
				const selectedStatus = this.value;
				rows.forEach(row => {
					const rowStatus = row.dataset.status || '';
					const matches = !selectedStatus || rowStatus === selectedStatus;
					row.style.display = matches ? '' : 'none';
				});
			});

			// Checkbox selection
			selectAllCheckbox?.addEventListener('change', function() {
				rowCheckboxes.forEach(cb => {
					const isVisible = cb.closest('tr').style.display !== 'none';
					if (isVisible) {
						cb.checked = this.checked;
					}
				});
				updateBatchUI();
			});

			rowCheckboxes.forEach(checkbox => {
				checkbox.addEventListener('change', updateBatchUI);
			});

			function updateBatchUI() {
				const selectedCount = document.querySelectorAll('.js-row-checkbox:checked').length;
				if (batchCountEl) batchCountEl.textContent = selectedCount;
				if (batchActionsContainer) {
					batchActionsContainer.style.display = selectedCount > 0 ? 'flex' : 'none';
				}
			}

			batchApproveBtn?.addEventListener('click', function() {
				const checkedCheckboxes = Array.from(document.querySelectorAll('.js-row-checkbox:checked'));
				if (checkedCheckboxes.length === 0) return;
				checkedCheckboxes.forEach(checkbox => {
					const row = checkbox.closest('tr');
					const approveForm = row?.querySelector('.js-approve-form');
					if (approveForm) approveForm.submit();
				});
			});

			batchRejectBtn?.addEventListener('click', function() {
				const checkedCheckboxes = Array.from(document.querySelectorAll('.js-row-checkbox:checked'));
				if (checkedCheckboxes.length === 0) return;
				checkedCheckboxes.forEach(checkbox => {
					const row = checkbox.closest('tr');
					const rejectForm = row?.querySelector('.js-reject-form');
					if (rejectForm) rejectForm.submit();
				});
			});

			batchCancelBtn?.addEventListener('click', function() {
				rowCheckboxes.forEach(cb => cb.checked = false);
				if (selectAllCheckbox) selectAllCheckbox.checked = false;
				updateBatchUI();
			});
		});
	</script>
@endsection
