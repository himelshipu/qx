@extends('frontend.layouts.app')

@section('content')
	<div class="container mx-auto px-4 py-8 max-w-6xl">
		<div class="mb-8 flex justify-between items-center">
			<div>
				<h1 class="text-3xl font-bold mb-2">Payment Statements</h1>
				<p class="text-gray-600">Download and review your payment statements</p>
			</div>
			<a href="{{ route('payment-statements.pdf') }}" class="btn btn-primary">
				<i class="fas fa-file-pdf mr-2"></i>Download Full Statement
			</a>
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

			<!-- Date Range -->
			<div class="bg-white rounded-lg shadow p-6">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-gray-600 text-sm font-medium">Period Covered</p>
						<p class="text-sm font-semibold mt-2">
							@if ($statementsByMonth->count() > 0)
								{{ $statementsByMonth->last()['month'] }} - {{ $statementsByMonth->first()['month'] }}
							@else
								N/A
							@endif
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

		<!-- Monthly Statements -->
		<div class="bg-white rounded-lg shadow p-6">
			<h2 class="text-xl font-bold mb-6">Monthly Statements</h2>

			@if ($statementsByMonth->count() > 0)
				<div class="space-y-4">
					@foreach ($statementsByMonth as $statement)
						<div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
							<div class="flex justify-between items-center">
								<div>
									<h3 class="font-semibold text-lg">{{ $statement['month'] }}</h3>
									<p class="text-sm text-gray-600">
										{{ $statement['items']->count() }} transaction(s) •
										<strong>${{ number_format($statement['total'], 2) }}</strong>
									</p>
								</div>
								<div class="flex gap-2">
									<a href="{{ route('payment-statements.pdf.monthly', ['month' => $statement['month']]) }}"
										class="btn btn-sm btn-outline">
										<i class="fas fa-download"></i> Download PDF
									</a>
									<button type="button" onclick="toggleMonth('month-{{ $loop->index }}')" class="btn btn-sm btn-ghost">
										<i class="fas fa-chevron-down"></i>
									</button>
								</div>
							</div>

							<!-- Month Details (Hidden by default) -->
							<div id="month-{{ $loop->index }}" class="hidden mt-4 pt-4 border-t">
								<div class="overflow-x-auto">
									<table class="w-full text-sm">
										<thead class="bg-gray-50 border-b">
											<tr>
												<th class="px-4 py-2 text-left font-semibold">Type</th>
												<th class="px-4 py-2 text-left font-semibold">Campaign/Order</th>
												<th class="px-4 py-2 text-left font-semibold">Description</th>
												<th class="px-4 py-2 text-right font-semibold">Amount</th>
												<th class="px-4 py-2 text-center font-semibold">Date</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($statement['items'] as $item)
												<tr class="border-b hover:bg-gray-50">
													<td class="px-4 py-2">
														@if ($item['type'] === 'Package Work')
															<span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Package</span>
														@else
															<span class="inline-block bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">Campaign</span>
														@endif
													</td>
													<td class="px-4 py-2 font-medium">{{ $item['campaign_or_order'] }}</td>
													<td class="px-4 py-2 text-gray-600">{{ $item['description'] }}</td>
													<td class="px-4 py-2 text-right font-semibold text-green-600">
														${{ number_format($item['amount'], 2) }}
													</td>
													<td class="px-4 py-2 text-center text-sm">
														{{ $item['paid_date']->format('M d, Y') }}
													</td>
												</tr>
											@endforeach
										</tbody>
										<tfoot class="bg-gray-50 border-t font-semibold">
											<tr>
												<td colspan="3" class="px-4 py-2 text-right">Month Subtotal:</td>
												<td class="px-4 py-2 text-right text-green-600">
													${{ number_format($statement['total'], 2) }}
												</td>
												<td></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>
					@endforeach
				</div>

				<!-- Grand Total -->
				<div class="mt-6 pt-6 border-t bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-6">
					<div class="flex justify-between items-center">
						<div>
							<p class="text-gray-600 font-medium">Grand Total (All Time)</p>
							<p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($totalPaid, 2) }}</p>
						</div>
						<div class="text-right">
							<p class="text-gray-600 font-medium">Total Transactions</p>
							<p class="text-2xl font-bold text-blue-600 mt-1">{{ $totalTransactions }}</p>
						</div>
					</div>
				</div>
			@else
				<div class="text-center py-12 text-gray-500">
					<svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<p>No payment statements available yet.</p>
				</div>
			@endif
		</div>
	</div>

	<script>
		function toggleMonth(monthId) {
			const element = document.getElementById(monthId);
			element.classList.toggle('hidden');
		}
	</script>
@endsection
