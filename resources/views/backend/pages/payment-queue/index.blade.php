@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payment Queue" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: PAYMENT QUEUE KPIs ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			<!-- Total Pending Items -->
			<div
				class="bg-linear-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 border border-orange-200 dark:border-orange-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-orange-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm2 0v12h12V6H6zm2 3h8v2H8V9zm0 4h5v2H8v-2z"></path>
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
				class="bg-linear-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
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
				class="bg-linear-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-5 hover:shadow-lg transition-all">
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
				class="bg-linear-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-xl p-5 hover:shadow-lg transition-all">
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
					{{ $topUnpaidInfluencers->first()['influencer']->display_name ?? 'N/A' }}</p>
				<p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">
					${{ number_format($topUnpaidInfluencers->first()['amount'] ?? 0, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
					{{ $topUnpaidInfluencers->first()['item_count'] ?? 0 }} items</p>
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
					<path d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm2 0v12h12V6H6zm2 3h8v2H8V9zm0 4h5v2H8v-2z"></path>
				</svg>
				Unpaid Package Items ({{ $unPaidItems->count() }})
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Order #</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Package</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Source</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Due Date</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Workflow Status</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Payout Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($unPaidItems as $item)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->order?->order_number ?? 'N/A' }}
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $item->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->package?->name ?? 'N/A' }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->order?->campaign?->title ?? ($item->order?->brand?->brand_name ? 'Brand: ' . $item->order->brand->brand_name : 'Direct Package Purchase') }}
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
									@php
										$workflowColors = [
											'approved' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
											'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
											'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
											'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
										];
										$workflowColor = $workflowColors[$item->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
									@endphp
									<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $workflowColor }}">
										{{ ucfirst($item->status) }}
									</span>
								</td>
								<td class="py-4 px-4 cursor-pointer">
									<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors" data-mark-paid="item" data-item-id="{{ $item->id }}" data-item-type="item" data-amount="{{ $item->line_total }}" title="Click to mark as paid">
										Unpaid
									</span>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="8" class="py-8 text-center text-gray-500 dark:text-gray-400">
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
				Unpaid Campaign Orders ({{ $unPaidCampaignItems->count() + $unPaidSubOrders->count() }})
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Order #</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Source</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
						<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Due/Completed Date</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Workflow Status</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Payout Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@php
							$workflowColors = [
								'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
								'approved' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
								'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
								'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
							];
						@endphp

						@foreach ($unPaidCampaignItems as $item)
							@php
								$workflowColor = $workflowColors[$item->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
							@endphp
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->order?->order_number ?? 'N/A' }}
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $item->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $item->order?->campaign?->title ?? ($item->order?->brand?->brand_name ? 'Brand: ' . $item->order->brand->brand_name : 'Campaign Work') }}
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($item->line_total, 2) }}</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">{{ $item->due_date?->format('M d, Y') ?? 'N/A' }}</td>
								<td class="py-4 px-4">
									<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $workflowColor }}">{{ ucfirst($item->status) }}</span>
								</td>
							<td class="py-4 px-4 cursor-pointer">
								<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors" data-mark-paid="item" data-item-id="{{ $item->id }}" data-item-type="item" data-amount="{{ $item->line_total }}" title="Click to mark as paid">
										Unpaid
									</span>
								</td>
							</tr>
						@endforeach

						@foreach ($unPaidSubOrders as $subOrder)
							@php
								$workflowColor = $workflowColors[$subOrder->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
							@endphp
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $subOrder->order?->order_number ?? 'N/A' }}
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $subOrder->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $subOrder->order?->campaign?->title ?? ($subOrder->order?->brand?->brand_name ? 'Brand: ' . $subOrder->order->brand->brand_name : 'Campaign Work') }}
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($subOrder->amount, 2) }}</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">{{ $subOrder->completed_at?->format('M d, Y') ?? 'N/A' }}</td>
								<td class="py-4 px-4">
									<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $workflowColor }}">{{ ucfirst($subOrder->status) }}</span>
								</td>
								<td class="py-4 px-4 cursor-pointer">
									<span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 hover:bg-orange-200 dark:hover:bg-orange-900/50 transition-colors" data-mark-paid="suborder" data-item-id="{{ $subOrder->id }}" data-item-type="sub-order" data-amount="{{ $subOrder->amount }}" title="Click to mark as paid">
										Unpaid
									</span>
								</td>
							</tr>
						@endforeach

						@if ($unPaidCampaignItems->isEmpty() && $unPaidSubOrders->isEmpty())
							<tr>
								<td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No unpaid campaign orders
								</td>
							</tr>
						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- MARK AS PAID MODAL -->
