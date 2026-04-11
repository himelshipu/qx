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
				<div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

					<!-- Left: Title + Status Selector -->
					<div class="flex-1 min-w-0">
						<h1 class="text-xl sm:text-2xl font-semibold text-gray-900 dark:text-white mb-3">
							{{ $campaign->title }}
						</h1>

						<!-- Status Selector for Brand Owners -->
						@if (auth()->user()->user_type === 'brand' && $campaign->brand_id === auth()->user()->brand?->id)
							<div class="inline-block" x-data="campaignStatusForm()">
								<div class="flex items-center gap-3 flex-wrap">
									<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Status:</label>
									<div class="flex items-center gap-2">
										<select @change="updateStatus"
											:disabled="isLoading"
											class="px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
											x-model="selectedStatus">
											<option value="draft">Draft</option>
											<option value="published">Published</option>
											<option value="paused">Paused</option>
											<option value="closed">Closed</option>
											<option value="archived">Archived</option>
										</select>
										<span class="inline-flex items-center opacity-0 transition-opacity" :class="{ 'opacity-100': isLoading }" x-show="isLoading">
											<svg class="w-4 h-4 text-gray-600 dark:text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
												<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
												<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
											</svg>
										</span>
										<span class="text-xs font-medium px-2 py-1 rounded transition-all opacity-0" :class="feedbackClass" x-show="showFeedback">
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
									<span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
										</svg>
										Active (Published)
									</span>
								@elseif ($campaign->status === 'paused')
									<span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16M9.383 5a1 1 0 011.234 1.471L7.669 10l2.948 3.529A1 1 0 119.617 15l-4-4.771a1 1 0 010-1.458l4-4.771z" clip-rule="evenodd"/>
										</svg>
										Paused
									</span>
								@elseif ($campaign->status === 'closed')
									<span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.526a6 6 0 008.367 8.368l5.657 5.657a1 1 0 01-1.414 1.414l-5.657-5.657z" clip-rule="evenodd"/>
										</svg>
										Closed
									</span>
								@elseif ($campaign->status === 'archived')
									<span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
											<path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" clip-rule="evenodd"/>
										</svg>
										Archived
									</span>
								@else
									<span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
										<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd" d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.077 13.308-5.077 18.384 0a1 1 0 01-1.414 1.414zM14.95 11.05a7 7 0 00-9.9 0 1 1 0 01-1.414-1.414 9 9 0 0112.728 0 1 1 0 01-1.414 1.414zM12.12 13.88a3 3 0 00-4.242 0 1 1 0 01-1.415-1.415 5 5 0 017.072 0 1 1 0 01-1.415 1.415zM9 16a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
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

							<form action="{{ route('frontend.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this campaign? This action cannot be undone.');">
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

			<!-- Influencer Work Progress -->
			@if (($workProgress ?? collect())->count() > 0)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden mb-6">
					<div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-3">
						<div>
							<h2 class="text-lg font-bold text-gray-900 dark:text-white">Influencer Work Progress</h2>
							<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tracks delivery stage for approved influencers in this campaign.</p>
						</div>
						<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
							{{ $workProgress->count() }} Active
						</span>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
						@foreach ($workProgress as $progressItem)
							@php
								$progressBarClass = match ($progressItem['status_key']) {
									'completed' => 'bg-emerald-500',
									'on_review' => 'bg-indigo-500',
									'in_progress' => 'bg-blue-500',
									'accepted' => 'bg-cyan-500',
									'pending' => 'bg-amber-500',
									'cancelled' => 'bg-red-500',
									default => 'bg-gray-400',
								};

								$badgeClass = match ($progressItem['status_key']) {
									'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
									'on_review' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
									'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
									'accepted' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300',
									'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
									'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
									default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
								};
							@endphp

							<div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-gray-50/70 dark:bg-gray-900/30">
								<div class="flex items-start justify-between gap-3">
									<div class="min-w-0">
										<p class="font-semibold text-gray-900 dark:text-white truncate">{{ $progressItem['influencer_name'] }}</p>
										@if ($progressItem['influencer_handle'])
											<p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $progressItem['influencer_handle'] }}</p>
										@endif
										@if ($progressItem['agreed_amount'])
											<p class="mt-1 text-xs font-semibold text-gray-700 dark:text-gray-300">Budget: {{ $progressItem['currency'] }} {{ number_format((float) $progressItem['agreed_amount'], 2) }}</p>
										@endif
									</div>
									<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $badgeClass }}">
										{{ $progressItem['status_label'] }}
									</span>
								</div>

								<div class="mt-4">
									<div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-1.5">
										<span>Progress</span>
										<span class="font-semibold">{{ $progressItem['progress_percent'] }}%</span>
									</div>
									<div class="w-full h-2 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
										<div class="h-2 rounded-full {{ $progressBarClass }}" style="width: {{ $progressItem['progress_percent'] }}%"></div>
									</div>
								</div>

								<p class="text-xs text-gray-500 dark:text-gray-400 mt-3">
									Last update:
									{{ $progressItem['updated_at']?->format('M d, Y h:i A') ?? $progressItem['decided_at']?->format('M d, Y h:i A') ?? 'Pending order kickoff' }}
								</p>
								<div class="mt-3 flex items-center gap-2">
									<a href="{{ route('frontend.conversations.open-order', ['influencer' => $progressItem['influencer_id']]) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
										<x-icons.message-square class="h-3.5 w-3.5" />
										Message
									</a>
								</div>
							</div>
						@endforeach
					</div>
				</div>
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
							<option value="approved">Approved</option>
							<option value="completed">Completed</option>
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
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Work Status</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300 hidden md:table-cell">Budget</th>
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
										@elseif ($application->status === 'completed')
											<span
												class="inline-flex rounded-full bg-teal-100 px-2.5 py-1 text-xs font-semibold text-teal-700 dark:bg-teal-900/30 dark:text-teal-300">Completed</span>
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
									@php
										$progressEntry = ($progressByApplication ?? collect())->get($application->id);
										$workStatusKey = $progressEntry['status_key'] ?? $application->work_status;
										if (! $workStatusKey && $application->status === 'completed') {
											$workStatusKey = 'completed';
										}
										$workStatusLabelMap = [
											'pending' => 'Order Pending',
											'accepted' => 'Accepted',
											'in_progress' => 'In Progress',
											'on_review' => 'On Review',
											'completed' => 'Completed',
										];
										$workStatus = $workStatusKey ? ($workStatusLabelMap[$workStatusKey] ?? ucfirst(str_replace('_', ' ', $workStatusKey))) : null;
										$assignment = ($assignmentByInfluencer ?? collect())->get($application->influencer_id);
										$agreedAmount = $assignment?->agreed_amount ?? $application->agreed_rate ?? $application->proposed_rate;
										$budgetCurrency = strtoupper((string) ($campaign->currency ?? 'USD'));
									@endphp
									<td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 hidden md:table-cell">
										@if(($application->status === 'approved' || $application->status === 'completed') && $workStatus)
											@php
												$workStatusChipStyle = match ($workStatus) {
													'Completed' => 'background-color: rgba(209, 250, 229, 1); color: rgb(4, 120, 87);',
													'On Review' => 'background-color: rgba(224, 231, 255, 1); color: rgb(67, 56, 202);',
													'In Progress' => 'background-color: rgba(219, 234, 254, 1); color: rgb(29, 78, 216);',
													'Accepted' => 'background-color: rgba(207, 250, 254, 1); color: rgb(14, 116, 144);',
													default => 'background-color: rgba(254, 243, 199, 1); color: rgb(180, 83, 9);',
												};
											@endphp
											<span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold" style="{{ $workStatusChipStyle }}">
												{{ $workStatus }}
											</span>
										@else
											—
										@endif
									</td>
									<td class="px-4 py-3 hidden md:table-cell text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
										@if ($agreedAmount)
											<span class="font-semibold">{{ $budgetCurrency }} {{ number_format((float) $agreedAmount, 2) }}</span>
										@else
											—
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
											@if ($campaign->status === 'closed')
												<!-- Campaign is closed - disable all actions -->
												<button disabled
													class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed opacity-50"
													title="Campaign is closed - cannot approve or decline"
													onclick="window.toast?.info('This campaign is closed. No further actions can be taken.')">
													<x-icons.check class="w-3 h-3" />
												</button>
												<button disabled
													class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed opacity-50"
													title="Campaign is closed - cannot approve or decline"
													onclick="window.toast?.info('This campaign is closed. No further actions can be taken.')">
													<x-icons.x class="w-3 h-3" />
												</button>
											@elseif ($application->status === 'approved')
												<form action="{{ route('frontend.campaigns.brand-update-work-status', [$campaign->id, $application->id]) }}" method="POST" class="inline-flex items-center gap-1">
													@csrf
													<select name="work_status" class="rounded border border-gray-300 bg-white px-1.5 py-1 text-[11px] dark:border-gray-600 dark:bg-gray-700 dark:text-white">
														<option value="pending" @selected($application->work_status === 'pending')>Pending</option>
														<option value="accepted" @selected($application->work_status === 'accepted')>Accepted</option>
														<option value="in_progress" @selected($application->work_status === 'in_progress')>In Progress</option>
														<option value="on_review" @selected($application->work_status === 'on_review')>On Review</option>
														<option value="completed" @selected($application->work_status === 'completed')>Completed</option>
													</select>
													<button type="submit" class="px-2 py-1 text-[11px] rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50">Save</button>
												</form>
												<!-- Already approved -->
												<button disabled
													class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 cursor-not-allowed opacity-60"
													title="This influencer is already approved">
													<x-icons.check class="w-3 h-3" />
												</button>
												<button disabled
													class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 cursor-not-allowed opacity-50"
													title="Cannot decline an approved influencer"
													onclick="window.toast?.error('Cannot decline an approved influencer. They have already been approved for this campaign.')">
													<x-icons.x class="w-3 h-3" />
												</button>
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
											@elseif ($application->status === 'completed')
												<form action="{{ route('frontend.campaigns.brand-update-work-status', [$campaign->id, $application->id]) }}" method="POST" class="inline-flex items-center gap-1">
													@csrf
													<select name="work_status" class="rounded border border-gray-300 bg-white px-1.5 py-1 text-[11px] dark:border-gray-600 dark:bg-gray-700 dark:text-white">
														<option value="on_review" @selected($application->work_status === 'on_review')>On Review</option>
														<option value="completed" @selected($application->work_status === 'completed')>Completed</option>
													</select>
													<button type="submit" class="px-2 py-1 text-[11px] rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50">Save</button>
												</form>
												<!-- Work completed - disable all actions -->
												<button disabled
													class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 cursor-not-allowed opacity-50"
													title="Work is completed - cannot modify"
													onclick="window.toast?.info('This application is completed. The influencer has finished their work on this campaign.')">
													<x-icons.check class="w-3 h-3" />
												</button>
												<button disabled
													class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 cursor-not-allowed opacity-50"
													title="Work is completed - cannot modify"
													onclick="window.toast?.info('This application is completed. The influencer has finished their work on this campaign.')">
													<x-icons.x class="w-3 h-3" />
												</button>
											@else
												<!-- Pending / Applied / Invited - Actions enabled -->
												<form
													action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}"
													method="POST" class="inline js-approve-form">
													@csrf
													<input type="hidden" name="status" value="approved">
													<button type="submit"
														class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50 transition cursor-pointer"
														title="Approve this influencer">
														<x-icons.check class="w-3 h-3" />
													</button>
												</form>
												<form
													action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}"
													method="POST" class="inline js-reject-form">
													@csrf
													<input type="hidden" name="status" value="rejected">
													<button type="submit"
														class="px-2 py-1 text-xs rounded bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition cursor-pointer"
														title="Decline this influencer">
														<x-icons.x class="w-3 h-3" />
													</button>
												</form>
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
			<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 shadow-sm overflow-hidden">
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
										@php
											$statusColors = [
												'invited' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
												'applied' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
												'approved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
												'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
												'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
											];
											$badgeClass = $statusColors[$influencerApplication->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
											$statusLabel = match($influencerApplication->status) {
												'invited' => 'Invited',
												'applied' => 'Applied',
												'approved' => 'Approved',
												'rejected' => 'Not Selected',
												'completed' => 'Work Completed',
												 default => 'Unknown',
											};
										@endphp
										<span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold mt-1 {{ $badgeClass }}">
											{{ $statusLabel }}
										</span>
									</div>
									<div class="text-right">
										<p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Applied On</p>
										<p class="text-sm font-medium text-gray-900 dark:text-white mt-1">
											{{ $influencerApplication->applied_at?->format('M d, Y') ?? 'Pending' }}
										</p>
									</div>
								</div>
							</div>

							@if ($influencerApplication->status === 'approved')
								<!-- Show work status if approved -->
								<div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200 dark:border-emerald-900/50">
									<p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">✓ You're approved for this campaign!</p>
									<p class="text-sm text-emerald-800 dark:text-emerald-200">
										You can now see your work progress and deliverables above. Let the brand know if you have any questions about the requirements.
									</p>
								</div>
							@elseif ($influencerApplication->status === 'rejected')
								<!-- Show rejection message -->
								<div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-900/50">
									<p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-3">Not Selected</p>
									<p class="text-sm text-red-800 dark:text-red-200">
										Unfortunately, you were not selected for this campaign. Other influencers have been chosen, but check for other campaigns that might be a good fit!
									</p>
								</div>
							@elseif ($influencerApplication->status === 'applied')
								<!-- Show pending decision message -->
								<div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-900/50">
									<p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3">Waiting for Review</p>
									<p class="text-sm text-blue-800 dark:text-blue-200">
										The brand is reviewing your application. You'll be notified once they make a decision.
									</p>
								</div>
							@elseif ($influencerApplication->status === 'completed')
								<!-- Show completion message -->
								<div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-4 border border-emerald-200 dark:border-emerald-900/50">
									<p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100 mb-3">✓ Work Completed</p>
									<p class="text-sm text-emerald-800 dark:text-emerald-200">
										Great work! You've successfully completed this campaign. Thank you for your collaboration!
									</p>
								</div>
							@endif

							<!-- Work Status Update Section (for approved applications) -->
							@if ($influencerApplication->status === 'approved')
								<div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-900/50 mt-4">
									<p class="text-xs font-semibold text-blue-600 dark:text-blue-300 uppercase tracking-wide mb-3">Update Work Status</p>
									<form method="POST" action="{{ route('frontend.campaigns.update-work-status', $influencerApplication) }}" class="space-y-3">
										@csrf
										<div>
											<label class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide block mb-2">Current Status</label>
											<select name="work_status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
												<option value="pending" {{ $influencerApplication->work_status === 'pending' ? 'selected' : '' }}>Order Pending</option>
												<option value="accepted" {{ $influencerApplication->work_status === 'accepted' ? 'selected' : '' }}>Accepted</option>
												<option value="in_progress" {{ $influencerApplication->work_status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
												<option value="on_review" {{ $influencerApplication->work_status === 'on_review' ? 'selected' : '' }}>On Review</option>
												<option value="completed" {{ $influencerApplication->work_status === 'completed' ? 'selected' : '' }}>Completed</option>
											</select>
										</div>
										<button type="submit" class="w-full px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition">
											Update Status
										</button>
									</form>
								</div>
							@endif

							<!-- Action Buttons -->
							<div class="mt-4">
								@if ($influencerApplication->status !== 'rejected' && $influencerApplication->status !== 'completed')
									<form method="POST" action="{{ route('frontend.campaigns.withdraw-application', $influencerApplication) }}">
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
								<form method="POST" action="{{ route('frontend.campaigns.apply', $campaign) }}" class="flex flex-col sm:flex-row gap-2">
									@csrf
									<input type="email" name="email" placeholder="Your email" value="{{ auth()->user()->email }}" disabled class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300" />
									<button type="submit" class="px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-600 transition whitespace-nowrap">
										Apply Now
									</button>
								</form>
							</div>
						</div>
					@endif
				</div>
			</div>
		@endif

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
				let submitted = 0;
				checkedCheckboxes.forEach(checkbox => {
					const row = checkbox.closest('tr');
					const approveForm = row?.querySelector('.js-approve-form');
					if (approveForm) {
						submitted++;
						approveForm.submit();
					}
				});

				if (submitted === 0) {
					window.toast?.warning('Declined influencers cannot be approved again.');
				}
			});

			batchRejectBtn?.addEventListener('click', function() {
				const checkedCheckboxes = Array.from(document.querySelectorAll('.js-row-checkbox:checked'));
				if (checkedCheckboxes.length === 0) return;
				let submitted = 0;
				checkedCheckboxes.forEach(checkbox => {
					const row = checkbox.closest('tr');
					const rejectForm = row?.querySelector('.js-reject-form');
					if (rejectForm) {
						submitted++;
						rejectForm.submit();
					}
				});

				if (submitted === 0) {
					window.toast?.warning('Approved influencers cannot be declined.');
				}
			});

			batchCancelBtn?.addEventListener('click', function() {
				rowCheckboxes.forEach(cb => cb.checked = false);
				if (selectAllCheckbox) selectAllCheckbox.checked = false;
				updateBatchUI();
			});
		});

		// Campaign Status Update Handler
		function campaignStatusForm() {
			return {
				selectedStatus: '{{ $campaign->status }}',
				isLoading: false,
				showFeedback: false,
				feedbackText: '',
				feedbackClass: '',
				
				async updateStatus() {
					const newStatus = this.selectedStatus;
					if (newStatus === '{{ $campaign->status }}') {
						return;
					}

					this.isLoading = true;
					this.showFeedback = false;

					try {
						const response = await fetch('{{ route("frontend.campaigns.update-status", $campaign) }}', {
							method: 'PATCH',
							headers: {
								'Content-Type': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
								'Accept': 'application/json'
							},
							body: JSON.stringify({
								status: newStatus
							})
						});

						const data = await response.json();

						if (data.success) {
							this.feedbackClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
							this.feedbackText = data.message;
							this.showFeedback = true;

							// Show success feedback for 3 seconds
							setTimeout(() => {
								this.showFeedback = false;
							}, 3000);

							// Scroll to top to show the success
							window.scrollTo({ top: 0, behavior: 'smooth' });
						} else {
							this.feedbackClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
							this.feedbackText = data.message || 'Failed to update status';
							this.showFeedback = true;

							// Revert on error
							this.selectedStatus = '{{ $campaign->status }}';
						}
					} catch (error) {
						console.error('Error updating campaign status:', error);
						this.feedbackClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
						this.feedbackText = 'An error occurred. Please try again.';
						this.showFeedback = true;

						// Revert on error
						this.selectedStatus = '{{ $campaign->status }}';
					} finally {
						this.isLoading = false;
					}
				}
			}
		}
	</script>
@endsection
