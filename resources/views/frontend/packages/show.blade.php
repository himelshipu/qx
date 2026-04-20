@php
	$isOwner =
	    auth()->check() &&
	    auth()->user()->user_type === 'influencer' &&
	    $package->influencer_id === auth()->user()->influencer->id;
	$isBrand = auth()->check() && auth()->user()->user_type === 'brand';
	$isAdmin = auth()->check() && in_array(auth()->user()->user_type, ['admin', 'superadmin']);
	$purchasedOrder = $isBrand
	    ? $package->orderItems()->whereHas('order', fn($q) => $q->where('brand_id', auth()->user()->brand->id))->first()
	    : null;
@endphp

@extends('frontend.layouts.app')

@section('content')
	<section class="py-10 ">
		<div class="max-w-screen-xl mx-auto px-4 space-y-8">

			<!-- Back & Actions -->
			<div class="flex justify-between items-center">
				<a href="{{ route('frontend.packages.index') }}"
					class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400">
					<x-icons.chevron-right class="w-4 h-4 -rotate-180" />
					Back to Packages
				</a>

				@if ($isOwner || $isAdmin)
					<div class="flex items-center gap-3">
						<a href="{{ route('frontend.packages.edit', $package) }}"
							class="px-4 py-2 text-xs font-semibold text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition">
							Edit Package
						</a>
						<form action="{{ route('frontend.packages.destroy', $package) }}" method="POST">
							@csrf @method('DELETE')
							<button onclick="return confirm('Are you sure? This action is irreversible.')"
								class="px-4 py-2 text-xs font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
								Delete
							</button>
						</form>
					</div>
				@endif
			</div>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

				<!-- Main Content -->
				<div class="lg:col-span-2 space-y-8">

					<!-- Package Header -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8">
						<div class="flex justify-between items-start">
							<div>
								<h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $package->name }}</h1>
								<p class="text-sm text-gray-500 mt-1">
									by <a href="{{ route('influencer.profile', $package->influencer->user->slug) }}"
										class="font-medium text-purple-600 hover:underline">{{ $package->influencer->user->name }}</a>
								</p>
							</div>
							<span class="text-xs font-semibold px-3 py-1 rounded-full"
								style="background-color: {{ \App\Helpers\PlatformHelper::getPlatformColor($package->platform) }}; color: white;">
								{{ \App\Helpers\PlatformHelper::getPlatformName($package->platform) }}
							</span>
						</div>
						<p class="text-gray-700 dark:text-gray-300 mt-6 leading-relaxed">{{ $package->description }}</p>
					</div>

					<!-- Purchase Details (for Brands who purchased) -->
					@if ($purchasedOrder)
						<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Your Order Details</h2>
							<div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
								<div>
									<p class="text-xs text-gray-500">Order Status</p>
									<p class="text-lg font-bold text-purple-600 dark:text-purple-400">
										{{ ucwords(str_replace('_', ' ', $purchasedOrder->order->status)) }}</p>
								</div>
								<div>
									<p class="text-xs text-gray-500">Purchased On</p>
									<p class="text-lg font-bold text-gray-900 dark:text-white">
										{{ $purchasedOrder->order->created_at->format('M d, Y') }}</p>
								</div>
								<div>
									<p class="text-xs text-gray-500">Paid</p>
									<p class="text-lg font-bold text-green-600">
										${{ number_format($purchasedOrder->line_total, 2) }}</p>
								</div>
								<div>
									<a href="{{ route('frontend.orders.show', $purchasedOrder->order_id) }}"
										class="w-full px-4 py-2 text-sm font-semibold text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition">
										View Order
									</a>
								</div>
							</div>
						</div>
					@endif

					<!-- Recent Orders (for Owner/Admin) -->
					@if ($isOwner || $isAdmin)
						<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
							<div class="p-6 border-b dark:border-gray-700">
								<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Orders</h2>
							</div>
							@if ($package->orderItems->count() > 0)
								<div class="overflow-x-auto">
									<table class="w-full text-sm">
										<thead class="bg-gray-50 dark:bg-gray-700/40">
											<tr>
												<th class="px-6 py-3 text-left">Brand</th>
												<th class="px-6 py-3 text-left">Date</th>
												<th class="px-6 py-3 text-left">Amount</th>
												<th class="px-6 py-3 text-left">Status</th>
												<th class="px-6 py-3 text-right"></th>
											</tr>
										</thead>
										<tbody class="divide-y dark:divide-gray-700">
											@foreach ($package->orderItems->sortByDesc('created_at')->take(5) as $item)
												<tr>
													<td class="px-6 py-4">{{ $item->order->brand->name }}</td>
													<td class="px-6 py-4 text-gray-500">{{ $item->order->created_at->format('M d, Y') }}</td>
													<td class="px-6 py-4 font-semibold">${{ number_format($item->line_total, 2) }}</td>
													<td class="px-6 py-4">
														<span
															class="px-2 py-1 text-xs font-medium rounded-full {{ \App\Helpers\OrderStatusHelper::getStatusClasses($item->order->status) }}">
															{{ ucwords(str_replace('_', ' ', $item->order->status)) }}
														</span>
													</td>
													<td class="px-6 py-4 text-right">
														<a href="{{ route('dashboard.orders.show', $item->order_id) }}"
															class="text-purple-600 hover:underline text-xs">View</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							@else
								<p class="p-6 text-center text-gray-500">No orders yet for this package.</p>
							@endif
						</div>
					@endif

				</div>

				<!-- Sidebar -->
				<div class="space-y-6">
					<!-- Pricing & Purchase Card -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
						<div class="flex justify-between items-center mb-4">
							<p class="text-xs text-gray-500">Price</p>
							<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($package->base_price, 0) }}
							</p>
						</div>
						<div class="flex justify-between items-center mb-6">
							<p class="text-xs text-gray-500">Delivery</p>
							<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package->delivery_days }} days</p>
						</div>

						@if ($isBrand && !$purchasedOrder)
							<a href="{{ route('cart.add', $package->id) }}"
								class="w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-purple-600 rounded-xl hover:bg-purple-700 transition">
								<x-icons.shopping-cart class="w-4 h-4" />
								Add to Cart
							</a>
						@elseif($isOwner)
							<p class="text-xs text-center text-gray-500 bg-gray-100 dark:bg-gray-700 p-3 rounded-lg">This is your
								package.</p>
						@elseif($purchasedOrder)
							<p class="text-xs text-center text-gray-500 bg-green-100 dark:bg-green-900/30 p-3 rounded-lg">You have
								already purchased this package.</p>
						@else
							<a href="{{ route('login') }}"
								class="w-full flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-purple-600 rounded-xl hover:bg-purple-700 transition">
								Login to Purchase
							</a>
						@endif
					</div>

					<!-- Influencer Card -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6">
						<h3 class="text-sm font-semibold text-gray-500 mb-4 uppercase">About the Influencer</h3>
						<div class="flex items-center gap-4">
							<img src="{{ $package->influencer->user->avatar_url }}" alt="{{ $package->influencer->user->name }}"
								class="w-14 h-14 rounded-full object-cover">
							<div>
								<p class="font-semibold text-gray-900 dark:text-white">{{ $package->influencer->user->name }}</p>
								<p class="text-xs text-gray-500">{{ $package->influencer->user->tagline ?? 'Creator' }}</p>
							</div>
						</div>
						<div class="grid grid-cols-2 gap-4 mt-4 text-center text-sm">
							<div>
								<p class="text-xs text-gray-500">Followers</p>
								<p class="font-bold text-gray-900 dark:text-white">
									{{ \App\Helpers\NumberHelper::format($package->influencer->platformStats->sum('follower_count')) }}
								</p>
							</div>
							
						</div>
						<a href="{{ route('influencer.profile', $package->influencer->user->slug) }}"
							class="mt-4 block text-center w-full px-4 py-2 text-sm font-medium text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 dark:bg-purple-900/20 dark:text-purple-300 dark:hover:bg-purple-800/30 transition">
							View Profile
						</a>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
