@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payments" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: PAYMENT KPIs ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
			<!-- Total Payments -->
			<div
				class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Transactions</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalPayments }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Payments</p>
			</div>

			<!-- Successful Payments -->
			<div
				class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-green-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-3 py-1 rounded-full">{{ round(($successfulPayments / max($totalPayments, 1)) * 100) }}%</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($capturedAmount, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Captured ({{ $successfulPayments }})</p>
			</div>

			<!-- Pending Payments -->
			<div
				class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/40 px-3 py-1 rounded-full">Awaiting</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($pendingAmount, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Pending ({{ $pendingPayments }})</p>
			</div>

			<!-- Failed Payments -->
			<div
				class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-red-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/40 px-3 py-1 rounded-full">Action
						Required</span>
				</div>
				<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($failedAmount, 2) }}</p>
				<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Failed ({{ $failedPayments }})</p>
			</div>
		</div>

		<!-- ============ SECTION 2: PAYMENT SUMMARY ============ -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Payment Summary -->
			<div
				class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h4.89z" />
					</svg>
					Payment Overview
				</h3>
				<div class="grid grid-cols-2 gap-4">
					<div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Total Amount</p>
						<p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalAmount, 2) }}</p>
					</div>
					<div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Captured</p>
						<p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($capturedAmount, 2) }}</p>
					</div>
					<div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Pending</p>
						<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($pendingAmount, 2) }}</p>
					</div>
					<div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Failed</p>
						<p class="text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($failedAmount, 2) }}</p>
					</div>
				</div>
			</div>

			<!-- Top Brands by Payment -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
					<span class="flex items-center gap-2">
						<svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
							<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
						</svg>
						Top Brands
					</span>
					<span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">by payments</span>
				</h3>
				<div class="space-y-3">
					@forelse ($topBrandsByPayment as $index => $brand)
						<div
							class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<div class="flex items-center gap-3 flex-1">
								<div
									class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-sm font-bold">
									{{ $index + 1 }}
								</div>
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $brand->brand_name }}</p>
									<p class="text-xs text-gray-500">{{ $brand->payment_count }} payments</p>
								</div>
							</div>
							<span
								class="text-sm font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($brand->total_amount, 2) }}</span>
						</div>
					@empty
						<p class="text-sm text-gray-500 text-center py-3">No payments yet</p>
					@endforelse
				</div>
			</div>
		</div>

		<!-- ============ SECTION 3: PAYMENTS TABLE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
					<path
						d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h4.89z" />
				</svg>
				All Payments
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Payment ID</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Brand</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Campaign</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Provider</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($payments as $payment)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<button onclick="openPaymentDetailsModal({{ $payment->id }})"
										class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
										{{ $payment->id }}
									</button>
								</td>
								<td class="py-4 px-4">
									<span
										class="text-gray-900 dark:text-white font-medium">{{ $payment->order->brand->brand_name ?? 'N/A' }}</span>
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-600 dark:text-gray-400">{{ $payment->order->campaign->title ?? 'N/A' }}</span>
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($payment->amount, 2) }}
								</td>
								<td class="py-4 px-4">
									@php
										$statusColors = [
										    'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
										    'authorized' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
										    'captured' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
										    'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
										    'refunded' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300',
										    'partially_refunded' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300',
										];
										$color = $statusColors[$payment->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
										$canRefund = in_array($payment->status, ['authorized', 'captured']);
										$canRetry = in_array($payment->status, ['failed', 'pending']);
									@endphp
									<div class="flex items-center gap-2">
										<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
											{{ ucfirst($payment->status) }}
										</span>
										@if ($canRefund || $canRetry)
											<div class="relative group">
												<button class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
													<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
														<path
															d="M6 10c0 .55.45 1 1 1h1v3c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-3h1c.55 0 1-.45 1-1s-.45-1-1-1H7c-.55 0-1 .45-1 1zm9 0c0 .55.45 1 1 1h1v3c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-3h1c.55 0 1-.45 1-1s-.45-1-1-1h-5c-.55 0-1 .45-1 1z" />
													</svg>
												</button>
												<div
													class="hidden group-hover:block absolute right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-10">
													@if ($canRefund)
														<button onclick="openRefundModal({{ $payment->id }})"
															class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Refund</button>
													@endif
													@if ($canRetry)
														<button onclick="retryPayment({{ $payment->id }})"
															class="block w-full text-left px-3 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Retry</button>
													@endif
												</div>
											</div>
										@endif
									</div>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ ucfirst($payment->payment_provider) }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $payment->created_at->format('M d, Y') }}
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No payments found
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			@if ($payments->hasPages())
				<div class="mt-4 flex items-center justify-between">
					<div class="text-sm text-gray-600 dark:text-gray-400">
						Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
					</div>
					<div class="flex gap-2">
						{{ $payments->links() }}
					</div>
				</div>
			@endif
		</div>

	</div>

	<!-- ==================== MODALS ==================== -->

	<!-- PAYMENT DETAILS MODAL -->
	<div id="paymentDetailsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closePaymentDetailsModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
			<div
				class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Payment Details</h2>
				<button onclick="closePaymentDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<div id="paymentDetailsContent" class="p-6">
				<p class="text-center text-gray-500">Loading...</p>
			</div>
		</div>
	</div>

	<!-- REFUND MODAL -->
	<div id="refundModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closeRefundModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
			<div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Refund Payment</h2>
				<button onclick="closeRefundModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<form id="refundForm" class="p-6 space-y-4">
				@csrf
				@method('POST')
				<input type="hidden" id="refund_payment_id" name="payment_id">

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Refund Amount</label>
					<input type="number" id="refund_amount" name="amount" step="0.01" min="0.01"
						class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
						placeholder="Leave blank to refund full amount">
				</div>

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reason</label>
					<textarea name="reason"
					 class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
					 rows="3" placeholder="Refund reason..."></textarea>
				</div>

				<div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
					<p class="text-sm text-red-800 dark:text-red-300">This action will refund the customer. Please verify the amount
						before proceeding.</p>
				</div>

				<div class="flex gap-3 pt-4">
					<button type="submit"
						class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold text-sm">
						Process Refund
					</button>
					<button type="button" onclick="closeRefundModal()"
						class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold text-sm">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- JAVASCRIPT -->
	<script>
		// Payment Details
		function openPaymentDetailsModal(paymentId) {
			document.getElementById('paymentDetailsModal').classList.remove('hidden');
			loadPaymentDetails(paymentId);
		}

		function closePaymentDetailsModal() {
			document.getElementById('paymentDetailsModal').classList.add('hidden');
		}

		function loadPaymentDetails(paymentId) {
			fetch(`{{ url('dashboard/payments') }}/${paymentId}`)
				.then(r => r.json())
				.then(data => {
					const payment = data.payment;
					const items = data.order_items || [];

					let itemsHtml = items.length > 0 ?
						`<div class="mt-4 border-t pt-4"><h4 class="font-semibold mb-3">Order Items</h4>
						<div class="space-y-2">
							${items.map(item => `
										<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
											<p class="text-sm font-semibold">${item.description}</p>
											<div class="flex justify-between items-center mt-1">
												<span class="text-xs text-gray-600 dark:text-gray-400">${item.status}</span>
												<p class="text-sm font-bold">$${Number(item.amount).toFixed(2)}</p>
											</div>
										</div>
									`).join('')}
						</div></div>` :
						'<p class="text-sm text-gray-500 mt-4">No items found</p>';

					document.getElementById('paymentDetailsContent').innerHTML = `
						<div class="space-y-4">
							<div class="grid grid-cols-2 gap-4">
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Brand</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payment.brand}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Campaign</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payment.campaign}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Amount</p>
									<p class="font-semibold text-gray-900 dark:text-white">$${Number(payment.amount).toFixed(2)}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Status</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payment.status.toUpperCase()}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Payment Method</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payment.payment_method}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Date</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payment.created_at}</p>
								</div>
							</div>
							<div class="border-t pt-4">
								<p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Stripe ID</p>
								<p class="font-mono text-sm text-gray-900 dark:text-white break-all">${payment.stripe_id || 'N/A'}</p>
							</div>
							${itemsHtml}
						</div>
					`;
				})
				.catch(e => {
					document.getElementById('paymentDetailsContent').innerHTML =
						`<p class="text-red-500">Error loading details</p>`;
					console.error(e);
				});
		}

		// Refund
		function openRefundModal(paymentId) {
			document.getElementById('refundModal').classList.remove('hidden');
			document.getElementById('refund_payment_id').value = paymentId;
		}

		function closeRefundModal() {
			document.getElementById('refundModal').classList.add('hidden');
			document.getElementById('refundForm').reset();
		}

		document.getElementById('refundForm')?.addEventListener('submit', async (e) => {
			e.preventDefault();
			const paymentId = document.getElementById('refund_payment_id').value;
			const formData = new FormData(document.getElementById('refundForm'));
			const data = Object.fromEntries(formData);

			try {
				const response = await fetch(`{{ url('dashboard/payments') }}/${paymentId}/refund`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();

				if (response.ok) {
					alert('Refund processed successfully!');
					closeRefundModal();
					location.reload();
				} else {
					alert('Error: ' + (result.message || 'Failed to process refund'));
				}
			} catch (error) {
				alert('Error: ' + error.message);
			}
		});

		// Retry Payment
		function retryPayment(paymentId) {
			if (confirm('Retry this payment?')) {
				fetch(`{{ url('dashboard/payments') }}/${paymentId}/retry`, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
						}
					})
					.then(r => r.json())
					.then(data => {
						alert(data.message || 'Payment retry initiated');
						location.reload();
					})
					.catch(e => alert('Error: ' + e.message));
			}
		}
	</script>
@endsection
