@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-white dark:bg-gray-900 px-4 py-6 sm:px-6 lg:px-8" data-campaign-show-root
		data-campaign-status="{{ $campaign->status }}"
		data-update-status-url="{{ route('frontend.campaigns.update-status', $campaign) }}">
		<div class="max-w-full mx-auto">
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
				<div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

					<!-- Left: Title + Status Selector -->
					<div class="flex-1 min-w-0">
						<h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-3">
							{{ $campaign->title }}
						</h1>
						<div class="mb-3 flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
							<span
								class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-gray-700 dark:bg-gray-700 dark:text-gray-300">
								Owned by {{ $brandName }}
							</span>
						</div>

						<!-- Status Selector for Brand Owners -->
						@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
							<div class="inline-block" x-data="campaignStatusForm()">
								<div class="flex items-center gap-3 flex-wrap">
									<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Status:</label>
									<div class="flex items-center gap-2">
										<select @change="updateStatus" :disabled="isLoading"
											class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
											x-model="selectedStatus">
											<option value="draft">Draft</option>
											<option value="published">Published</option>
											<option value="paused">Paused</option>
											<option value="closed">Closed</option>
											<option value="archived">Archived</option>
										</select>
										<span class="inline-flex items-center opacity-0 transition-opacity" :class="{ 'opacity-100': isLoading }"
											x-show="isLoading">
											<svg class="w-4 h-4 text-gray-600 dark:text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
												<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
												</circle>
												<path class="opacity-75" fill="currentColor"
													d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
												</path>
											</svg>
										</span>
										<span class="text-xs font-medium px-2 py-1 rounded transition-all opacity-0" :class="feedbackClass"
											x-show="showFeedback">
											<span x-text="feedbackText"></span>
										</span>
									</div>
								</div>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
									Change the campaign status to manage its visibility and activity.
								</p>
							</div>
						@else
							<!-- Status Badge for Non-Owners -->
							<div class="inline-block">
								@if ($campaign->status === 'published')
									<span
										class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
												clip-rule="evenodd" />
										</svg>
										Active (Published)
									</span>
								@elseif ($campaign->status === 'paused')
									<span
										class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M10 18a8 8 0 100-16 8 8 0 000 16M9.383 5a1 1 0 011.234 1.471L7.669 10l2.948 3.529A1 1 0 119.617 15l-4-4.771a1 1 0 010-1.458l4-4.771z"
												clip-rule="evenodd" />
										</svg>
										Paused
									</span>
								@elseif ($campaign->status === 'closed')
									<span
										class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.368l5.657 5.657a1 1 0 01-1.414 1.414l-5.657-5.657z"
												clip-rule="evenodd" />
										</svg>
										Closed
									</span>
								@elseif ($campaign->status === 'archived')
									<span
										class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z" />
											<path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" clip-rule="evenodd" />
										</svg>
										Archived
									</span>
								@else
									<span
										class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.077 13.308-5.077 18.384 0a1 1 0 01-1.414 1.414zM14.95 11.05a7 7 0 00-9.9 0 1 1 0 01-1.414-1.414 9 9 0 0112.728 0 1 1 0 01-1.414 1.414zM12.12 13.88a3 3 0 00-4.242 0 1 1 0 01-1.415-1.415 5 5 0 017.072 0 1 1 0 01-1.415 1.415zM9 16a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z"
												clip-rule="evenodd" />
										</svg>
										Draft
									</span>
								@endif
							</div>
						@endif
					</div>

					<!-- Right: Actions -->
					@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
						<div class="flex items-center gap-2 shrink-0">
							<a href="{{ route('frontend.campaigns.edit', $campaign) }}"
								class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition">
								<x-icons.edit class="w-4 h-4" />
								<span class="hidden sm:inline">Edit</span>
							</a>

							<form action="{{ route('frontend.campaigns.destroy', $campaign) }}" method="POST"
								onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.');">
								@csrf @method('DELETE')
								<button type="submit"
									class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-medium rounded-lg border border-red-300 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 transition">
									<x-icons.trash class="w-4 h-4" />
									<span class="hidden sm:inline">Delete</span>
								</button>
							</form>
						</div>
					@endif
				</div>

				<!-- Row 2 -->
				<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 text-sm">

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
							{{ $campaign->applications->whereIn('status', ['invited', 'applied', 'countered_by_brand', 'countered_by_influencer'])->count() }}
						</p>
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

			<!-- Influencer Work Progress -->
			@if (($workProgress ?? collect())->count() > 0)
				<div id="work-progress"
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden mb-6">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-3">
						<div>
							<h2 class="text-lg font-bold text-gray-900 dark:text-white">Influencer Work Progress</h2>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tracks delivery stage for approved influencers in this
								campaign.</p>
						</div>
						<span
							class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
							{{ $workProgress->count() }} Active
						</span>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
						@foreach ($workProgress as $progressItem)
							<div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50/70 dark:bg-gray-900/30">
								<div class="flex items-start justify-between gap-3">
									<div class="min-w-0">
										<p class="font-semibold text-gray-900 dark:text-white truncate">{{ $progressItem['influencer_name'] }}</p>
										@if ($progressItem['influencer_handle'])
											<p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $progressItem['influencer_handle'] }}</p>
										@endif
										@if ($progressItem['agreed_amount'])
											<p class="mt-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Budget:
												{{ $progressItem['currency'] }} {{ number_format((float) $progressItem['agreed_amount'], 2) }}</p>
										@endif
									</div>
									<span
										class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $progressItem['status_badge_class'] }}">
										{{ $progressItem['status_label'] }}
									</span>
								</div>

								<div class="mt-4">
									<div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
										<span>Progress</span>
										<span class="font-semibold">{{ $progressItem['progress_percent'] }}%</span>
									</div>
									<div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
										<div class="h-2 rounded-full {{ $progressItem['progress_bar_class'] }}"
											style="width: {{ $progressItem['progress_percent'] }}%"></div>
									</div>
								</div>

								<p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
									Last update:
									{{ $progressItem['updated_at']?->format('M d, Y h:i A') ?? ($progressItem['decided_at']?->format('M d, Y h:i A') ?? 'Pending order kickoff') }}
								</p>
								<div class="mt-3 flex items-center gap-2">
									<a href="{{ route('frontend.conversations.open-order', ['influencer' => $progressItem['influencer_id']]) }}"
										class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
										<x-icons.message-square class="h-3.5 w-3.5" />
										Message
									</a>
								</div>
							</div>
						@endforeach
					</div>
				</div>
			@endif

			<!-- Deliverables Checklist: ONLY FOR BRAND OWNERS -->
			@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
				@php
					$allDeliverables = collect();
					foreach ($workProgress as $progress) {
					    $subOrder = $latestSubOrdersByInfluencer->get($progress['influencer_id']);
					    if ($subOrder && $subOrder->deliverables) {
					        foreach ($subOrder->deliverables as $deliverable) {
					            $allDeliverables->push([
					                'deliverable' => $deliverable,
					                'influencer_name' => $progress['influencer_name'],
					                'influencer_id' => $progress['influencer_id'],
					                'subOrder' => $subOrder,
					            ]);
					        }
					    }
					}
				@endphp

				@if ($allDeliverables->count() > 0)
					<div
						class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden mb-6">
						<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-3">
							<div>
								<h2 class="text-lg font-bold text-gray-900 dark:text-white">Deliverables Checklist</h2>
								<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Review and approve work submitted by influencers.</p>
							</div>
							@php
								$approvedCount = $allDeliverables->where('deliverable.status', 'approved')->count();
								$totalCount = $allDeliverables->count();
							@endphp
							<span
								class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
								{{ $approvedCount }}/{{ $totalCount }} Approved
							</span>
						</div>

						<div class="divide-y divide-gray-200 dark:divide-gray-800">
							@foreach ($allDeliverables as $item)
								@php
									$deliverable = $item['deliverable'];
									$statusClass = match ($deliverable->status) {
									    'submitted' => 'bg-yellow-50 border-l-4 border-yellow-500 dark:bg-yellow-900/20',
									    'approved' => 'bg-green-50 border-l-4 border-green-500 dark:bg-green-900/20',
									    'changes_requested' => 'bg-amber-50 border-l-4 border-amber-500 dark:bg-amber-900/20',
									    'rejected' => 'bg-red-50 border-l-4 border-red-500 dark:bg-red-900/20',
									    default => 'bg-gray-50 border-l-4 border-gray-400 dark:bg-gray-900/20',
									};
									$statusBadgeClass = match ($deliverable->status) {
									    'submitted' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
									    'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
									    'changes_requested' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
									    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
									    default => 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300',
									};
								@endphp
								<div class="p-4 {{ $statusClass }}">
									<div class="flex items-start justify-between gap-4 flex-wrap">
										<div class="flex-1 min-w-0">
											<div class="flex items-center gap-2 mb-1">
												<svg class="w-4 h-4 text-gray-600 dark:text-gray-400 flex-shrink-0" fill="currentColor"
													viewBox="0 0 20 20">
													<path
														d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
												</svg>
												<span class="font-semibold text-gray-900 dark:text-white text-sm">{{ $item['influencer_name'] }} -
													{{ ucfirst($deliverable->deliverable_type) }}</span>
											</div>
											@if ($deliverable->notes)
												<p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $deliverable->notes }}</p>
											@endif
											@if ($deliverable->file_path || $deliverable->external_url)
												<p class="text-xs text-blue-600 dark:text-blue-400 mb-2">
													@if ($deliverable->file_path)
														<a href="#" class="hover:underline">{{ basename($deliverable->file_path) }}</a>
													@elseif ($deliverable->external_url)
														<a href="{{ $deliverable->external_url }}" target="_blank" rel="noopener" class="hover:underline">View
															Link ↗</a>
													@endif
												</p>
											@endif
											<div class="text-xs text-gray-500 dark:text-gray-400">Submitted
												{{ $deliverable->created_at?->format('M d, Y') ?: 'Recently' }}</div>
										</div>
										<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClass }} flex-shrink-0">
											{{ str_replace('_', ' ', ucfirst($deliverable->status)) }}
										</span>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endif
			@endif

			<!-- Applications Section: ONLY FOR BRAND OWNERS -->
			@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
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
								<option value="countered_by_brand">Countered By Brand</option>
								<option value="countered_by_influencer">Countered By Influencer</option>
								<option value="approved">Approved</option>
								<option value="completed">Completed</option>
								<option value="rejected">Rejected</option>
								<option value="declined_by_brand">Declined By Brand</option>
								<option value="declined_by_influencer">Declined By Influencer</option>
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
									<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Work Status
									</th>
									<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Budget</th>
									<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden sm:table-cell">Decided
									</th>
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
													class="w-8 h-8 rounded-full bg-linear-to-br from-blue-400 to-blue-600 flex items-center justify-center shrink-0">
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
											{{ ($applicationUi[$application->id]['max_followers'] ?? 0) > 0 ? number_format($applicationUi[$application->id]['max_followers']) : 'N/A' }}
										</td>
										<td class="px-4 py-3 hidden lg:table-cell text-gray-700 dark:text-gray-300">
											{{ ($applicationUi[$application->id]['avg_engagement'] ?? 0) > 0 ? number_format($applicationUi[$application->id]['avg_engagement'], 2) . '%' : '—' }}
										</td>
										<td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs whitespace-nowrap">
											{{ $application->applied_at?->format('M d, Y') ?? '—' }}
										</td>
										<td class="px-4 py-3">
											<span
												class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $applicationUi[$application->id]['status_class'] ?? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' }}">{{ $applicationUi[$application->id]['status_label'] ?? 'Invited' }}</span>
										</td>
										<td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell">
											@if (
												($application->status === 'approved' || $application->status === 'completed') &&
													($applicationUi[$application->id]['work_status'] ?? null))
												<span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold"
													style="{{ $applicationUi[$application->id]['work_status_style'] ?? '' }}">
													{{ $applicationUi[$application->id]['work_status'] }}
												</span>
											@else
												—
											@endif
										</td>
										<td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell whitespace-nowrap">
											@if ($application->agreed_rate)
												<span class="font-semibold text-emerald-700 dark:text-emerald-300">
													{{ $applicationUi[$application->id]['currency'] ?? strtoupper((string) ($campaign->currency ?? 'USD')) }}
													{{ number_format((float) $application->agreed_rate, 2) }}
												</span>
											@else
												<div class="space-y-0.5">
													@if ($application->influencer_offer || $application->proposed_rate)
														<p>Inf:
															{{ $applicationUi[$application->id]['currency'] ?? strtoupper((string) ($campaign->currency ?? 'USD')) }}
															{{ number_format((float) ($application->influencer_offer ?? $application->proposed_rate), 2) }}</p>
													@endif
													@if ($application->brand_offer)
														<p>Brand:
															{{ $applicationUi[$application->id]['currency'] ?? strtoupper((string) ($campaign->currency ?? 'USD')) }}
															{{ number_format((float) $application->brand_offer, 2) }}</p>
													@endif
													@if (!$application->influencer_offer && !$application->proposed_rate && !$application->brand_offer)
														<p class="text-gray-400">—</p>
													@endif
												</div>
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
											<div class="flex flex-wrap items-center gap-1.5 js-action-buttons">
												<a href="{{ route('frontend.conversations.open-order', ['influencer' => $application->influencer_id]) }}"
													class="px-2 py-1 text-xs rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
													title="Message this influencer">
													<x-icons.message-square class="w-3 h-3" />
												</a>
												@if ($application->status === 'approved')
													@if ($progressByApplication[$application->id] ?? null)
														<span
															class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
															Order: {{ $progressByApplication[$application->id]['status_label'] ?? 'Pending' }}
														</span>
														<a href="#work-progress"
															class="px-2 py-1 text-xs rounded border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 transition">View</a>
													@else
														<span
															class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
															Approved, waiting for order
														</span>
													@endif
												@elseif ($application->status === 'rejected')
													<!-- Already declined -->
													<button disabled
														class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 cursor-not-allowed opacity-50"
														title="Cannot approve a declined influencer"
														onclick="window.toast?.error('Cannot approve a declined influencer. Their application has already been rejected.')">
														<x-icons.check class="w-3 h-3" />
													</button>
													<button disabled
														class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 cursor-not-allowed opacity-60"
														title="This influencer is already declined">
														<x-icons.x class="w-3 h-3" />
													</button>
												@elseif (in_array($application->status, ['declined_by_brand', 'declined_by_influencer'], true))
													<button disabled
														class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed opacity-60"
														title="Negotiation was declined">
														<x-icons.x class="w-3 h-3" />
													</button>
												@elseif ($application->status === 'completed')
													@if ($progressByApplication[$application->id] ?? null)
														<span
															class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
															Completed
														</span>
														<a href="#work-progress"
															class="px-2 py-1 text-xs rounded border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300 transition">View</a>
													@else
														<span
															class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
															Completed
														</span>
													@endif
												@elseif ($campaign->status === 'closed')
													<button disabled
														class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed opacity-50"
														title="Campaign is closed - negotiation unavailable">
														<x-icons.x class="w-3 h-3" />
													</button>
												@else
													{{-- Show Negotiate button only in negotiable states --}}
													@if (in_array($application->status, ['applied', 'countered_by_brand', 'countered_by_influencer', 'invited']))
														<button type="button"
															class="px-3 py-1.5 text-xs rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 transition font-medium js-negotiate-btn"
															data-app-id="{{ $application->id }}" data-campaign-id="{{ $campaign->id }}"
															data-action-url="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}"
															data-status="{{ $application->status }}"
															data-influencer-name="{{ $application->influencer->user->name }}"
															data-influencer-offer="{{ $application->influencer_offer ?? '' }}"
															data-brand-offer="{{ $application->brand_offer ?? '' }}"
															data-last-counter-by="{{ $application->last_counter_by ?? '' }}" title="Open negotiation modal">
															Negotiate
														</button>
													@endif
												@endif
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
											<p class="text-sm">📭 No influencer applications yet</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			@else
				<!-- INFLUENCER VIEW: Show their application status and relevant actions -->
				<div
					class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
						<h2 class="text-lg font-bold text-gray-900 dark:text-white">Your Application</h2>
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Manage your participation in this campaign</p>
					</div>

					<div class="px-5 py-6">
						@if ($influencerApplication)
							<!-- Influencer has applied -->
							<div class="space-y-4">
								<!-- Application Status Card -->
								<div class="rounded-lg bg-gray-50 dark:bg-gray-700/50 p-4 border border-gray-200 dark:border-gray-700">
									<div class="flex items-center justify-between gap-4">
										<div>
											<p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Status</p>
											<span
												class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold mt-1 {{ $influencerApplicationUi['status_class'] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
												{{ $influencerApplicationUi['status_label'] ?? 'Unknown' }}
											</span>
										</div>
										<div class="text-right">
											<p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Applied On</p>
											<p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
												{{ $influencerApplication->applied_at?->format('M d, Y') ?? 'Pending' }}
											</p>
											@if ($influencerApplication->agreed_rate)
												<p class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 mt-1">
													Agreed: {{ $influencerApplicationUi['currency'] ?? strtoupper((string) ($campaign->currency ?? 'USD')) }}
													{{ number_format((float) $influencerApplication->agreed_rate, 2) }}
												</p>
											@endif
										</div>
									</div>
								</div>

								@if ($influencerApplication->status === 'approved')
									<!-- Show work status if approved -->
									<div
										class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200 dark:border-emerald-900/50">
										<p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">✓ You're approved for this
											campaign!</p>
										<p class="text-sm text-emerald-800 dark:text-emerald-200">
											You can now see your work progress and deliverables above. Let the brand know if you have any questions about
											the requirements.
										</p>
									</div>
								@elseif ($influencerApplication->status === 'rejected')
									<!-- Show rejection message -->
									<div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-900/50">
										<p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-3">Not Selected</p>
										<p class="text-sm text-red-800 dark:text-red-200">
											Unfortunately, you were not selected for this campaign. Other influencers have been chosen, but check for
											other campaigns that might be a good fit!
										</p>
									</div>
								@elseif ($influencerApplication->status === 'applied')
									<!-- Show pending decision message -->
									<div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-900/50">
										<p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Waiting for Review</p>
										<p class="text-sm text-blue-800 dark:text-blue-200">
											The brand is reviewing your application. You'll be notified once they make a decision.
										</p>
										@if ($influencerApplication->influencer_offer || $influencerApplication->proposed_rate)
											<p class="text-xs font-semibold text-blue-700 dark:text-blue-300 mt-2">
												Your Offer: {{ strtoupper((string) ($campaign->currency ?? 'USD')) }}
												{{ number_format((float) ($influencerApplication->influencer_offer ?? $influencerApplication->proposed_rate), 2) }}
											</p>
										@endif
									</div>
								@elseif ($influencerApplication->status === 'countered_by_brand')
									<div
										class="rounded-lg bg-indigo-50 dark:bg-indigo-900/20 p-4 border border-indigo-200 dark:border-indigo-900/50">
										<p class="text-sm font-semibold text-indigo-900 dark:text-indigo-100 mb-2">Brand sent a counter offer</p>
										<p class="text-sm text-indigo-800 dark:text-indigo-200">
											Brand Offer: <span class="font-semibold">{{ strtoupper((string) ($campaign->currency ?? 'USD')) }}
												{{ number_format((float) ($influencerApplication->brand_offer ?? 0), 2) }}</span>
										</p>
										@if ($influencerApplication->influencer_offer || $influencerApplication->proposed_rate)
											<p class="text-xs text-indigo-700 dark:text-indigo-300 mt-1">Your Last Offer:
												{{ strtoupper((string) ($campaign->currency ?? 'USD')) }}
												{{ number_format((float) ($influencerApplication->influencer_offer ?? $influencerApplication->proposed_rate), 2) }}
											</p>
										@endif
									</div>

									<div class="rounded-lg bg-white dark:bg-gray-800 p-4 border border-gray-200 dark:border-gray-700">
										<p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">Respond to
											Offer</p>
										<div class="flex flex-col gap-2">
											<form method="POST" action="{{ route('frontend.campaigns.respond-offer', $influencerApplication) }}">
												@csrf
												<input type="hidden" name="action" value="accept" />
												<button type="submit"
													class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition">Accept
													Brand Offer</button>
											</form>
											<form method="POST" action="{{ route('frontend.campaigns.respond-offer', $influencerApplication) }}"
												class="flex items-center gap-2">
												@csrf
												<input type="hidden" name="action" value="counter" />
												<input type="number" name="influencer_offer" min="0.01" step="0.01" required
													class="flex-1 rounded border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
													value="{{ (float) ($influencerApplication->influencer_offer ?? ($influencerApplication->proposed_rate ?? 0)) ?: '' }}"
													placeholder="Your Counter Offer" />
												<button type="submit"
													class="px-3 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Counter</button>
											</form>
											<form method="POST" action="{{ route('frontend.campaigns.respond-offer', $influencerApplication) }}">
												@csrf
												<input type="hidden" name="action" value="decline" />
												<button type="submit"
													class="w-full px-4 py-2 text-sm font-semibold rounded-lg border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/30 transition">Decline
													Offer</button>
											</form>
										</div>
									</div>
								@elseif ($influencerApplication->status === 'countered_by_influencer')
									<div
										class="rounded-lg bg-purple-50 dark:bg-purple-900/20 p-4 border border-purple-200 dark:border-purple-900/50">
										<p class="text-sm font-semibold text-purple-900 dark:text-purple-100 mb-3">Counter Offer Sent</p>
										<p class="text-sm text-purple-800 dark:text-purple-200">
											You're waiting for the brand to respond to your counter offer of
											<span class="font-semibold">{{ strtoupper((string) ($campaign->currency ?? 'USD')) }}
												{{ number_format((float) ($influencerApplication->influencer_offer ?? ($influencerApplication->proposed_rate ?? 0)), 2) }}</span>.
										</p>
									</div>
								@elseif ($influencerApplication->status === 'completed')
									<!-- Show completion message -->
									<div
										class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200 dark:border-emerald-900/50">
										<p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">✓ Work Completed</p>
										<p class="text-sm text-emerald-800 dark:text-emerald-200">
											Great work! You've successfully completed this campaign. Thank you for your collaboration!
										</p>
									</div>
								@elseif (in_array($influencerApplication->status, ['declined_by_brand', 'declined_by_influencer'], true))
									<div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-900/50">
										<p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-3">Negotiation Closed</p>
										<p class="text-sm text-red-800 dark:text-red-200">
											This pricing negotiation has been declined and is no longer active.
										</p>
									</div>
								@endif

								<!-- Work Status Update Section (only when the order exists) -->
								@if ($influencerApplication->status === 'approved')
									@if ($progressByApplication[$influencerApplication->id] ?? null)
										<div
											class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200 dark:border-emerald-900/50">
											<p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">✓ You're approved and the order
												is live</p>
											<p class="text-sm text-emerald-800 dark:text-emerald-200">
												Your delivery stages are now tracked from the actual campaign order above.
											</p>
										</div>

										<div
											class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-900/50 mt-4">
											<p class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase tracking-wide mb-3">Update Work
												Status</p>
											<form method="POST" action="{{ route('frontend.campaigns.update-work-status', $influencerApplication) }}"
												class="space-y-3">
												@csrf
												<div>
													<label
														class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide block mb-2">Current
														Status</label>
													<select name="work_status"
														class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
														<option value="pending" {{ $influencerApplication->work_status === 'pending' ? 'selected' : '' }}>Order
															Pending</option>
														<option value="accepted" {{ $influencerApplication->work_status === 'accepted' ? 'selected' : '' }}>
															Accepted</option>
														<option value="in_progress"
															{{ $influencerApplication->work_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
														<option value="on_review" {{ $influencerApplication->work_status === 'on_review' ? 'selected' : '' }}>On
															Review</option>
														<option value="completed" {{ $influencerApplication->work_status === 'completed' ? 'selected' : '' }}>
															Completed</option>
													</select>
												</div>
												<button type="submit"
													class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition">Update
													Status</button>
											</form>
										</div>
									@else
										<div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 p-4 border border-amber-200 dark:border-amber-900/50">
											<p class="text-sm font-semibold text-amber-900 dark:text-amber-100 mb-3">✓ You're approved</p>
											<p class="text-sm text-amber-800 dark:text-amber-200">The order has not been created yet, so there is no
												delivery status to update.</p>
										</div>
									@endif
								@endif

								<!-- Action Buttons -->
								<div class="mt-4">
									@if ($influencerApplicationUi['can_withdraw'] ?? false)
										<form method="POST"
											action="{{ route('frontend.campaigns.withdraw-application', $influencerApplication) }}">
											@csrf
											<button type="submit" onclick="return confirm('Are you sure you want to withdraw your application?')"
												class="w-full px-4 py-2 text-sm font-semibold rounded-lg border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300 dark:hover:bg-red-900/30 transition">
												Withdraw Application
											</button>
										</form>
									@endif
								</div>
							</div>
						@else
							<!-- Influencer hasn't applied yet -->
							<div class="space-y-4">
								<div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-900/50">
									<p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-2">Interested in this campaign?</p>
									<p class="text-sm text-blue-800 dark:text-blue-200 mb-4">
										Apply to show your interest and let the brand know why you'd be great for this project!
									</p>
									<form method="POST" action="{{ route('frontend.campaigns.apply', $campaign) }}" class="space-y-2">
										@csrf
										<input type="number" name="influencer_offer" min="0.01" step="0.01" required
											placeholder="Your Offer Price"
											class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white" />
										<textarea name="pitch_message" rows="3" placeholder="Add a short pitch (optional)"
										 class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white"></textarea>
										<button type="submit"
											class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition whitespace-nowrap">
											Apply Now
										</button>
									</form>
								</div>
							</div>
						@endif
					</div>
				</div>
			@endif

			<!-- Negotiation Modal -->
			<div x-data="negotiationModal()" x-show="open"
				class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;"
				@keydown.escape="closeModal()">

				<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full" @click.stop>
					<!-- Header -->
					<div class="border-b border-gray-200 dark:border-gray-700 p-6">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
							Negotiate with <span x-text="influencerName"></span>
						</h3>
						<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="statusLabel"></p>
					</div>

					<!-- Body -->
					<div class="p-6 space-y-4">
						<!-- Influencer's Current Offer -->
						<div x-show="influencerOffer">
							<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide block mb-1">
								Influencer's Offer
							</label>
							<div class="px-3 py-2 rounded-lg border border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20">
								<p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
									$<span x-text="parseFloat(influencerOffer).toFixed(2)"></span>
								</p>
							</div>
						</div>

						<!-- Brand's Current Offer -->
						<div x-show="brandOffer">
							<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide block mb-1">
								Your Counter Offer
							</label>
							<div
								class="px-3 py-2 rounded-lg border border-purple-300 dark:border-purple-700 bg-purple-50 dark:bg-purple-900/20">
								<p class="text-sm font-semibold text-purple-900 dark:text-purple-100">
									$<span x-text="parseFloat(brandOffer).toFixed(2)"></span>
								</p>
							</div>
						</div>

						<!-- Message when waiting for counter -->
						<div x-show="waitingForResponse"
							class="p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
							<p class="text-xs text-amber-800 dark:text-amber-200" x-text="waitingMessage"></p>
						</div>

						<!-- New Counter Input (only show if you can counter) -->
						<div x-show="canCounter">
							<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide block mb-1">
								Send New Counter Offer
							</label>
							<input type="number" x-model.number="newCounterPrice" min="0.01" step="0.01"
								placeholder="Enter your offer"
								class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" />
						</div>
					</div>

					<!-- Footer -->
					<div class="border-t border-gray-200 dark:border-gray-700 p-6 flex flex-wrap gap-2">
						<!-- Accept Button (only when valid offer exists) -->
						<button @click="acceptApplication()" type="button" x-show="canAccept"
							class="flex-1 min-w-24 px-4 py-2 text-sm font-semibold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 dark:hover:bg-emerald-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
							:disabled="isSubmitting" :title="acceptDisabledReason">
							<span x-show="!isSubmitting">Accept</span>
							<span x-show="isSubmitting" class="inline-flex items-center justify-center gap-1">
								<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
									</circle>
									<path class="opacity-75" fill="currentColor"
										d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
									</path>
								</svg>
								<span>Accept</span>
							</span>
						</button>

						<!-- Counter Button (only when you can counter) -->
						<button @click="counterApplication()" type="button" x-show="canCounter"
							class="flex-1 min-w-24 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 dark:hover:bg-indigo-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
							:disabled="!newCounterPrice || newCounterPrice <= 0 || isSubmitting">
							<span x-show="!isSubmitting">Counter</span>
							<span x-show="isSubmitting" class="inline-flex items-center justify-center gap-1">
								<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
									</circle>
									<path class="opacity-75" fill="currentColor"
										d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
									</path>
								</svg>
								<span>Counter</span>
							</span>
						</button>

						<!-- Reject Button -->
						<button @click="rejectApplication()" type="button"
							class="flex-1 min-w-24 px-4 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 dark:hover:bg-red-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
							:disabled="isSubmitting">
							<span x-show="!isSubmitting">Reject</span>
							<span x-show="isSubmitting" class="inline-flex items-center justify-center gap-1">
								<svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
									</circle>
									<path class="opacity-75" fill="currentColor"
										d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
									</path>
								</svg>
								<span>Reject</span>
							</span>
						</button>

						<!-- Close Button -->
						<button @click="closeModal()" type="button"
							class="min-w-24 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
							:disabled="isSubmitting">
							Close
						</button>
					</div>
				</div>
			</div>

		@endsection
