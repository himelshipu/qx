<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
	<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800 sm:px-6">
		<div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
			<div>
				<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Payment</h2>
				<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Submit payment details for this order and track admin
					verification.</p>
			</div>
			<span
				class="rounded-full px-3 py-1 text-xs font-semibold {{ $brandPaymentStateClass }}">{{ $brandPaymentStateLabel }}</span>
		</div>
	</div>
	<div class="space-y-5 p-5 sm:p-6">
		<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Order Total</p>
				<p class="mt-2 text-lg font-semibold text-indigo-900 dark:text-indigo-50">${{ number_format($brandTotalAmount, 2) }}
				</p>
			</div>
			<div
				class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/40 dark:bg-emerald-900/20">
				<p class="text-xs uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Confirmed Paid</p>
				<p class="mt-2 text-lg font-semibold text-emerald-900 dark:text-emerald-50">
					${{ number_format($brandConfirmedTotal, 2) }}</p>
			</div>
			<div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/20">
				<p class="text-xs uppercase tracking-wider text-amber-700 dark:text-amber-300">Pending Review</p>
				<p class="mt-2 text-lg font-semibold text-amber-900 dark:text-amber-50">${{ number_format($brandPendingTotal, 2) }}
				</p>
			</div>
			<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-900/20">
				<p class="text-xs uppercase tracking-wider text-rose-700 dark:text-rose-300">Balance</p>
				<p class="mt-2 text-lg font-semibold text-rose-900 dark:text-rose-50">
					@if ($brandOverpaidAmount > 0)
						+${{ number_format($brandOverpaidAmount, 2) }}
					@elseif ($brandBalanceDue > 0)
						-${{ number_format($brandBalanceDue, 2) }}
					@else
						Cleared
					@endif
				</p>
			</div>
		</div>

		<div class="space-y-3">
			<div class="flex items-center justify-between gap-3">
				<h3 class="text-sm font-semibold text-gray-900 dark:text-white">Submitted Payments</h3>
				<span class="text-xs text-gray-500 dark:text-gray-400">{{ $brandPayments->count() }}
					record{{ $brandPayments->count() === 1 ? '' : 's' }}</span>
			</div>
			@if ($brandPayments->isNotEmpty())
				<div class="space-y-3">
					@foreach ($brandPayments as $payment)
						@php
							$paymentStateClass = match ($payment->status) {
							    'confirmed' => 'bg-emerald-100 text-emerald-700',
							    'rejected' => 'bg-rose-100 text-rose-700',
							    default => 'bg-amber-100 text-amber-700',
							};
							$submittedAt = $payment->submitted_at ?: $payment->created_at;
						@endphp
						<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60">
							<div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
								<div>
									<div class="flex flex-wrap items-center gap-2">
										<span
											class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $paymentStateClass }}">{{ ucfirst($payment->status) }}</span>
										<span
											class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format((float) $payment->amount, 2) }}</span>
									</div>
									<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Submitted
										{{ $submittedAt?->format('M d, Y h:i A') ?? 'just now' }}</p>
									<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Ref: {{ $payment->reference_number ?: 'N/A' }} •
										Invoice: {{ $payment->invoice_id ?: 'N/A' }}</p>
								</div>
								<div class="text-xs text-gray-600 dark:text-gray-400">{{ $payment->brandUser?->name ?? 'Brand' }}</div>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div
					class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
					No payment submissions yet.</div>
			@endif

			<form action="{{ route('frontend.orders.brand-payments.store', $order) }}" method="POST"
				class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/60 xl:grid-cols-2">
				@csrf
				<div class="xl:col-span-2">
					<h3 class="text-sm font-semibold text-gray-900 dark:text-white">Submit Payment Details</h3>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter amount and reference or invoice ID.</p>
				</div>
				<label class="space-y-1">
					<span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</span>
					<input type="number" name="amount" min="0.01" step="0.01" required
						value="{{ number_format($brandBalanceDue > 0 ? $brandBalanceDue : $brandTotalAmount, 2, '.', '') }}"
						class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
				</label>
				<label class="space-y-1">
					<span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference Number</span>
					<input type="text" name="reference_number" maxlength="120"
						class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
				</label>
				<label class="space-y-1">
					<span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice ID</span>
					<input type="text" name="invoice_id" maxlength="120"
						class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
				</label>
				<label class="space-y-1 xl:col-span-2">
					<span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Note</span>
					<textarea name="brand_note" rows="3" maxlength="1000"
					 class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
				</label>
				<div class="xl:col-span-2">
					<button type="submit"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">Submit
						Payment</button>
				</div>
			</form>
		</div>
	</div>
</div>
