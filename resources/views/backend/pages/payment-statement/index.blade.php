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

		<!-- ============ SECTION 2: INFLUENCER STATEMENT ============ -->
		<div id="statementContainer" class="hidden"></div>

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
				const response = await fetch(`/dashboard/payment-statement/${influencerId}`, {
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						'Accept': 'text/html',
					},
				});

				if (!response.ok) {
					throw new Error(`HTTP ${response.status}`);
				}

				const html = await response.text();
				const statementContainer = document.getElementById('statementContainer');
				statementContainer.innerHTML = html;
				document.getElementById('statementContainer').classList.remove('hidden');
				document.getElementById('emptyState').classList.add('hidden');
			} catch (error) {
				console.error(error);
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
