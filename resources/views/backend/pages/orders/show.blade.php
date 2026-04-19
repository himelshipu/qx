@extends('backend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Orders', 'url' => route('dashboard.orders.index')]]" pageTitle="Order {{ $order->order_number }}" />

	@php
		$orderStatusMap = [
		    'pending' => [
		        'label' => 'Pending',
		        'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
		    ],
		    'accepted' => [
		        'label' => 'Accepted',
		        'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
		    ],
		    'in_progress' => [
		        'label' => 'In Progress',
		        'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
		    ],
		    'in-progress' => [
		        'label' => 'In Progress',
		        'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
		    ],
		    'completed' => [
		        'label' => 'Completed',
		        'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
		    ],
		    'cancelled' => [
		        'label' => 'Cancelled',
		        'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
		    ],
		    'refunded' => ['label' => 'Refunded', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
		];

		$itemStatusMap = [
		    'pending' => [
		        'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
		        'label' => 'Pending',
		    ],
		    'accepted' => [
		        'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
		        'label' => 'Accepted by Influencer',
		    ],
		    'in_progress' => [
		        'class' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
		        'label' => 'Work In Progress',
		    ],
		    'delivered' => [
		        'class' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
		        'label' => 'Delivered for Review',
		    ],
		    'approved' => [
		        'class' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
		        'label' => 'Approved for Payout',
		    ],
		    'completed' => [
		        'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
		        'label' => 'Completed',
		    ],
		    'rejected' => ['class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300', 'label' => 'Rejected'],
		    'cancelled' => [
		        'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
		        'label' => 'Cancelled',
		    ],
		];

		$currentOrderStatus = $orderStatusMap[$order->status] ?? [
		    'label' => ucfirst(str_replace('_', ' ', $order->status)),
		    'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
		];
		$hasCampaignContext = $order->campaign_id !== null || $order->subOrders->isNotEmpty();
		$orderType = $hasCampaignContext
		    ? 'Campaign'
		    : ($order->items->whereNotNull('package_id')->count() > 0
		        ? 'Package'
		        : 'General');
		$orderTypeClass =
		    $orderType === 'Campaign'
		        ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300'
		        : ($orderType === 'Package'
		            ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
		            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300');

		$forType = match (true) {
		    $order->buyer?->user_type === 'brand' => 'Brand',
		    $order->buyer?->user_type === 'influencer' => 'Influencer',
		    $order->buyer?->user_type === 'admin' => 'Admin',
		    $order->buyer?->user_type === 'moderator' => 'Moderator',
		    $order->brand_id !== null => 'Brand',
		    default => 'N/A',
		};

		$itemAcceptedLabel =
		    $orderType === 'Campaign' ? 'Accepted by Influencer (Campaign)' : 'Accepted by Influencer (Package)';

		$orderScopeLabel =
		    $orderType === 'Campaign'
		        ? 'Campaign order workflow'
		        : ($orderType === 'Package'
		            ? 'Package purchase workflow'
		            : 'General workflow');

		$itemStatusHint =
		    $orderType === 'Campaign'
		        ? 'Item status tracks campaign deliverable progress for this influencer.'
		        : 'Item status tracks package delivery progress for this influencer.';

		$forType = match ($order->buyer?->user_type) {
		    'brand' => 'Brand',
		    'influencer' => 'Influencer',
		    'admin' => 'Admin',
		    'moderator' => 'Moderator',
		    default => $forType,
		};
		$forTypeClass =
		    $forType === 'Brand'
		        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
		        : ($forType === 'Influencer'
		            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
		            : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300');

		$itemsCount = $order->items->count();
		$paidItemsCount = $order->items->whereNotNull('paid_at')->count();
		$unpaidItemsCount = max($itemsCount - $paidItemsCount, 0);
		$paidProgress = $itemsCount > 0 ? (int) round(($paidItemsCount / $itemsCount) * 100) : 0;
		$influencerCount = $order->items->pluck('influencer_id')->filter()->unique()->count();

		$itemsSubtotal = (float) $order->items->sum(static fn($item) => (float) $item->line_total);
		$subtotal = (float) ($order->subtotal ?? 0);
		if ($subtotal <= 0 && $itemsSubtotal > 0) {
		    $subtotal = $itemsSubtotal;
		}

		$serviceFee = (float) ($order->service_fee ?? 0);
		$taxAmount = (float) ($order->tax_amount ?? 0);
		$totalAmount = (float) ($order->total_amount ?? 0);
		if ($totalAmount <= 0) {
		    $totalAmount = $subtotal + $serviceFee + $taxAmount;
		}
		if ($serviceFee <= 0 && $totalAmount > $subtotal && $subtotal > 0) {
		    $serviceFee = max($totalAmount - $subtotal - $taxAmount, 0);
		}
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
					<span
						class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $currentOrderStatus['class'] }}">
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
				<p class="mt-2 text-xl font-semibold text-indigo-700 dark:text-indigo-200">{{ strtoupper($order->currency) }}
					{{ number_format($totalAmount, 2) }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Items Paid</p>
				<p class="mt-2 text-xl font-semibold text-green-700 dark:text-green-200">{{ $paidItemsCount }} /
					{{ $itemsCount }}</p>
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
					<div
						class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h2 class="text-base font-semibold text-gray-900 dark:text-white">Order Items</h2>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $itemsCount }} item(s)</span>
					</div>
					<div class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse ($order->items as $item)
							@php
								$itemStatusInfo = $itemStatusMap[$item->status] ?? [
								    'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
								    'label' => ucfirst(str_replace('_', ' ', $item->status)),
								];
								$paymentClass = $item->paid_at
								    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
								    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
								$itemDueDate = $item->due_date;
								if (!$itemDueDate && $item->package?->delivery_days !== null && ($order->placed_at ?? $order->created_at)) {
								    $itemDueDate = ($order->placed_at ?? $order->created_at)
								        ->copy()
								        ->addDays((int) $item->package->delivery_days);
								}
							@endphp
							<div class="p-5">
								<div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
									<div class="min-w-0 flex-1">
										<div class="flex flex-wrap items-center gap-2">
											<h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
											<span
												class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $itemStatusInfo['class'] }}">{{ $itemStatusInfo['label'] }}</span>
											<span
												class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentClass }}">{{ $item->paid_at ? 'Paid' : 'Payment Pending' }}</span>
										</div>
										<div class="mt-2 grid grid-cols-1 gap-2 text-sm text-gray-600 dark:text-gray-300 sm:grid-cols-2">
											<p><span class="font-medium text-gray-900 dark:text-white">Influencer:</span>
												{{ $item->influencer?->display_name ?: $item->influencer?->user?->name ?? 'N/A' }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Qty:</span> {{ $item->quantity }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Due:</span>
												{{ $itemDueDate ? $itemDueDate->format('M d, Y') : 'N/A' }}</p>
											<p><span class="font-medium text-gray-900 dark:text-white">Package:</span>
												{{ $item->package?->name ?? 'N/A' }}</p>
											@if ($item->paid_at)
												<p><span class="font-medium text-gray-900 dark:text-white">Payout Amount:</span>
													{{ strtoupper($order->currency) }}
													{{ number_format((float) ($item->payout_amount ?? $item->line_total), 2) }}</p>
												<p><span class="font-medium text-gray-900 dark:text-white">Marked By:</span>
													{{ $item->payoutMarkedBy?->name ?? 'System' }}</p>
												@if ($item->payout_reference)
													<p class="sm:col-span-2"><span class="font-medium text-gray-900 dark:text-white">Reference:</span>
														{{ $item->payout_reference }}</p>
												@endif
												@if ($item->payout_note)
													<p class="sm:col-span-2"><span class="font-medium text-gray-900 dark:text-white">Note:</span>
														{{ $item->payout_note }}</p>
												@endif
											@endif
										</div>
										@if ($item->description)
											<p class="mt-2 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $item->description }}</p>
										@endif
									</div>
									<div
										class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-right dark:border-gray-700 dark:bg-gray-800/60">
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Unit</p>
										<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
											{{ number_format((float) $item->unit_price, 2) }}</p>
										<p class="mt-2 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Line Total</p>
										<p class="text-lg font-bold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
											{{ number_format((float) $item->line_total, 2) }}</p>
									</div>
								</div>

								<div class="mt-4 grid grid-cols-1 gap-3 border-t border-gray-200 pt-4 dark:border-gray-700 xl:grid-cols-2">
									<form action="{{ route('dashboard.order-items.update-status', $item) }}" method="POST" class="space-y-2">
										@csrf
										@method('PUT')
										<div class="flex items-center gap-2">
											<label for="item_status_{{ $item->id }}" class="sr-only">Item status</label>
											<select id="item_status_{{ $item->id }}" name="status"
												class="h-10 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
												<option value="pending" @selected($item->status === 'pending')>Pending</option>
												<option value="accepted" @selected($item->status === 'accepted')>{{ $itemAcceptedLabel }}</option>
												<option value="in_progress" @selected($item->status === 'in_progress')>Work In Progress</option>
												<option value="delivered" @selected($item->status === 'delivered')>Delivered for Review</option>
												<option value="approved" @selected($item->status === 'approved')>Approved for Payout</option>
												<option value="rejected" @selected($item->status === 'rejected')>Rejected</option>
												<option value="cancelled" @selected($item->status === 'cancelled')>Cancelled</option>
											</select>
											<button type="submit"
												class="inline-flex h-10 items-center gap-1 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700"
												title="Save item-level workflow stage">
												<x-icons.check class="h-4 w-4" />Save Item Stage
											</button>
										</div>
										<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
											<label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
												<input type="checkbox" name="force_transition" value="1"
													class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
												Force transition
											</label>
											<input type="text" name="transition_note" maxlength="1000" placeholder="Reason (required if force)"
												class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
										</div>
									</form>

									@if (!$item->paid_at)
										<form action="{{ route('dashboard.order-items.mark-paid', $item) }}" method="POST"
											class="flex flex-col gap-2">
											@csrf
											<div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
												<input type="number" name="amount" min="0.01" step="0.01"
													value="{{ number_format((float) $item->line_total, 2, '.', '') }}" required placeholder="Payout amount"
													class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
												<input type="text" name="payout_reference" maxlength="120" placeholder="Reference (optional)"
													class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
												<input type="text" name="payout_note" maxlength="1000" placeholder="Note (optional)"
													class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
											</div>
											<button type="submit"
												class="inline-flex h-10 items-center gap-1 rounded-lg bg-green-600 px-4 text-sm font-semibold text-white transition hover:bg-green-700">
												<x-icons.wallet class="h-4 w-4" />Mark Paid
											</button>
										</form>
									@else
										<span
											class="inline-flex h-10 items-center rounded-lg border border-green-200 bg-green-50 px-4 text-sm font-semibold text-green-700 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300">Paid
											{{ optional($item->paid_at)->format('M d, Y') }}</span>
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

				@if ($order->childOrders->isNotEmpty())
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div
							class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<div>
								<h2 class="text-base font-semibold text-gray-900 dark:text-white">Child Orders</h2>
								<p class="text-xs text-gray-500 dark:text-gray-400">One child order per influencer under this checkout.</p>
							</div>
							<span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $order->childOrders->count() }} child
								order(s)</span>
						</div>
						<div class="p-5">
							<div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
								@foreach ($order->childOrders as $childOrder)
									@php
										$childInfluencer = $childOrder->acceptedForInfluencer;
										$childStatusColor = match ($childOrder->status) {
										    'completed' => 'emerald',
										    'cancelled' => 'red',
										    'accepted', 'in_progress', 'delivered' => 'blue',
										    default => 'yellow',
										};
									@endphp
									<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
										<div class="flex items-start justify-between gap-3">
											<div>
												<p class="font-semibold text-gray-900 dark:text-white">
													{{ $childInfluencer?->display_name ?: $childInfluencer?->user?->name ?? 'Influencer' }}</p>
												<p class="text-xs text-gray-500 dark:text-gray-400">{{ $childOrder->order_number }}</p>
											</div>
											<span
												class="inline-flex rounded-full bg-{{ $childStatusColor }}-100 px-2.5 py-1 text-xs font-semibold text-{{ $childStatusColor }}-800 dark:bg-{{ $childStatusColor }}-900/20 dark:text-{{ $childStatusColor }}-100">{{ ucfirst(str_replace('_', ' ', $childOrder->status)) }}</span>
										</div>
										<div class="mt-4 grid grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-300">
											<div>
												<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Items</p>
												<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $childOrder->items->count() }}</p>
											</div>
											<div>
												<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
												<p class="mt-1 font-semibold text-gray-900 dark:text-white">
													${{ number_format((float) $childOrder->total_amount, 2) }}</p>
											</div>
											<div class="col-span-2">
												<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Packages</p>
												<p class="mt-1 text-gray-700 dark:text-gray-300">{{ $childOrder->items->pluck('title')->implode(', ') }}
												</p>
											</div>
										</div>
										<a href="{{ route('dashboard.orders.show', $childOrder) }}"
											class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">Open
											child order</a>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				@endif

				@if ($orderType === 'Campaign')
					@php
						$subOrders = $order->subOrders;
						$completedCount = $subOrders->where('status', 'completed')->count();
						$totalCount = $subOrders->count();
						$completionPercent = $totalCount > 0 ? (int) round(($completedCount / $totalCount) * 100) : 0;
						$paidCount = $subOrders->whereNotNull('paid_at')->count();
					@endphp
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<!-- Header -->
						<div
							class="border-b border-gray-200 bg-linear-to-r from-blue-50 to-indigo-50 px-5 py-4 dark:border-gray-800 dark:from-blue-900/20 dark:to-indigo-900/20">
							<div class="flex items-center justify-between gap-4">
								<div>
									<h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
										<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
											<path
												d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v-1h8v1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
										</svg>
										Influencers (Campaign Order)
									</h2>
									<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Track work and payment status for each influencer</p>
								</div>
								<div class="flex items-center gap-2">
									<div class="text-center">
										<div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $completedCount }}/{{ $totalCount }}
										</div>
										<div class="text-xs text-gray-600 dark:text-gray-400">Completed</div>
									</div>
									<div class="w-1 h-12 bg-gray-200 dark:bg-gray-700"></div>
									<div class="text-center">
										<div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $paidCount }}/{{ $totalCount }}
										</div>
										<div class="text-xs text-gray-600 dark:text-gray-400">Paid</div>
									</div>
								</div>
							</div>
							<!-- Progress Bar -->
							<div class="mt-4 flex items-center gap-2">
								<div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
									<div class="h-full bg-linear-to-r from-blue-500 to-indigo-600 rounded-full transition-all"
										style="width: {{ $completionPercent }}%"></div>
								</div>
								<span
									class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-12 text-right">{{ $completionPercent }}%</span>
							</div>
						</div>

						<!-- Influencers Grid -->
						<div class="p-5">
							@if ($subOrders->isEmpty())
								<div class="text-center py-8 text-gray-500 dark:text-gray-400">
									<p class="text-sm">No influencers yet. Approve influencers from the campaign to create sub-orders.</p>
								</div>
							@else
								<div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
									@foreach ($subOrders as $subOrder)
										@php
											$subOrderStatusMap = [
											    'pending' => ['color' => 'yellow', 'icon' => 'clock', 'label' => 'Pending - Waiting'],
											    'accepted' => ['color' => 'cyan', 'icon' => 'check', 'label' => 'Accepted'],
											    'in_progress' => ['color' => 'blue', 'icon' => 'lightning', 'label' => 'In Progress'],
											    'delivered' => ['color' => 'purple', 'icon' => 'document-check', 'label' => 'Delivered'],
											    'on_review' => ['color' => 'indigo', 'icon' => 'document', 'label' => 'On Review'],
											    'changes_requested' => ['color' => 'amber', 'icon' => 'exclamation', 'label' => 'Changes Requested'],
											    'approved' => ['color' => 'teal', 'icon' => 'thumb-up', 'label' => 'Approved'],
											    'completed' => ['color' => 'emerald', 'icon' => 'check-circle', 'label' => 'Completed'],
											    'review_pending' => ['color' => 'orange', 'icon' => 'chat', 'label' => 'Review Pending'],
											    'reviewed' => ['color' => 'green', 'icon' => 'star', 'label' => 'All Reviewed'],
											    'cancelled' => ['color' => 'red', 'icon' => 'x', 'label' => 'Cancelled'],
											];
											$statusInfo = $subOrderStatusMap[$subOrder->status] ?? [
											    'color' => 'gray',
											    'icon' => 'question',
											    'label' => ucfirst(str_replace('_', ' ', $subOrder->status)),
											];
											$statusClass = match ($statusInfo['color']) {
											    'yellow' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/10 dark:border-yellow-900/30',
											    'cyan' => 'bg-cyan-50 border-cyan-200 dark:bg-cyan-900/10 dark:border-cyan-900/30',
											    'indigo' => 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/10 dark:border-indigo-900/30',
											    'blue' => 'bg-blue-50 border-blue-200 dark:bg-blue-900/10 dark:border-blue-900/30',
											    'purple' => 'bg-purple-50 border-purple-200 dark:bg-purple-900/10 dark:border-purple-900/30',
											    'amber' => 'bg-amber-50 border-amber-200 dark:bg-amber-900/10 dark:border-amber-900/30',
											    'teal' => 'bg-teal-50 border-teal-200 dark:bg-teal-900/10 dark:border-teal-900/30',
											    'emerald' => 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/10 dark:border-emerald-900/30',
											    'orange' => 'bg-orange-50 border-orange-200 dark:bg-orange-900/10 dark:border-orange-900/30',
											    'green' => 'bg-green-50 border-green-200 dark:bg-green-900/10 dark:border-green-900/30',
											    'red' => 'bg-red-50 border-red-200 dark:bg-red-900/10 dark:border-red-900/30',
											    default => 'bg-gray-50 border-gray-200 dark:bg-gray-900/10 dark:border-gray-900/30',
											};
											$textClass = match ($statusInfo['color']) {
											    'yellow' => 'text-yellow-700 dark:text-yellow-300',
											    'cyan' => 'text-cyan-700 dark:text-cyan-300',
											    'indigo' => 'text-indigo-700 dark:text-indigo-300',
											    'blue' => 'text-blue-700 dark:text-blue-300',
											    'purple' => 'text-purple-700 dark:text-purple-300',
											    'amber' => 'text-amber-700 dark:text-amber-300',
											    'teal' => 'text-teal-700 dark:text-teal-300',
											    'emerald' => 'text-emerald-700 dark:text-emerald-300',
											    'orange' => 'text-orange-700 dark:text-orange-300',
											    'green' => 'text-green-700 dark:text-green-300',
											    'red' => 'text-red-700 dark:text-red-300',
											    default => 'text-gray-700 dark:text-gray-300',
											};
										@endphp
										<div class="rounded-lg border {{ $statusClass }} p-4 transition hover:shadow-md">
											<!-- Influencer Header -->
											<div class="flex items-start justify-between gap-2 mb-4">
												<div class="min-w-0 flex-1">
													<h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">
														{{ $subOrder->influencer?->display_name ?: $subOrder->influencer?->user?->name ?? 'Influencer' }}
													</h3>
													<p class="text-xs text-gray-600 dark:text-gray-400 truncate">
														{{ $subOrder->influencer?->user?->email ?? '' }}
													</p>
												</div>
											</div>

											<!-- Status & Payment Badges -->
											<div class="flex flex-wrap gap-2 mb-4">
												<!-- Work Status Badge -->
												<span
													class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $textClass }} bg-white/50 dark:bg-gray-900/30">
													@if ($statusInfo['color'] === 'emerald')
														<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
															<path fill-rule="evenodd"
																d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
																clip-rule="evenodd" />
														</svg>
													@elseif ($statusInfo['color'] === 'red')
														<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
															<path fill-rule="evenodd"
																d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
																clip-rule="evenodd" />
														</svg>
													@else
														<svg class="w-3.5 h-3.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
															<path fill-rule="evenodd"
																d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z"
																clip-rule="evenodd" />
														</svg>
													@endif
													{{ $statusInfo['label'] }}
												</span>
												<!-- Payment Status Badge -->
												<span
													class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold {{ $subOrder->paid_at ? 'text-green-700 dark:text-green-300 bg-green-100/50 dark:bg-green-900/30' : 'text-amber-700 dark:text-amber-300 bg-amber-100/50 dark:bg-amber-900/30' }}">
													<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
														<path
															d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
													</svg>
													{{ $subOrder->paid_at ? 'Paid' : 'Pending Payment' }}
												</span>
											</div>

											<!-- Amount -->
											<div class="mb-4 p-3 rounded-lg bg-white/70 dark:bg-gray-900/40">
												<p class="text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</p>
												<p class="text-lg font-bold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
													{{ number_format((float) $subOrder->amount, 2) }}</p>
											</div>

											<!-- Timeline -->
											<div class="space-y-2 mb-4 text-xs">
												<div class="flex justify-between">
													<span class="text-gray-600 dark:text-gray-400">Accepted:</span>
													<span
														class="font-medium text-gray-900 dark:text-white">{{ $subOrder->accepted_at?->format('M d, Y') ?? '—' }}</span>
												</div>
												<div class="flex justify-between">
													<span class="text-gray-600 dark:text-gray-400">Completed:</span>
													<span
														class="font-medium text-gray-900 dark:text-white">{{ $subOrder->completed_at?->format('M d, Y') ?? '—' }}</span>
												</div>
												<div class="flex justify-between">
													<span class="text-gray-600 dark:text-gray-400">Paid:</span>
													<span
														class="font-medium text-gray-900 dark:text-white">{{ $subOrder->paid_at?->format('M d, Y') ?? '—' }}</span>
												</div>
												@if ($subOrder->paid_at)
													<div class="flex justify-between">
														<span class="text-gray-600 dark:text-gray-400">Payout:</span>
														<span class="font-medium text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
															{{ number_format((float) ($subOrder->payout_amount ?? $subOrder->amount), 2) }}</span>
													</div>
													@if ($subOrder->payout_reference)
														<div class="flex justify-between gap-2">
															<span class="text-gray-600 dark:text-gray-400">Reference:</span>
															<span
																class="font-medium text-gray-900 dark:text-white truncate">{{ $subOrder->payout_reference }}</span>
														</div>
													@endif
												@endif
											</div>

											<!-- Deliverables Section -->
											@php
												$deliverables = $subOrder->deliverables ?? collect();
											@endphp
											@if ($deliverables->count() > 0)
												<div
													class="mb-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/40">
													<p class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider mb-3">
														Deliverables ({{ $deliverables->count() }})</p>
													<div class="space-y-2">
														@foreach ($deliverables as $deliverable)
															@php
																$delivStatusClass = match ($deliverable->status) {
																    'submitted' => 'bg-yellow-50 border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-900/40',
																    'approved' => 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-900/40',
																    'changes_requested' => 'bg-amber-50 border-amber-200 dark:bg-amber-900/20 dark:border-amber-900/40',
																    'rejected' => 'bg-red-50 border-red-200 dark:bg-red-900/20 dark:border-red-900/40',
																    default => 'bg-gray-50 border-gray-200 dark:bg-gray-900/20 dark:border-gray-900/40',
																};
																$delivStatusBadgeClass = match ($deliverable->status) {
																    'submitted' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
																    'approved' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
																    'changes_requested' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
																    'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
																    default => 'bg-gray-100 text-gray-700 dark:bg-gray-900/40 dark:text-gray-300',
																};
															@endphp
															<div class="border {{ $delivStatusClass }} rounded p-2">
																<div class="flex items-start justify-between gap-2 mb-2">
																	<div class="flex items-center gap-2 min-w-0">
																		<svg class="w-4 h-4 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor"
																			viewBox="0 0 20 20">
																			<path
																				d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" />
																		</svg>
																		<div class="min-w-0">
																			<p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">
																				{{ ucfirst($deliverable->deliverable_type) }}</p>
																			@if ($deliverable->file_path)
																				<p class="text-xs text-gray-500 dark:text-gray-400 truncate">
																					{{ basename($deliverable->file_path) }}</p>
																			@elseif ($deliverable->external_url)
																				<p class="text-xs text-blue-600 dark:text-blue-400 truncate"><a
																						href="{{ $deliverable->external_url }}" target="_blank" rel="noopener">View Link</a></p>
																			@endif
																		</div>
																	</div>
																	<span
																		class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $delivStatusBadgeClass }} flex-shrink-0">{{ str_replace('_', ' ', ucfirst($deliverable->status)) }}</span>
																</div>
																@if ($deliverable->notes)
																	<p class="text-xs text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $deliverable->notes }}</p>
																@endif
																<div class="text-xs text-gray-500 dark:text-gray-400 mb-2">Uploaded by
																	{{ $deliverable->uploadedBy?->name ?? 'Unknown' }} • {{ $deliverable->created_at?->format('M d, Y') }}
																</div>

																@if ($deliverable->status === 'submitted')
																	<div class="flex gap-1 flex-wrap">
																		<form action="{{ route('dashboard.sub-orders.approve-deliverable', $subOrder) }}" method="POST"
																			class="inline" style="--data-id: {{ $deliverable->id }}">
																			@csrf
																			<input type="hidden" name="deliverable_id" value="{{ $deliverable->id }}">
																			<input type="hidden" name="status" value="approved">
																			<button type="submit"
																				class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded transition">
																				Approve
																			</button>
																		</form>
																		<form action="{{ route('dashboard.sub-orders.approve-deliverable', $subOrder) }}" method="POST"
																			class="inline">
																			@csrf
																			<input type="hidden" name="deliverable_id" value="{{ $deliverable->id }}">
																			<input type="hidden" name="status" value="changes_requested">
																			<button type="submit"
																				class="px-2 py-1 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded transition">
																				Changes
																			</button>
																		</form>
																		<form action="{{ route('dashboard.sub-orders.approve-deliverable', $subOrder) }}" method="POST"
																			class="inline">
																			@csrf
																			<input type="hidden" name="deliverable_id" value="{{ $deliverable->id }}">
																			<input type="hidden" name="status" value="rejected">
																			<button type="submit"
																				class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition">
																				Reject
																			</button>
																		</form>
																	</div>
																@endif
															</div>
														@endforeach
													</div>
												</div>
											@endif

											<!-- Actions -->
											<div class="space-y-2 pt-4 border-t border-gray-200 dark:border-gray-700">
												<form method="POST" action="{{ route('dashboard.sub-orders.update-status', $subOrder) }}"
													class="space-y-2">
													@csrf
													@method('PUT')
													<label for="status_{{ $subOrder->id }}"
														class="block text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Work
														Status</label>
													<select id="status_{{ $subOrder->id }}" name="status"
														class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm text-gray-900 dark:text-white font-medium cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition">
														<option value="pending" {{ $subOrder->status === 'pending' ? 'selected' : '' }}>Pending - waiting to
															start</option>
														<option value="accepted" {{ $subOrder->status === 'accepted' ? 'selected' : '' }}>Accepted - influencer
															approved</option>
														<option value="in_progress" {{ $subOrder->status === 'in_progress' ? 'selected' : '' }}>In Progress -
															work ongoing</option>
														<option value="delivered" {{ $subOrder->status === 'delivered' ? 'selected' : '' }}>Delivered - work
															submitted for review</option>
														<option value="on_review" {{ $subOrder->status === 'on_review' ? 'selected' : '' }}>On Review - admin
															reviewing</option>
														<option value="changes_requested" {{ $subOrder->status === 'changes_requested' ? 'selected' : '' }}>
															Changes Requested - needs revision</option>
														<option value="approved" {{ $subOrder->status === 'approved' ? 'selected' : '' }}>Approved - ready for
															payout</option>
														<option value="completed" {{ $subOrder->status === 'completed' ? 'selected' : '' }}>Completed - work
															finished</option>
														<option value="review_pending" {{ $subOrder->status === 'review_pending' ? 'selected' : '' }}>Review
															Pending - awaiting feedback</option>
														<option value="reviewed" {{ $subOrder->status === 'reviewed' ? 'selected' : '' }}>Reviewed - both
															parties completed</option>
														<option value="cancelled" {{ $subOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
													</select>
													<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
														<label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
															<input type="checkbox" name="force_transition" value="1"
																class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
															Force transition
														</label>
														<input type="text" name="transition_note" maxlength="1000" placeholder="Reason (required if force)"
															class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
													</div>
													<button type="submit"
														class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition">
														Save Work Status
													</button>
												</form>

												@if (!$subOrder->paid_at)
													<form method="POST" action="{{ route('dashboard.sub-orders.mark-paid', $subOrder) }}" class="block">
														@csrf
														<div class="grid grid-cols-1 gap-2 mb-2">
															<input type="number" name="amount" min="0.01" step="0.01"
																value="{{ number_format((float) $subOrder->amount, 2, '.', '') }}" required
																placeholder="Payout amount"
																class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-gray-900 dark:text-white">
															<input type="text" name="payout_reference" maxlength="120" placeholder="Reference (optional)"
																class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-gray-900 dark:text-white">
															<input type="text" name="payout_note" maxlength="1000" placeholder="Note (optional)"
																class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-xs text-gray-900 dark:text-white">
														</div>
														<button type="submit"
															class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition">
															<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
																<path fill-rule="evenodd"
																	d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
																	clip-rule="evenodd" />
															</svg>
															Mark Paid
														</button>
													</form>
												@else
													<div
														class="w-full px-3 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold rounded-lg text-center">
														✓ Paid {{ $subOrder->paid_at->format('M d') }}
													</div>
												@endif
											</div>
										</div>
									@endforeach
								</div>
							@endif
						</div>
					</div>
				@endif
				<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
					<div class="border-b border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-gray-800/50">
						<h3 class="text-base font-semibold text-gray-900 dark:text-white">Payment History</h3>
					</div>
					<div class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse ($order->payments as $payment)
							@php
								$paymentStatusClass =
								    $payment->status === 'paid'
								        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
								        : ($payment->status === 'failed'
								            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
								            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300');
							@endphp
							<div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white">
										{{ strtoupper($payment->currency ?? $order->currency) }} {{ number_format((float) $payment->amount, 2) }}
									</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($payment->payment_provider ?? 'manual') }}
										• {{ optional($payment->paid_at ?? $payment->created_at)->format('M d, Y h:i A') }}</p>
								</div>
								<span
									class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $paymentStatusClass }}">{{ ucfirst($payment->status ?? 'pending') }}</span>
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
							<label for="order_status"
								class="block text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order
								Status</label>
							<p class="text-xs text-gray-500 dark:text-gray-400">This updates the master order stage:
								{{ $orderScopeLabel }}.</p>
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ $itemStatusHint }}</p>
							<select id="order_status" name="status"
								class="h-10 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm font-medium text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
								<option value="pending" @selected($order->status === 'pending')>Pending</option>
								<option value="accepted" @selected($order->status === 'accepted')>Accepted</option>
								<option value="in_progress" @selected(in_array($order->status, ['in_progress', 'in-progress']))>In Progress</option>
								<option value="delivered" @selected($order->status === 'delivered')>Delivered</option>
								<option value="completed" @selected($order->status === 'completed')>Completed</option>
								<option value="cancelled" @selected($order->status === 'cancelled')>Cancelled</option>
							</select>
							<div class="grid grid-cols-1 gap-2">
								<label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
									<input type="checkbox" name="force_transition" value="1"
										class="rounded border-gray-300 text-amber-600 focus:ring-amber-500">
									Force transition (admin override)
								</label>
								<input type="text" name="transition_note" maxlength="1000" placeholder="Reason (required if force)"
									class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
							</div>
							<button type="submit"
								class="inline-flex h-10 w-full items-center justify-center gap-1 rounded-lg bg-blue-600 px-4 text-sm font-semibold text-white transition hover:bg-blue-700"
								title="Save overall order stage">
								<x-icons.check class="h-4 w-4" />Save Order Stage
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
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
								{{ number_format($subtotal, 2) }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Platform Charge (20%)</span>
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
								{{ number_format($serviceFee, 2) }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-gray-600 dark:text-gray-400">Tax</span>
							<span class="font-semibold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
								{{ number_format($taxAmount, 2) }}</span>
						</div>
						<div class="border-t border-gray-200 pt-2 dark:border-gray-700">
							<div class="flex items-center justify-between">
								<span class="font-semibold text-gray-900 dark:text-white">Total</span>
								<span class="text-base font-bold text-gray-900 dark:text-white">{{ strtoupper($order->currency) }}
									{{ number_format($totalAmount, 2) }}</span>
							</div>
						</div>
						<div class="pt-1 text-xs text-gray-500 dark:text-gray-400">Paid via payment logs:
							{{ strtoupper($order->currency) }} {{ number_format($totalPaidAmount, 2) }}</div>
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
							@if ($orderType === 'Campaign')
								<p class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $order->campaign?->title ?? 'Campaign order' }}
								</p>
							@else
								<p class="mt-1 font-semibold text-gray-900 dark:text-white">Not Applicable</p>
							@endif
						</div>
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Source</p>
							<p class="mt-1 font-semibold text-gray-900 dark:text-white">
								{{ $orderType === 'Package' ? 'Package Purchase' : ($orderType === 'Campaign' ? 'Campaign Order' : 'General Order') }}
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
