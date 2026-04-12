@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payment Statements" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: FILTER & SEARCH ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
				<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
					<path d="M3 13h2v8H3z M17 13h2v8h-2z M5 21h14v2H5z M7 5h10v8H7z"></path>
				</svg>
				Select Influencer for Statement
			</h3>
			<div class="flex flex-col md:flex-row gap-4">
				<select id="influencerSelect" onchange="loadInfluencerStatement()"
					class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
					<option value="">Choose an influencer...</option>
					@foreach ($influencers as $influencer)
						<option value="{{ $influencer->id }}">{{ $influencer->display_name }} ({{ $influencer->user?->email ?? 'N/A' }})
						</option>
					@endforeach
				</select>
				<button onclick="loadInfluencerStatement()"
					class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
					Load Statement
				</button>
			</div>
		</div>

		<!-- ============ SECTION 2: INFLUENCER STATEMENT (Hidden initially) ============ -->
		<div id="statementContainer" class="hidden space-y-6">
			<!-- Statistics -->
			<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
				<div
					class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-xl p-5">
					<div class="flex items-center justify-between mb-3">
						<div class="w-10 h-10 rounded-lg bg-green-500 text-white flex items-center justify-center">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm0 22c-5.514 0-10-4.486-10-10s4.486-10 10-10 10 4.486 10 10-4.486 10-10 10zm3.5-10c0 1.933-1.567 3.5-3.5 3.5s-3.5-1.567-3.5-3.5 1.567-3.5 3.5-3.5 3.5 1.567 3.5 3.5z">
								</path>
							</svg>
						</div>
						<span
							class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-3 py-1 rounded-full">Total</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalPaidAmount">$0.00</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Amount Paid</p>
				</div>

				<div
					class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5">
					<div class="flex items-center justify-between mb-3">
						<div class="w-10 h-10 rounded-lg bg-blue-500 text-white flex items-center justify-center">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"></path>
							</svg>
						</div>
						<span
							class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Count</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white" id="transactionCount">0</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Transactions</p>
				</div>

				<div
					class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-xl p-5">
					<div class="flex items-center justify-between mb-3">
						<div class="w-10 h-10 rounded-lg bg-purple-500 text-white flex items-center justify-center">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z">
								</path>
							</svg>
						</div>
						<span
							class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-3 py-1 rounded-full">Period</span>
					</div>
					<p class="text-sm font-bold text-gray-900 dark:text-white" id="dateRange">N/A</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Period Covered</p>
				</div>
			</div>

			<!-- Transactions Table -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
						<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"></path>
					</svg>
					Payment Transactions
				</h3>
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="border-b border-gray-200 dark:border-gray-700">
							<tr>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Type</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Campaign/Order</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Description</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Reference</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Marked By</th>
								<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700" id="statementTableBody">
							<tr>
								<td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
									Select an influencer to view statement
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<!-- Action Buttons -->
				<div class="mt-6 flex gap-3">
					<button onclick="downloadFullPDF()"
						class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
						<svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
							<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"></path>
						</svg>
						Download Full PDF
					</button>
					<button onclick="downloadMonthlyPDF()"
						class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
						<svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z">
							</path>
						</svg>
						Download Monthly PDF
					</button>
				</div>
			</div>
		</div>

		<!-- Empty State -->
		<div id="emptyState" class="text-center py-12">
			<svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
				<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"></path>
			</svg>
			<p class="text-gray-500 dark:text-gray-400">Select an influencer to view their payment statement</p>
		</div>
	</div>

	<script>
		const selectedInfluencerId = new URLSearchParams(window.location.search).get('influencer');
		if (selectedInfluencerId) {
			document.getElementById('influencerSelect').value = selectedInfluencerId;
			loadInfluencerStatement();
		}

		async function loadInfluencerStatement() {
			const influencerId = document.getElementById('influencerSelect').value;
			if (!influencerId) return;

			try {
				const response = await fetch(`/dashboard/payment-statement/${influencerId}`);
				const html = await response.text();
				// Parse just the data we need
				const parser = new DOMParser();
				const doc = parser.parseFromString(html, 'text/html');

				// Update statement container
				document.getElementById('statementContainer').classList.remove('hidden');
				document.getElementById('emptyState').classList.add('hidden');

				// You would need to extract the data from the response
				// This is a simplified version - in production, return JSON from the controller
			} catch (error) {
				alert('Error loading statement');
			}
		}

		function downloadFullPDF() {
			const influencerId = document.getElementById('influencerSelect').value;
			if (!influencerId) {
				alert('Please select an influencer');
				return;
			}
			window.location.href = `/dashboard/payment-statement/${influencerId}/pdf`;
		}

		function downloadMonthlyPDF() {
			const influencerId = document.getElementById('influencerSelect').value;
			if (!influencerId) {
				alert('Please select an influencer');
				return;
			}
			const month = prompt('Enter month (YYYY-MM):', new Date().toISOString().substring(0, 7));
			if (month) {
				window.location.href = `/dashboard/payment-statement/${influencerId}/pdf?month=${month}`;
			}
		}
	</script>
@endsection
