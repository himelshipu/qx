@extends('backend.layouts.app')

@section('title', 'Order Details')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Orders', 'url' => route('dashboard.orders.index')]]" pageTitle="Order {{ $order->order_number }}" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format((float) $order->total_amount, 2) }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $order->buyer?->name ?? 'N/A' }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->buyer?->email ?? '-' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Placed</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ optional($order->placed_at ?? $order->created_at)?->format('M d, Y H:i') }}</p>
				</div>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h3>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Item</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Creator</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Qty</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Line Total</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
						@forelse ($order->items as $item)
							<tr>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item->title }}</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->creator?->user?->name ?? $item->creator?->display_name ?? 'N/A' }}</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ strtoupper($order->currency) }} {{ number_format((float) $item->unit_price, 2) }}</td>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format((float) $item->line_total, 2) }}</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No order items found.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

