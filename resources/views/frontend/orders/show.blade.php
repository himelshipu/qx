@extends('frontend.layouts.app')

@section('title', "Order {$order->order_number}")

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-950 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-6xl mx-auto">
			<!-- Header Section -->
			<div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900 mb-8">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Order {{ $order->order_number }}</h1>
						<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
							Placed on {{ optional($order->placed_at ?? $order->created_at)->format('M d, Y \a\t h:i A') }}
						</p>
					</div>
					<div class="flex items-center gap-3">
						@php
							$statusColors = [
								'pending' => 'yellow',
								'accepted' => 'blue',
								'in-progress' => 'purple',
								'in_progress' => 'purple',
								'completed' => 'green',
								'cancelled' => 'red',
								'delivered' => 'green',
							];
							$color = $statusColors[$order->status] ?? 'gray';
						@endphp
						<span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-{{ $color }}-100 dark:bg-{{ $color }}-900/20 text-{{ $color }}-800 dark:text-{{ $color }}-100">
							{{ ucfirst(str_replace('-', ' ', str_replace('_', ' ', $order->status))) }}
						</span>
					</div>
				</div>
			</div>

			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<!-- Main Content -->
				<div class="lg:col-span-2 space-y-6">
					<!-- Order Items -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Items</h2>
						</div>
						<div class="divide-y divide-gray-200 dark:divide-gray-800">
							@forelse ($order->items as $item)
								<div class="p-6">
									<div class="flex items-start justify-between">
										<div class="flex-1">
											<h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->title }}</h3>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												Influencer:
												<span class="font-medium">
													{{ $item->influencer?->display_name ?: $item->influencer?->user?->name ?? 'N/A' }}
												</span>
											</p>

											@if ($item->description)
												<p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2">
													{{ $item->description }}
												</p>
											@endif

											<!-- Item Details Grid -->
											<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4">
												<div>
													<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</p>
													<p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">{{ $item->quantity }}</p>
												</div>
												@if ($item->due_date)
													<div>
														<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</p>
														<p class="text-sm font-semibold text-gray-900 dark:text-white mt-1">
															{{ $item->due_date->format('M d, Y') }}
														</p>
													</div>
												@endif
												<div>
													<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
													@php
														$itemStatusColors = [
															'pending' => 'yellow',
															'accepted' => 'blue',
															'in-progress' => 'purple',
															'in_progress' => 'purple',
															'delivered' => 'green',
															'completed' => 'green',
															'cancelled' => 'red',
														];
														$itemColor = $itemStatusColors[$item->status] ?? 'gray';
													@endphp
													<span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-{{ $itemColor }}-100 dark:bg-{{ $itemColor }}-900/20 text-{{ $itemColor }}-800 dark:text-{{ $itemColor }}-100 mt-1">
														{{ ucfirst(str_replace('_', ' ', $item->status)) }}
													</span>
												</div>
											</div>
										</div>

										<div class="text-right ml-6 min-w-fit">
											<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Price</p>
											<p class="text-lg font-semibold text-gray-900 dark:text-white mt-1">
												${{ number_format($item->unit_price, 2) }}
											</p>
											<p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mt-2">Line Total</p>
											<p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
												${{ number_format($item->line_total, 2) }}
											</p>
										</div>
									</div>
								</div>
							@empty
								<div class="p-6 text-center text-gray-500 dark:text-gray-400">
									No order items found.
								</div>
							@endforelse
						</div>
					</div>

					<!-- Timeline -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Timeline</h2>
						</div>
						<div class="p-6">
							<div class="space-y-6">
								<!-- Order Placed -->
								<div class="flex gap-4">
									<div class="flex flex-col items-center">
										<div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0">
											<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
											</svg>
										</div>
										<div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700 my-2"></div>
									</div>
									<div class="flex-1 pt-1">
										<p class="font-semibold text-gray-900 dark:text-white">Order Placed</p>
										<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
											{{ $order->placed_at?->format('M d, Y \a\t h:i A') }}
										</p>
									</div>
								</div>

								<!-- Awaiting Response or Accepted -->
								@if ($order->accepted_at)
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
												</svg>
											</div>
											<div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700 my-2"></div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">Order Accepted</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												{{ $order->accepted_at->format('M d, Y \a\t h:i A') }}
											</p>
										</div>
									</div>
								@else
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
											</div>
											<div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700 my-2"></div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">Awaiting Influencer Response</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												Influencer will accept or negotiate this order
											</p>
										</div>
									</div>
								@endif

								<!-- In Progress -->
								@if ($order->status === 'in-progress' || $order->status === 'in_progress' || $order->completed_at)
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
												</svg>
											</div>
											<div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700 my-2"></div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">In Progress</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												Influencer is working on your content
											</p>
										</div>
									</div>
								@else
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
											</div>
											<div class="w-0.5 h-12 bg-gray-200 dark:bg-gray-700 my-2"></div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">In Progress</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												Waiting to start
											</p>
										</div>
									</div>
								@endif

								<!-- Completed -->
								@if ($order->completed_at)
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
												</svg>
											</div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">Order Completed</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												{{ $order->completed_at->format('M d, Y \a\t h:i A') }}
											</p>
										</div>
									</div>
								@else
									<div class="flex gap-4">
										<div class="flex flex-col items-center">
											<div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
												<svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
											</div>
										</div>
										<div class="flex-1 pt-1">
											<p class="font-semibold text-gray-900 dark:text-white">Order Completion</p>
											<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
												Pending completion
											</p>
										</div>
									</div>
								@endif
							</div>
						</div>
					</div>

					<!-- Messaging Section -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<h2 class="text-lg font-semibold text-gray-900 dark:text-white">Messages & Communication</h2>
						</div>
						<div class="p-6">
							<p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
								Message the influencer about this order. You can discuss details, ask questions, or share feedback.
							</p>
							<a href="{{ route('frontend.conversations.index') }}"
								class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 dark:hover:bg-purple-600 text-white text-sm font-semibold rounded-lg transition">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
								</svg>
								Open Conversations
							</a>
						</div>
					</div>
				</div>

				<!-- Sidebar -->
				<div class="lg:col-span-1 space-y-6">
					<!-- Order Summary -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<h3 class="font-semibold text-gray-900 dark:text-white">Order Summary</h3>
						</div>
						<div class="p-6 space-y-3">
							<div class="flex justify-between text-sm">
								<span class="text-gray-600 dark:text-gray-400">Subtotal</span>
								<span class="font-medium text-gray-900 dark:text-white">
									${{ number_format($order->items->sum('line_total'), 2) }}
								</span>
							</div>
							<div class="flex justify-between text-sm">
								<span class="text-gray-600 dark:text-gray-400">Total Items</span>
								<span class="font-medium text-gray-900 dark:text-white">{{ $order->items->count() }}</span>
							</div>
							<div class="flex justify-between text-sm">
									<span class="text-gray-600 dark:text-gray-400">Influencers</span>
									<span class="font-medium text-gray-900 dark:text-white">
										{{ $order->items->pluck('influencer_id')->unique()->count() }}
								</span>
							</div>
							<div class="border-t border-gray-200 dark:border-gray-700 pt-3 flex justify-between">
								<span class="font-semibold text-gray-900 dark:text-white">Total Amount</span>
								<span class="text-lg font-bold text-gray-900 dark:text-white">
									${{ number_format($order->items->sum('line_total'), 2) }}
								</span>
							</div>
						</div>
					</div>

					<!-- Order Information -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-800 dark:bg-gray-800/50">
							<h3 class="font-semibold text-gray-900 dark:text-white">Order Information</h3>
						</div>
						<div class="p-6 space-y-3 text-sm">
							<div>
								<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Order Number</p>
								<p class="font-medium text-gray-900 dark:text-white mt-1">{{ $order->order_number }}</p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Placed Date</p>
								<p class="font-medium text-gray-900 dark:text-white mt-1">
									{{ $order->placed_at?->format('M d, Y') }}
								</p>
							</div>
							@if ($order->accepted_at)
								<div>
									<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Accepted Date</p>
									<p class="font-medium text-gray-900 dark:text-white mt-1">
										{{ $order->accepted_at->format('M d, Y') }}
									</p>
								</div>
							@endif
							@if ($order->completed_at)
								<div>
									<p class="text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wider">Completed Date</p>
									<p class="font-medium text-gray-900 dark:text-white mt-1">
										{{ $order->completed_at->format('M d, Y') }}
									</p>
								</div>
							@endif
						</div>
					</div>

					<!-- Actions -->
					<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
						<div class="p-6 space-y-3">
							<a href="{{ route('frontend.orders.index') }}"
								class="block w-full px-4 py-2 text-center bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">
								Back to Orders
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection