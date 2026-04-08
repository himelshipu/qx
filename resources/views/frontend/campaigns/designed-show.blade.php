@extends('frontend.layouts.app')

@section('content')
	<div class="px-2 py-8">
		<div class="max-w-6xl mx-auto">
			<!-- Back Link & Actions Header -->
			<div class="flex items-center justify-between mb-8 gap-4">
				<a href="{{ route('frontend.campaigns.index') }}"
					class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition">
					<x-icons.chevron-right class="w-4 h-4 -rotate-180" />
					Back to Campaigns
				</a>
				@if (auth()->user()->user_type === 'brand' && $campaign->created_by === auth()->user()->id)
					<div class="flex items-center gap-2">
						<a href="{{ route('frontend.campaigns.edit', $campaign) }}"
							class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
							<x-icons.edit class="w-4 h-4" />
							Edit
						</a>
						<form action="{{ route('frontend.campaigns.destroy', $campaign) }}" method="POST" class="inline">
							@csrf
							@method('DELETE')
							<button type="submit" data-confirm-title="Delete Campaign"
								data-confirm-message="Delete this campaign? This action cannot be undone." data-confirm-button="Delete"
								data-confirm-variant="danger" title="Delete campaign"
								class="js-confirmable inline-flex items-center gap-2 rounded-lg border border-red-300 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30">
								<x-icons.trash class="w-4 h-4" />
								Delete
							</button>
						</form>
					</div>
				@endif
			</div>

			<!-- Campaign Header -->
			<div class="mb-8 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
					<div class="flex-1">
						<h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $campaign->title }}</h1>
						<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
							{{ \Illuminate\Support\Str::headline($campaign->campaign_type ?? 'General Campaign') }}
						</p>
						<div class="mt-4 flex flex-wrap items-center gap-2">
							<span
								class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold uppercase
								{{ $campaign->status === 'published'
								    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300'
								    : ($campaign->status === 'paused'
								        ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
								        : ($campaign->status === 'closed'
								            ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
								            : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300')) }}">
								{{ \Illuminate\Support\Str::headline($campaign->status ?? 'published') }}
							</span>
							@if ($campaign->is_active)
								<span class="inline-flex rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
									Active
								</span>
							@else
								<span class="inline-flex rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-300">
									Inactive
								</span>
							@endif
						</div>
					</div>
				</div>
			</div>

			<!-- Campaign Details Grid -->
			<div class="grid grid-cols-2 gap-3 mb-8 sm:grid-cols-4">
				<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Applications</p>
					<p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign->applications_count }}</p>
				</div>
				<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</p>
					<p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign->categories->count() }}</p>
				</div>
				<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencer Target</p>
					<p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign->targeting?->influencer_count ?? '—' }}</p>
				</div>
				<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Follower Ranges</p>
					<p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign->followerRanges->count() }}</p>
				</div>
			</div>

			<!-- Main Content -->
			<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
				<!-- Left Column -->
				<div class="lg:col-span-2 space-y-6">
					<!-- Campaign Overview -->
					<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Campaign Overview</h2>
						<div class="grid grid-cols-2 gap-6 sm:grid-cols-3">
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Budget Range</p>
								<p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
									${{ number_format($campaign->budget_min, 0) }} - ${{ number_format($campaign->budget_max, 0) }}
								</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Start Date</p>
								<p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
									{{ $campaign->start_date?->format('M d, Y') ?? '—' }}
								</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">End Date</p>
								<p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
									{{ $campaign->end_date?->format('M d, Y') ?? '—' }}
								</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Currency</p>
								<p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ $campaign->currency ?? 'USD' }}</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Created By</p>
								<p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">{{ $campaign->createdBy?->name ?? '—' }}</p>
							</div>
						</div>
					</div>

					<!-- Description -->
					<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Description</h2>
						<p class="text-sm leading-6 text-gray-700 dark:text-gray-300">
							{{ $campaign->description ?: '—' }}
						</p>
					</div>

					<!-- Instructions -->
					<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Content Instructions</h2>
						<p class="whitespace-pre-wrap text-sm leading-6 text-gray-700 dark:text-gray-300">
							{{ $campaign->instructions ?: '—' }}
						</p>
					</div>

					<!-- Categories, Ranges, Countries -->
					<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
						<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
							<h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</h3>
							<div class="mt-3 flex flex-wrap gap-2">
								@forelse ($campaign->categories as $category)
									<span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
										{{ $category->name }}
									</span>
								@empty
									<span class="text-sm text-gray-500 dark:text-gray-400">None assigned</span>
								@endforelse
							</div>
						</div>

						<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
							<h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Follower Ranges</h3>
							<div class="mt-3 flex flex-wrap gap-2">
								@forelse ($campaign->followerRanges as $range)
									<span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
										{{ $range->label }}
									</span>
								@empty
									<span class="text-sm text-gray-500 dark:text-gray-400">None selected</span>
								@endforelse
							</div>
						</div>
					</div>

					<!-- Targeting Info -->
					<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Targeting Criteria</h3>
						<div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Target Gender</p>
								<p class="mt-2 font-semibold text-gray-900 dark:text-white">
									{{ \Illuminate\Support\Str::headline($campaign->targeting?->target_gender ?? 'Any') }}
								</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Age Range</p>
								<p class="mt-2 font-semibold text-gray-900 dark:text-white">
									@if ($campaign->targeting?->age_min || $campaign->targeting?->age_max)
										{{ $campaign->targeting?->age_min ?? '0' }} - {{ $campaign->targeting?->age_max ?? '99' }}
									@else
										—
									@endif
								</p>
							</div>
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Countries</p>
								<p class="mt-2 font-semibold text-gray-900 dark:text-white">
									{{ $campaign->targetCountries->count() > 0 ? $campaign->targetCountries->count() . ' Selected' : '—' }}
								</p>
							</div>
						</div>
						@if ($campaign->targetCountries->count() > 0)
							<div class="mt-4 flex flex-wrap gap-2">
								@foreach ($campaign->targetCountries as $country)
									<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">
										{{ $country->country_code }}
									</span>
								@endforeach
							</div>
						@endif
					</div>

					<!-- Invited Influencers Section (Brand Only) -->
					@if (auth()->user()?->user_type === 'brand' && $campaign->created_by === auth()->user()->id)
						@if ($invitedInfluencers->count() > 0)
							<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
								<h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Invited Influencers</h2>
								<div class="space-y-3">
									@foreach ($invitedInfluencers as $application)
										<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
											<div class="flex items-center gap-3 flex-1">
												<!-- Avatar -->
												<div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center flex-shrink-0">
													<span class="text-sm font-bold text-white">{{ substr($application->influencer->user->name ?? 'N', 0, 1) }}</span>
												</div>
												<!-- Influencer Details -->
												<div class="flex-1 min-w-0">
													<h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $application->influencer->user->name ?? 'Unknown' }}</h3>
													<p class="text-xs text-gray-600 dark:text-gray-400">{{ $application->influencer->display_name ?? 'Influencer' }}</p>
													@if ($application->proposed_rate)
														<p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Rate: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $campaign->currency }} {{ number_format((float) $application->proposed_rate, 2) }}</span></p>
													@endif
												</div>
											</div>

											<!-- Action Buttons -->
											<div class="flex items-center gap-2 flex-shrink-0">
												<form action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}" method="POST" class="inline">
													@csrf
													<button type="submit" name="status" value="approved"
														class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-100 px-3 py-2 text-xs font-medium text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:hover:bg-emerald-900/50 transition">
														<x-icons.check class="w-4 h-4" />
														<span class="hidden sm:inline">Approve</span>
													</button>
												</form>
												<form action="{{ route('frontend.campaigns.update-application-status', [$campaign->id, $application->id]) }}" method="POST" class="inline">
													@csrf
													<button type="submit" name="status" value="rejected"
														class="inline-flex items-center gap-1.5 rounded-lg bg-red-100 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition">
														<x-icons.x class="w-4 h-4" />
														<span class="hidden sm:inline">Decline</span>
													</button>
												</form>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						@endif
					@endif

				<!-- Application Status for Influencer -->
				@if (auth()->user()?->user_type === 'influencer')
					@if ($influencerApplication)
							<div class="rounded-lg border border-blue-200 bg-blue-50 p-6 dark:border-blue-900/30 dark:bg-blue-900/20">
								<h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-4">Your Application Status</h3>
								<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
									<div>
										<p class="text-sm text-blue-700 dark:text-blue-400 mb-1">Status:</p>
										<span
											class="inline-flex rounded-full px-3 py-1.5 text-sm font-semibold
											{{ $application->status === 'accepted'
											    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300'
											    : ($application->status === 'rejected'
											        ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
											        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300') }}">
							{{ \Illuminate\Support\Str::headline($influencerApplication->status) }}
										</span>
									</div>
									<div>
										<p class="text-sm text-blue-700 dark:text-blue-400 mb-1">Applied on:</p>
										<p class="font-semibold text-blue-900 dark:text-blue-300">{{ $influencerApplication->applied_at?->format('M d, Y') }}</p>
									</div>
								</div>
								@if ($influencerApplication->pitch_message)
									<div class="mt-4 border-t border-blue-200 pt-4 dark:border-blue-900/30">
										<p class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-2">Your Pitch:</p>
										<p class="text-sm text-blue-800 dark:text-blue-200">{{ $influencerApplication->pitch_message }}</p>
									</div>
								@endif
							</div>
						@endif
					@endif
				</div>

				<!-- Right Column - Sidebar -->
				<div class="lg:col-span-1 space-y-6">
					<!-- Brand Info -->
					<div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Campaign Owner</h3>
						<div class="space-y-3">
							<div>
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand Name</p>
								<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $brandName }}</p>
							</div>
							@if ($campaign->brand?->user?->email)
								<div>
									<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</p>
									<p class="mt-1 text-sm text-gray-700 dark:text-gray-300">{{ $campaign->brand->user->email }}</p>
								</div>
							@endif
						</div>
					</div>

					<!-- Quick Stats -->
					<div class="space-y-3">
						@if ($campaign->applications->count() > 0)
							<div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/30 dark:bg-blue-900/20">
								<p class="text-xs font-semibold text-blue-600 dark:text-blue-400">Invited</p>
								<p class="mt-1 text-lg font-bold text-blue-700 dark:text-blue-300">{{ $applicationStats['invited'] }}</p>
							</div>
							<div class="rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/30 dark:bg-amber-900/20">
								<p class="text-xs font-semibold text-amber-600 dark:text-amber-400">Applied</p>
								<p class="mt-1 text-lg font-bold text-amber-700 dark:text-amber-300">{{ $applicationStats['applied'] }}</p>
							</div>
							<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/30 dark:bg-emerald-900/20">
								<p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400">Accepted</p>
								<p class="mt-1 text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ $applicationStats['accepted'] }}</p>
							</div>
							<div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900/30 dark:bg-red-900/20">
								<p class="text-xs font-semibold text-red-600 dark:text-red-400">Rejected</p>
								<p class="mt-1 text-lg font-bold text-red-700 dark:text-red-300">{{ $applicationStats['rejected'] }}</p>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection