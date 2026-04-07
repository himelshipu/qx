{{-- Frontend Campaigns Designed Show --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-5xl mx-auto">
			<!-- Back Link -->
			<div class="mb-8">
				<a href="{{ route('frontend.campaigns.index') }}"
					class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
					</svg>
					Back to Campaigns
				</a>
			</div>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
				<!-- Main Content -->
				<div class="lg:col-span-2 space-y-6">
					<!-- Header Section -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm">
						<div class="mb-6">
							<div class="flex items-start justify-between gap-4 mb-4">
								<div>
									<h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $campaign->title }}</h1>
									<p class="text-gray-600 dark:text-gray-400">
										{{ \Illuminate\Support\Str::headline($campaign->campaign_type ?? 'General Campaign') }}
									</p>
								</div>
								<span
									class="inline-block px-4 py-2 rounded-full text-xs font-semibold uppercase whitespace-nowrap
									{{ $campaign->status === 'published'
									    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300'
									    : ($campaign->status === 'paused'
									        ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300'
									        : ($campaign->status === 'closed'
									            ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
									            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300')) }}">
									{{ \Illuminate\Support\Str::headline($campaign->status ?? 'published') }}
								</span>
							</div>
						</div>

						<!-- Description -->
						<div class="border-t border-gray-200 dark:border-gray-700 pt-6">
							<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Campaign Overview</h2>
							<p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $campaign->description }}</p>
						</div>
					</div>

					<!-- Details Section -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm">
						<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Campaign Details</h2>

						<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
							<!-- Budget -->
							<div
								class="p-4 rounded-xl bg-gradient-to-br from-purple-50 to-purple-/5 dark:from-purple-900/20 dark:to-purple-900/5 border border-purple-100 dark:border-purple-900/30">
								<p class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1">Budget Range</p>
								<p class="text-2xl font-bold text-gray-900 dark:text-white">
									${{ number_format($campaign->budget_min, 0) }} - ${{ number_format($campaign->budget_max, 0) }}
								</p>
							</div>

							<!-- Duration -->
							<div
								class="p-4 rounded-xl bg-gradient-to-br from-blue-50 to-blue-5 dark:from-blue-900/20 dark:to-blue-900/5 border border-blue-100 dark:border-blue-900/30">
								<p class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1">Campaign Duration
								</p>
								<p class="text-base font-bold text-gray-900 dark:text-white">
									{{ $campaign->start_date?->format('M d, Y') }} - {{ $campaign->end_date?->format('M d, Y') }}
								</p>
							</div>

							<!-- Currency -->
							<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
								<p class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1">Currency</p>
								<p class="text-lg font-bold text-gray-900 dark:text-white">{{ $campaign->currency ?? 'USD' }}</p>
							</div>

							<!-- Active Status -->
							<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
								<p class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400 mb-1">Campaign Status
								</p>
								<div class="flex items-center gap-2">
									<div class="w-3 h-3 rounded-full {{ $campaign->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></div>
									<p class="text-lg font-bold text-gray-900 dark:text-white">
										{{ $campaign->is_active ? 'Active' : 'Inactive' }}
									</p>
								</div>
							</div>
						</div>
					</div>

					<!-- Instructions Section -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm">
						<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Content Instructions</h2>
						<div class="prose prose-sm dark:prose-invert max-w-none">
							<p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap leading-relaxed">{{ $campaign->instructions }}</p>
						</div>
					</div>

					<!-- Application Status for Influencers -->
					@if (auth()->user()->user_type === 'influencer')
						@php
							$application = $campaign->applications?->first();
						@endphp
						@if ($application)
							<div class="bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-200 dark:border-blue-900/30 p-6">
								<h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-3">Your Application Status</h3>
								<div class="flex items-center justify-between">
									<div>
										<p class="text-sm text-blue-700 dark:text-blue-400 mb-2">Status:</p>
										<span
											class="inline-block px-3 py-1.5 rounded-full text-sm font-semibold
											{{ $application->status === 'accepted'
											    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300'
											    : ($application->status === 'rejected'
											        ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'
											        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300') }}">
											{{ \Illuminate\Support\Str::headline($application->status) }}
										</span>
									</div>
									<div class="text-right">
										<p class="text-sm text-blue-700 dark:text-blue-400 mb-1">Applied on:</p>
										<p class="font-semibold text-blue-900 dark:text-blue-300">{{ $application->applied_at?->format('M d, Y') }}
										</p>
									</div>
								</div>
							</div>
						@endif
					@endif
				</div>

				<!-- Sidebar -->
				<div class="lg:col-span-1">
					<!-- Brand/Creator Info Card -->
					<div
						class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm sticky top-8">
						<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Campaign Owner</h3>

						@php
							$brandName = $campaign->brand?->brand_name ?? ($campaign->createdBy?->name ?? 'Unknown');
							$brand = $campaign->brand;
						@endphp

						<div class="space-y-4">
							<div>
								<p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Brand Name</p>
								<p class="font-semibold text-gray-900 dark:text-white">{{ $brandName }}</p>
							</div>

							@if ($campaign->categories && $campaign->categories->count() > 0)
								<div class="pt-4 border-t border-gray-200 dark:border-gray-700">
									<p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Target Niches</p>
									<div class="flex flex-wrap gap-2">
										@foreach ($campaign->categories as $category)
											<span
												class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
												{{ $category->name }}
											</span>
										@endforeach
									</div>
								</div>
							@endif

							@if ($campaign->targeting?->follower_ranges || ($campaign->targeting && collect($campaign->targeting)->isNotEmpty()))
								<div class="pt-4 border-t border-gray-200 dark:border-gray-700">
									<p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Targeting Criteria</p>
									<ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
										@if ($campaign->targeting?->target_gender)
											<li>• Gender: <span
													class="font-semibold">{{ \Illuminate\Support\Str::headline($campaign->targeting->target_gender) }}</span>
											</li>
										@endif
										@if ($campaign->targeting?->age_min || $campaign->targeting?->age_max)
											<li>• Age Range: <span class="font-semibold">{{ $campaign->targeting->age_min ?? '13' }} -
													{{ $campaign->targeting->age_max ?? '65' }}</span></li>
										@endif
									</ul>
								</div>
							@endif

							@if (auth()->user()->user_type === 'brand' && $campaign->created_by === auth()->user()->id)
								<div class="pt-6 border-t border-gray-200 dark:border-gray-700 space-y-3">
									<a href="{{ route('frontend.campaigns.edit', $campaign) }}"
										class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold transition">
										<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
											</path>
										</svg>
										Edit Campaign
									</a>

									<a href="{{ route('frontend.campaigns.index') }}"
										class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold transition">
										Back to List
									</a>
								</div>
							@elseif (auth()->user()->user_type === 'influencer')
								<div class="pt-6 border-t border-gray-200 dark:border-gray-700">
									<a href="{{ route('frontend.campaigns.index') }}"
										class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold transition">
										Back to Opportunities
									</a>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
