@extends('backend.layouts.app')

@section('title', 'Payouts')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Payouts" />

	<div class="flex flex-col gap-6 p-2">
		<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
			<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
				<div class="max-w-2xl">
					<p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500 dark:text-slate-400">Offline Payout Ledger</p>
					<h1 class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">Payouts</h1>
					<p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
						This page tracks offline payouts recorded against order items and sub-orders. It is not a gateway transaction log.
					</p>
				</div>
				<div class="flex flex-wrap gap-3">
					<a href="{{ route('dashboard.payment-statement.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
						View Statements
					</a>
					<a href="{{ route('dashboard.payment-audit.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-700 dark:hover:bg-slate-600">
						Open Audit Log
					</a>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm dark:border-blue-900/40 dark:bg-blue-900/15">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Payout Records</p>
				<p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $totalPayouts }}</p>
				<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Total offline payouts tracked</p>
			</div>
			<div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-900/15">
				<p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Total Amount</p>
				<p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($totalAmount, 2) }}</p>
				<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Combined payout value</p>
			</div>
			<div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900/40 dark:bg-amber-900/15">
				<p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">This Month</p>
				<p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">${{ number_format($thisMonthAmount, 2) }}</p>
				<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Payouts marked this month</p>
			</div>
			<div class="rounded-2xl border border-violet-200 bg-violet-50 p-5 shadow-sm dark:border-violet-900/40 dark:bg-violet-900/15">
				<p class="text-xs font-semibold uppercase tracking-wider text-violet-700 dark:text-violet-300">Influencers Paid</p>
				<p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $uniqueInfluencers }}</p>
				<p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Unique payout recipients</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
			<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2 dark:border-slate-800 dark:bg-slate-900">
				<div class="flex items-center justify-between gap-4 mb-5">
					<div>
						<h2 class="text-lg font-bold text-slate-900 dark:text-white">Payout Records</h2>
						<p class="text-sm text-slate-500 dark:text-slate-400">Combined ledger of offline payouts from package work and campaign work.</p>
					</div>
					<div class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
						Recent activity
					</div>
				</div>

				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="border-b border-slate-200 dark:border-slate-700">
							<tr>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Influencer</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Type</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Order / Campaign</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Amount</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Reference</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Marked By</th>
								<th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Paid At</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
							@forelse ($paginatedEntries as $entry)
								<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
									<td class="px-4 py-4">
										<div>
											<p class="font-semibold text-slate-900 dark:text-white">{{ $entry['influencer']?->display_name ?? 'N/A' }}</p>
											<p class="text-xs text-slate-500 dark:text-slate-400">{{ $entry['influencer']?->user?->email ?? 'N/A' }}</p>
										</div>
									</td>
									<td class="px-4 py-4">
										<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $entry['type'] === 'Campaign Work' ? 'bg-violet-100 text-violet-800 dark:bg-violet-900/30 dark:text-violet-200' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200' }}">
											{{ $entry['type'] }}
										</span>
									</td>
									<td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $entry['campaign_or_brand'] }}</td>
									<td class="px-4 py-4 font-semibold text-slate-900 dark:text-white">${{ number_format((float) $entry['amount'], 2) }}</td>
									<td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $entry['reference'] ?? 'N/A' }}</td>
									<td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $entry['marked_by'] }}</td>
									<td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $entry['paid_at']?->format('M d, Y h:i A') }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">No payout records found.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($paginatedEntries->hasPages())
					<div class="mt-6 border-t border-slate-200 pt-4 dark:border-slate-800">
						{{ $paginatedEntries->links() }}
					</div>
				@endif
			</div>

			<div class="space-y-6">
				<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-lg font-bold text-slate-900 dark:text-white">Source Split</h2>
						<span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Totals</span>
					</div>
					<div class="space-y-4">
						<div>
							<div class="mb-1 flex items-center justify-between text-sm">
								<span class="text-slate-600 dark:text-slate-300">Package Work</span>
								<span class="font-semibold text-slate-900 dark:text-white">${{ number_format($packageAmount, 2) }}</span>
							</div>
							<div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800">
								<div class="h-2 rounded-full bg-emerald-500" style="width: {{ $totalAmount > 0 ? min(100, ($packageAmount / $totalAmount) * 100) : 0 }}%"></div>
							</div>
						</div>
						<div>
							<div class="mb-1 flex items-center justify-between text-sm">
								<span class="text-slate-600 dark:text-slate-300">Campaign Work</span>
								<span class="font-semibold text-slate-900 dark:text-white">${{ number_format($campaignAmount, 2) }}</span>
							</div>
							<div class="h-2 rounded-full bg-slate-100 dark:bg-slate-800">
								<div class="h-2 rounded-full bg-violet-500" style="width: {{ $totalAmount > 0 ? min(100, ($campaignAmount / $totalAmount) * 100) : 0 }}%"></div>
							</div>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-lg font-bold text-slate-900 dark:text-white">Top Influencers</h2>
						<span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">By payout</span>
					</div>
					<div class="space-y-3">
						@forelse ($topInfluencers as $index => $row)
							<div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-3 dark:bg-slate-800/70">
								<div class="flex min-w-0 items-center gap-3">
									<div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-bold text-white dark:bg-slate-100 dark:text-slate-900">{{ $index + 1 }}</div>
									<div class="min-w-0">
										<p class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $row['influencer']?->display_name ?? 'N/A' }}</p>
										<p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $row['count'] }} payout records</p>
									</div>
								</div>
								<span class="shrink-0 text-sm font-bold text-emerald-600 dark:text-emerald-300">${{ number_format($row['amount'], 2) }}</span>
							</div>
						@empty
							<p class="py-6 text-center text-sm text-slate-500 dark:text-slate-400">No influencer payout summary yet.</p>
						@endforelse
					</div>
				</div>

				<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
					<div class="flex items-center justify-between mb-4">
						<h2 class="text-lg font-bold text-slate-900 dark:text-white">Recent Entries</h2>
						<span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Last 8</span>
					</div>
					<div class="space-y-3">
						@forelse ($recentEntries as $entry)
							<div class="rounded-xl border border-slate-200 p-3 dark:border-slate-700">
								<p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $entry['influencer']?->display_name ?? 'N/A' }}</p>
								<p class="text-xs text-slate-500 dark:text-slate-400">{{ $entry['campaign_or_brand'] }}</p>
								<div class="mt-2 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
									<span>{{ $entry['type'] }}</span>
									<span class="font-semibold text-slate-900 dark:text-white">${{ number_format((float) $entry['amount'], 2) }}</span>
								</div>
							</div>
						@empty
							<p class="py-6 text-center text-sm text-slate-500 dark:text-slate-400">No recent entries yet.</p>
						@endforelse
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection