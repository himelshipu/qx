<div id="orders-results">
	<div class="overflow-x-auto">
		<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
			<thead class="bg-gray-50 dark:bg-gray-800/50">
				<tr>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">For</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</th>
					<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand / Campaign</th>
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

						$orderType = $order->campaign_id ? 'Campaign' : (($order->package_items_count ?? 0) > 0 ? 'Package' : 'Unknown');
						$typeColor = match ($orderType) {
						    'Campaign' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
						    'Package' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
						    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
						};

						$forType = match ($order->buyer?->user_type) {
						    'brand' => 'Brand',
						    'influencer' => 'Influencer',
						    'admin' => 'Admin',
						    'moderator' => 'Moderator',
						    default => 'N/A',
						};
						$forColor = match ($forType) {
						    'Brand' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
						    'Influencer' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
						    'Admin', 'Moderator' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
						    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
						};
					@endphp
					<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
						<td class="px-4 py-3">
							<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
							<p class="text-xs text-gray-500 dark:text-gray-400">#{{ $order->id }}</p>
						</td>
						<td class="px-4 py-3 text-sm">
							<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeColor }}">{{ $orderType }}</span>
						</td>
						<td class="px-4 py-3 text-sm">
							<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $forColor }}">{{ $forType }}</span>
						</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $order->buyer?->name ?? 'N/A' }}</td>
						<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
							<p>{{ $order->brand?->brand_name ?? 'N/A' }}</p>
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->campaign?->title ?? ($orderType === 'Package' ? 'Package Purchase' : '-') }}</p>
						</td>
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
						<td colspan="9" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No orders found for the current filters.</td>
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

