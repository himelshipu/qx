@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-4xl mx-auto">
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Checkout</h1>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
				<!-- Order Summary -->
				<div class="lg:col-span-2">
					<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
						<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Order Items</h2>

						<div class="space-y-4">
							@foreach ($items as $item)
								@php
									$creator = $item->package->creator;
								@endphp
								<div class="flex items-center gap-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
									<div class="flex-1">
										<h3 class="font-semibold text-gray-900 dark:text-white">
											{{ $item->package->name }}
										</h3>
										<p class="text-sm text-gray-600 dark:text-gray-400">
											by {{ $creator->display_name ?? $creator->user->name }}
										</p>
										<p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
											{{ $item->package->description ?? 'No description' }}
										</p>
									</div>
									<div class="text-right">
										<p class="text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
										<p class="font-semibold text-gray-900 dark:text-white">
											${{ number_format($item->unit_price * $item->quantity, 2) }}
										</p>
									</div>
								</div>
							@endforeach
						</div>
					</div>

					<!-- Next Steps Info -->
					<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/40 rounded-xl p-6">
						<div class="flex items-start gap-3">
							<svg class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
								viewBox="0 0 20 20">
								<path fill-rule="evenodd"
									d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z"
									clip-rule="evenodd"></path>
							</svg>
							<div>
								<h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">What Happens Next?</h3>
								<ol class="text-sm text-blue-800 dark:text-blue-200 space-y-1">
									<li>✓ Your order will be created</li>
									<li>✓ A conversation will open with each creator</li>
									<li>✓ You can discuss details and negotiate terms</li>
									<li>✓ Track progress until delivery</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<!-- Checkout Sidebar -->
				<div class="lg:col-span-1">
					<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 sticky top-4">
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Order Summary</h3>

						<div class="space-y-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
							<div class="flex justify-between text-sm">
								<span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
								<span class="font-medium text-gray-900 dark:text-white">
									${{ number_format($cart->total_price, 2) }}
								</span>
							</div>
							<div class="flex justify-between text-sm">
								<span class="text-gray-600 dark:text-gray-400">Items:</span>
								<span class="font-medium text-gray-900 dark:text-white">
									{{ $items->sum('quantity') }}
								</span>
							</div>
							<div class="flex justify-between text-sm">
								<span class="text-gray-600 dark:text-gray-400">Creators:</span>
								<span class="font-medium text-gray-900 dark:text-white">
									{{ $items->groupBy('package.creator_id')->count() }}
								</span>
							</div>
						</div>

						<div class="flex justify-between mb-6">
							<span class="font-semibold text-gray-900 dark:text-white">Total:</span>
							<span class="text-lg font-bold text-gray-900 dark:text-white">
								${{ number_format($cart->total_price, 2) }}
							</span>
						</div>

						<form action="{{ route('cart.complete-checkout') }}" method="POST">
							@csrf
							<button type="submit"
								class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition mb-3">
								Complete Order
							</button>
						</form>

						<a href="{{ route('cart.index') }}"
							class="block text-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 text-sm font-medium py-2">
							← Back to Cart
						</a>

						<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
							<p class="text-xs text-gray-500 dark:text-gray-400">
								💡 After clicking "Complete Order", you'll be redirected to your conversations where you can message the creators
								directly.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
