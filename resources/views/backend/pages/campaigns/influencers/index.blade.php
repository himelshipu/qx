@extends('backend.layouts.app')

@section('title', 'Campaign Influencers')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Campaigns', 'url' => route('dashboard.campaigns.standard')]]" pageTitle="Campaign Influencers - {{ $campaign->title }}" />

	<div class="space-y-6">
		<!-- Campaign Info -->
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $campaign->title }}</h3>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $campaign->description }}</p>
				</div>
				<div class="flex gap-2">
					<a href="{{ route('dashboard.campaigns.influencers.create', $campaign) }}"
						class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
						</svg>
						Assign Influencers
					</a>
					<a href="{{ route('dashboard.campaigns.standard') }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						Back
					</a>
				</div>
			</div>
		</div>

		<!-- Influencers List -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assigned Influencers</h3>
				<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage influencer assignments and approval status</p>
			</div>

			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Influencer</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Email</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Status</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Agreed Amount</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Assigned Date</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
						@forelse ($influencers as $influencer)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
									{{ $influencer->influencer->display_name ?? $influencer->influencer->user->name }}
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									{{ $influencer->influencer->user->email }}
								</td>
								<td class="px-4 py-3 text-sm">
									@if ($influencer->status === 'approved')
										<span
											class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">
											Approved
										</span>
									@elseif ($influencer->status === 'rejected')
										<span
											class="inline-flex rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-300">
											Rejected
										</span>
									@elseif ($influencer->status === 'cancelled')
										<span
											class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-300">
											Cancelled
										</span>
									@else
										<span
											class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
											Assigned (Pending)
										</span>
									@endif
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									@if ($influencer->agreed_amount)
										{{ strtoupper($campaign->currency ?? 'USD') }} {{ number_format((float) $influencer->agreed_amount, 2) }}
									@else
										—
									@endif
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									{{ $influencer->created_at->format('M d, Y') }}
								</td>
								<td class="px-4 py-3 text-sm">
									<div class="flex gap-2">
										@if ($influencer->status === 'assigned')
												<form action="{{ route('campaign-influencers.approve', $influencer) }}" method="POST" class="inline-flex items-center gap-2">
												@csrf
													<input
														type="number"
														name="agreed_amount"
														min="0.01"
														step="0.01"
														placeholder="Amount"
														required
														class="w-24 rounded border border-gray-300 px-2 py-1 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
													>
												<button type="submit"
													class="rounded bg-green-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-green-700">
													Approve
												</button>
											</form>
											<form action="{{ route('campaign-influencers.reject', $influencer) }}" method="POST" class="inline">
												@csrf
												<button type="submit"
													class="rounded bg-red-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-red-700">
													Reject
												</button>
											</form>
										@endif
										@if (in_array($influencer->status, ['assigned', 'approved']))
											<form action="{{ route('campaign-influencers.cancel', $influencer) }}" method="POST" class="inline">
												@csrf
												<button type="submit"
													class="rounded bg-gray-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-gray-700">
													Cancel
												</button>
											</form>
										@endif
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
									No influencers assigned yet. <a href="{{ route('dashboard.campaigns.influencers.create', $campaign) }}"
										class="text-blue-600 hover:underline">Assign influencers</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- Create Order Button (if approved influencers exist) -->
		@if ($influencers->where('status', 'approved')->count() > 0)
			<div class="rounded-xl border border-green-200 bg-green-50 p-5 dark:border-green-900/30 dark:bg-green-900/20">
				<div class="flex items-center justify-between">
					<div>
						<h3 class="font-semibold text-green-900 dark:text-green-100">Ready to Create Orders</h3>
						<p class="mt-1 text-sm text-green-800 dark:text-green-300">
							You have {{ $influencers->where('status', 'approved')->count() }} approved influencers. Create the master order
							with sub-orders.
						</p>
					</div>
					<form action="{{ route('dashboard.orders.create-from-campaign') }}" method="POST" class="inline">
						@csrf
						<input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
						<input type="hidden" name="brand_id" value="{{ $campaign->brand_id }}">
						<button type="submit"
							class="rounded-lg bg-green-600 px-6 py-2 font-medium text-white transition hover:bg-green-700">
							Create Master Order
						</button>
					</form>
				</div>
			</div>
		@endif
	</div>
@endsection
