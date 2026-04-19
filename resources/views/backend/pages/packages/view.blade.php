@extends('backend.layouts.app')

@section('title', 'Package Details')

@section('content')
	@php
		$statusClass = $package->is_active
		    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
		    : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Package Details" />

	<div class="space-y-6">
		<!-- Header Section -->
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $package->name }}</h2>
					<div class="mt-2 flex flex-wrap items-center gap-2">
						<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
							{{ $package->is_active ? 'Active' : 'Inactive' }}
						</span>
						<span
							class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
							{{ \Illuminate\Support\Str::upper($package->platform) }}
						</span>
					</div>
				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('dashboard.packages.edit', $package) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.edit class="h-4 w-4" />
						Edit Package
					</a>
					<a href="{{ route('dashboard.packages.index') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back to List
					</a>
				</div>
			</div>
		</div>

		<!-- Influencer Profile Section -->
		@if ($package->influencer)
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Package Influencer</h3>
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center">
					@if ($package->influencer->user?->profile_image_path)
				<img src="{{ \App\Helpers\ImageHelper::url($package->influencer->user->profile_image_path) }}"
							alt="{{ $package->influencer->user->name }}"
							class="h-16 w-16 rounded-full object-cover">
					@else
						<div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg">
							{{ substr($package->influencer->user?->name ?? 'C', 0, 1) }}
						</div>
					@endif
					<div>
						<p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $package->influencer->user?->name ?? 'N/A' }}</p>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ $package->influencer->user?->email ?? 'N/A' }}</p>
						@if ($package->influencer->bio)
							<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $package->influencer->bio }}</p>
						@endif
						<div class="mt-3 flex gap-4 text-sm">
							@if ($package->influencer->followers_count)
								<div>
									<span class="font-semibold text-gray-900 dark:text-white">{{ number_format($package->influencer->followers_count) }}</span>
									<span class="text-gray-500 dark:text-gray-400">Followers</span>
								</div>
							@endif
							
						</div>
					</div>
				</div>
			</div>
		@endif

		<!-- Stats Grid -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Base Price</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ \Illuminate\Support\Str::upper($package->currency) }} {{ number_format((float) $package->base_price, 2) }}
				</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Delivery Days</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ $package->delivery_days ?? '-' }}
				</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Revisions</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ $package->revisions_included ?? '-' }}
				</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Orders</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ $orders?->count() ?? 0 }}
				</p>
			</div>
		</div>

		<!-- Grid Layout -->
		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<!-- Package Information -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Package Information</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Platform</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Str::upper($package->platform) }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Currency</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ \Illuminate\Support\Str::upper($package->currency) }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Delivery Days</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $package->delivery_days ?? 'Not specified' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Revisions</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $package->revisions_included ?? 'Not specified' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Created At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ $package->created_at?->format('M d, Y h:i A') ?? 'N/A' }}
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Last Updated</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ $package->updated_at?->format('M d, Y h:i A') ?? 'N/A' }}
						</dd>
					</div>
				</dl>
			</div>

			<!-- Usage Statistics -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Usage Statistics</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Cart Items</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $package->cart_items_count }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Order Items</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $package->order_items_count }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Total Usage</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							{{ $package->cart_items_count + $package->order_items_count }}
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Status</dt>
						<dd class="font-medium">
							<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
								{{ $package->is_active ? 'Active' : 'Inactive' }}
							</span>
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Total Orders</dt>
						<dd class="font-medium text-green-600 dark:text-green-400 text-lg">
							{{ $orders?->count() ?? 0 }}
						</dd>
					</div>
				</dl>
			</div>
		</div>

		<!-- Description -->
		@if ($package->description)
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Description</h3>
				<p class="mt-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
					{{ $package->description }}
				</p>
			</div>
		@endif

		<!-- Orders Table -->
		@if ($orders && $orders->count() > 0)
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brands That Ordered This Package</h3>
				<div class="overflow-x-auto">
					<table class="min-w-full">
						<thead>
							<tr class="border-b border-gray-200 dark:border-gray-700">
								<th class="flex-1 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Contact</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Date</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Amount</th>
								<th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@foreach ($orders as $order)
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
									<td class="flex-1 px-4 py-3">
										<div>
											<p class="font-medium text-gray-900 dark:text-white">{{ $order->brand?->brand_name ?? 'N/A' }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">Order #{{ $order->order_number }}</p>
										</div>
									</td>
									<td class="px-4 py-3">
										<div>
											<p class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->brand?->user?->name ?? 'N/A' }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->brand?->user?->email ?? 'N/A' }}</p>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
										{{ $order->created_at?->format('M d, Y') ?? 'N/A' }}
										<br>
										<span class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at?->format('h:i A') ?? '' }}</span>
									</td>
									<td class="px-4 py-3">
										<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
											@if($order->status === 'pending')
												bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
											@elseif($order->status === 'accepted')
												bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
											@elseif($order->status === 'completed')
												bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
											@else
												bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300
											@endif
										">
											{{ ucfirst($order->status) }}
										</span>
									</td>
									<td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
										{{ strtoupper($order->currency) }} {{ number_format((float) $order->total_amount, 2) }}
									</td>
									<td class="px-4 py-3 text-center">
										<a href="{{ route('dashboard.orders.show', $order->id) }}"
											class="inline-flex items-center justify-center h-8 w-8 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition"
											title="View Order Details">
											<x-icons.eye class="h-4 w-4" />
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		@else
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900 text-center">
				<p class="text-sm text-gray-500 dark:text-gray-400">No orders for this package yet</p>
			</div>
		@endif
	</div>
@endsection
