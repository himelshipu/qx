@extends('backend.layouts.app')

@section('title', 'Campaign Details')

@section('content')
	@php
		$invitedCount = $campaign->applications->where('status', 'invited')->count();
		$appliedCount = $campaign->applications->whereIn('status', ['applied', 'countered_by_brand', 'countered_by_influencer'])->count();
		$approvedCount = $campaign->applications->where('status', 'approved')->count();
		$rejectedCount = $campaign->applications->whereIn('status', ['rejected', 'declined_by_brand', 'declined_by_influencer'])->count();
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Campaign Details" />

	<div class="space-y-6">
		<!-- Header Section -->
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
				<div class="flex-1">
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign->title }}</h2>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $campaign->campaign_type ?? 'Standard Campaign' }} • {{ $campaign->brand?->brand_name ?? 'N/A' }}</p>
					<span class="mt-2 inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $campaign->dashboard_status_badge_class }}">
						{{ $campaign->dashboard_status_label }}
					</span>
				</div>
				<div class="flex flex-col gap-2 sm:flex-row sm:items-center">
					<!-- Status Dropdown -->
					<form action="{{ route('dashboard.campaigns.update-status', $campaign) }}" method="POST" class="inline">
						@csrf
						<div class="flex items-center gap-2">
							<label for="status" class="text-xs font-semibold text-gray-600 dark:text-gray-400">STATUS:</label>
							<select name="status" onchange="this.form.submit()"
								class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white">
								<option value="published" {{ $campaign->status === 'published' ? 'selected' : '' }}>Published</option>
								<option value="paused" {{ $campaign->status === 'paused' ? 'selected' : '' }}>Paused</option>
								<option value="closed" {{ $campaign->status === 'closed' ? 'selected' : '' }}>Closed</option>
								<option value="archived" {{ $campaign->status === 'archived' ? 'selected' : '' }}>Archived</option>
							</select>
						</div>
					</form>

					<a href="{{ route('dashboard.campaigns.edit', $campaign) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-1.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						Edit
					</a>
					<a href="{{ route('dashboard.campaigns.standard') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back
					</a>
				</div>
			</div>
		</div>

		<!-- Stats Card - Consolidated -->
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Campaign Overview</h3>

			<div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-4">
				<!-- Applications Summary -->
				<div class="rounded-lg border border-gray-200 bg-gradient-to-br from-blue-50 to-blue-100 p-4 dark:border-gray-700 dark:from-blue-900/20 dark:to-blue-900/10">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-xs font-semibold text-blue-600 dark:text-blue-400">Total Applications</p>
							<p class="mt-2 text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $campaign->applications_count }}</p>
						</div>
						<svg class="h-8 w-8 text-blue-300 dark:text-blue-800" fill="currentColor" viewBox="0 0 20 20">
							<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v-1h8v1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
						</svg>
					</div>
				</div>

				<!-- Categories -->
				<div class="rounded-lg border border-gray-200 bg-gradient-to-br from-purple-50 to-purple-100 p-4 dark:border-gray-700 dark:from-purple-900/20 dark:to-purple-900/10">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-xs font-semibold text-purple-600 dark:text-purple-400">Categories</p>
							<p class="mt-2 text-3xl font-bold text-purple-700 dark:text-purple-300">{{ $campaign->categories->count() }}</p>
							<p class="mt-1 text-xs text-purple-600 dark:text-purple-400">{{ $campaign->categories->pluck('name')->join(', ') ?: 'None' }}</p>
						</div>
						<svg class="h-8 w-8 text-purple-300 dark:text-purple-800" fill="currentColor" viewBox="0 0 20 20">
							<path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM15 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2h-2zM5 13a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM15 13a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2h-2z" />
						</svg>
					</div>
				</div>

				<!-- Follower Ranges -->
				<div class="rounded-lg border border-gray-200 bg-gradient-to-br from-orange-50 to-orange-100 p-4 dark:border-gray-700 dark:from-orange-900/20 dark:to-orange-900/10">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-xs font-semibold text-orange-600 dark:text-orange-400">Follower Ranges</p>
							<p class="mt-2 text-3xl font-bold text-orange-700 dark:text-orange-300">{{ $campaign->followerRanges->count() }}</p>
							<p class="mt-1 text-xs text-orange-600 dark:text-orange-400">{{ $campaign->followerRanges->pluck('label')->join(', ') ?: 'None' }}</p>
						</div>
						<svg class="h-8 w-8 text-orange-300 dark:text-orange-800" fill="currentColor" viewBox="0 0 20 20">
							<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
							<path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path>
						</svg>
					</div>
				</div>

				<!-- Target Countries -->
				<div class="rounded-lg border border-gray-200 bg-gradient-to-br from-green-50 to-green-100 p-4 dark:border-gray-700 dark:from-green-900/20 dark:to-green-900/10">
					<div class="flex items-start justify-between">
						<div>
							<p class="text-xs font-semibold text-green-600 dark:text-green-400">Countries</p>
							<p class="mt-2 text-3xl font-bold text-green-700 dark:text-green-300">{{ $campaign->targetCountries->count() }}</p>
							<p class="mt-1 text-xs text-green-600 dark:text-green-400">{{ $campaign->targetCountries->pluck('country_code')->join(', ') ?: 'None' }}</p>
						</div>
						<svg class="h-8 w-8 text-green-300 dark:text-green-800" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Application Status Stats -->
		@if ($campaign->applications->count() > 0)
			<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
				<div class="rounded-lg border-l-4 border-blue-500 bg-blue-50 p-4 dark:bg-blue-900/20">
					<p class="text-xs font-semibold text-blue-600 dark:text-blue-400">INVITED</p>
					<p class="mt-1 text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $invitedCount }}</p>
				</div>

				<div class="rounded-lg border-l-4 border-amber-500 bg-amber-50 p-4 dark:bg-amber-900/20">
					<p class="text-xs font-semibold text-amber-600 dark:text-amber-400">APPLIED</p>
					<p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $appliedCount }}</p>
				</div>

				<div class="rounded-lg border-l-4 border-green-500 bg-green-50 p-4 dark:bg-green-900/20">
					<p class="text-xs font-semibold text-green-600 dark:text-green-400">APPROVED</p>
					<p class="mt-1 text-2xl font-bold text-green-700 dark:text-green-300">{{ $approvedCount }}</p>
				</div>

				<div class="rounded-lg border-l-4 border-red-500 bg-red-50 p-4 dark:bg-red-900/20">
					<p class="text-xs font-semibold text-red-600 dark:text-red-400">REJECTED</p>
					<p class="mt-1 text-2xl font-bold text-red-700 dark:text-red-300">{{ $rejectedCount }}</p>
				</div>
			</div>
		@endif

		<!-- Campaign Details Grid -->
		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Campaign Info</h3>
				<dl class="mt-4 space-y-2 text-sm">
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Budget</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ $campaign->budget_range_label }}
						</dd>
					</div>
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Duration</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->start_date?->format('M d') }} - {{ $campaign->end_date?->format('M d, Y') }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Created</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->created_at?->format('M d, Y') }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Creator</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->createdBy?->name ?? 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Targeting</h3>
				<dl class="mt-4 space-y-2 text-sm">
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Influencers</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $campaign->targeting?->influencer_count ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Gender</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Str::headline($campaign->targeting?->target_gender ?? 'Any') }}</dd>
					</div>
					<div class="flex justify-between">
						<dt class="text-gray-600 dark:text-gray-400">Age Range</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							@if ($campaign->targeting?->age_min || $campaign->targeting?->age_max)
								{{ $campaign->targeting?->age_min ?? '0' }} - {{ $campaign->targeting?->age_max ?? '99' }}
							@else
								Any
							@endif
						</dd>
					</div>
				</dl>
			</div>
		</div>

		<!-- Applicants Table (Compact) -->
		@if ($campaign->applications->count() > 0)
			<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="border-b border-gray-200 p-5 dark:border-gray-800">
					<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Applicants ({{ $campaign->applications->count() }})</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="w-full text-xs">
						<thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
							<tr>
								<th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Name</th>
								<th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Email</th>
								<th class="px-4 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
								<th class="px-4 py-2 text-right font-semibold text-gray-700 dark:text-gray-300">Applied</th>
								<th class="px-4 py-2 text-right font-semibold text-gray-700 dark:text-gray-300">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@foreach ($campaign->applications->take(10) as $application)
								@php
									$statusClass = match ($application->status) {
										'invited' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
										'applied' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
										'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
										'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
										default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
									};
								@endphp
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
									<td class="px-4 py-2">
										<a href="{{ route('dashboard.influencers.view', $application->influencer) }}"
											class="font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
											{{ $application->influencer->display_name }}
										</a>
									</td>
									<td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $application->influencer->user?->email ?? 'N/A' }}</td>
									<td class="px-4 py-2">
										<span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">
											{{ \Illuminate\Support\Str::headline($application->status) }}
										</span>
									</td>
									<td class="px-4 py-2 text-right text-gray-600 dark:text-gray-400">{{ $application->applied_at?->format('M d') }}</td>
									<td class="px-4 py-2 text-right">
										<div class="flex items-center justify-end gap-2">
											@if (in_array($application->status, ['approved', 'rejected']))
												<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $application->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
													{{ \Illuminate\Support\Str::headline($application->status) }}
												</span>
											@else
												<form action="{{ route('dashboard.campaigns.update-application-status', [$campaign, $application]) }}" method="POST">
													@csrf
													<input type="hidden" name="status" value="approved">
													<button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-green-700">
														<x-icons.check class="h-3.5 w-3.5" />Approve
													</button>
												</form>
												<form action="{{ route('dashboard.campaigns.update-application-status', [$campaign, $application]) }}" method="POST">
													@csrf
													<input type="hidden" name="status" value="rejected">
													<button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-700">
														<x-icons.x class="h-3.5 w-3.5" />Reject
													</button>
												</form>
											@endif
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@if ($campaign->applications->count() > 10)
					<div class="border-t border-gray-200 px-4 py-2 text-center text-xs text-gray-600 dark:border-gray-700 dark:text-gray-400">
						Showing 10 of {{ $campaign->applications->count() }} applicants
					</div>
				@endif
			</div>
		@endif

		<!-- Description -->
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Description & Instructions</h3>
			<div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
				<div>
					<p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Description</p>
					<p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $campaign->description ?: 'No description provided.' }}</p>
				</div>
				<div>
					<p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Instructions</p>
					<p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $campaign->instructions ?: 'No instructions provided.' }}</p>
				</div>
			</div>
		</div>
	</div>
@endsection
