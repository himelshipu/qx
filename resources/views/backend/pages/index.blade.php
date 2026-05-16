@extends('backend.layouts.app')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Dashboard" />

	<div class="flex flex-col gap-6 p-2">
		<!-- ============ SECTION 1: KEY PERFORMANCE INDICATORS (KPIs) ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
			<!-- Total Orders KPI -->
			<a href="{{ route('dashboard.orders.index') }}" class="block group">
				<div
					class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-700 rounded-xl p-5 hover:shadow-lg transition-all">
					<div class="flex items-center justify-between mb-3">
						<div
							class="w-12 h-12 rounded-lg bg-blue-500 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path d="M3 3h18v2H3V3zm0 4h18v2H3V7zm0 4h18v2H3v-2zm0 4h18v2H3v-2zm0 4h18v2H3v-2z" />
							</svg>
						</div>
						<span
							class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900/40 px-3 py-1 rounded-full">Total</span>
					</div>
					<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalOrders }}</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Orders</p>
				</div>
			</a>

			<!-- Completed Orders KPI -->
			<a href="{{ route('dashboard.orders.index') . '?status=completed' }}" class="block group">
				<div
					class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-700 rounded-xl p-5 hover:shadow-lg transition-all">
					<div class="flex items-center justify-between mb-3">
						<div
							class="w-12 h-12 rounded-lg bg-green-500 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
							</svg>
						</div>
						<span
							class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-3 py-1 rounded-full">{{ number_format($conversionRate, 1) }}%</span>
					</div>
					<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $completedOrders }}</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Completed Orders</p>
				</div>
			</a>

			<!-- Pending Orders KPI -->
			<a href="{{ route('dashboard.orders.index') . '?status=pending' }}" class="block group">
				<div
					class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-5 hover:shadow-lg transition-all">
					<div class="flex items-center justify-between mb-3">
						<div
							class="w-12 h-12 rounded-lg bg-yellow-500 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
							</svg>
						</div>
						<span
							class="text-xs font-bold text-yellow-600 dark:text-yellow-400 bg-yellow-100 dark:bg-yellow-900/40 px-3 py-1 rounded-full">Awaiting</span>
					</div>
					<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingOrders }}</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Pending Orders</p>
				</div>
			</a>

			<!-- Total Revenue KPI -->
			<a href="{{ route('dashboard.orders.index') }}" class="block group">
				<div
					class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-700 rounded-xl p-5 hover:shadow-lg transition-all">
					<div class="flex items-center justify-between mb-3">
						<div
							class="w-12 h-12 rounded-lg bg-purple-500 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
							<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
							</svg>
						</div>
						<span
							class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/40 px-3 py-1 rounded-full">Revenue</span>
					</div>
					<p class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($totalOrderValue, 2) }}</p>
					<p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Total Order Value</p>
				</div>
			</a>
		</div>

		<!-- ============ SECTION 2: PLATFORM METRICS ============ -->
		<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
			<!-- Brands Metric -->
			<a href="{{ route('dashboard.brands.index') }}" class="block group">
				<div
					class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all">
					<div class="flex items-center gap-3 mb-2">
						<div
							class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z" />
							</svg>
						</div>
						<span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">Brands</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBrands }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activeBrands }} verified</p>
				</div>
			</a>

			<!-- Influencers Metric -->
			<a href="{{ route('dashboard.influencers.index') }}" class="block group">
				<div
					class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all">
					<div class="flex items-center gap-3 mb-2">
						<div class="w-10 h-10 rounded-lg bg-pink-100 dark:bg-pink-900/30 text-pink-600 flex items-center justify-center">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
							</svg>
						</div>
						<span class="text-xs font-semibold text-pink-600 dark:text-pink-400">Influencers</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalInfluencers }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activeInfluencers }} active</p>
				</div>
			</a>

			<!-- Packages Metric -->
			<a href="{{ route('dashboard.packages.index') }}" class="block group">
				<div
					class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all">
					<div class="flex items-center gap-3 mb-2">
						<div
							class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
							</svg>
						</div>
						<span class="text-xs font-semibold text-orange-600 dark:text-orange-400">Packages</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalPackages }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Available</p>
				</div>
			</a>

			<!-- Campaigns Metric -->
			<a href="{{ route('dashboard.campaigns.standard') }}" class="block group">
				<div
					class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-all">
					<div class="flex items-center gap-3 mb-2">
						<div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 flex items-center justify-center">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h4.89z" />
							</svg>
						</div>
						<span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400">Campaigns</span>
					</div>
					<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalCampaigns }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $publishedCampaigns }} published</p>
				</div>
			</a>
		</div>

		<!-- ============ SECTION 3: ORDER STATUS & REVENUE ============ -->
		<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
			<!-- Order Status Distribution -->
			<div
				class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h4.89z" />
					</svg>
					Order Status Overview
				</h3>
				<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
					<div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-900">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Total</p>
						<p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalOrders }}</p>
					</div>
					<div
						class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-900">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Completed</p>
						<p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $completedOrders }}</p>
					</div>
					<div
						class="text-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-900">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">Pending</p>
						<p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingOrders }}</p>
					</div>
					<div
						class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-100 dark:border-purple-900">
						<p class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-2">In Progress</p>
						<p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $inProgressOrders }}</p>
					</div>
				</div>
			</div>

			<!-- Revenue Summary -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
					</svg>
					Revenue Summary
				</h3>
				<div class="space-y-3">
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</span>
						<span class="font-bold text-gray-900 dark:text-white">${{ number_format($totalOrderValue, 2) }}</span>
					</div>
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">Completed</span>
						<span class="font-bold text-green-600 dark:text-green-400">${{ number_format($completedOrderValue, 2) }}</span>
					</div>
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
						<span class="font-bold text-yellow-600 dark:text-yellow-400">${{ number_format($pendingOrderValue, 2) }}</span>
					</div>
					<div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
						<span class="text-sm text-gray-600 dark:text-gray-400">Service Fees</span>
						<span class="font-bold text-gray-900 dark:text-white">${{ number_format($totalServiceFees, 2) }}</span>
					</div>
				</div>
			</div>
		</div>

		<!-- ============ SECTION 4: TOP PERFORMERS ============ -->
		<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
			<!-- Top Brands -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
					<span class="flex items-center gap-2">
						<svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
						</svg>
						Top Brands
					</span>
					<a href="{{ route('dashboard.brands.index') }}"
						class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">View All</a>
				</h3>
				<div class="space-y-3">
					@forelse ($topBrands as $index => $brand)
						<div
							class="flex flex-col md:flex-row gap-2 items-start md:items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<div class="flex items-center gap-3 flex-1">
								<div
									class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center text-sm font-bold">
									{{ $index + 1 }}
								</div>
								<div>
									<p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $brand->brand_name }}</p>
									<p class="text-[10px] sm:text-xs text-gray-500">{{ $brand->industry ?? 'N/A' }}</p>
								</div>
							</div>
							<span class="text-[10px] sm:text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ $brand->orders_count ?? 0 }}
								orders</span>
						</div>
					@empty
						<p class="text-sm text-gray-500 text-center py-3">No brands yet</p>
					@endforelse
				</div>
			</div>

			<!-- Top Influencers -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
					<span class="flex items-center gap-2">
						<svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
						</svg>
						Top Influencers
					</span>
					<a href="{{ route('dashboard.influencers.index') }}"
						class="text-xs text-pink-600 dark:text-pink-400 hover:underline">View All</a>
				</h3>
				<div class="space-y-3">
					@forelse ($topInfluencers as $index => $influencer)
						<div
							class="flex flex-col md:flex-row gap-2 items-start md:items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<div class="flex items-center gap-3 flex-1">
								<div
									class="w-8 h-8 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 flex items-center justify-center text-sm font-bold">
									{{ $index + 1 }}
								</div>
								<div>
									<p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $influencer->display_name }}</p>
									<p class="text-[10px] sm:text-xs text-gray-500">Creator Profile</p>
								</div>
							</div>
							<span class="text-[10px] sm:text-xs font-bold text-pink-600 dark:text-pink-400">{{ $influencer->orders_count ?? 0 }}
								orders</span>
						</div>
					@empty
						<p class="text-sm text-gray-500 text-center py-3">No influencers yet</p>
					@endforelse
				</div>
			</div>

			<!-- Top Packages -->
			<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center justify-between">
					<span class="flex items-center gap-2">
						<svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z" />
						</svg>
						Top Packages
					</span>
					<a href="{{ route('dashboard.packages.index') }}"
						class="text-xs text-orange-600 dark:text-orange-400 hover:underline">View All</a>
				</h3>
				<div class="space-y-3">
					@forelse ($topPackages as $index => $package)
						<div
							class="flex flex-col md:flex-row gap-2 items-start md:items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
							<div class="flex items-center gap-3 flex-1">
								<div
									class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center text-sm font-bold">
									{{ $index + 1 }}
								</div>
								<div>
									<p class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $package->name }}</p>
									<p class="text-[10px] sm:text-xs text-gray-500">${{ number_format($package->price, 2) }}</p>
								</div>
							</div>
							<span class="text-[10px] sm:text-xs font-bold text-orange-600 dark:text-orange-400">{{ $package->orders_count ?? 0 }}
								orders</span>
						</div>
					@empty
						<p class="text-sm text-gray-500 text-center py-3">No packages yet</p>
					@endforelse
				</div>
			</div>
		</div>

		<!-- ============ SECTION 5: RECENT ORDERS TABLE ============ -->
		<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
			<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex flex-col md:flex-row items-center justify-between">
				<span class="flex items-center gap-2">
					<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h4.89z" />
					</svg>
					Recent Orders
				</span>
				<a href="{{ route('dashboard.orders.index') }}"
					class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View All Orders</a>
			</h3>
			<div class="overflow-x-auto">
				<table class="w-full text-sm">
					<thead class="border-b border-gray-200 dark:border-gray-700">
						<tr>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Order #</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Brand</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Amount</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Status</th>
							<th class="text-left py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Date</th>
							<th class="text-center py-3 px-4 font-semibold text-gray-700 dark:text-gray-300">Action</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse ($recentOrders as $order)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
								<td class="py-4 px-4">
									<a href="{{ route('dashboard.orders.show', $order->id) }}"
										class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
										{{ $order->order_number }}
									</a>
								</td>
								<td class="py-4 px-4">
									<span class="text-gray-900 dark:text-white">{{ $order->brand->brand_name ?? 'N/A' }}</span>
								</td>
								<td class="py-4 px-4 font-semibold text-gray-900 dark:text-white">${{ number_format($order->total_amount, 2) }}
								</td>
								<td class="py-4 px-4">
									@php
										$statusColors = [
										    'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
										    'in-progress' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
										    'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
										    'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
										];
										$color = $statusColors[$order->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300';
									@endphp
									<span class="px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
										{{ ucfirst(str_replace('-', ' ', $order->status)) }}
									</span>
								</td>
								<td class="py-4 px-4 text-gray-600 dark:text-gray-400">
									{{ $order->created_at->format('M d, Y') }}
								</td>
								<td class="py-4 px-4 text-center">
									<a href="{{ route('dashboard.orders.show', $order->id) }}"
										class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-semibold">
										View
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="py-8 text-center text-gray-500 dark:text-gray-400">
									No orders yet
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<!-- ============ SECTION 6: CHARTS ============ -->
		<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
			<x-backend.shell.chart :chartData="$monthlyUsers" title="Monthly User Registrations" />
			<x-backend.shell.statistics-chart :monthlyOrders="$monthlyOrders" :monthlyCampaigns="$monthlyCampaigns" />
		</div>

	</div>

	<style>
		[x-cloak] {
			display: none !important;
		}
	</style>
@endsection
