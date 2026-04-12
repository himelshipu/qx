@extends('backend.layouts.app')

@section('content')
	<div class="flex items-center justify-between mb-6">
		<x-backend.shell.breadcrumb pageTitle="Payouts" />
		<button onclick="openCreatePayoutModal()"
			class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-semibold flex items-center gap-2">
			<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
				<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z" />
			</svg>
			New Payout
		</button>
	</div>

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: PAYOUT KPIs ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
			<!-- Total Payouts -->
			<div
				class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded-full">Total</span>
				</div>
				<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPayouts }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Total Payouts</p>
			</div>

			<!-- Paid Payouts -->
			<div
				class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-green-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-2 py-1 rounded-full">Complete</span>
				</div>
				<p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($paidAmount, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Paid ({{ $paidPayouts }})</p>
			</div>

			<!-- Processing Payouts -->
			<div
				class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-2 py-1 rounded-full">Processing</span>
				</div>
				<p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($processingAmount, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Processing ({{ $processingPayouts }})</p>
			</div>

			<!-- Pending Payouts -->
			<div
				class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/40 px-2 py-1 rounded-full">Action</span>
				</div>
				<p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($pendingAmount, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Pending ({{ $pendingPayouts }})</p>
			</div>

			<!-- Failed Payouts -->
			<div
				class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-800/20 border border-red-200 dark:border-red-700 rounded-xl p-5 hover:shadow-lg transition-all">
				<div class="flex items-center justify-between mb-3">
					<div class="w-12 h-12 rounded-lg bg-red-500 text-white flex items-center justify-center">
						<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
							<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
						</svg>
					</div>
					<span
						class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/40 px-2 py-1 rounded-full">Error</span>
				</div>
				<p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($failedAmount, 2) }}</p>
				<p class="text-xs text-gray-600 dark:text-gray-300 mt-1">Failed ({{ $failedPayouts }})</p>
			</div>
		</div>

		<!-- ============ SECTION 2: SUMMARY & TOP INFLUENCERS ============ -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Payout Summary -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
					</svg>
					Payout Summary
				</h3>
				<div class="space-y-3">
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">Total Amount</span>
						<span class="font-bold text-gray-900 dark:text-white">${{ number_format($totalPayoutAmount, 2) }}</span>
					</div>
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">This Month</span>
						<span class="font-bold text-green-600 dark:text-green-400">${{ number_format($payoutsThisMonth, 2) }}</span>
					</div>
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">This Week</span>
						<span class="font-bold text-blue-600 dark:text-blue-400">${{ number_format($payoutsThisWeek, 2) }}</span>
					</div>
					<div class="flex justify-between items-center">
						<span class="text-sm text-gray-600 dark:text-gray-400">Average Payout</span>
						<span
							class="font-bold text-gray-900 dark:text-white">${{ number_format($totalPayouts > 0 ? $totalPayoutAmount / $totalPayouts : 0, 2) }}</span>
					</div>
				</div>
			</div>

			<!-- Top Influencers by Payout -->
			<div
				class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
					<span class="flex items-center gap-2">
						<svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
						</svg>
						Top Influencers
					</span>
					<span class="text-xs font-semibold text-pink-600 dark:text-pink-400">by earnings</span>
				</h3>
				<div class="space-y-3">
					@forelse ($topInfluencersByPayout as $index => $influencer)
						<div
							class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<div class="flex items-center gap-3 flex-1">
								<div
									class="w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 flex items-center justify-center text-sm font-bold">
									{{ $index + 1 }}
								</div>
								<div>
									<p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $influencer->display_name }}</p>
									<p class="text-xs text-gray-500">{{ $influencer->payout_count }} payouts</p>
								</div>
							</div>
							<span
								class="text-sm font-bold text-pink-600 dark:text-pink-400">${{ number_format($influencer->total_amount, 2) }}</span>
						</div>
					@empty
						<p class="text-sm text-gray-500 text-center py-3">No payouts yet</p>
					@endforelse
				</div>
			</div>
		</div>

		<!-- ============ SECTION 3: PENDING PAYOUTS (QUICK ACTION) ============ -->
		@if ($pendingPayoutsList->count() > 0)
			<div class="bg-white dark:bg-gray-900 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
						<path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
					</svg>
					Pending Payouts - Action Required
					<span
						class="ml-auto text-sm font-normal text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/30 px-3 py-1 rounded-full">{{ $pendingPayoutsList->count() }}
						pending</span>
				</h3>
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="border-b border-gray-200 dark:border-gray-700">
							<tr>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Items</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Account</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Created</th>
								<th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Action</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@foreach ($pendingPayoutsList as $payout)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
									<td class="py-4 px-4">
										<span class="font-semibold text-gray-900 dark:text-white">{{ $payout->influencer->display_name }}</span>
									</td>
									<td class="py-4 px-4 font-bold text-gray-900 dark:text-white">${{ number_format($payout->amount, 2) }}</td>
									<td class="py-4 px-4 text-gray-600 dark:text-gray-400">{{ $payout->items->count() }} items</td>
									<td class="py-4 px-4">
										<span
											class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
											{{ $payout->payoutAccount->provider ?? 'N/A' }}
										</span>
									</td>
									<td class="py-4 px-4 text-gray-600 dark:text-gray-400">{{ $payout->created_at->format('M d, Y') }}</td>
									<td class="py-4 px-4 text-center">
										<button onclick="openPayoutDetailsModal({{ $payout->id }})"
											class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-semibold">Details</button>
										<span class="mx-2 text-gray-400">|</span>
										<button onclick="openMarkAsPaidModal({{ $payout->id }})"
											class="text-green-600 dark:text-green-400 hover:underline text-xs font-semibold">Mark Paid</button>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		@endif

		<!-- ============ SECTION 4: ALL PAYOUTS TABLE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
				<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
					<path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
				</svg>
				All Payouts
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Payout ID</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Influencer</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Payment Method</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Created</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Paid</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($payouts as $payout)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<button onclick="openPayoutDetailsModal({{ $payout->id }})"
										class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
										{{ $payout->id }}
									</button>
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white font-medium">{{ $payout->influencer->display_name }}</span>
								</td>
								<td class="py-4 px-4 font-bold text-gray-900 dark:text-white">${{ number_format($payout->amount, 2) }}</td>
								<td class="py-4 px-4">
									@php
										$statusColors = [
										    'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
										    'processing' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
										    'paid' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
										    'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
										    'cancelled' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300',
										];
										$color = $statusColors[$payout->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
									@endphp
									<div class="flex items-center gap-2">
										<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
											{{ ucfirst($payout->status) }}
										</span>
										@if ($payout->status !== 'paid' && $payout->status !== 'cancelled')
											<button onclick="openStatusUpdateModal({{ $payout->id }}, '{{ $payout->status }}')"
												class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
												<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
													<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
												</svg>
											</button>
										@endif
									</div>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ ucfirst($payout->payoutAccount->provider ?? 'N/A') }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $payout->created_at->format('M d, Y') }}
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $payout->paid_at ? $payout->paid_at->format('M d, Y') : '-' }}
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No payouts found
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			@if ($payouts->hasPages())
				<div class="mt-4 flex items-center justify-between">
					<div class="text-sm text-gray-600 dark:text-gray-400">
						Showing {{ $payouts->firstItem() }} to {{ $payouts->lastItem() }} of {{ $payouts->total() }} results
					</div>
					<div class="flex gap-2">
						{{ $payouts->links() }}
					</div>
				</div>
			@endif
		</div>

	</div>

	<!-- ==================== MODALS ==================== -->

	<!-- CREATE PAYOUT MODAL -->
	<div id="createPayoutModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closeCreatePayoutModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
			<div
				class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Create New Payout</h2>
				<button onclick="closeCreatePayoutModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<form id="createPayoutForm" class="p-6 space-y-4">
				@csrf
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Influencer *</label>
						<select id="influencer_id" name="influencer_id" required
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
							onchange="loadInfluencerAccounts()">
							<option value="">Select an influencer</option>
							@foreach ($influencers as $influencer)
								<option value="{{ $influencer->id }}">{{ $influencer->display_name }}</option>
							@endforeach
						</select>
					</div>

					<div>
						<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payment Account *</label>
						<select id="payout_account_id" name="payout_account_id" required
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
							<option value="">Select a payment account</option>
						</select>
					</div>

					<div>
						<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount (USD) *</label>
						<input type="number" id="amount" name="amount" step="0.01" min="0.01" required
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
							placeholder="0.00">
					</div>

					<div>
						<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Currency</label>
						<select name="currency"
							class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
							<option value="USD">USD</option>
							<option value="EUR">EUR</option>
							<option value="GBP">GBP</option>
						</select>
					</div>
				</div>

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Link Work Items (Optional)</label>
					<div class="space-y-2 max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg p-3">
						@forelse ($unassignedItems as $item)
							<label class="flex items-center gap-2 cursor-pointer">
								<input type="checkbox" name="order_item_ids[]" value="{{ $item->id }}" class="w-4 h-4">
								<span class="text-sm text-gray-700 dark:text-gray-300">
									{{ $item->description }} - ({{ $item->order->campaign->name ?? 'Campaign' }}) -
									${{ number_format($item->amount, 2) }}
								</span>
							</label>
						@empty
							<p class="text-sm text-gray-500 text-center py-2">No unassigned completed work items</p>
						@endforelse
					</div>
				</div>

				<div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
					<button type="submit"
						class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm">
						Create Payout
					</button>
					<button type="button" onclick="closeCreatePayoutModal()"
						class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold text-sm">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- PAYOUT DETAILS MODAL -->
	<div id="payoutDetailsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closePayoutDetailsModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
			<div
				class="sticky top-0 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Payout Details</h2>
				<button onclick="closePayoutDetailsModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<div id="payoutDetailsContent" class="p-6">
				<p class="text-center text-gray-500">Loading...</p>
			</div>
		</div>
	</div>

	<!-- MARK AS PAID MODAL -->
	<div id="markAsPaidModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closeMarkAsPaidModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
			<div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Mark Payout as Paid</h2>
				<button onclick="closeMarkAsPaidModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<form id="markAsPaidForm" class="p-6 space-y-4">
				@csrf
				@method('POST')
				<input type="hidden" id="mark_paid_payout_id" name="payout_id">

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">External Payout ID (e.g., Stripe
						ID)</label>
					<input type="text" name="external_payout_id"
						class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
						placeholder="Optional">
				</div>

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reference/Note</label>
					<textarea name="reference"
					 class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
					 rows="3" placeholder="Add any notes about this payout..."></textarea>
				</div>

				<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
					<p class="text-sm text-blue-800 dark:text-blue-300">This will mark the payout as paid and set the payment timestamp
						to now.</p>
				</div>

				<div class="flex gap-3 pt-4">
					<button type="submit"
						class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm">
						Mark as Paid
					</button>
					<button type="button" onclick="closeMarkAsPaidModal()"
						class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold text-sm">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- STATUS UPDATE MODAL -->
	<div id="statusUpdateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
		onclick="if(event.target === this) closeStatusUpdateModal()">
		<div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
			<div class="border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Update Payout Status</h2>
				<button onclick="closeStatusUpdateModal()" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
					</svg>
				</button>
			</div>

			<form id="statusUpdateForm" class="p-6 space-y-4">
				@csrf
				@method('PUT')
				<input type="hidden" id="status_update_payout_id" name="payout_id">

				<div>
					<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Status *</label>
					<select name="status" required
						class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
						<option value="">Select a status</option>
						<option value="pending">Pending</option>
						<option value="processing">Processing</option>
						<option value="paid">Paid</option>
						<option value="failed">Failed</option>
						<option value="cancelled">Cancelled</option>
					</select>
				</div>

				<div class="flex gap-3 pt-4">
					<button type="submit"
						class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm">
						Update Status
					</button>
					<button type="button" onclick="closeStatusUpdateModal()"
						class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-400 dark:hover:bg-gray-600 font-semibold text-sm">
						Cancel
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- JAVASCRIPT -->
	<script>
		// Create Payout
		function openCreatePayoutModal() {
			document.getElementById('createPayoutModal').classList.remove('hidden');
		}

		function closeCreatePayoutModal() {
			document.getElementById('createPayoutModal').classList.add('hidden');
			document.getElementById('createPayoutForm').reset();
		}

		document.getElementById('createPayoutForm')?.addEventListener('submit', async (e) => {
			e.preventDefault();
			const formData = new FormData(document.getElementById('createPayoutForm'));
			const data = Object.fromEntries(formData);
			data.order_item_ids = Array.from(document.querySelectorAll('input[name="order_item_ids[]"]:checked'))
				.map(el => el.value);

			try {
				const response = await fetch('{{ route('dashboard.payouts.store') }}', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();

				if (response.ok) {
					alert('Payout created successfully!');
					closeCreatePayoutModal();
					location.reload();
				} else {
					alert('Error: ' + (result.message || 'Failed to create payout'));
				}
			} catch (error) {
				alert('Error: ' + error.message);
			}
		});

		// Load Influencer Accounts
		function loadInfluencerAccounts() {
			const influencerId = document.getElementById('influencer_id').value;
			const accountSelect = document.getElementById('payout_account_id');

			if (!influencerId) {
				accountSelect.innerHTML = '<option value="">Select a payment account</option>';
				return;
			}

			fetch(`{{ url('dashboard/payouts/influencer') }}/${influencerId}/accounts`)
				.then(r => r.json())
				.then(data => {
					accountSelect.innerHTML = '<option value="">Select a payment account</option>';
					data.accounts?.forEach(account => {
						const option = document.createElement('option');
						option.value = account.id;
						option.textContent =
							`${account.provider} - ${account.account_name || account.account_identifier}`;
						accountSelect.appendChild(option);
					});
				})
				.catch(e => console.error('Error loading accounts:', e));
		}

		// Payout Details
		function openPayoutDetailsModal(payoutId) {
			document.getElementById('payoutDetailsModal').classList.remove('hidden');
			loadPayoutDetails(payoutId);
		}

		function closePayoutDetailsModal() {
			document.getElementById('payoutDetailsModal').classList.add('hidden');
		}

		function loadPayoutDetails(payoutId) {
			fetch(`{{ url('dashboard/payouts') }}/${payoutId}`)
				.then(r => r.json())
				.then(data => {
					const payout = data.payout;
					const items = data.items || [];

					let itemsHtml = items.length > 0 ?
						`<div class="mt-4 border-t pt-4"><h4 class="font-semibold mb-3">Linked Work Items</h4>
						<div class="space-y-2">
							${items.map(item => `
										<div class="p-3 bg-gray-50 dark:bg-gray-800 rounded">
											<p class="text-sm font-semibold">${item.campaign}</p>
											<p class="text-xs text-gray-600 dark:text-gray-400">${item.order_item}</p>
											<p class="text-sm font-bold mt-1">$${Number(item.amount).toFixed(2)}</p>
										</div>
									`).join('')}
						</div></div>` :
						'<p class="text-sm text-gray-500 mt-4">No items linked</p>';

					document.getElementById('payoutDetailsContent').innerHTML = `
						<div class="space-y-4">
							<div class="grid grid-cols-2 gap-4">
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Influencer</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payout.influencer.user.full_name}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Amount</p>
									<p class="font-semibold text-gray-900 dark:text-white">$${Number(payout.amount).toFixed(2)}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Status</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payout.status.toUpperCase()}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Payment Method</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payout.payoutAccount.provider}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Created</p>
									<p class="font-semibold text-gray-900 dark:text-white">${new Date(payout.created_at).toLocaleDateString()}</p>
								</div>
								<div>
									<p class="text-xs text-gray-600 dark:text-gray-400">Paid Date</p>
									<p class="font-semibold text-gray-900 dark:text-white">${payout.paid_at ? new Date(payout.paid_at).toLocaleDateString() : '-'}</p>
								</div>
							</div>
							${itemsHtml}
						</div>
					`;
				})
				.catch(e => {
					document.getElementById('payoutDetailsContent').innerHTML =
						`<p class="text-red-500">Error loading details</p>`;
					console.error(e);
				});
		}

		// Mark as Paid
		function openMarkAsPaidModal(payoutId) {
			document.getElementById('markAsPaidModal').classList.remove('hidden');
			document.getElementById('mark_paid_payout_id').value = payoutId;
		}

		function closeMarkAsPaidModal() {
			document.getElementById('markAsPaidModal').classList.add('hidden');
			document.getElementById('markAsPaidForm').reset();
		}

		document.getElementById('markAsPaidForm')?.addEventListener('submit', async (e) => {
			e.preventDefault();
			const payoutId = document.getElementById('mark_paid_payout_id').value;
			const formData = new FormData(document.getElementById('markAsPaidForm'));
			const data = Object.fromEntries(formData);

			try {
				const response = await fetch(`{{ url('dashboard/payouts') }}/${payoutId}/mark-paid`, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();

				if (response.ok) {
					alert('Payout marked as paid!');
					closeMarkAsPaidModal();
					location.reload();
				} else {
					alert('Error: ' + (result.message || 'Failed to update payout'));
				}
			} catch (error) {
				alert('Error: ' + error.message);
			}
		});

		// Status Update
		function openStatusUpdateModal(payoutId, currentStatus) {
			document.getElementById('statusUpdateModal').classList.remove('hidden');
			document.getElementById('status_update_payout_id').value = payoutId;
			document.querySelector('select[name="status"]').value = currentStatus;
		}

		function closeStatusUpdateModal() {
			document.getElementById('statusUpdateModal').classList.add('hidden');
			document.getElementById('statusUpdateForm').reset();
		}

		document.getElementById('statusUpdateForm')?.addEventListener('submit', async (e) => {
			e.preventDefault();
			const payoutId = document.getElementById('status_update_payout_id').value;
			const formData = new FormData(document.getElementById('statusUpdateForm'));
			const data = Object.fromEntries(formData);

			try {
				const response = await fetch(`{{ url('dashboard/payouts') }}/${payoutId}`, {
					method: 'PUT',
					headers: {
						'Content-Type': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
					},
					body: JSON.stringify(data)
				});

				const result = await response.json();

				if (response.ok) {
					alert('Payout status updated!');
					closeStatusUpdateModal();
					location.reload();
				} else {
					alert('Error: ' + (result.message || 'Failed to update payout'));
				}
			} catch (error) {
				alert('Error: ' + error.message);
			}
		});
	</script>
@endsection
