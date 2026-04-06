@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-4xl mx-auto">
			<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Shopping Cart</h1>

			@if ($items->count() > 0)
				<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
					<!-- Cart Items -->
					<div class="lg:col-span-2">
						<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
							<table class="w-full">
								<thead class="border-b border-gray-200 dark:border-gray-700">
									<tr>
										<th class="text-left text-sm font-semibold text-gray-900 dark:text-white pb-4">Package</th>
										<th class="text-left text-sm font-semibold text-gray-900 dark:text-white pb-4">Creator</th>
										<th class="text-left text-sm font-semibold text-gray-900 dark:text-white pb-4">Price</th>
										<th class="text-left text-sm font-semibold text-gray-900 dark:text-white pb-4">Qty</th>
										<th class="text-right text-sm font-semibold text-gray-900 dark:text-white pb-4">Total</th>
										<th class="text-right text-sm font-semibold text-gray-900 dark:text-white pb-4">Action</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@foreach ($items as $item)
										@php
											$creator = $item->package->creator;
										@endphp
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
											<td class="py-4 text-sm font-medium text-gray-900 dark:text-white">
												{{ $item->package->name }}
											</td>
											<td class="py-4 text-sm text-gray-600 dark:text-gray-400">
												{{ $creator->display_name ?? $creator->user->name }}
											</td>
											<td class="py-4 text-sm text-gray-900 dark:text-white">
												${{ number_format($item->unit_price, 2) }}
											</td>
											<td class="py-4 text-sm">
												<form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
													@csrf
													@method('PUT')
													<input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="999"
														class="w-12 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-2 py-1 text-sm"
														onchange="this.form.submit()">
												</form>
											</td>
											<td class="py-4 text-sm font-semibold text-gray-900 dark:text-white text-right">
												${{ number_format($item->unit_price * $item->quantity, 2) }}
											</td>
											<td class="py-4 text-sm text-right">
												<form action="{{ route('cart.remove', $item) }}" method="POST" class="inline">
													@csrf
													@method('DELETE')
													<button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">
														Remove
													</button>
												</form>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>

							<div class="mt-6 flex gap-3 justify-between">
								<a href="{{ route('influencers') }}"
									class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
									<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
									</svg>
									Continue Shopping
								</a>
								<form action="{{ route('cart.clear') }}" method="POST" class="inline">
									@csrf
									<button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-sm font-medium">
										Clear Cart
									</button>
								</form>
							</div>
						</div>
					</div>

					<!-- Cart Summary -->
					<div class="lg:col-span-1">
						<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 sticky top-4">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
							@php
								$subtotal = (float) $cart->total_price;
								$fee = $subtotal * 0.02;
								$total = $subtotal + $fee;
							@endphp

							<div class="space-y-3 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
								<div class="flex justify-between text-sm">
									<span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
									<span class="font-medium text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
								</div>
								<div class="flex justify-between text-sm">
									<span class="text-gray-600 dark:text-gray-400">Fee (2%):</span>
									<span class="font-medium text-gray-900 dark:text-white">${{ number_format($fee, 2) }}</span>
								</div>
							</div>

							<div class="flex justify-between mb-6">
								<span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
								<span class="text-lg font-bold text-gray-900 dark:text-white">${{ number_format($total, 2) }}</span>
							</div>

							<form action="{{ route('cart.checkout') }}" method="POST" class="w-full">
								@csrf
								<button type="submit"
									class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition">
									Proceed to Checkout
								</button>
							</form>

							<p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
								You'll be able to message the creators after checkout
							</p>
						</div>
					</div>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
					<div class="mb-4">
						<svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
						</svg>
					</div>
					<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
					<p class="text-gray-600 dark:text-gray-400 mb-6">Browse our creators and packages to get started</p>
					<a href="{{ route('influencers') }}"
						class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition">
						Start Shopping
					</a>
				</div>
			@endif
		</div>
	</div>
@endsection
