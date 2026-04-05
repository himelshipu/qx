@extends('backend.layouts.app')

@section('title', 'Shopping Carts')

@section('content')
	<div class="space-y-8">
		<!-- Header -->
		<div class="flex justify-between items-center">
			<div>
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Shopping Carts</h1>
				<p class="text-gray-600 dark:text-gray-400 mt-2">Manage and view all brand shopping carts</p>
			</div>
		</div>

		<!-- Filters Card -->
		<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
			<form method="GET" class="space-y-4">
				<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<!-- Search -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Search by Email or Brand Name
						</label>
						<input type="text" name="search" value="{{ request('search') }}" placeholder="Enter email or brand name..."
							class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
					</div>

					<!-- Brand Filter -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Filter by Brand
						</label>
						<select name="brand_id"
							class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
							<option value="">All Brands</option>
							@foreach ($brands as $brand)
								<option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
									{{ $brand->company_name }}
								</option>
							@endforeach
						</select>
					</div>

					<!-- Status Filter -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
							Filter by Status
						</label>
						<select name="status"
							class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
							<option value="">All Statuses</option>
							<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
							<option value="abandoned" {{ request('status') == 'abandoned' ? 'selected' : '' }}>Abandoned</option>
							<option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
						</select>
					</div>
				</div>

				<div class="flex gap-2">
					<button type="submit"
						class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
						Apply Filters
					</button>
					<a href="{{ route('carts.index') }}"
						class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors font-medium">
						Reset
					</a>
				</div>
			</form>
		</div>

		<!-- Carts Table -->
		<div
			class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
						<tr>
							<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Brand / User</th>
							<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Items</th>
							<th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">Subtotal</th>
							<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
							<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Created</th>
							<th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@forelse($carts as $cart)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
								<td class="px-6 py-4">
									<div class="flex flex-col">
										<span class="font-medium text-gray-900 dark:text-white">
											{{ $cart->user->brand->company_name ?? $cart->user->name }}
										</span>
										<span class="text-sm text-gray-500 dark:text-gray-400">{{ $cart->user->email }}</span>
									</div>
								</td>
								<td class="px-6 py-4">
									<span
										class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100">
										{{ $cart->items->count() }} item{{ $cart->items->count() !== 1 ? 's' : '' }}
									</span>
								</td>
								<td class="px-6 py-4 text-right">
									<span class="font-semibold text-gray-900 dark:text-white">
										${{ number_format($cart->items->sum(function ($item) {return $item->unit_price * $item->quantity;}),2) }}
									</span>
								</td>
								<td class="px-6 py-4">
									<span
										class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if ($cart->status === 'active') bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-100
                                    @elseif($cart->status === 'abandoned')
                                        bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-100
                                    @else
                                        bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100 @endif
                                ">
										{{ ucfirst($cart->status) }}
									</span>
								</td>
								<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
									{{ $cart->created_at->format('M d, Y') }}
								</td>
								<td class="px-6 py-4 text-right">
									<a href="{{ route('carts.show', $cart) }}"
										class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
										View Details
										<svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
										</svg>
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-6 py-12 text-center">
									<div class="flex flex-col items-center justify-center">
										<svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
										</svg>
										<h3 class="text-gray-600 dark:text-gray-400 font-medium">No shopping carts found</h3>
										<p class="text-gray-500 dark:text-gray-500 text-sm mt-1">Try adjusting your filters or search criteria</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			@if ($carts->hasPages())
				<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
					{{ $carts->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
