@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-6xl">
		<div class="mb-8">
			<h1 class="text-3xl font-bold mb-2">Earnings Dashboard</h1>
			<p class="text-gray-600">Track your earnings, payments, and withdrawal requests</p>
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

		@if ($errors->any())
			<div class="alert alert-error mb-6">
				<div class="font-bold">Errors:</div>
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<!-- Statistics Cards -->
		<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
			<!-- Total Earned -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Total Earned</p>
						<p class="text-3xl font-bold text-green-600 mt-2">
							${{ number_format($totalEarned, 2) }}
						</p>
					</div>
					<div class="bg-green-100 rounded-full p-3">
						<svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
							<path
								d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
						</svg>
					</div>
				</div>
			</div>

			<!-- Total Paid -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Total Paid</p>
						<p class="text-3xl font-bold text-blue-600 mt-2">
							${{ number_format($totalPaid, 2) }}
						</p>
					</div>
					<div class="bg-blue-100 rounded-full p-3">
						<svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
								clip-rule="evenodd" />
						</svg>
					</div>
				</div>
			</div>

			<!-- Pending Payment -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Pending Payment</p>
						<p class="text-3xl font-bold text-orange-600 mt-2">
							${{ number_format($pendingPayment, 2) }}
						</p>
						<p class="text-sm text-gray-500 mt-2">{{ $totalUnpaid }} unpaid order(s)</p>
					</div>
					<div class="bg-orange-100 rounded-full p-3">
						<svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z"
								clip-rule="evenodd" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabs -->
		<div class="tabs mb-6" role="tablist">
			<input type="radio" name="earnings_tabs" id="tab_overview" class="tab-toggle" checked aria-selected="true" />
			<label for="tab_overview" class="tab" role="tab">Overview</label>

			<input type="radio" name="earnings_tabs" id="tab_paid" class="tab-toggle" aria-selected="false" />
			<label for="tab_paid" class="tab" role="tab">Paid Items</label>

			<input type="radio" name="earnings_tabs" id="tab_unpaid" class="tab-toggle" aria-selected="false" />
			<label for="tab_unpaid" class="tab" role="tab">Pending Items</label>

			@if ($totalUnpaid > 0)
				<input type="radio" name="earnings_tabs" id="tab_withdraw" class="tab-toggle" aria-selected="false" />
				<label for="tab_withdraw" class="tab" role="tab">Withdraw Funds</label>
			@endif
		</div>

		<!-- Tab Content -->
		<div class="tab-content">
			<!-- Overview Tab -->
			<div class="tab-pane active" id="tab_overview" role="tabpanel">
				<div class="bg-white rounded-lg shadow p-6 mb-6">
					<h2 class="text-xl font-bold mb-6">Monthly Earnings Breakdown</h2>
					@if ($monthlyEarnings->count() > 0)
						<div class="overflow-x-auto">
							<table class="w-full text-sm">
								<thead class="bg-gray-50 border-b">
									<tr>
										<th class="px-4 py-3 text-left font-semibold">Month</th>
										<th class="px-4 py-3 text-right font-semibold">Earned</th>
										<th class="px-4 py-3 text-right font-semibold">Paid</th>
										<th class="px-4 py-3 text-right font-semibold">Pending</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($monthlyEarnings as $month => $data)
										<tr class="border-b hover:bg-gray-50">
											<td class="px-4 py-3 font-medium">
												{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}
											</td>
											<td class="px-4 py-3 text-right text-green-600 font-semibold">
												${{ number_format($data['earned'], 2) }}
											</td>
											<td class="px-4 py-3 text-right text-blue-600 font-semibold">
												${{ number_format($data['paid'], 2) }}
											</td>
											<td class="px-4 py-3 text-right text-orange-600 font-semibold">
												${{ number_format($data['pending'], 2) }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center py-8 text-gray-500">
							<p>No earnings data available yet.</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Paid Items Tab -->
			<div class="tab-pane" id="tab_paid" role="tabpanel">
				<div class="bg-white rounded-lg shadow p-6">
					<h2 class="text-xl font-bold mb-6">Payment History</h2>
					@if ($paidItems->count() > 0 || $paidSubOrders->count() > 0)
						<div class="space-y-4">
							<!-- Paid Order Items -->
							@foreach ($paidItems as $item)
								<div class="border rounded-lg p-4 hover:bg-gray-50">
									<div class="flex justify-between items-start mb-2">
										<div>
											<p class="font-semibold">{{ $item->package->title ?? 'Package' }}</p>
											<p class="text-sm text-gray-600">
												Campaign: {{ $item->order->campaign->title ?? 'N/A' }} |
												Brand: {{ $item->order->brand->company_name ?? 'N/A' }}
											</p>
										</div>
										<div class="text-right">
											<p class="font-bold text-green-600">${{ number_format($item->payout_amount ?? $item->line_total, 2) }}</p>
											<p class="text-xs text-gray-500">Paid on {{ $item->paid_at?->format('M d, Y') }}</p>
										</div>
									</div>
								</div>
							@endforeach

							<!-- Paid Sub Orders -->
							@foreach ($paidSubOrders as $subOrder)
								<div class="border rounded-lg p-4 hover:bg-gray-50">
									<div class="flex justify-between items-start mb-2">
										<div>
											<p class="font-semibold">Sub Order</p>
											<p class="text-sm text-gray-600">
												Campaign: {{ $subOrder->order->campaign->title ?? 'N/A' }}
											</p>
										</div>
										<div class="text-right">
											<p class="font-bold text-green-600">${{ number_format($subOrder->payout_amount ?? $subOrder->amount, 2) }}
											</p>
											<p class="text-xs text-gray-500">Paid on {{ $subOrder->paid_at?->format('M d, Y') }}</p>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-8 text-gray-500">
							<p>No paid items yet.</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Pending Items Tab -->
			<div class="tab-pane" id="tab_unpaid" role="tabpanel">
				<div class="bg-white rounded-lg shadow p-6">
					<h2 class="text-xl font-bold mb-6">Pending Payments</h2>
					@if (
						$completedItems->filter(fn($item) => !$item->paid_at)->count() > 0 ||
							$completedSubOrders->filter(fn($so) => !$so->paid_at)->count() > 0)
						<div class="space-y-4">
							<!-- Unpaid Order Items -->
							@foreach ($completedItems->filter(fn($item) => !$item->paid_at) as $item)
								<div class="border border-orange-200 rounded-lg p-4 bg-orange-50 hover:bg-orange-100">
									<div class="flex justify-between items-start mb-2">
										<div>
											<p class="font-semibold">{{ $item->package->title ?? 'Package' }}</p>
											<p class="text-sm text-gray-600">
												Campaign: {{ $item->order->campaign->title ?? 'N/A' }} |
												Brand: {{ $item->order->brand->company_name ?? 'N/A' }}
											</p>
											<p class="text-xs text-gray-500 mt-1">Approved on {{ $item->approved_at?->format('M d, Y') }}</p>
										</div>
										<div class="text-right">
											<p class="font-bold text-orange-600">${{ number_format($item->line_total, 2) }}</p>
											<span class="inline-block bg-orange-200 text-orange-800 text-xs px-2 py-1 rounded mt-1">Pending</span>
										</div>
									</div>
								</div>
							@endforeach

							<!-- Unpaid Sub Orders -->
							@foreach ($completedSubOrders->filter(fn($so) => !$so->paid_at) as $subOrder)
								<div class="border border-orange-200 rounded-lg p-4 bg-orange-50 hover:bg-orange-100">
									<div class="flex justify-between items-start mb-2">
										<div>
											<p class="font-semibold">Sub Order</p>
											<p class="text-sm text-gray-600">
												Campaign: {{ $subOrder->order->campaign->title ?? 'N/A' }}
											</p>
											<p class="text-xs text-gray-500 mt-1">Completed on {{ $subOrder->completed_at?->format('M d, Y') }}</p>
										</div>
										<div class="text-right">
											<p class="font-bold text-orange-600">${{ number_format($subOrder->amount, 2) }}</p>
											<span class="inline-block bg-orange-200 text-orange-800 text-xs px-2 py-1 rounded mt-1">Pending</span>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-8 text-gray-500">
							<p>No pending payments. Great work!</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Withdraw Funds Tab (if there are unpaid items) -->
			@if ($totalUnpaid > 0)
				<div class="tab-pane" id="tab_withdraw" role="tabpanel">
					<div class="bg-white rounded-lg shadow p-6">
						<h2 class="text-xl font-bold mb-6">Request Withdrawal</h2>
						<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
							<p class="text-blue-800">
								You have <strong>${{ number_format($pendingPayment, 2) }}</strong> available for withdrawal.
							</p>
						</div>
						<form action="{{ route('earnings.withdraw') }}" method="POST">
							@csrf
							<div class="mb-4">
								<label for="amount" class="block text-sm font-semibold mb-2">Withdrawal Amount</label>
								<div class="flex gap-2">
									<input type="number" id="amount" name="amount" step="0.01" min="0"
										max="{{ $pendingPayment }}" class="flex-1 input input-bordered" placeholder="Enter amount" required>
									<button type="button" class="btn btn-outline" onclick="setMaxAmount()">Max</button>
								</div>
								@error('amount')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>

							<div class="mb-6">
								<label for="payment_method" class="block text-sm font-semibold mb-2">Payment Method</label>
								<select id="payment_method" name="payment_method" class="w-full select select-bordered" required>
									<option value="">Select a payment method</option>
									<option value="bank_transfer">Bank Transfer</option>
									<option value="paypal">PayPal</option>
									<option value="stripe">Stripe</option>
								</select>
								@error('payment_method')
									<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
								@enderror
							</div>

							<button type="submit" class="btn btn-primary">Request Withdrawal</button>
						</form>
					</div>
				</div>
			@endif
		</div>
	</div>

	<script>
		function setMaxAmount() {
			const maxAmount = {{ $pendingPayment }};
			document.getElementById('amount').value = maxAmount.toFixed(2);
		}
	</script>
@endsection
