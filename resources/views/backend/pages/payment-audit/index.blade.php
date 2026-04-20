@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payment Audit Log" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: PAYMENT STATISTICS ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			<!-- Total Marked as Paid -->
			<div
				class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-green-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"></path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-3 py-1 rounded-full">Paid</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalMarked }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Marked as Paid</p>
			</div>

			<!-- Total Amount Paid -->
			<div
				class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm0 22c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10zm3.5-10c0 1.933-1.567 3.5-3.5 3.5s-3.5-1.567-3.5-3.5 1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Total</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($totalAmount, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Amount Paid</p>
			</div>

			<!-- This Month's Payments -->
			<div
				class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-purple-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"></path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-3 py-1 rounded-full">{{ now()->format('M Y') }}</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($markedThisMonth, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">This Month</p>
			</div>

			<!-- Admin Count -->
			<div
				class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-indigo-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
							</path>
						</svg>
					</div>
					<span
						class="text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/40 px-3 py-1 rounded-full">Admins</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $paymentsByAdmin->count() }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Admins Processed Payments</p>
			</div>
		</div>

		<!-- ============ SECTION 2: PAYMENTS BY ADMIN ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
					</path>
				</svg>
				Payment Processing by Admin
			</h3>
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				@forelse ($paymentsByAdmin as $adminPayment)
					<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
						<p class="font-semibold text-gray-900 dark:text-white truncate">
							{{ $adminPayment->payoutMarkedBy?->name ?? 'Unknown' }}</p>
						<div class="mt-3 space-y-2">
							<div class="flex justify-between">
								<span class="text-xs text-gray-600 dark:text-gray-400">Payments Processed</span>
								<span class="text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $adminPayment->count }}</span>
							</div>
							<div class="flex justify-between">
								<span class="text-xs text-gray-600 dark:text-gray-400">Total Amount</span>
								<span
									class="text-sm font-bold text-green-600 dark:text-green-400">${{ number_format($adminPayment->amount, 2) }}</span>
							</div>
						</div>
					</div>
				@empty
					<p class="col-span-full text-center text-gray-500 dark:text-gray-400 py-4">No payment processing data</p>
				@endforelse
			</div>
		</div>

		<!-- ============ SECTION 3: PAYMENT TIMELINE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z">
					</path>
				</svg>
				Payment History Timeline
			</h3>
			<div class="space-y-4 max-h-96 overflow-y-auto">
				@forelse ($allPayments->take(50) as $payment)
					<div class="border-l-4 border-green-500 pl-4 py-2">
						<div class="flex items-start justify-between">
							<div class="flex-1">
								<div class="flex items-center gap-2 mb-1">
									<span
										class="px-2 py-1 rounded text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
										{{ $payment['type'] }}
									</span>
									<span class="font-semibold text-gray-900 dark:text-white">{{ $payment['influencer'] }}</span>
								</div>
								<p class="text-sm text-gray-600 dark:text-gray-400">{{ $payment['description'] }}</p>
								<div class="flex gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400">
									<span>Marked by: <strong>{{ $payment['marked_by'] }}</strong></span>
									<span>Ref: <strong>{{ $payment['reference'] ?? 'N/A' }}</strong></span>
								</div>
								@if ($payment['note'])
									<p class="text-xs text-gray-600 dark:text-gray-400 mt-1 italic">Note: {{ $payment['note'] }}</p>
								@endif
							</div>
							<div class="text-right">
								<p class="font-bold text-green-600 dark:text-green-400">${{ number_format($payment['amount'], 2) }}</p>
								<p class="text-xs text-gray-500 mt-1">{{ $payment['marked_at']->format('M d, Y H:i') }}</p>
								<div class="flex gap-1 mt-2">
									<button onclick="undo{{ $payment['type'] }}({{ $payment['id'] }})"
										class="text-xs px-2 py-1 rounded bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors">
										Undo
									</button>
								</div>
							</div>
						</div>
					</div>
				@empty
					<p class="text-center text-gray-500 dark:text-gray-400 py-4">No payment history</p>
				@endforelse
			</div>

			@if ($allPayments->count() > 50)
				<div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
					Showing 50 of {{ $allPayments->count() }} payments
				</div>
			@endif
		</div>
	</div>

	<script>
		function undoOrderItem(id) {
			if (confirm('Are you sure you want to undo this payment?')) {
				// Make AJAX request to undo
				fetch(`/dashboard/payment-audit/undo-item/${id}`, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
					}
				}).then(() => location.reload()).catch(err => alert('Error: ' + err));
			}
		}

		function undoSubOrder(id) {
			if (confirm('Are you sure you want to undo this payment?')) {
				// Make AJAX request to undo
				fetch(`/dashboard/payment-audit/undo-suborder/${id}`, {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}',
					}
				}).then(() => location.reload()).catch(err => alert('Error: ' + err));
			}
		}
	</script>
@endsection
