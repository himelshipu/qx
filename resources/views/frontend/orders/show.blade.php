@extends('frontend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	@php
		$normalizeWorkflowStatus = static function (?string $status): string {
		    $normalized = (string) $status;

		    return match ($normalized) {
		        'accepted', 'in-progress' => 'in_progress',
		        'on_review' => 'delivered',
		        'completed' => 'approved',
		        default => $normalized,
		    };
		};

		$workflowLabel = static function (?string $status) use ($normalizeWorkflowStatus): string {
		    return match ($normalizeWorkflowStatus($status)) {
		        'pending' => 'Pending',
		        'in_progress' => 'In Progress',
		        'delivered' => 'Delivered',
		        'approved' => 'Approved',
		        'rejected' => 'Rejected',
		        'cancelled' => 'Cancelled',
		        default => 'Unknown',
		    };
		};

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
		$displayStatuses = $displayItems->pluck('status')->map(fn($status) => $normalizeWorkflowStatus((string) $status));
		if ($displayStatuses->isNotEmpty()) {
		    if ($displayStatuses->every(fn($status) => $status === 'pending')) {
		        $displayOrderStatus = 'pending';
		    } elseif ($displayStatuses->every(fn($status) => $status === 'in_progress')) {
		        $displayOrderStatus = 'in_progress';
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

	<div class="min-h-screen px-4 py-8 sm:px-6 lg:px-8">
		<div class="mx-auto max-w-full space-y-6">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 sm:p-6">
				<div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
					<div class="space-y-2">
						<div class="flex flex-wrap items-center gap-2">
							<span
								class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $currentStatus['badge'] }}">
								<span class="h-2 w-2 rounded-full {{ $currentStatus['dot'] }}"></span>
								{{ $workflowLabel($displayOrderStatus) }}
							</span>
							<span
								class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $isParentOrder ? 'Parent Order' : 'Child Order' }}</span>
						</div>
						<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order {{ $order->order_number }}</h1>
						<p class="text-sm text-gray-600 dark:text-gray-400">Placed
							{{ optional($order->placed_at ?? $order->created_at)->format('M d, Y \\a\\t h:i A') }}</p>
						@if ($isInfluencer && $order->parentOrder)
							<p class="text-sm text-gray-600 dark:text-gray-400">Parent checkout: <span
									class="font-semibold text-gray-900 dark:text-white">{{ $order->parentOrder->order_number }}</span></p>
						@endif
					</div>

					<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
						<div
							class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-900/40 dark:bg-emerald-900/20">
							<p class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Items</p>
							<p class="mt-1 text-lg font-semibold text-emerald-900 dark:text-emerald-50">{{ $displayItems->count() }}</p>
						</div>
						<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/70">
							<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencers</p>
							<p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
								{{ $displayItems->pluck('influencer_id')->filter()->unique()->count() }}</p>
						</div>
						<div
							class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-900/40 dark:bg-indigo-900/20">
							<p class="text-xs uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Subtotal</p>
							<p class="mt-1 text-lg font-semibold text-indigo-900 dark:text-indigo-50">
								${{ number_format((float) $order->subtotal, 2) }}</p>
						</div>
						@if ($isBrand)
							<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-800/70">
								<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
								<p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
									${{ number_format((float) $order->total_amount, 2) }}</p>
							</div>
						@endif
					</div>
				</div>
			</div>

			<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
					<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
						<div>
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Unified Tasks</h2>
							<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">All package and campaign work items follow the same task
								flow here.</p>
						</div>
						<div
							class="rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
							{{ $unifiedTasks->count() }} task{{ $unifiedTasks->count() === 1 ? '' : 's' }}</div>
					</div>
				</div>
				<div class="space-y-3 p-4 sm:p-6">
					@forelse ($unifiedTasks as $task)
						@php
							$taskDueDate = $task['due_date'] ?? null;
							$taskTimeline = [
							    ['label' => 'Pending', 'done' => true],
							    [
							        'label' => 'In Progress',
							        'done' => in_array($task['status'], ['in_progress', 'delivered', 'approved'], true),
							    ],
							    ['label' => 'Delivered', 'done' => in_array($task['status'], ['delivered', 'approved'], true)],
							    ['label' => 'Reviewed', 'done' => in_array($task['status'], ['approved'], true)],
							];
						@endphp
						<div class="rounded-xl border border-gray-200 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-900/50">
							<div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
								<div class="min-w-0">
									<div class="flex flex-wrap items-center gap-2">
										<h3 class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $task['title'] }}</h3>
										<span
											class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $task['status_classes'] }}">{{ $task['status_label'] }}</span>
										<span
											class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $task['kind'] === 'package' ? 'Package Task' : 'Campaign Task' }}</span>
									</div>
									<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $task['subtitle'] }} • Influencer: <span
											class="font-medium text-gray-900 dark:text-white">{{ $task['influencer_name'] }}</span></p>
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Amount:
										${{ number_format($task['amount'], 2) }}{{ $taskDueDate ? ' • Due ' . $taskDueDate->format('M d, Y') : '' }}
									</p>
								</div>
								<div class="w-full max-w-xl">
									<div class="flex items-stretch gap-2 overflow-x-auto pb-1">
										@foreach ($taskTimeline as $timelineStep)
											<div class="flex items-center gap-2 shrink-0">
												<div
													class="rounded-lg border px-2.5 py-2 {{ $timelineStep['done'] ? 'border-emerald-200 bg-emerald-50' : 'border-gray-200 bg-white' }}">
													<div
														class="flex items-center gap-1 text-[11px] font-semibold {{ $timelineStep['done'] ? 'text-emerald-700' : 'text-gray-500' }}">
														<span
															class="h-1.5 w-1.5 rounded-full {{ $timelineStep['done'] ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
														{{ $timelineStep['label'] }}
													</div>
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
					@empty
						<div
							class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
							No tasks found for this order.</div>
					@endforelse
				</div>
			</div>

			@if ($isBrand)
				@include('frontend.orders.partials.brand-section')
			@endif

			@if ($isInfluencer)
				@include('frontend.orders.partials.influencer-section')
			@endif
		</div>
	</div>
@endsection

@include('frontend.orders.partials.show-scripts')
