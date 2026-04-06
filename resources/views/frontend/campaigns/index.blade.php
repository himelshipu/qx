{{-- Frontend Campaigns Index --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-6xl mx-auto">
			<div class="flex justify-between items-center mb-8">
				<div>
					<h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
						@if ($userType === 'brand')
							My Campaigns
						@else
							Applied Campaigns
						@endif
					</h1>
					<p class="text-gray-600 dark:text-gray-400">
						@if ($userType === 'brand')
							Manage and create campaigns for your brand
						@else
							View campaigns you've applied to
						@endif
					</p>
				</div>
				@if ($userType === 'brand')
					<a href="{{ route('frontend.campaigns.create') }}"
						class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
						Create Campaign
					</a>
				@endif
			</div>

			@if ($campaigns->isEmpty())
				<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
					<svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
						</path>
					</svg>
					<h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
						@if ($userType === 'brand')
							No campaigns yet
						@else
							No applied campaigns
						@endif
					</h2>
					<p class="text-gray-600 dark:text-gray-400 mb-6">
						@if ($userType === 'brand')
							Get started by creating your first campaign to find perfect creators for your brand
						@else
							Start applying to campaigns to collaborate with brands
						@endif
					</p>
					@if ($userType === 'brand')
						<a href="{{ route('frontend.campaigns.create') }}"
							class="inline-block px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
							Create Your First Campaign
						</a>
					@else
						<a href="{{ route('home') }}"
							class="inline-block px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition">
							Explore Opportunities
						</a>
					@endif
				</div>
			@else
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
					@foreach ($campaigns as $campaign)
						<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6 flex flex-col">
							<div class="mb-4">
								<div class="flex items-start justify-between mb-2">
									<h2 class="text-lg font-semibold text-gray-900 dark:text-white flex-1">{{ $campaign->title }}</h2>
									<span
										class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $campaign->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
										{{ ucfirst($campaign->status) }}
									</span>
								</div>
								<p class="text-sm text-gray-600 dark:text-gray-400">
									by {{ $campaign->createdBy->name ?? 'Unknown' }}
								</p>
							</div>

							<p class="text-gray-700 dark:text-gray-300 text-sm mb-4 flex-grow">
								{{ Str::limit($campaign->description, 120) }}
							</p>

							<div class="space-y-3 mb-6 pt-4 border-t border-gray-200 dark:border-gray-700">
								<div class="flex justify-between text-sm">
									<span class="text-gray-600 dark:text-gray-400">Budget:</span>
									<span class="font-semibold text-gray-900 dark:text-white">
										${{ number_format($campaign->budget_min, 0) }} - ${{ number_format($campaign->budget_max, 0) }}
									</span>
								</div>
								<div class="flex justify-between text-sm">
									<span class="text-gray-600 dark:text-gray-400">Running:</span>
									<span class="font-semibold text-gray-900 dark:text-white">
										{{ $campaign->start_date?->format('M d') }} - {{ $campaign->end_date?->format('M d, Y') }}
									</span>
								</div>
								@if ($userType === 'creator')
									<div class="flex justify-between text-sm">
										<span class="text-gray-600 dark:text-gray-400">Application Status:</span>
										<span class="font-semibold text-blue-600 dark:text-blue-400">
											@php
												$application = $campaign->applications->first();
											@endphp
											{{ $application ? ucfirst($application->status) : 'N/A' }}
										</span>
									</div>
								@endif
							</div>

							<div class="flex gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
								<a href="{{ route('frontend.campaigns.show', $campaign) }}"
									class="flex-1 text-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
									View Details
								</a>
								@if ($userType === 'brand')
									<a href="{{ route('frontend.campaigns.edit', $campaign) }}"
										class="flex-1 text-center px-4 py-2 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-medium rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/50 transition">
										Edit
									</a>
								@endif
							</div>
						</div>
					@endforeach
				</div>

				<!-- Pagination -->
				{{ $campaigns->links() }}
			@endif
		</div>
	</div>
@endsection
