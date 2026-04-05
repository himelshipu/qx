@extends('backend.layouts.app')

@section('title', 'Order Details')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Orders', 'url' => route('dashboard.orders.index')]]" pageTitle="Order {{ $order->order_number }}" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
						{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
						{{ number_format((float) $order->total_amount, 2) }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $order->buyer?->name ?? 'N/A' }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->buyer?->email ?? '-' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Placed</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
						{{ optional($order->placed_at ?? $order->created_at)?->format('M d, Y H:i') }}</p>
				</div>
			</div>
		</div>

		{{-- Sub-Orders (Workflow A: Campaign Orders) --}}
		@if ($order->subOrders->count() > 0)
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900/30 dark:bg-blue-900/20">
				<div class="flex items-center gap-2 mb-4">
					<svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Master Order with Sub-Orders</h3>
				</div>
				<p class="text-sm text-blue-800 dark:text-blue-300">This is a campaign order split into individual sub-orders for
					each approved influencer.</p>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="border-b border-gray-200 p-5 dark:border-gray-800">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sub-Orders for Influencers</h3>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage individual influencer orders and track payments</p>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Influencer</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Amount</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Paid</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Paid</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@foreach ($order->subOrders as $subOrder)
								<tr>
									<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
										{{ $subOrder->creator->display_name ?? $subOrder->creator->user->name }}
									</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ strtoupper($subOrder->currency) }} {{ number_format((float) $subOrder->amount, 2) }}
									</td>
									<td class="px-4 py-3 text-sm">
										@php
											$statusColor = match ($subOrder->status) {
											    'pending' => 'yellow',
											    'accepted' => 'blue',
											    'in_progress' => 'purple',
											    'on_review' => 'orange',
											    'completed' => 'green',
											    'cancelled' => 'red',
											    default => 'gray',
											};
										@endphp
										<span
											class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-300">
											{{ ucfirst(str_replace('_', ' ', $subOrder->status)) }}
										</span>
									</td>
									<td class="px-4 py-3 text-sm">
										@if ($subOrder->paid_at)
											<span class="inline-flex gap-1 items-center text-green-700 dark:text-green-400">
												<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd"
														d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
														clip-rule="evenodd"></path>
												</svg>
												{{ $subOrder->paid_at->format('M d') }}
											</span>
										@else
											<span class="text-gray-500 dark:text-gray-400">Not paid</span>
										@endif
									</td>
									<td class="px-4 py-3 text-sm space-x-2">
										<form action="{{ route('sub-orders.update-status', $subOrder) }}" method="POST" class="inline">
											@csrf
											@method('PUT')
											<select name="status" onchange="this.form.submit()"
												class="rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white">
												<option value="pending" @if ($subOrder->status === 'pending') selected @endif>Pending</option>
												<option value="accepted" @if ($subOrder->status === 'accepted') selected @endif>Accepted</option>
												<option value="in_progress" @if ($subOrder->status === 'in_progress') selected @endif>In Progress</option>
												<option value="on_review" @if ($subOrder->status === 'on_review') selected @endif>On Review</option>
												<option value="completed" @if ($subOrder->status === 'completed') selected @endif>Completed</option>
												<option value="cancelled" @if ($subOrder->status === 'cancelled') selected @endif>Cancelled</option>
											</select>
										</form>
										@if (!$subOrder->paid_at)
											<form action="{{ route('sub-orders.mark-paid', $subOrder) }}" method="POST" class="inline">
												@csrf
												<button type="submit"
													class="rounded bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700">
													Mark Paid
												</button>
											</form>
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		@else
			<!-- Regular Order Items -->
			<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="border-b border-gray-200 p-5 dark:border-gray-800">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h3>
				</div>
				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Item</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Creator</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Qty</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Unit</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Line Total</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
									Status</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($order->items as $item)
								<tr>
									<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->title }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										{{ $item->creator?->user?->name ?? ($item->creator?->display_name ?? 'N/A') }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ strtoupper($order->currency) }}
										{{ number_format((float) $item->unit_price, 2) }}</td>
									<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
										{{ number_format((float) $item->line_total, 2) }}</td>
									<td class="px-4 py-3 text-sm">
										<span
											class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
											{{ ucfirst(str_replace('_', ' ', $item->status)) }}
										</span>
									</td>
									<td class="px-4 py-3 text-sm">
										@php
											$itemPaidAt = \App\Models\OrderItem::find($item->id)?->paid_at;
										@endphp
										@if ($itemPaidAt)
											<span class="inline-flex gap-1 items-center text-green-700 dark:text-green-400">
												<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
													<path fill-rule="evenodd"
														d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
														clip-rule="evenodd"></path>
												</svg>
												{{ $itemPaidAt->format('M d') }}
											</span>
										@else
											<span class="text-gray-500 dark:text-gray-400">Not paid</span>
										@endif
									</td>
									<td class="px-4 py-3 text-sm space-x-2">
										<form action="{{ route('dashboard.order-items.update-status', $item) }}" method="POST" class="inline">
											@csrf
											@method('PUT')
											<select name="status" onchange="this.form.submit()"
												class="rounded border border-gray-300 px-2 py-1 text-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white">
												<option value="pending" @if ($item->status === 'pending') selected @endif>Pending</option>
												<option value="accepted" @if ($item->status === 'accepted') selected @endif>Accepted</option>
												<option value="in_progress" @if ($item->status === 'in_progress') selected @endif>In Progress</option>
												<option value="delivered" @if ($item->status === 'delivered') selected @endif>Delivered</option>
												<option value="approved" @if ($item->status === 'approved') selected @endif>Approved</option>
												<option value="rejected" @if ($item->status === 'rejected') selected @endif>Rejected</option>
												<option value="cancelled" @if ($item->status === 'cancelled') selected @endif>Cancelled</option>
											</select>
										</form>
										@if (!$itemPaidAt)
											<form action="{{ route('dashboard.order-items.mark-paid', $item) }}" method="POST" class="inline">
												@csrf
												<button type="submit"
													class="rounded bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700">
													Mark Paid
												</button>
											</form>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No order items
										found.
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		@endif
	</div>
@endsection
