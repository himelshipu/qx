@extends('backend.layouts.app')

@section('title', 'Cart Details')

@section('content')
	<div class="space-y-8">
		<!-- Header -->
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Cart Details</h1>
				<p class="text-gray-600 dark:text-gray-400 mt-2">
					@if ($isAdmin)
						Review cart for {{ $cart->user->brand->company_name ?? $cart->user->name }}
					@else
						Your Shopping Cart
					@endif
				</p>
			</div>
			<a href="{{ $isAdmin ? route('dashboard.carts.index') : route('dashboard.index') }}"
				class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
				← Back
			</a>
		</div>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
			<!-- Cart Items -->
			<div class="lg:col-span-2 space-y-6">
				<!-- Cart Owner Info (Admin View) -->
				@if ($isAdmin)
					<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
						<h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-4">Cart Owner Information</h3>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
							<div>
								<label class="block text-sm text-blue-800 dark:text-blue-300 font-medium mb-1">Brand Name</label>
								<p class="text-blue-900 dark:text-blue-100 font-semibold">{{ $cart->user->brand->company_name ?? 'N/A' }}</p>
							</div>
							<div>
								<label class="block text-sm text-blue-800 dark:text-blue-300 font-medium mb-1">Contact Person</label>
								<p class="text-blue-900 dark:text-blue-100 font-semibold">{{ $cart->user->name }}</p>
							</div>
							<div>
								<label class="block text-sm text-blue-800 dark:text-blue-300 font-medium mb-1">Email</label>
								<p class="text-blue-900 dark:text-blue-100">{{ $cart->user->email }}</p>
							</div>
							<div>
								<label class="block text-sm text-blue-800 dark:text-blue-300 font-medium mb-1">Phone</label>
								<p class="text-blue-900 dark:text-blue-100">{{ $cart->user->phone ?? 'N/A' }}</p>
							</div>
						</div>
					</div>
				@endif

				<!-- Items List -->
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Cart Items ({{ $cart->items->count() }})</h2>

					@if ($cart->items->count() > 0)
						<div class="space-y-4">
							@foreach ($cart->items as $item)
								<div class="flex gap-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
									@if ($item->package->influencer)
										<img src="{{ image_url($item->package->influencer->profile_image_path ?? '/default.webp') }}"
											alt="{{ $item->package->influencer->user->name }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0" />
									@endif

									<div class="flex-1 min-w-0">
										<div class="flex justify-between items-start mb-2">
											<div>
												<h4 class="font-semibold text-gray-900 dark:text-white">{{ $item->package->name }}</h4>
												<p class="text-sm text-gray-600 dark:text-gray-400">
													by <span class="font-medium">{{ $item->package->influencer->user->name ?? 'Unknown' }}</span>
												</p>
											</div>
											<div class="text-right flex-shrink-0">
												<p class="text-lg font-bold text-gray-900 dark:text-white">
													${{ number_format($item->unit_price * $item->quantity, 2) }}
												</p>
												<p class="text-xs text-gray-500 dark:text-gray-400">
													${{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}
												</p>
											</div>
										</div>

										@if ($item->package->description)
											<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ Str::limit($item->package->description, 100) }}
											</p>
										@endif

										<div class="flex flex-wrap gap-2 justify-between items-start mt-3">
											<div class="flex flex-wrap gap-2 text-xs">
												@if ($item->package->delivery_days)
													<span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
														⏱️ {{ $item->package->delivery_days }} day{{ $item->package->delivery_days > 1 ? 's' : '' }} delivery
													</span>
												@endif
												@if ($item->currency)
													<span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
														💱 {{ $item->currency }}
													</span>
												@endif
												<span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">
													Added {{ $item->created_at->format('M d, Y') }}
												</span>
											</div>
											@if (!$isAdmin)
												<form method="POST" action="{{ route('cart.remove', $item) }}" style="display: inline;">
													@csrf
													@method('DELETE')
													<button type="submit"
														class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline transition-colors">
														Remove
													</button>
												</form>
											@endif
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="text-center py-12">
							<svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
							</svg>
							<p class="text-gray-600 dark:text-gray-400 font-medium">Your cart is empty</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Sidebar: Summary & Actions -->
			<div class="space-y-6">
				<!-- Cart Summary -->
				<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Order Summary</h3>

					<div class="space-y-3">
						<div class="flex justify-between text-gray-600 dark:text-gray-400">
							<span>Items:</span>
							<span class="font-medium">{{ $cart->items->count() }}</span>
						</div>

						<div class="flex justify-between text-gray-600 dark:text-gray-400">
							<span>Influencers:</span>
							<span class="font-medium">{{ $cart->items->count() }} influencer{{ $cart->items->count() !== 1 ? 's' : '' }}</span>
						</div>

						@php
							$subtotal = $cart->items->sum(function ($item) {
							    return $item->unit_price * $item->quantity;
							});
						@endphp

						<div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
							<div class="flex justify-between text-gray-900 dark:text-white font-bold text-lg">
								<span>Subtotal:</span>
								<span>${{ number_format($subtotal, 2) }}</span>
							</div>
						</div>

						<div class="text-sm text-gray-500 dark:text-gray-400 pt-2">
							Tax and shipping calculated at checkout
						</div>
					</div>

					<!-- Cart Status -->
					<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
						<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">Cart Status</label>
						<div
							class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if ($cart->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100
                        @elseif($cart->status === 'abandoned')
                            bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100
                        @else
                            bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 @endif
                    ">
							{{ ucfirst($cart->status) }}
						</div>
					</div>

					<!-- Created/Updated -->
					<div
						class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2 text-xs text-gray-500 dark:text-gray-400">
						<div class="flex justify-between">
							<span>Created:</span>
							<span>{{ $cart->created_at->format('M d, Y · h:i A') }}</span>
						</div>
						@if ($cart->updated_at->ne($cart->created_at))
							<div class="flex justify-between">
								<span>Last Updated:</span>
								<span>{{ $cart->updated_at->format('M d, Y · h:i A') }}</span>
							</div>
						@endif
					</div>
				</div>

				<!-- Actions -->
				@if ($cart->items->count() > 0 && !$isAdmin && $cart->status === 'active')
					<div class="space-y-3">
						<form method="POST" action="{{ route('cart.checkout') }}">
							@csrf
							<button type="submit"
								class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
								Proceed to Checkout
							</button>
						</form>
						<a href="{{ route('influencers') }}"
							class="w-full block text-center px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
							Continue Shopping
						</a>
					</div>
				@endif

				@if ($cart->items->count() === 0 && !$isAdmin)
					<a href="{{ route('influencers') }}"
						class="w-full block text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
						Start Shopping
					</a>
				@endif

				<!-- Admin Info Box -->
				@if ($isAdmin)
					<div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
						<h4 class="font-semibold text-purple-900 dark:text-purple-100 mb-2">Admin Actions</h4>
						<p class="text-sm text-purple-800 dark:text-purple-300">
							This cart is being viewed in admin mode. You can see all details but cannot modify it directly.
						</p>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
