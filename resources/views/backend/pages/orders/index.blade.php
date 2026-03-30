@extends('backend.layouts.app')

@section('title', 'Orders')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Orders" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Orders</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/40 dark:bg-yellow-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Pending</p>
				<p class="mt-2 text-2xl font-semibold text-yellow-700 dark:text-yellow-200">{{ $stats['pending'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Completed</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['completed'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Revenue</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">${{ number_format($stats['revenue'], 2) }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Management</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track and review live order records.</p>
			</div>

			<div class="p-5">
				<form method="GET" action="{{ route('dashboard.orders.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-4">
					<div class="md:col-span-2">
						<label for="q" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<input id="q" name="q" type="text" value="{{ $search }}"
							placeholder="Order #, buyer, brand, campaign"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div>
						<label for="status" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							@foreach (['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'] as $state)
								<option value="{{ $state }}" {{ $status === $state ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $state)) }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end gap-2">
						<button type="submit" class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Apply</button>
						<a href="{{ route('dashboard.orders.index') }}" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-center text-sm font-medium leading-10 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Reset</a>
					</div>
				</form>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Campaign</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Placed</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($orders as $order)
								@php
									$statusColor = match ($order->status) {
										'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
										'cancelled', 'refunded' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
										'in_progress', 'accepted', 'delivered' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
										default => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
									};
								@endphp
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $order->buyer?->name ?? 'N/A' }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $order->brand?->brand_name ?? 'N/A' }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $order->campaign?->title ?? '-' }}</td>
									<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format((float) $order->total_amount, 2) }}</td>
									<td class="px-4 py-3 text-sm">
										<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ optional($order->placed_at ?? $order->created_at)?->format('M d, Y H:i') }}</td>
									<td class="px-4 py-3 text-right">
										<a href="{{ route('dashboard.orders.show', $order) }}"
											class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
											title="View order">
											<x-icons.eye class="h-4 w-4" />
										</a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No orders found for the current filters.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($orders->hasPages())
					<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
						{{ $orders->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection

