@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payment Statement" />

	<div class="flex flex-col gap-6 p-2">
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<input type="hidden" id="influencerSelect" value="{{ $influencer->id }}">
			<div class="flex items-center justify-between gap-4 mb-4">
				<div>
					<h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $influencer->display_name }}</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400">{{ $influencer->user?->email ?? 'N/A' }}</p>
				</div>
				<a href="{{ route('dashboard.payment-statement.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Back</a>
			</div>
			@include('backend.pages.payment-statement._statement', [
				'influencer' => $influencer,
				'statement' => $statement,
				'totalPaid' => $totalPaid,
				'itemCount' => $itemCount,
				'dateRange' => $dateRange,
			])
		</div>
	</div>

	<script>
		function downloadFullPDF() {
			const influencerId = document.getElementById('influencerSelect').value;
			window.location.href = `/dashboard/payment-statement/${influencerId}/pdf`;
		}

		function downloadMonthlyPDF() {
			const influencerId = document.getElementById('influencerSelect').value;
			const month = prompt('Enter month (YYYY-MM):', new Date().toISOString().substring(0, 7));
			if (month) {
				window.location.href = `/dashboard/payment-statement/${influencerId}/pdf?month=${month}`;
			}
		}
	</script>
@endsection