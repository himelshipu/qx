@extends('backend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Orders', 'url' => route('dashboard.orders.index')]]" pageTitle="Order {{ $order->order_number }}" />

	@php
		$orderStatusMap = [
			'pending' => ['label' => 'Pending', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
			'accepted' => ['label' => 'Accepted', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'],
			'in_progress' => ['label' => 'In Progress', 'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
			'in-progress' => ['label' => 'In Progress', 'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'],
			'completed' => ['label' => 'Completed', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
			'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
			'refunded' => ['label' => 'Refunded', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
		];

		$itemStatusMap = [
			'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
			'accepted' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
			'in_progress' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
			'delivered' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
			'approved' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
			'completed' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
			'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
			'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
		];

		$currentOrderStatus = $orderStatusMap[$order->status] ?? ['label' => ucfirst(str_replace('_', ' ', $order->status)), 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'];
		$orderType = $order->campaign_id ? 'Campaign' : ($order->items->whereNotNull('package_id')->count() > 0 ? 'Package' : 'General');
		$orderTypeClass = $orderType === 'Campaign'
			? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
			: ($orderType === 'Package' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300');

		$forType = match ($order->buyer?->user_type) {
			'brand' => 'Brand',
			'influencer' => 'Influencer',
			'admin' => 'Admin',
			'moderator' => 'Moderator',
			default => 'N/A',
		};
		$forTypeClass = $forType === 'Brand'
			? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
			: ($forType === 'Influencer'
				? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
				: 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300');

		$itemsCount = $order->items->count();
		$paidItemsCount = $order->items->whereNotNull('paid_at')->count();
		$unpaidItemsCount = max($itemsCount - $paidItemsCount, 0);
		$paidProgress = $itemsCount > 0 ? (int) round(($paidItemsCount / $itemsCount) * 100) : 0;
		$influencerCount = $order->items->pluck('influencer_id')->filter()->unique()->count();

		$subtotal = (float) ($order->subtotal ?? $order->items->sum('line_total'));
		$serviceFee = (float) ($order->service_fee ?? 0);
		$taxAmount = (float) ($order->tax_amount ?? 0);
		$totalAmount = (float) ($order->total_amount ?? ($subtotal + $serviceFee + $taxAmount));
		$totalPaidAmount = (float) $order->payments->where('status', 'paid')->sum('amount');
	@endphp

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Details</p>
					<h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</h1>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
						Placed {{ optional($order->placed_at ?? $order->created_at)->format('M d, Y \a\t h:i A') }}
					</p>
				</div>
				<div class="flex flex-wrap items-center gap-2">
					<span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $currentOrderStatus['class'] }}">
						<x-icons.activity class="h-3.5 w-3.5" />{{ $currentOrderStatus['label'] }}
					</span>
					<span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $orderTypeClass }}">
						@if ($orderType === 'Campaign')
							<x-icons.campaign class="h-3.5 w-3.5" />
						@elseif ($orderType === 'Package')
							<x-icons.package class="h-3.5 w-3.5" />
						@else
							<x-icons.bag class="h-3.5 w-3.5" />
						@endif
						{{ $orderType }}
					</span>
					<span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $forTypeClass }}">
						<x-icons.user class="h-3.5 w-3.5" />For {{ $forType }}
					</span>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Total Amount</p>
				<p class="mt-2 text-xl font-semibold text-indigo-700 dark:text-indigo-200">{{ strtoupper($order->currency) }} {{ number_format($totalAmount, 2) }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Items Paid</p>
				<p class="mt-2 text-xl font-semibold text-green-700 dark:text-green-200">{{ $paidItemsCount }} / {{ $itemsCount }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Influencers</p>
				<p class="mt-2 text-xl font-semibold text-blue-700 dark:text-blue-200">{{ $influencerCount }}</p>
			</div>
			<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">Payments Logged</p>
				<p class="mt-2 text-xl font-semibold text-amber-700 dark:text-amber-200">{{ $order->payments->count() }}</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
			<div class="space-y-6 lg:col-span-2">
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h2 class="text-base font-semibold text-gray-900 dark:text-white">Order Items</h2>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $itemsCount }} item(s)</span>
					</div>
					<div class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse ($order->items as $item)
							@php
								$itemStatusClass = $itemStatusMap[$item->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
								$paymentClass = $item->paid_at
									? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
									: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
							@endphp
							<div class="p-5">
								<div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
									<div class="min-w-0 flex-1">
										<div class="flex flex-wrap items-center gap-2">
											<h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
											<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $itemStatusClass }}">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</span>
											<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentClass }}">{{ $item->paid_at ? 'Paid' : 'Payment Pending' }}</span>
										</div>
										<div class="mt-2 grid grid-cols-1 gap-2 text-sm text-gray-600 dark:text-gray-300 sm:grid-cols-2">
											<p><span class="font-medium text-gray-900 dark:text-white">Influencer:</span> {{ $item->influencer?->display_name ?: $item->influencer?->user?->name ?? 'N/A' }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Qty:</span> {{ $item->quantity }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Due:</span> {{ $item->due_date ? $item->due_date->format('M d, Y') : 'N/A' }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Package:</span> {{ $item->package?->name ?? 'N/A' }}</p>
										</div>
										@if ($item->description)
											<p class="mt-2 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $item->description }}</p>
										@endif
									</div>
									<div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-right dark:border-gray-700 dark:bg-gray-800/60">
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</p>
										<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format((float) $item->unit_price, 2) }}</p>
										<p class="mt-2 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Line Total</p>
										<p class="text-lg font-bold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format((float) $item->line_total, 2) }}</p>
									</div>
								</div>

								<div class="mt-4 flex flex-col gap-3 border-t border-gray-200 pt-4 dark:border-gray-700 xl:flex-row xl:items-center xl:justify-between">
									<form action="{{ route('dashboard.order-items.update-status', $item) }}" method="POST" class="flex flex-1 items-center gap-2">
										@csrf
										@method('PUT')
										<label for="item_status_{{ $item->id }}" class="sr-only">Item status</label>
										<select id="item_status_{{ $item->id }}" name="status"
											class="h-10 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
											<option value="pending" @selected($item->status === 'pending')>Pending</option>
											<option value="accepted" @selected($item->status === 'accepted')>Accepted</option>
											<option value="in_progress" @selected($item->status === 'in_progress')>In Progress</option>
											<option value="delivered" @selected($item->status === 'delivered')>Delivered</option>
											<option value="approved" @selected($item->status === 'approved')>Approved</option>
											<option value="rejected" @selected($item->status === 'rejected')>Rejected</option>
											<option value="cancelled" @selected($item->status === 'cancelled')>Cancelled</option>
											<option value="completed" @selected($item->status === 'completed')>Completed</option>
										</select>
										<button type="submit" class="inline-flex h-10 items-center gap-1 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700">
											<x-icons.check class="h-4 w-4" />Update Status
										</button>
									</form>

									@if (!$item->paid_at)
										<form action="{{ route('dashboard.order-items.mark-paid', $item) }}" method="POST">
											@csrf
											<button type="submit" class="inline-flex h-10 items-center gap-1 rounded-lg bg-green-600 px-4 text-sm font-semibold text-white transition hover:bg-green-700">
												<x-icons.wallet class="h-4 w-4" />Mark Paid
											</button>
										</form>
									@else
										<span class="inline-flex h-10 items-center rounded-lg border border-green-200 bg-green-50 px-4 text-sm font-semibold text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">Paid {{ optional($item->paid_at)->format('M d, Y') }}</span>
									@endif
								</div>
							</div>
						@empty
							<div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">
								No order items found.
							</div>
						@endforelse
					</div>
				</div>

				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Payment History</h3>
					</div>
					<div class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse ($order->payments as $payment)
							@php
								$paymentStatusClass = $payment->status === 'paid'
									? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
									: ($payment->status === 'failed'
										? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
										: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300');
							@endphp
							<div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($payment->currency ?? $order->currency) }} {{ number_format((float) $payment->amount, 2) }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($payment->payment_provider ?? 'manual') }} • {{ optional($payment->paid_at ?? $payment->created_at)->format('M d, Y h:i A') }}</p>
								</div>
								<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentStatusClass }}">{{ ucfirst($payment->status ?? 'pending') }}</span>
							</div>
						@empty
							<div class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No payment records yet.</div>
						@endforelse
					</div>
				</div>
			</div>

			<div class="space-y-6 lg:col-span-1">
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
					</div>
					<div class="space-y-4 p-5">
						<form action="{{ route('dashboard.orders.update-status', $order) }}" method="POST" class="space-y-3">
							@csrf
							@method('PUT')
							<label for="order_status" class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Status</label>
							<select id="order_status" name="status"
								class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
								<option value="pending" @selected($order->status === 'pending')>Pending</option>
								<option value="accepted" @selected($order->status === 'accepted')>Accepted</option>
								<option value="in-progress" @selected($order->status === 'in-progress')>In Progress</option>
								<option value="in_progress" @selected($order->status === 'in_progress')>In Progress (alt)</option>
								<option value="completed" @selected($order->status === 'completed')>Completed</option>
								<option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
							</select>
							<button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-1 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700">
								<x-icons.check class="h-4 w-4" />Update Order Status
							</button>
						</form>

						<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
							<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment Progress</p>
							<div class="mt-2 h-2 w-full rounded-full bg-gray-200 dark:bg-gray-700">
								<div class="h-2 rounded-full bg-green-500" style="width: {{ $paidProgress }}%"></div>
							</div>
							<div class="mt-2 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
								<span>{{ $paidItemsCount }} paid</span>
								<span>{{ $unpaidItemsCount }} unpaid</span>
							</div>
						</div>
					</div>
				</div>

				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Order Summary</h3>
					</div>
					<div class="space-y-2 p-5 text-sm">
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Subtotal</span>
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format($subtotal, 2) }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Service Fee</span>
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format($serviceFee, 2) }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Tax</span>
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format($taxAmount, 2) }}</span>
						</div>
						<div class="border-t border-gray-200 pt-2 dark:border-gray-700">
							<div class="flex items-center justify-between">
								<span class="font-semibold text-gray-900 dark:text-white">Total</span>
								<span class="text-base font-bold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }} {{ number_format($totalAmount, 2) }}</span>
							</div>
						</div>
						<div class="pt-1 text-xs text-gray-500 dark:text-gray-400">Paid via payment logs: {{ strtoupper($order->currency) }} {{ number_format($totalPaidAmount, 2) }}</div>
					</div>
				</div>

				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Participants</h3>
					</div>
					<div class="space-y-4 p-5 text-sm">
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</p>
							<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order->buyer?->name ?? 'N/A' }}</p>
							<p class="text-xs text-gray-500 dark:text-gray-400 break-all">{{ $order->buyer?->email ?? 'N/A' }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</p>
							<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order->brand?->brand_name ?? 'N/A' }}</p>
						</div>
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Campaign</p>
							<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order->campaign?->title ?? 'N/A' }}</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
