@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-6xl">
		<div class="mb-8">
			<h1 class="text-3xl font-bold mb-2">Payment Queue</h1>
			<p class="text-gray-600">Track pending payments waiting to be processed</p>
		</div>

		@if (session('success'))
			<div class="alert alert-success mb-6">
				{{ session('success') }}
			</div>
		@endif

		@if (session('error'))
			<div class="alert alert-error mb-6">
				{{ session('error') }}
			</div>
		@endif

		<!-- Statistics Cards -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
			<!-- Pending Items -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Pending Items</p>
						<p class="text-3xl font-bold text-orange-600 mt-2">
							{{ $totalUnpaidItems }}
						</p>
					</div>
					<div class="bg-orange-100 rounded-full p-3">
						<svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
							<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
							<path fill-rule="evenodd"
								d="M4 5a2 2 0 012-2 1 1 0 000-2H2a2 2 0 00-2 2v9a2 2 0 002 2h12a2 2 0 002-2V5a1 1 0 10 2h2a2 2 0 00-2-2 1 1 0 000 2H4z"
								clip-rule="evenodd"></path>
						</svg>
					</div>
				</div>
			</div>

			<!-- Pending Sub-Orders -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Pending Sub-Orders</p>
						<p class="text-3xl font-bold text-blue-600 mt-2">
							{{ $totalUnpaidSubOrders }}
						</p>
					</div>
					<div class="bg-blue-100 rounded-full p-3">
						<svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
							<path
								d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 6H6.28l-.31-1.243A1 1 0 005 4H3z">
							</path>
							<path d="M16 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
							<path d="M4 16a2 2 0 11-4 0 2 2 0 014 0z"></path>
						</svg>
					</div>
				</div>
			</div>

			<!-- Total Pending Amount -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Total Pending</p>
						<p class="text-3xl font-bold text-red-600 mt-2">
							${{ number_format($totalUnpaidAmount, 2) }}
						</p>
					</div>
					<div class="bg-red-100 rounded-full p-3">
						<svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
								clip-rule="evenodd"></path>
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Alerts for Overdue and Due Soon -->
		@if ($overdue->count() > 0)
			<div class="alert alert-error mb-6">
				<svg class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2m2-2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<div>
					<h3 class="font-bold">Overdue Items!</h3>
					<div class="text-sm">You have {{ $overdue->count() }} overdue payment(s) awaiting processing.</div>
				</div>
			</div>
		@endif

		@if ($dueSoon->count() > 0)
			<div class="alert alert-warning mb-6">
				<svg class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<div>
					<h3 class="font-bold">Due Soon</h3>
					<div class="text-sm">You have {{ $dueSoon->count() }} payment(s) due within the next 7 days.</div>
				</div>
			</div>
		@endif

		<!-- Pending Items Section -->
		<div class="bg-white rounded-lg shadow p-6 mb-8">
			<h2 class="text-xl font-bold mb-6">Pending Package Items</h2>
			@if ($unpaidItems->count() > 0)
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="bg-gray-50 border-b">
							<tr>
								<th class="px-4 py-3 text-left font-semibold">Package</th>
								<th class="px-4 py-3 text-left font-semibold">Campaign/Brand</th>
								<th class="px-4 py-3 text-right font-semibold">Amount</th>
								<th class="px-4 py-3 text-center font-semibold">Due Date</th>
								<th class="px-4 py-3 text-center font-semibold">Status</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($unpaidItems as $item)
								<tr class="border-b hover:bg-gray-50">
									<td class="px-4 py-3">
										<p class="font-medium">{{ $item->package->title ?? 'Package' }}</p>
										<p class="text-xs text-gray-500">Order #{{ $item->order_id }}</p>
									</td>
									<td class="px-4 py-3">
										<p class="font-medium">{{ $item->order->campaign->title ?? ($item->order->brand->company_name ?? 'N/A') }}</p>
									</td>
									<td class="px-4 py-3 text-right font-semibold text-orange-600">
										${{ number_format($item->line_total, 2) }}
									</td>
									<td class="px-4 py-3 text-center">
										@if ($item->due_date)
											<span class="text-sm">{{ $item->due_date->format('M d, Y') }}</span>
											@if ($item->due_date->isPast())
												<p class="text-xs text-red-600 font-semibold mt-1">OVERDUE</p>
											@elseif ($item->due_date->diffInDays(now()) <= 7)
												<p class="text-xs text-yellow-600 font-semibold mt-1">DUE SOON</p>
											@endif
										@else
											<span class="text-sm text-gray-400">N/A</span>
										@endif
									</td>
									<td class="px-4 py-3 text-center">
										<span class="inline-block bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded-full">
											Pending
										</span>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@else
				<div class="text-center py-8 text-gray-500">
					<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<p>No pending package items. Great work!</p>
				</div>
			@endif
		</div>

		<!-- Pending Sub-Orders Section -->
		<div class="bg-white rounded-lg shadow p-6">
			<h2 class="text-xl font-bold mb-6">Pending Campaign Sub-Orders</h2>
			@if ($unpaidSubOrders->count() > 0)
				<div class="space-y-4">
					@foreach ($unpaidSubOrders as $subOrder)
						<div class="border rounded-lg p-4 hover:bg-gray-50">
							<div class="flex justify-between items-start mb-2">
								<div>
									<p class="font-semibold">{{ $subOrder->order->campaign->title ?? 'Campaign' }}</p>
									<p class="text-sm text-gray-600">Sub-Order #{{ $subOrder->id }}</p>
									<p class="text-xs text-gray-500 mt-2">Created {{ $subOrder->created_at->format('M d, Y') }}</p>
								</div>
								<div class="text-right">
									<p class="font-bold text-orange-600 text-lg">${{ number_format($subOrder->amount, 2) }}</p>
									<span class="inline-block bg-orange-100 text-orange-800 text-xs px-3 py-1 rounded mt-2">
										Pending
									</span>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="text-center py-8 text-gray-500">
					<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<p>No pending sub-orders. Great work!</p>
				</div>
			@endif
		</div>
	</div>
@endsection
