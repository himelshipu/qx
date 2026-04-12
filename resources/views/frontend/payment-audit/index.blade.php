@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-6xl">
		<div class="mb-8">
			<h1 class="text-3xl font-bold mb-2">Payment Audit Log</h1>
			<p class="text-gray-600">Complete history of all payments marked as processed</p>
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
			<!-- Total Paid -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Total Paid</p>
						<p class="text-3xl font-bold text-green-600 mt-2">
							${{ number_format($totalPaid, 2) }}
						</p>
					</div>
					<div class="bg-green-100 rounded-full p-3">
						<svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
							<path
								d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z">
							</path>
						</svg>
					</div>
				</div>
			</div>

			<!-- Total Transactions -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Total Transactions</p>
						<p class="text-3xl font-bold text-blue-600 mt-2">
							{{ $totalTransactions }}
						</p>
					</div>
					<div class="bg-blue-100 rounded-full p-3">
						<svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
								clip-rule="evenodd"></path>
						</svg>
					</div>
				</div>
			</div>

			<!-- This Month -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">This Month</p>
						<p class="text-3xl font-bold text-indigo-600 mt-2">
							${{ number_format($paidThisMonth, 2) }}
						</p>
					</div>
					<div class="bg-indigo-100 rounded-full p-3">
						<svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
							<path
								d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v2h16V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z">
							</path>
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Payment Timeline -->
		<div class="bg-white rounded-lg shadow p-6">
			<h2 class="text-xl font-bold mb-6">Payment Timeline</h2>

			@if ($allPayments->count() > 0)
				<div class="space-y-4">
					@foreach ($allPayments as $payment)
						<div class="border-l-4 border-green-500 rounded-lg p-4 bg-gradient-to-r from-green-50 to-transparent hover:shadow">
							<div class="flex justify-between items-start mb-2">
								<div class="flex-1">
									<div class="flex items-center gap-2 mb-1">
										<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">
											{{ $payment['type'] }}
										</span>
										<span class="text-xs text-gray-500">{{ $payment['marked_at']->format('M d, Y H:i A') }}</span>
									</div>
									<p class="font-semibold text-gray-900">{{ $payment['campaign_or_brand'] }}</p>
									<p class="text-sm text-gray-600">{{ $payment['description'] }}</p>
									<div class="flex gap-4 mt-2 text-xs text-gray-500">
										@if ($payment['reference'])
											<span><strong>Ref:</strong> {{ $payment['reference'] }}</span>
										@endif
										<span><strong>Mark By:</strong> {{ $payment['marked_by'] }}</span>
									</div>
								</div>
								<div class="text-right">
									<p class="text-2xl font-bold text-green-600">${{ number_format($payment['amount'], 2) }}</p>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				<!-- Pagination -->
				@if ($paidItems->hasPages() || $paidSubOrders->hasPages())
					<div class="mt-6 pt-6 border-t">
						<div class="flex justify-center">
							<nav class="inline-flex gap-2">
								@if ($paidItems->onFirstPage() && $paidSubOrders->onFirstPage())
									<span class="px-4 py-2 text-gray-500 cursor-not-allowed">« Previous</span>
								@else
									<a href="{{ $paidItems->previousPageUrl() ?? $paidSubOrders->previousPageUrl() }}"
										class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">« Previous</a>
								@endif

								@if ($paidItems->hasMorePages() || $paidSubOrders->hasMorePages())
									<a href="{{ $paidItems->nextPageUrl() ?? $paidSubOrders->nextPageUrl() }}"
										class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Next »</a>
								@else
									<span class="px-4 py-2 text-gray-500 cursor-not-allowed">Next »</span>
								@endif
							</nav>
						</div>
					</div>
				@endif
			@else
				<div class="text-center py-12 text-gray-500">
					<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<p>No payment records yet. Payments will appear here as they are processed.</p>
				</div>
			@endif
		</div>
	</div>
@endsection
