<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
	<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
		<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
		<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Child and campaign order entries for this checkout.</p>
	</div>
	<div class="space-y-3 p-4 sm:p-6">
		@if ($order->childOrders->isNotEmpty())
			<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
				<h3 class="text-sm font-semibold text-gray-900 dark:text-white">Child Orders</h3>
				<div class="mt-3 space-y-2">
					@foreach ($order->childOrders as $childOrder)
						@php
							$childInfluencer = $childOrder->acceptedForInfluencer;
							$childName = $childInfluencer?->display_name ?: $childInfluencer?->user?->name ?: 'Influencer';
						@endphp
						<div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800/50">
							<div class="flex flex-wrap items-center justify-between gap-2">
								<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $childName }}</p>
								<p class="text-xs text-gray-500 dark:text-gray-400">{{ $childOrder->order_number }} •
									${{ number_format((float) $childOrder->total_amount, 2) }}</p>
							</div>
							<div class="mt-2 flex flex-wrap gap-2">
								@if ($childInfluencer?->user?->slug)
									<a href="{{ route('influencer.profile', ['slug' => $childInfluencer->user->slug]) }}"
										class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">View
										influencer</a>
								@endif
								@if ($childInfluencer)
									<a
										href="{{ route('frontend.conversations.open-order', ['influencer' => $childInfluencer, 'order' => $childOrder]) }}"
										class="inline-flex items-center rounded-lg bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">Messages</a>
								@endif
							</div>
						</div>
					@endforeach
				</div>
			</div>
		@endif

		@if ($order->subOrders->isNotEmpty())
			<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
				<h3 class="text-sm font-semibold text-gray-900 dark:text-white">Campaign Orders</h3>
				<div class="mt-3 space-y-2">
					@foreach ($order->subOrders as $subOrder)
						@php
							$campaign = $subOrder->order->campaign ?? null;
							$influencer = $subOrder->influencer;
						@endphp
						<div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-800/50">
							<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $campaign->title ?? 'Campaign' }}</p>
							<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
								{{ $influencer?->user?->name ?? ($influencer?->display_name ?? 'Influencer') }} •
								${{ number_format((float) $subOrder->amount, 2) }}</p>
							@if ($subOrder->status === 'on_review')
								<div class="mt-2 grid grid-cols-2 gap-2">
									<form method="POST"
										action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $subOrder]) }}">
										@csrf
										@method('PUT')
										<input type="hidden" name="status" value="approved">
										<button type="submit"
											class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500">Approve</button>
									</form>
									<form method="POST"
										action="{{ route('frontend.orders.items.update-decision', ['order' => $order, 'item' => $subOrder]) }}">
										@csrf
										@method('PUT')
										<input type="hidden" name="status" value="rejected">
										<button type="submit"
											class="w-full rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">Reject</button>
									</form>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		@endif

		@if ($isParentOrder)
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900/50">
				<p class="text-xs text-gray-600 dark:text-gray-400">Close the parent order only after every child item is approved.
				</p>
				@if ($orderIsCompleted)
					<p
						class="mt-2 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700">
						Order already completed.</p>
				@elseif ($brandCanComplete)
					<form class="mt-2" method="POST" action="{{ route('frontend.orders.complete', $order) }}">
						@csrf
						@method('PUT')
						<button type="submit"
							class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-500">Complete
							Order</button>
					</form>
				@else
					<p
						class="mt-2 rounded-lg border border-dashed border-gray-300 px-4 py-2 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
						Completion becomes available after all child items are approved.</p>
				@endif
			</div>
		@endif
	</div>
</div>
