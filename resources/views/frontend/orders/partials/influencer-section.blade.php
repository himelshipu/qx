@php
	$influencerItems = $order->items->where('influencer_id', auth()->user()->influencer?->id)->values();
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-6 lg:col-span-2">
		<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
				<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Work Items</h2>
				<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Update each task as you progress. Completed stays with the
					brand or admin.</p>
			</div>
			<div class="space-y-4 p-4 sm:p-6">
				@forelse ($influencerItems as $item)
					@php
						$itemStatus = $statusStyles[$item->status] ?? ['badge' => 'bg-gray-100 text-gray-700', 'dot' => 'bg-gray-400'];
						$normalizedItemStatus = match ((string) $item->status) {
						    'accepted', 'in-progress' => 'in_progress',
						    default => (string) $item->status,
						};
						$nextStatusOptions = match ($normalizedItemStatus) {
						    'pending' => [['value' => 'in_progress', 'label' => 'Start Work (In Progress)']],
						    'in_progress' => [['value' => 'delivered', 'label' => 'Mark Delivered (For Review)']],
						    'rejected' => [['value' => 'in_progress', 'label' => 'Resume Work (After Rejection)']],
						    default => [],
						};
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
										{{ $workflowLabel((string) $item->status) }}
									</span>
								</div>
								<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Package: <span
										class="font-medium text-gray-900 dark:text-white">{{ $item->package?->name ?? 'N/A' }}</span></p>
							</div>

							@if ($isInfluencerStatusLocked)
								<div
									class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-3 text-xs text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 lg:w-56">
									<p class="font-semibold">Task status locked</p>
									<p class="mt-1">Brand already {{ $item->status === 'approved' ? 'approved' : 'finalized' }} this task.</p>
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
										required @disabled(empty($nextStatusOptions))>
										@foreach ($nextStatusOptions as $option)
											<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
										@endforeach
									</select>
									@if (!empty($nextStatusOptions))
										<button type="submit"
											class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Save
											status</button>
									@else
										<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">No action available at this stage.</p>
									@endif
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

	@include('frontend.orders.partials.brand-sidebar')
</div>
