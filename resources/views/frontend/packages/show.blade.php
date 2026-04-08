@extends('frontend.layouts.app')

@section('content')
	<section class="py-12">
		<div class="max-w-screen-xl mx-auto px-4">
			<!-- Breadcrumb and Back Button -->
			<div class="mb-8">
				<a href="{{ route('frontend.packages.index') }}"
					class="inline-flex items-center text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-medium transition-colors">
					<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
					Back to Packages
				</a>
			</div>

			<!-- Main Content Grid -->
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
				<!-- Package Details (Left Side) -->
				<div class="lg:col-span-2">
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
						<!-- Header Section -->
						<div class="p-8 border-b border-gray-200 dark:border-gray-700">
							<div class="flex items-start justify-between gap-4 mb-4">
								<div>
									<h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $package->name }}</h1>
									<p class="text-lg text-gray-600 dark:text-gray-400">by {{ $package->influencer->user->name }}</p>
								</div>
								@php
									$platformColors = [
									    'facebook' => '#1877F2',
									    'instagram' => '#E1306C',
									    'tiktok' => '#000000',
									    'linkedin' => '#0A66C2',
									    'x' => '#000000',
									    'youtube' => '#FF0000',
									    'ugc' => '#A855F7',
									    'other' => '#6B7280',
									];
									$platformColor = $platformColors[$package->platform] ?? '#6B7280';
								@endphp
								<span class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white rounded-full whitespace-nowrap"
									style="background-color: {{ $platformColor }}">
									{{ match ($package->platform) {
									    'facebook' => 'Facebook',
									    'instagram' => 'Instagram',
									    'tiktok' => 'TikTok',
									    'linkedin' => 'LinkedIn',
									    'x' => 'X',
									    'youtube' => 'YouTube',
									    'ugc' => 'UGC',
									    'other' => 'Other',
									    default => ucfirst($package->platform),
									} }}
								</span>
							</div>
						</div>

						<!-- Description Section -->
						<div class="p-8 border-b border-gray-200 dark:border-gray-700">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h2>
							<p class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $package->description }}</p>
						</div>

						<!-- Package Details Grid -->
						<div class="p-8 border-b border-gray-200 dark:border-gray-700">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Package Details</h2>
							<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
								<div>
									<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Base Price</p>
									<div class="flex items-baseline gap-1">
										<span
											class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($package->base_price, 0) }}</span>
										<span class="text-sm text-gray-600 dark:text-gray-400">{{ $package->currency ?? 'USD' }}</span>
									</div>
								</div>

								<div>
									<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Delivery Time</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $package->delivery_days }} <span
											class="text-sm text-gray-600 dark:text-gray-400">days</span></p>
								</div>

								<div>
									<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Created</p>
									<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package->created_at->format('M d, Y') }}</p>
								</div>

								<div>
									<p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Orders</p>
									<p class="text-2xl font-bold text-gray-900 dark:text-white">
										{{ $package->orderItems ? $package->orderItems->count() : 0 }}</p>
								</div>
							</div>
						</div>

						<!-- Action Buttons -->
						@if (auth()->user() &&
								auth()->user()->user_type === 'influencer' &&
								$package->influencer_id === auth()->user()->influencer->id)
							<div class="p-8 bg-gray-50 dark:bg-gray-700/50 flex gap-3">
								<a href="{{ route('frontend.packages.edit', $package) }}"
									class="flex-1 inline-flex items-center justify-center px-6 py-3 text-base font-semibold text-white dark:text-gray-900 bg-purple-600 dark:bg-purple-500 rounded-xl hover:bg-purple-700 dark:hover:bg-purple-400 transition-colors active:scale-95">
									<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
									</svg>
									Edit Package
								</a>
								<button onclick="if(confirm('Are you sure?')) document.getElementById('delete-form').submit();"
									class="flex-1 inline-flex items-center justify-center px-6 py-3 text-base font-semibold text-white dark:text-gray-900 bg-red-600 dark:bg-red-500 rounded-xl hover:bg-red-700 dark:hover:bg-red-400 transition-colors active:scale-95">
									<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4v2h16V7h-3z" />
									</svg>
									Delete Package
								</button>
								<form id="delete-form" action="{{ route('frontend.packages.destroy', $package) }}" method="POST"
									class="hidden">
									@csrf
									@method('DELETE')
								</form>
							</div>
						@elseif (auth()->user() && auth()->user()->user_type === 'brand')
							<!-- Brands viewing their purchased packages don't see action buttons here -->
						@else
							<div class="p-8 bg-gray-50 dark:bg-gray-700/50">
								<a href="{{ route('login') }}"
									class="w-full inline-flex items-center justify-center px-6 py-4 text-lg font-semibold text-white bg-purple-600 hover:bg-purple-700 rounded-xl transition-colors active:scale-95">
									<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
									</svg>
									Login to Purchase
								</a>
							</div>
						@endif
					</div>
				</div>

				<!-- Sidebar (Right Side) -->
				<div class="lg:col-span-1">
					<!-- Influencer Card -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
						<div class="p-6">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Influencer</h3>
							<div class="flex items-center gap-4 mb-4">
								<div
									class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
									<span class="text-2xl font-bold text-white">{{ substr($package->influencer->user->name, 0, 1) }}</span>
								</div>
								<div class="flex-1">
									<p class="font-semibold text-gray-900 dark:text-white">{{ $package->influencer->user->name }}</p>
									<p class="text-sm text-gray-600 dark:text-gray-400">{{ $package->platform }} Creator</p>
								</div>
							</div>
							<a href="{{ route('influencer.profile', $package->influencer->user->slug) }}"
								class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/40 transition-colors">
								View Profile
							</a>
						</div>
					</div>

					<!-- Quick Stats Card -->
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
						<div class="p-6">
							<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
							<div class="space-y-4">
								<div class="flex items-center justify-between">
									<span class="text-gray-600 dark:text-gray-400">Total Sales</span>
									<span
										class="text-2xl font-bold text-gray-900 dark:text-white">{{ $package->orderItems ? $package->orderItems->count() : 0 }}</span>
								</div>
								<div class="h-px bg-gray-200 dark:bg-gray-700"></div>
								<div class="flex items-center justify-between">
									<span class="text-gray-600 dark:text-gray-400">Total Revenue</span>
									<span
										class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format(($package->orderItems ? $package->orderItems->count() : 0) * $package->base_price, 0) }}</span>
								</div>
								<div class="h-px bg-gray-200 dark:bg-gray-700"></div>
								<div class="flex items-center justify-between">
									<span class="text-gray-600 dark:text-gray-400">Avg. Delivery</span>
									<span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package->delivery_days }} days</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Orders Section (Only for Package Owner) -->
			@if (auth()->user() &&
					auth()->user()->user_type === 'influencer' &&
					$package->influencer_id === auth()->user()->influencer->id)
				<div class="mt-12">
					<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
						<div class="p-8 border-b border-gray-200 dark:border-gray-700">
							<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Orders & Purchases</h2>
							<p class="text-gray-600 dark:text-gray-400 mt-1">Brands that have purchased this package</p>
						</div>

						@if ($package->orderItems && $package->orderItems->count() > 0)
							<div class="overflow-x-auto">
								<table class="w-full">
									<thead>
										<tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
											<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Date</th>
											<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Brand</th>
											<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Amount</th>
											<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
											<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Action</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($package->orderItems->sortByDesc('created_at') as $orderItem)
											<tr
												class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
												<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
													{{ $orderItem->order->created_at->format('M d, Y') }}
												</td>
												<td class="px-6 py-4">
													<div>
														<p class="text-sm font-medium text-gray-900 dark:text-white">{{ $orderItem->order->brand->name }}</p>
														<p class="text-xs text-gray-600 dark:text-gray-400">{{ $orderItem->order->brand->user->email }}</p>
													</div>
												</td>
												<td class="px-6 py-4">
													<span class="text-sm font-semibold text-gray-900 dark:text-white">
														${{ number_format($orderItem->line_total, 0) }}
													</span>
												</td>
												<td class="px-6 py-4">
													<span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full"
														:class="{
														    'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300': @json($orderItem->order->status) === 'pending',
														    'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300': @json($orderItem->order->status) === 'in_progress',
														    'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300': @json($orderItem->order->status) === 'completed',
														    'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300': @json($orderItem->order->status) === 'cancelled'
														}">
														{{ ucwords(str_replace('_', ' ', $orderItem->order->status)) }}
													</span>
												</td>
												<td class="px-6 py-4">
													@php
														$conversation = $orderItem->order->conversations?->first();
													@endphp
													@if ($conversation)
														<a href="{{ route('conversations.show', $conversation) }}"
															class="text-sm font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 transition-colors">
															Message
														</a>
													@else
														<span class="text-sm text-gray-400 dark:text-gray-500">—</span>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						@else
							<div class="text-center py-12">
								<svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M20 7l-8-4-8 4m16 0l-8 4m0 0l-8-4m8 4v10l8-4v-10M4 7v10l8 4" />
								</svg>
								<p class="text-gray-600 dark:text-gray-400">No orders yet. Your package is ready to be purchased!</p>
							</div>
						@endif
					</div>
				</div>
			@endif
		</div>
	</section>
@endsection
