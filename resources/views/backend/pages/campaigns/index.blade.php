@extends('backend.layouts.app')

@section('title', 'Campaigns')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Campaigns" />

	<div class="space-y-6">
		@include('backend.pages.campaigns._alerts')

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">{{ $stats['draft'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['active'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Campaign Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage campaign targeting, schedules, and status across the
						platform.</p>
				</div>
				<div class="flex flex-wrap items-center gap-2">
					<a href="{{ route('dashboard.campaigns.index') }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						Switch to Enhanced View
					</a>
					<a href="{{ route('dashboard.campaigns.create') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						New Campaign
					</a>
				</div>
			</div>

			<div class="p-5">
				<form method="GET" action="{{ route('dashboard.campaigns.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-6">
					<div class="md:col-span-3">
						<label for="q"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by title, type, status, or description"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<label for="status"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($statusOptions as $option)
								<option value="{{ $option['value'] }}" {{ $status === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					<div>
						<label for="type"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</label>
						<select id="type" name="type"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($typeOptions as $option)
								<option value="{{ $option['value'] }}" {{ $type === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end gap-2">
						<button type="submit"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Apply
						</button>
						<a href="{{ route('dashboard.campaigns.index') }}"
							class="h-10 w-full rounded-lg border border-gray-200 px-3 text-center text-sm font-medium leading-10 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Reset
						</a>
					</div>
				</form>

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
								@php
									$statusClass = match ($campaign->status) {
									    'published' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
									    'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
									    'closed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
									    'archived' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
									    default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
									};

									$dependencyCount =
									    $campaign->applications_count +
									    $campaign->orders_count +
									    $campaign->order_items_count +
									    $campaign->cart_items_count;
									$minBudgetLabel =
									    $campaign->budget_min !== null
									        ? $campaign->currency . ' ' . number_format((float) $campaign->budget_min, 2)
									        : 'N/A';
									$maxBudgetLabel =
									    $campaign->budget_max !== null
									        ? $campaign->currency . ' ' . number_format((float) $campaign->budget_max, 2)
									        : 'N/A';
								@endphp
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3">
										<div>
											<a href="{{ route('dashboard.campaigns.view', $campaign) }}"
												class="text-sm font-semibold text-gray-900 transition hover:text-gray-700 dark:text-white dark:hover:text-gray-200">
												{{ $campaign->title }}
											</a>
											<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
												{{ \Illuminate\Support\Str::limit($campaign->description ?? 'No description provided.', 70) }}</p>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 capitalize dark:text-gray-300">{{ $campaign->campaign_type }}</td>
									<td class="px-4 py-3">
										<div class="flex flex-col gap-1">
											<span class="inline-flex w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
												{{ \Illuminate\Support\Str::headline($campaign->status) }}
											</span>
											<span
												class="text-xs {{ $campaign->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
												{{ $campaign->is_active ? 'Active' : 'Inactive' }}
											</span>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										@if ($campaign->budget_min === null && $campaign->budget_max === null)
											<span class="text-xs text-gray-500 dark:text-gray-400">Not set</span>
										@else
											{{ $minBudgetLabel }} - {{ $maxBudgetLabel }}
										@endif
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
												<button type="submit" {{ $dependencyCount > 0 ? 'disabled' : '' }}
													onclick="return confirm('Delete this campaign? This action cannot be undone.')"
													title="{{ $dependencyCount > 0 ? 'Cannot delete: campaign has linked records' : 'Delete campaign' }}"
													class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300">
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
										<a href="{{ route('dashboard.campaigns.create') }}"
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
		</div>
	</div>
@endsection
