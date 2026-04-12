@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payment Queue" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: PAYMENT QUEUE KPIs ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			<!-- Total Pending Items -->
			<div
				class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-orange-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z M4 5a2 2 0 012-2 1 1 0 000-2H2a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V5a1 1 0 10 2h2a2 2 0 00-2-2 1 1 0 000 2H4z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/40 px-3 py-1 rounded-full">Pending</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUnpaidItems }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Unpaid Package Items</p>
			</div>

			<!-- Total Pending Sub-Orders -->
			<div
				class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Queue</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalUnpaidSubOrders }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Unpaid Campaign Orders</p>
			</div>

			<!-- Total Pending Amount -->
			<div
				class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm0 22c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10zm3.5-10c0 1.933-1.567 3.5-3.5 3.5s-3.5-1.567-3.5-3.5 1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/40 px-3 py-1 rounded-full">Total</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($totalUnpaidAmount, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Pending Amount</p>
			</div>

			<!-- Top Unpaid Influencer -->
			<div
				class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-purple-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-3 py-1 rounded-full">Top</span>
				</div>
				<p class="text-sm font-bold text-gray-900 dark:text-white truncate">
					{{ $topUnpaidInfluencers->first()?->influencer?->display_name ?? 'N/A' }}</p>
				<p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">
					${{ number_format($topUnpaidInfluencers->first()?->amount ?? 0, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
					{{ $topUnpaidInfluencers->first()?->item_count ?? 0 }} items</p>
			</div>
		</div>

		<!-- ============ SECTION 2: TOP UNPAID INFLUENCERS ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
					</path>
				</svg>
				Top Unpaid Influencers
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Rank</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Items Count</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Pending Amount</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($topUnpaidInfluencers->take(10) as $index => $entry)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<span
										class="inline-block w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-bold text-center">{{ $index + 1 }}</span>
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $entry['influencer']->display_name }}</span>
									<p class="text-xs text-gray-500">{{ $entry['influencer']->user?->email ?? 'N/A' }}</p>
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-semibold">{{ $entry['item_count'] }}</span>
								</td>
								<td class="py-4 px-4 font-bold text-orange-600 dark:text-orange-400">${{ number_format($entry['amount'], 2) }}
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No pending payments in queue
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- ============ SECTION 3: UNPAID ITEMS TABLE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z M4 5a2 2 0 012-2 1 1 0 000-2H2a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V5a1 1 0 10 2h2a2 2 0 00-2-2 1 1 0 000 2H4z">
					</path>
				</svg>
				Unpaid Package Items ({{ $unPaidItems->count() }})
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Package</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Campaign</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($unPaidItems as $item)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $item->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->package?->name ?? 'N/A' }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->order?->campaign?->title ?? 'N/A' }}
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($item->line_total, 2) }}
								</td>
								<td class="py-4 px-4">
									@php
										$dueDate = $item->due_date;
										$isOverdue = $dueDate && $dueDate->isPast();
										$isDueSoon = $dueDate && $dueDate->diffInDays(now()) <= 7;
									@endphp
									@if ($isOverdue)
										<span
											class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
											Overdue
										</span>
									@elseif($isDueSoon)
										<span
											class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
											Due Soon
										</span>
									@else
										<span class="text-gray-600 dark:text-gray-400">{{ $dueDate?->format('M d, Y') ?? 'N/A' }}</span>
									@endif
								</td>
								<td class="py-4 px-4">
									<span
										class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
										Pending
									</span>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No unpaid package items
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- ============ SECTION 4: UNPAID SUB-ORDERS TABLE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z">
					</path>
				</svg>
				Unpaid Campaign Orders ({{ $unPaidSubOrders->count() }})
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Campaign</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Created Date</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($unPaidSubOrders as $subOrder)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $subOrder->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $subOrder->order?->campaign?->title ?? 'N/A' }}
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($subOrder->amount, 2) }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $subOrder->created_at->format('M d, Y') }}
								</td>
								<td class="py-4 px-4">
									<span
										class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300">
										Pending
									</span>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No unpaid campaign orders
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