<div id="markAsPaidModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeMarkAsPaidModal()">
	<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
		<div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
			<h2 class="text-xl font-bold text-gray-900 dark:text-white">Mark as Paid</h2>
			<button onclick="closeMarkAsPaidModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
				</svg>
			</button>
		</div>

		<form id="markAsPaidForm" class="p-6 space-y-4">
			@csrf
			<input type="hidden" id="itemId" name="item_id">
			<input type="hidden" id="itemType" name="item_type">

			<div>
				<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payout Amount</label>
				<div class="relative">
					<span class="absolute inset-y-0 left-3 flex items-center text-gray-400">$</span>
					<input type="number" id="payoutAmount" name="amount" step="0.01" min="0.01" required
						class="w-full pl-8 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
						placeholder="0.00">
				</div>
			</div>

			<div>
				<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference/Note (Optional)</label>
				<input type="text" name="reference"
					class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
					placeholder="e.g., Bank transfer ID">
			</div>

			<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
				<p class="text-sm text-blue-800 dark:text-blue-300">This will mark the item as paid and record the payout timestamp.</p>
			</div>

			<div class="flex gap-3 pt-4">
				<button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm transition-colors">
					Mark as Paid
				</button>
				<button type="button" onclick="closeMarkAsPaidModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold text-sm transition-colors">
					Cancel
				</button>
			</div>
		</form>
	</div>
</div>

<script>
	function openMarkAsPaidModal(itemId, itemType, amount) {
		document.getElementById('itemId').value = itemId;
		document.getElementById('itemType').value = itemType;
		document.getElementById('payoutAmount').value = amount;
		document.getElementById('markAsPaidModal').classList.remove('hidden');
	}

	function closeMarkAsPaidModal() {
		document.getElementById('markAsPaidModal').classList.add('hidden');
		document.getElementById('markAsPaidForm').reset();
	}

	document.addEventListener('click', function(event) {
		const badge = event.target.closest('[data-mark-paid]');
		if (!badge) return;

		const itemId = badge.dataset.itemId;
		const itemType = badge.dataset.itemType;
		const amount = badge.dataset.amount;
		openMarkAsPaidModal(itemId, itemType, amount);
	});

	document.getElementById('markAsPaidForm')?.addEventListener('submit', async function(e) {
		e.preventDefault();
		const itemId = document.getElementById('itemId').value;
		const itemType = document.getElementById('itemType').value;
		const amount = document.getElementById('payoutAmount').value;
		const reference = document.querySelector('input[name="reference"]').value;

		try {
			const response = await fetch('{{ route("dashboard.payment-queue.mark-paid") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
				},
				body: JSON.stringify({ item_id: itemId, item_type: itemType, amount, reference })
			});

			if (!response.ok) throw new Error(`HTTP ${response.status}`);

			const result = await response.json();
			if (result.success) {
				if (window.toast) window.toast.success(result.message || 'Item marked as paid');
				closeMarkAsPaidModal();
				window.location.reload();
			} else {
				if (window.toast) window.toast.error(result.message || 'Failed to mark as paid');
			}
		} catch (error) {
			console.error(error);
			if (window.toast) window.toast.error(error.message || 'Error marking item as paid');
		}
	});
</script>
@endsection
