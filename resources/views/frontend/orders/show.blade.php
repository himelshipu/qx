@extends('frontend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	@php
		$statusStyles = [
		    'pending' => ['badge' => 'bg-amber-100 text-amber-700', 'dot' => 'bg-amber-500'],
		    'accepted' => ['badge' => 'bg-sky-100 text-sky-700', 'dot' => 'bg-sky-500'],
		    'in-progress' => ['badge' => 'bg-violet-100 text-violet-700', 'dot' => 'bg-violet-500'],
		    'in_progress' => ['badge' => 'bg-violet-100 text-violet-700', 'dot' => 'bg-violet-500'],
		    'delivered' => ['badge' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500'],
		    'approved' => ['badge' => 'bg-teal-100 text-teal-700', 'dot' => 'bg-teal-500'],
		    'completed' => ['badge' => 'bg-emerald-100 text-emerald-700', 'dot' => 'bg-emerald-500'],
		    'cancelled' => ['badge' => 'bg-rose-100 text-rose-700', 'dot' => 'bg-rose-500'],
		];

		$role = auth()->user()->user_type;
		$isBrand = $role === 'brand';
		$isInfluencer = $role === 'influencer';
		$isParentOrder = $orderContext['is_parent'];
		$brandSlug = $order->buyer?->slug;
		$brandLabel = $order->brand?->brand_name ?: $order->buyer?->name ?: 'Brand';
		$brandInitial = strtoupper(mb_substr((string) $brandLabel, 0, 1));

		$displayItems = $order->items;
		if ($displayItems->isEmpty() && $order->childOrders->isNotEmpty()) {
		    $displayItems = $order->childOrders->flatMap(fn($child) => $child->items)->values();
		}

		$displayOrderStatus = $order->status;
		$displayStatuses = $displayItems->pluck('status');
		if ($displayStatuses->isNotEmpty()) {
		    if ($displayStatuses->every(fn($status) => $status === 'pending')) {
		        $displayOrderStatus = 'pending';
		    } elseif ($displayStatuses->every(fn($status) => $status === 'accepted')) {
		        $displayOrderStatus = 'accepted';
		    } elseif ($displayStatuses->every(fn($status) => in_array($status, ['approved', 'completed'], true))) {
		        $displayOrderStatus = 'approved';
		    } elseif (
		        $displayStatuses->every(fn($status) => in_array($status, ['delivered', 'approved', 'completed'], true))
		    ) {
		        $displayOrderStatus = 'delivered';
		    } else {
		        $displayOrderStatus = 'in_progress';
		    }
		}

		$currentStatus = $statusStyles[$displayOrderStatus] ?? [
		    'badge' => 'bg-gray-100 text-gray-700',
		    'dot' => 'bg-gray-400',
		];
		$orderIsCompleted = $order->status === 'completed' || $order->completed_at !== null;
		$brandCanComplete =
		    $isBrand &&
		    $isParentOrder &&
		    $displayItems->isNotEmpty() &&
		    $displayItems->every(fn($item) => in_array($item->status, ['approved', 'completed'], true));

		$brandInfo = $order->buyer
		    ? [
		        'name' => $brandLabel,
		        'slug' => $brandSlug,
		        'email' => $order->buyer->email,
		    ]
		    : null;
	@endphp

	<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
		<div class="mx-auto max-w-full space-y-6">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
				<div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
					<div class="space-y-2">
						<div class="flex flex-wrap items-center gap-2">
							<span
								class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $currentStatus['badge'] }}">
								<span class="h-2 w-2 rounded-full {{ $currentStatus['dot'] }}"></span>
								{{ ucfirst(str_replace(['-', '_'], ' ', $displayOrderStatus)) }}
							</span>
							<span
								class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
								{{ $isParentOrder ? 'Parent Order' : 'Child Order' }}
							</span>
						</div>
						<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order {{ $order->order_number }}</h1>
						<p class="text-sm text-gray-600 dark:text-gray-400">Placed
							{{ optional($order->placed_at ?? $order->created_at)->format('M d, Y \a\t h:i A') }}</p>
						@if ($isInfluencer && $order->parentOrder)
							<p class="text-sm text-gray-600 dark:text-gray-400">Parent checkout: <span
									class="font-semibold text-gray-900 dark:text-white">{{ $order->parentOrder->order_number }}</span></p>
						@endif
					</div>

					<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
						<div
							class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900/40 dark:bg-emerald-900/20">
							<div class="flex items-start justify-between gap-3">
								<div>
									<p class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Items</p>
									<p class="mt-1 text-lg font-semibold text-emerald-900 dark:text-emerald-50">{{ $displayItems->count() }}</p>
								</div>
								<svg class="h-5 w-5 text-emerald-500 dark:text-emerald-300" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M4 19h16M6 9h12M7 13h10"></path>
								</svg>
							</div>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/70">
							<div class="flex items-start justify-between gap-3">
								<div>
									<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencers</p>
									<p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
										{{ $displayItems->pluck('influencer_id')->filter()->unique()->count() }}</p>
								</div>
								<svg class="h-5 w-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17 20h5v-1a4 4 0 00-4-4h-1m-4-3a4 4 0 100-8 4 4 0 000 8zm-6 8v-1a4 4 0 014-4h4a4 4 0 014 4v1"></path>
								</svg>
							</div>
						</div>
						<div
							class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-900/40 dark:bg-indigo-900/20">
							<div class="flex items-start justify-between gap-3">
								<div>
									<p class="text-xs uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Subtotal</p>
									<p class="mt-1 text-lg font-semibold text-indigo-900 dark:text-indigo-50">
										${{ number_format((float) $order->subtotal, 2) }}</p>
								</div>
								<svg class="h-5 w-5 text-indigo-500 dark:text-indigo-300" fill="none" stroke="currentColor"
									viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M12 8c-1.105 0-2 .672-2 1.5S10.895 11 12 11s2 .672 2 1.5S13.105 14 12 14m0-6V6m0 12v-2m8-4a8 8 0 11-16 0 8 8 0 0116 0z">
									</path>
								</svg>
							</div>
						</div>
						@if ($isBrand)
							<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/70">
								<div class="flex items-start justify-between gap-3">
									<div>
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
										<p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
											${{ number_format((float) $order->total_amount, 2) }}</p>
									</div>
									<svg class="h-5 w-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l4-4 3 3 5-6"></path>
									</svg>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>

			@if ($isBrand)
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
					<div class="space-y-6 lg:col-span-2">
						<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
							<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
								<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
									<div>
										<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Main Order Summary</h2>
										<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">One checkout grouped by influencer child orders.</p>
									</div>
									<div class="rounded-xl bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-700">
										{{ $order->childOrders->count() }} child {{ $order->childOrders->count() === 1 ? 'order' : 'orders' }}</div>
								</div>
							</div>
							<div class="p-5 sm:p-6">
								<div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
									<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Platform Charge</p>
										<p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
											${{ number_format((float) $order->service_fee, 2) }}</p>
									</div>
									<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand Total</p>
										<p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
											${{ number_format((float) $order->total_amount, 2) }}</p>
									</div>
									<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
										<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Completion Rule</p>
										<p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Complete after all child items are
											delivered and approved.</p>
									</div>
								</div>

								<div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
									<div class="mb-3 flex items-center justify-between gap-3">
										<h3 class="text-sm font-semibold text-gray-900 dark:text-white">Compact Timeline</h3>
										<p class="text-xs text-gray-500 dark:text-gray-400">Child orders resolve before closing the parent checkout.
										</p>
									</div>
									<div class="flex items-stretch gap-2 overflow-x-auto pb-1">
										@foreach ($timeline as $step)
											@php $done = $step['state'] === 'done'; @endphp
											<div class="flex items-center gap-2 shrink-0">
												<div
													class="rounded-xl border px-3 py-2 {{ $done ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white' }}">
													<div
														class="flex items-center gap-2 text-xs font-semibold {{ $done ? 'text-emerald-700' : 'text-gray-500' }}">
														<span class="h-2 w-2 rounded-full {{ $done ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
														{{ $step['label'] }}
													</div>
													<p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">{{ $step['value'] }}</p>
												</div>
												@if (!$loop->last)
													<svg class="h-4 w-4 shrink-0 self-center text-gray-400" fill="none" stroke="currentColor"
														viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14"></path>
													</svg>
												@endif
											</div>
										@endforeach
									</div>
								</div>
							</div>
						</div>

						<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
							<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
								<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Child Orders</h2>
								<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Expand a child order to see its packages, profile links
									and messages.</p>
							</div>
							<div class="space-y-3 p-4 sm:p-6">
								@forelse ($order->childOrders as $childOrder)
									@php
										$childInfluencer = $childOrder->acceptedForInfluencer;
										$childItems = $childOrder->items;
										$childItemStatuses = $childItems->pluck('status');
										$computedChildStatus = $childOrder->status;
										if ($childItemStatuses->isNotEmpty()) {
										    if ($childItemStatuses->every(fn($status) => $status === 'pending')) {
										        $computedChildStatus = 'pending';
										    } elseif ($childItemStatuses->every(fn($status) => $status === 'accepted')) {
										        $computedChildStatus = 'accepted';
										    } elseif ($childItemStatuses->every(fn($status) => in_array($status, ['approved', 'completed'], true))) {
										        $computedChildStatus = 'approved';
										    } elseif (
										        $childItemStatuses->every(
										            fn($status) => in_array($status, ['delivered', 'approved', 'completed'], true),
										        )
										    ) {
										        $computedChildStatus = 'delivered';
										    } else {
										        $computedChildStatus = 'in_progress';
										    }
										}
										$childStatus = $statusStyles[$computedChildStatus] ?? [
										    'badge' => 'bg-gray-100 text-gray-700',
										    'dot' => 'bg-gray-400',
										];
										$conversation = $orderContext['conversation_by_influencer']->get($childInfluencer?->id);
										$influencerName = $childInfluencer?->display_name ?: $childInfluencer?->user?->name ?: 'Influencer';
										$latestDeliveredAt = $childItems->pluck('delivered_at')->filter()->sortDesc()->first();
										$latestApprovedAt = $childItems->pluck('approved_at')->filter()->sortDesc()->first();
										$hasRejectedItem = $childItemStatuses->contains('rejected');
										$timelineSteps = [
										    ['label' => 'Placed', 'value' => $childOrder->placed_at?->format('M d g:iA') ?? '—', 'done' => true],
										    [
										        'label' => 'Accepted',
										        'value' => $childOrder->accepted_at?->format('M d g:iA') ?? 'Waiting',
										        'done' =>
										            (bool) $childOrder->accepted_at ||
										            in_array($computedChildStatus, ['accepted', 'in_progress', 'delivered', 'completed'], true),
										    ],
										    [
										        'label' => 'Delivered',
										        'value' => $latestDeliveredAt?->format('M d g:iA') ?? 'Waiting',
										        'done' => in_array($computedChildStatus, ['delivered', 'completed'], true),
										    ],
										    [
										        'label' => 'Reviewed',
										        'value' => $hasRejectedItem ? 'Rejected' : $latestApprovedAt?->format('M d g:iA') ?? 'Waiting',
										        'done' => $hasRejectedItem || in_array($computedChildStatus, ['approved', 'completed'], true),
										    ],
										    [
										        'label' => 'Completed',
										        'value' => $childOrder->completed_at?->format('M d g:iA') ?? 'Pending',
										        'done' => $computedChildStatus === 'completed',
										    ],
										];
									@endphp
									<details data-child-order-details="{{ $childOrder->id }}"
										class="group rounded-xl border border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-gray-900/40">
										<summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 sm:px-5">
											<div class="min-w-0">
												<div class="flex flex-wrap items-center gap-2">
													<span class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $influencerName }}</span>
													<span
														class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $childStatus['badge'] }}">
														<span class="h-2 w-2 rounded-full {{ $childStatus['dot'] }}"></span>
														{{ ucfirst(str_replace('_', ' ', $computedChildStatus)) }}
													</span>
												</div>
												<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $childOrder->order_number }} •
													{{ $childItems->count() }} item{{ $childItems->count() !== 1 ? 's' : '' }} •
													${{ number_format((float) $childOrder->total_amount, 2) }}</p>
											</div>
											<svg class="h-5 w-5 text-gray-500 transition-transform duration-200 group-open:rotate-180" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
											</svg>
										</summary>
										<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800 sm:px-5">
											<div class="flex flex-wrap gap-2">
												@if ($childInfluencer?->user?->slug)
													<a href="{{ route('influencer.profile', ['slug' => $childInfluencer->user->slug]) }}"
														class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
														</svg>
														View influencer profile
													</a>
												@endif
												@if ($conversation)
													<a href="{{ route('frontend.conversations.show', $conversation->public_id) }}"
														class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z">
															</path>
														</svg>
														Open messages
													</a>
												@elseif ($childInfluencer)
													<a
														href="{{ route('frontend.conversations.open-order', ['influencer' => $childInfluencer, 'order' => $childOrder]) }}"
														class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z">
															</path>
														</svg>
														Start messages
													</a>
												@endif
											</div>

											<div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
												@foreach ($childItems as $item)
													@php
														$itemStatus = $statusStyles[$item->status] ?? [
														    'badge' => 'bg-gray-100 text-gray-700',
														    'dot' => 'bg-gray-400',
														];
														$itemDueDate = $item->due_date;
														if (!$itemDueDate && $item->package?->delivery_days !== null && $childOrder->placed_at) {
														    $itemDueDate = $childOrder->placed_at->copy()->addDays((int) $item->package->delivery_days);
														}
														$itemTimeline = [
														    ['label' => 'Placed', 'value' => $item->created_at?->format('M d g:iA') ?? '—', 'done' => true],
														    [
														        'label' => 'Accepted',
														        'value' => $item->accepted_at?->format('M d g:iA') ?? 'Waiting',
														        'done' =>
														            (bool) $item->accepted_at ||
														            in_array(
														                $item->status,
														                ['accepted', 'in_progress', 'in-progress', 'delivered', 'approved'],
														                true,
														            ),
														    ],
														    [
														        'label' => 'Delivered',
														        'value' => $item->delivered_at?->format('M d g:iA') ?? 'Waiting',
														        'done' => in_array($item->status, ['delivered', 'approved'], true) || (bool) $item->delivered_at,
														    ],
														];
													@endphp
													<div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-900">
														<div class="flex items-start justify-between gap-3">
															<div class="min-w-0">
																<p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $item->title }}</p>
																<p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $item->package?->name ?? 'Package' }}</p>
															</div>
															<span
																class="inline-flex items-center gap-2 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $itemStatus['badge'] }}">
																<span class="h-1.5 w-1.5 rounded-full {{ $itemStatus['dot'] }}"></span>
																{{ ucfirst(str_replace('_', ' ', $item->status)) }}
															</span>
														</div>
														<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Qty {{ $item->quantity }} • Due
															{{ $itemDueDate ? $itemDueDate->format('M d, Y') : 'Not set' }} •
															${{ number_format((float) $item->line_total, 2) }}</p>
														<div class="mt-3 flex items-stretch gap-2 overflow-x-auto pb-1">
															@foreach ($itemTimeline as $timelineStep)
																<div class="flex items-center gap-2 shrink-0">
																	<div
																		class="rounded-lg border px-2.5 py-2 {{ $timelineStep['done'] ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white' }}">
																		<div
																			class="flex items-center gap-1 text-[11px] font-semibold {{ $timelineStep['done'] ? 'text-emerald-700' : 'text-gray-500' }}">
																			<span
																				class="h-1.5 w-1.5 rounded-full {{ $timelineStep['done'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
																			{{ $timelineStep['label'] }}
																		</div>
																		<p class="mt-1 text-[10px] text-gray-500 dark:text-gray-400">{{ $timelineStep['value'] }}</p>
																	</div>
																	@if (!$loop->last)
																		<svg class="h-4 w-4 shrink-0 self-center text-gray-400" fill="none" stroke="currentColor"
																			viewBox="0 0 24 24">
																			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14">
																			</path>
																		</svg>
																	@endif
																</div>
															@endforeach
														</div>
														@if ($item->status === 'delivered')
															<div class="mt-3 grid grid-cols-2 gap-2">
																<form method="POST"
																	action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $item]) }}">
																	@csrf
																	@method('PUT')
																	<input type="hidden" name="status" value="approved">
																	<button type="submit"
																		class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Approve
																		Work</button>
																</form>
																<form method="POST"
																	action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $item]) }}">
																	@csrf
																	@method('PUT')
																	<input type="hidden" name="status" value="rejected">
																	<button type="submit"
																		class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">Reject
																		Work</button>
																</form>
															</div>
														@elseif ($item->status === 'rejected')
															<div class="mt-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2">
																<p class="text-xs font-semibold text-rose-700">✓ Work Rejected</p>
																<p class="mt-1 text-xs text-rose-600">Awaiting influencer resubmission</p>
															</div>
														@elseif ($item->status === 'approved' || $item->status === 'completed')
															@if ($item->brandToInfluencerReview)
																<div class="mt-3 rounded-lg border border-teal-200 bg-teal-50 px-3 py-3 text-xs text-teal-800">
																	<p class="font-semibold">Task review submitted</p>
																	<p class="mt-1">Rating: {{ $item->brandToInfluencerReview->rating }}/5</p>
																	@if ($item->brandToInfluencerReview->title)
																		<p class="mt-1 font-medium">{{ $item->brandToInfluencerReview->title }}</p>
																	@endif
																	@if ($item->brandToInfluencerReview->comment)
																		<p class="mt-1">{{ $item->brandToInfluencerReview->comment }}</p>
																	@endif
																</div>
															@else
																<form method="POST"
																	action="{{ route('frontend.orders.items.reviews.store', ['order' => $order, 'item' => $item]) }}"
																	class="mt-3 space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
																	@csrf
																	<p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Review this influencer task</p>
																	<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
																		<select name="rating"
																			class="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
																			required>
																			<option value="">Rating</option>
																			@for ($r = 5; $r >= 1; $r--)
																				<option value="{{ $r }}">{{ $r }} star{{ $r === 1 ? '' : 's' }}</option>
																			@endfor
																		</select>
																		<input type="text" name="title" maxlength="120" placeholder="Title (optional)"
																			class="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
																	</div>
																	<textarea name="comment" rows="2" maxlength="1200" placeholder="Comment (optional)"
																	 class="w-full rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
																	<button type="submit"
																		class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Submit
																		Task Review</button>
																</form>
															@endif
														@endif
													</div>
												@endforeach
											</div>
										</div>

									</details>
								@empty
									<div
										class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
										No child orders found for this checkout.</div>
								@endforelse
							</div>
						</div>

						<!-- Campaign Orders Section -->
						<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
							<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
								<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Campaign Orders</h2>
								<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Expand a campaign order to see its details, profile
									links and messages.</p>
							</div>
							<div class="space-y-3 p-4 sm:p-6">
								@forelse ($order->subOrders as $subOrder)
									@php
										$campaign = $subOrder->order->campaign ?? null;
										$influencer = $subOrder->influencer;
										$campaignStatusMap = [
										    'pending' => [
										        'badge' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
										        'dot' => 'bg-yellow-400',
										    ],
										    'accepted' => [
										        'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
										        'dot' => 'bg-blue-400',
										    ],
										    'in_progress' => [
										        'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
										        'dot' => 'bg-indigo-400',
										    ],
										    'delivered' => [
										        'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
										        'dot' => 'bg-amber-400',
										    ],
										    'on_review' => [
										        'badge' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
										        'dot' => 'bg-orange-400',
										    ],
										    'approved' => [
										        'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
										        'dot' => 'bg-emerald-400',
										    ],
										    'rejected' => [
										        'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
										        'dot' => 'bg-rose-400',
										    ],
										    'completed' => [
										        'badge' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
										        'dot' => 'bg-green-400',
										    ],
										];
										$campaignStatus = $campaignStatusMap[$subOrder->status] ?? [
										    'badge' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
										    'dot' => 'bg-gray-400',
										];
										$campaignConversation = $order->conversations()->where('handled_by_user_id', $influencer->id)->first();
									@endphp
									<details
										class="group rounded-xl border border-gray-200 bg-gray-50/70 dark:border-gray-800 dark:bg-gray-900/40">
										<summary
											class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 hover:bg-gray-100/50 dark:hover:bg-gray-800/50 sm:px-5">
											<div class="min-w-0">
												<div class="flex flex-wrap items-center gap-2">
													<span
														class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $influencer->user->name ?? $influencer->display_name }}</span>
													<span
														class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $campaignStatus['badge'] }}">
														<span class="h-2 w-2 rounded-full {{ $campaignStatus['dot'] }}"></span>
														{{ ucfirst(str_replace('_', ' ', $subOrder->status)) }}
													</span>
												</div>
												<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $campaign->title ?? 'Campaign' }} • 1 item •
													${{ number_format((float) $subOrder->amount, 2) }}</p>
											</div>
											<svg class="h-5 w-5 text-gray-500 transition-transform duration-200 group-open:rotate-180" fill="none"
												stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
											</svg>
										</summary>
										<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800 sm:px-5">
											<div class="flex flex-wrap gap-2">
												@if ($influencer->user?->slug)
													<a href="{{ route('influencer.profile', ['slug' => $influencer->user->slug]) }}"
														class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
														</svg>
														View influencer profile
													</a>
												@endif
												@if ($campaignConversation)
													<a href="{{ route('frontend.conversations.show', $campaignConversation->public_id) }}"
														class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z">
															</path>
														</svg>
														Open messages
													</a>
												@elseif ($influencer)
													<a
														href="{{ route('frontend.conversations.open-order', ['influencer' => $influencer, 'order' => $order]) }}"
														class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
														<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-4l-4 4v-4z">
															</path>
														</svg>
														Start messages
													</a>
												@endif
											</div>

											<div class="mt-4 space-y-3">
												<div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800/50">
													<div class="flex items-start justify-between">
														<div>
															<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campaign->title ?? 'Campaign' }}</p>
														</div>
														<span
															class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $campaignStatus['badge'] }}">
															<span class="h-2 w-2 rounded-full {{ $campaignStatus['dot'] }}"></span>
														</span>
													</div>
													<p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
														{{ $order->currency === 'USD' ? '$' : '' }}{{ number_format((float) $subOrder->amount, 2) }}</p>

													@if ($subOrder->status === 'on_review')
														<div class="mt-3 grid grid-cols-2 gap-2">
															<form method="POST"
																action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $subOrder]) }}">
																@csrf
																@method('PUT')
																<input type="hidden" name="status" value="approved">
																<button type="submit"
																	class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Approve
																	Work</button>
															</form>
															<form method="POST"
																action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $subOrder]) }}">
																@csrf
																@method('PUT')
																<input type="hidden" name="status" value="rejected">
																<button type="submit"
																	class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">Reject
																	Work</button>
															</form>
														</div>
													@elseif ($subOrder->status === 'rejected')
														<div class="mt-3 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2">
															<p class="text-xs font-semibold text-rose-700">✓ Work Rejected</p>
															<p class="mt-1 text-xs text-rose-600">Awaiting influencer resubmission</p>
														</div>
													@elseif ($subOrder->status === 'approved' || $subOrder->status === 'completed')
														@if ($subOrder->review)
															<div class="mt-3 rounded-lg border border-teal-200 bg-teal-50 px-3 py-3 text-xs text-teal-800">
																<p class="font-semibold">Campaign review submitted</p>
																<p class="mt-1">Rating: {{ $subOrder->review->rating }}/5</p>
																@if ($subOrder->review->title)
																	<p class="mt-1 font-medium">{{ $subOrder->review->title }}</p>
																@endif
																@if ($subOrder->review->comment)
																	<p class="mt-1">{{ $subOrder->review->comment }}</p>
																@endif
															</div>
														@else
															<form method="POST"
																action="{{ route('frontend.orders.items.reviews.store', ['order' => $order, 'item' => $subOrder]) }}"
																class="mt-3 space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
																@csrf
																<p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Review this campaign assignment</p>
																<div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
																	<select name="rating"
																		class="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"
																		required>
																		<option value="">Rating</option>
																		@for ($r = 5; $r >= 1; $r--)
																			<option value="{{ $r }}">{{ $r }} star{{ $r === 1 ? '' : 's' }}</option>
																		@endfor
																	</select>
																	<input type="text" name="title" maxlength="120" placeholder="Title (optional)"
																		class="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100">
																</div>
																<textarea name="comment" rows="2" maxlength="1200" placeholder="Comment (optional)"
																 class="w-full rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs text-gray-900 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100"></textarea>
																<button type="submit"
																	class="w-full rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500">Submit
																	Campaign Review</button>
															</form>
														@endif
													@endif
												</div>
											</div>
										</div>
									</details>
								@empty
									<div
										class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
										No campaign orders found for this checkout.</div>
								@endforelse
							</div>
						</div>
					</div>

					<div class="space-y-6 lg:col-span-1">
						@if ($brandInfo)
							<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
								<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
									<h3 class="font-semibold text-gray-900 dark:text-white">Brand Info</h3>
								</div>
								<div class="p-6 space-y-3 text-sm text-gray-600 dark:text-gray-400">
									<div class="flex items-center gap-4">
										<div
											class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-sm font-bold text-indigo-700">
											{{ $brandInitial }}</div>
										<div class="min-w-0">
											<p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $brandInfo['name'] }}</p>
											<p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $brandInfo['email'] }}</p>
										</div>
									</div>
									@if ($brandSlug && !$isBrand)
										<a href="{{ route('brand.profile', ['slug' => $brandSlug]) }}"
											class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
											</svg>
											View brand profile
										</a>
									@endif
									<p>Close the parent order only after every child item is approved.</p>
									@if ($orderIsCompleted)
										<p
											class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
											Order already completed.</p>
									@elseif ($brandCanComplete)
										<form method="POST" action="{{ route('frontend.orders.complete', $order) }}">
											@csrf
											@method('PUT')
											<button type="submit"
												class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-500">Complete
												Order</button>
										</form>
									@else
										<p
											class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
											Completion becomes available after all child items are approved.</p>
									@endif
									<a href="{{ route('frontend.orders.index') }}"
										class="block rounded-lg bg-gray-100 px-4 py-2 text-center font-semibold text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">Back
										to Orders</a>
								</div>
							</div>
						@endif
					</div>
			@endif

			@if ($isInfluencer)
				@php
					$influencerItems = $order->items->where('influencer_id', auth()->user()->influencer?->id)->values();
				@endphp
				<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
					<div class="space-y-6 lg:col-span-2">
						<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
							<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
								<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Work Items</h2>
								<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update each task as you progress. Completed stays with
									the brand or admin.</p>
							</div>
							<div class="space-y-4 p-4 sm:p-6">
								@forelse ($influencerItems as $item)
									@php
										$itemStatus = $statusStyles[$item->status] ?? [
										    'badge' => 'bg-gray-100 text-gray-700',
										    'dot' => 'bg-gray-400',
										];
										$itemDueDate = $item->due_date;
										if (!$itemDueDate && $item->package?->delivery_days !== null && $order->placed_at) {
										    $itemDueDate = $order->placed_at->copy()->addDays((int) $item->package->delivery_days);
										}
										$itemTimeline = [
										    ['label' => 'Placed', 'value' => $item->created_at?->format('M d g:iA') ?? '—', 'done' => true],
										    [
										        'label' => 'Accepted',
										        'value' => $item->accepted_at?->format('M d g:iA') ?? 'Waiting',
										        'done' =>
										            (bool) $item->accepted_at ||
										            in_array(
										                $item->status,
										                ['accepted', 'in_progress', 'in-progress', 'delivered', 'approved'],
										                true,
										            ),
										    ],
										    [
										        'label' => 'Delivered',
										        'value' => $item->delivered_at?->format('M d g:iA') ?? 'Waiting',
										        'done' => in_array($item->status, ['delivered', 'approved'], true) || (bool) $item->delivered_at,
										    ],
										];
										$isInfluencerStatusLocked = in_array($item->status, ['approved', 'completed', 'cancelled'], true);
									@endphp
									<div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
										<div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
											<div class="min-w-0">
												<div class="flex flex-wrap items-center gap-2">
													<h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
													<span
														class="inline-flex items-center gap-2 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $itemStatus['badge'] }}">
														<span class="h-2 w-2 rounded-full {{ $itemStatus['dot'] }}"></span>
														{{ ucfirst(str_replace('_', ' ', $item->status)) }}
													</span>
												</div>
												<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Package: <span
														class="font-medium text-gray-900 dark:text-white">{{ $item->package?->name ?? 'N/A' }}</span></p>
												<div class="mt-2 grid grid-cols-2 gap-3 text-xs text-gray-500 dark:text-gray-400 sm:grid-cols-4">
													<p>Qty: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $item->quantity }}</span></p>
													<p>Subtotal: <span
															class="font-semibold text-gray-700 dark:text-gray-300">${{ number_format((float) $item->line_total, 2) }}</span>
													</p>
													<p>Due: <span
															class="font-semibold text-gray-700 dark:text-gray-300">{{ $itemDueDate ? $itemDueDate->format('M d, Y') : 'Not set' }}</span>
													</p>
													<p>Order: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $order->order_number }}</span>
													</p>
												</div>
												<div class="mt-3 flex items-stretch gap-2 overflow-x-auto pb-1">
													@foreach ($itemTimeline as $timelineStep)
														<div class="flex items-center gap-2 shrink-0">
															<div
																class="rounded-lg border px-2.5 py-2 {{ $timelineStep['done'] ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white' }}">
																<div
																	class="flex items-center gap-1 text-[11px] font-semibold {{ $timelineStep['done'] ? 'text-emerald-700' : 'text-gray-500' }}">
																	<span
																		class="h-1.5 w-1.5 rounded-full {{ $timelineStep['done'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
																	{{ $timelineStep['label'] }}
																</div>
																<p class="mt-1 text-[10px] text-gray-500 dark:text-gray-400">{{ $timelineStep['value'] }}</p>
															</div>
															@if (!$loop->last)
																<svg class="h-4 w-4 shrink-0 self-center text-gray-400" fill="none" stroke="currentColor"
																	viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 12h14">
																	</path>
																</svg>
															@endif
														</div>
													@endforeach
												</div>
											</div>
											@if ($isInfluencerStatusLocked)
												<div
													class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 lg:w-56">
													<p class="font-semibold">Task status locked</p>
													<p class="mt-1">Brand already {{ $item->status === 'approved' ? 'approved' : 'finalized' }} this task.
													</p>
												</div>
											@else
												<form method="POST"
													action="{{ route('frontend.orders.items.update-status', ['order' => $order, 'item' => $item]) }}"
													class="w-full lg:w-56">
													@csrf
													@method('PUT')
													<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Update task status</label>
													<select name="status"
														class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
														required>
														<option value="pending" @selected($item->status === 'pending')>Pending</option>
														<option value="accepted" @selected($item->status === 'accepted')>Accepted</option>
														<option value="in_progress" @selected(in_array($item->status, ['in_progress', 'in-progress'], true))>In Progress</option>
														<option value="delivered" @selected(in_array($item->status, ['delivered', 'rejected'], true))>Delivered (Awaiting approval)</option>
													</select>
													<button type="submit"
														class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save
														status</button>
												</form>
											@endif
										</div>
									</div>
								@empty
									<div
										class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
										No assigned tasks in this order.</div>
								@endforelse
							</div>
						</div>
					</div>

					<div class="space-y-6 lg:col-span-1">
						@if ($brandInfo)
							<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
								<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
									<h3 class="font-semibold text-gray-900 dark:text-white">Brand Info</h3>
								</div>
								<div class="p-6">
									<div class="flex items-center gap-4">
										<div
											class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-sm font-bold text-indigo-700">
											{{ $brandInitial }}</div>
										<div class="min-w-0">
											<p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $brandInfo['name'] }}</p>
											<p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $brandInfo['email'] }}</p>
										</div>
									</div>
									@if ($brandSlug)
										<a href="{{ route('brand.profile', ['slug' => $brandSlug]) }}"
											class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
											</svg>
											View brand profile
										</a>
									@endif
								</div>
							</div>
						@endif

						<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
							<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
								<h3 class="font-semibold text-gray-900 dark:text-white">Review Brand</h3>
							</div>
							<div class="space-y-3 p-6 text-sm text-gray-600 dark:text-gray-400">
								@if ($canLeaveReview)
									<form method="POST" action="{{ route('frontend.orders.reviews.store', $order) }}" class="space-y-3">
										@csrf
										<div>
											<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Rating</label>
											<select name="rating"
												class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
												required>
												@for ($i = 5; $i >= 1; $i--)
													<option value="{{ $i }}">{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
												@endfor
											</select>
										</div>
										<div>
											<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Title</label>
											<input type="text" name="title"
												class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
												placeholder="Short review title">
										</div>
										<div>
											<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Comment</label>
											<textarea name="comment" rows="4"
											 class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
											 placeholder="Share your experience with the brand"></textarea>
										</div>
										<button type="submit"
											class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-500">Submit
											Review</button>
									</form>
								@elseif (($hasSubmittedReview ?? false) === true)
									<p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">You already
										reviewed this brand for this order.</p>
								@else
									<p
										class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
										You can leave a review after the brand completes the order.</p>
								@endif
								<a href="{{ route('frontend.orders.index') }}"
									class="block rounded-lg bg-gray-100 px-4 py-2 text-center font-semibold text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">Back
									to Orders</a>
							</div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection

@push('scripts')
	@if ($isBrand && $isParentOrder)
		<script>
			(function() {
				const detailsNodes = Array.from(document.querySelectorAll('details[data-child-order-details]'));
				if (detailsNodes.length === 0) {
					return;
				}

				const storageKey = 'order:' + @json((string) $order->id) + ':open-child-cards';

				const readOpenIds = () => {
					try {
						const parsed = JSON.parse(window.localStorage.getItem(storageKey) || '[]');
						return Array.isArray(parsed) ? new Set(parsed.map(String)) : new Set();
					} catch (error) {
						return new Set();
					}
				};

				const writeOpenIds = (openIds) => {
					window.localStorage.setItem(storageKey, JSON.stringify(Array.from(openIds)));
				};

				const openIds = readOpenIds();

				detailsNodes.forEach((detailsEl) => {
					const childId = String(detailsEl.dataset.childOrderDetails || '');
					if (!childId) {
						return;
					}

					if (openIds.has(childId)) {
						detailsEl.open = true;
					}

					detailsEl.addEventListener('toggle', () => {
						if (detailsEl.open) {
							openIds.add(childId);
						} else {
							openIds.delete(childId);
						}

						writeOpenIds(openIds);
					});
				});
			})();
		</script>
	@endif
@endpush
