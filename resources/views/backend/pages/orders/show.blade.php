@extends('backend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Orders', 'url' => route('dashboard.orders.index')]]" pageTitle="Order {{ $order->order_number }}" />

	<div class="space-y-6">
		<!-- Header Section -->
		<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h1>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
						Placed on {{ optional($order->placed_at ?? $order->created_at)->format('M d, Y \a\t h:i A') }}
					</p>
				</div>
				<div class="flex items-center gap-3">
					@php
						$statusColors = [
							'pending' => 'yellow',
							'accepted' => 'blue',
							'in-progress' => 'purple',
							'in_progress' => 'purple',
							'completed' => 'green',
							'cancelled' => 'red',
						];
						$color = $statusColors[$order->status] ?? 'gray';
					@endphp
					<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-{{ $color }}-100 dark:bg-{{ $color }}-900/20 text-{{ $color }}-800 dark:text-{{ $color }}-100">
						{{ ucfirst(str_replace('-', ' ', str_replace('_', ' ', $order->status))) }}
					</span>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Main Content -->
			<div class="lg:col-span-2 space-y-6">
				<!-- Order Items -->
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
					</div>
					<div class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse ($order->items as $item)
							<div class="p-6">
								<div class="flex items-start justify-between">
									<div class="flex-1">
										<h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
										<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
											Influencer:
											<span class="font-medium">
												{{ $item->influencer?->display_name ?: $item->influencer?->user?->name ?? 'N/A' }}
											</span>
										</p>

										@if ($item->description)
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
												{{ $item->description }}
											</p>
										@endif

										<!-- Item Details Grid -->
										<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
											<div>
												<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
												<p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
													{{ ucfirst(str_replace('_', ' ', $item->status)) }}
												</p>
											</div>
											<div>
												<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</p>
												<p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $item->quantity }}</p>
											</div>
											@if ($item->due_date)
												<div>
													<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</p>
													<p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
														{{ $item->due_date->format('M d, Y') }}
													</p>
												</div>
											@endif
											<div>
												<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment</p>
												@if ($item->paid_at)
													<p class="text-sm font-semibold text-green-600 dark:text-green-400 mt-1">Paid</p>
												@else
													<p class="text-sm font-semibold text-yellow-600 dark:text-yellow-400 mt-1">Pending</p>
												@endif
											</div>
										</div>
									</div>

									<div class="text-right ml-6 min-w-fit">
										<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Price</p>
										<p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
											${{ number_format($item->unit_price, 2) }}
										</p>
										<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-2">Line Total</p>
										<p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
											${{ number_format($item->line_total, 2) }}
										</p>
									</div>
								</div>

								<!-- Quick Actions -->
								<div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-3">
									<form action="{{ route('dashboard.order-items.update-status', $item) }}" method="POST" class="flex items-center gap-3">
										@csrf
										@method('PUT')
										<label for="item_status_{{ $item->id }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
											Status:
										</label>
										<select id="item_status_{{ $item->id }}" name="status"
											class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
											<option value="pending" @selected($item->status === 'pending')>Pending</option>
											<option value="accepted" @selected($item->status === 'accepted')>Accepted</option>
											<option value="in_progress" @selected($item->status === 'in_progress')>In Progress</option>
											<option value="delivered" @selected($item->status === 'delivered')>Delivered</option>
											<option value="completed" @selected($item->status === 'completed')>Completed</option>
										</select>
										<button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
											Update
										</button>
									</form>

									@if (!$item->paid_at)
										<form action="{{ route('dashboard.order-items.mark-paid', $item) }}" method="POST" class="inline">
											@csrf
											<button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
												Mark as Paid
											</button>
										</form>
									@endif
								</div>
							</div>
						@empty
							<div class="p-6 text-center text-gray-500 dark:text-gray-400">
								No order items found.
							</div>
						@endforelse
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="lg:col-span-1 space-y-6">
				<!-- Order Summary -->
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="font-semibold text-gray-900 dark:text-white">Order Summary</h3>
					</div>
					<div class="p-6 space-y-3">
						<div class="flex justify-between text-sm">
							<span class="text-gray-600 dark:text-gray-400">Subtotal</span>
							<span class="font-medium text-gray-900 dark:text-white">
								${{ number_format($order->items->sum('line_total'), 2) }}
							</span>
						</div>
						<div class="flex justify-between text-sm">
							<span class="text-gray-600 dark:text-gray-400">Total Items</span>
							<span class="font-medium text-gray-900 dark:text-white">{{ $order->items->count() }}</span>
						</div>
						<div class="flex justify-between text-sm">
							<span class="text-gray-600 dark:text-gray-400">Influencers</span>
							<span class="font-medium text-gray-900 dark:text-white">
								{{ $order->items->pluck('influencer_id')->unique()->count() }}
							</span>
						</div>
						<div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
							<span class="font-semibold text-gray-900 dark:text-white">Total Amount</span>
							<span class="text-lg font-bold text-gray-900 dark:text-white">
								${{ number_format($order->items->sum('line_total'), 2) }}
							</span>
						</div>
					</div>
				</div>

				<!-- Buyer Information -->
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="font-semibold text-gray-900 dark:text-white">Buyer Information</h3>
					</div>
					<div class="p-6 space-y-3 text-sm">
						<div>
							<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Name</p>
							<p class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->buyer?->name ?? 'N/A' }}</p>
						</div>
						<div>
							<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Email</p>
							<p class="font-medium text-gray-900 dark:text-white mt-1 break-all">{{ $order->buyer?->email ?? 'N/A' }}</p>
						</div>
						@if ($order->buyer?->phone)
							<div>
								<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Phone</p>
								<p class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->buyer->phone }}</p>
							</div>
						@endif
					</div>
				</div>

				<!-- Order Management -->
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="font-semibold text-gray-900 dark:text-white">Manage Order</h3>
					</div>
					<div class="p-6">
						<form action="{{ route('dashboard.orders.update-status', $order) }}" method="POST" class="space-y-3">
							@csrf
							@method('PUT')
							<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
								Order Status
							</label>
							<select name="status"
								class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
								<option value="pending" @selected($order->status === 'pending')>Pending</option>
								<option value="accepted" @selected($order->status === 'accepted')>Accepted</option>
								<option value="in-progress" @selected($order->status === 'in-progress')>In Progress</option>
								<option value="in_progress" @selected($order->status === 'in_progress')>In Progress (alt)</option>
								<option value="completed" @selected($order->status === 'completed')>Completed</option>
								<option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
							</select>
							<button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
								Update Status
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
