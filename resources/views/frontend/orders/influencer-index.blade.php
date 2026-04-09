{{-- frontend/pages/orders/influencer-index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'My Work Orders')

@section('content')
<section class="py-10" x-data="influencerOrdersFilter()">
    <div class="max-w-screen-2xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Work Orders</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage packages you've been hired to create, track deadlines, and monitor payments</p>
        </div>

        <!-- Filters Section -->
        @php
            $ordersCount = $orders->count();
        @endphp
        @if ($ordersCount > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-3 mb-8">
                <div>
                    <input type="text" x-model="search" placeholder="Search by order number..." @keyup="filterOrders()"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 text-sm focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition" />
                </div>
                <div class="relative">
                    <select x-model="status" @change="filterOrders()"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition appearance-none cursor-pointer pr-10">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 dark:text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
                <button @click="resetFilters()"
                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-sm rounded-lg transition-colors">
                    Reset
                </button>
            </div>
        @endif
        
        <!-- Empty State (no orders at all) -->
        <template x-if="orders.length === 0">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No work orders yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-sm">When brands purchase your packages, you'll see the work orders here. Make sure your packages are published and active!</p>
                </div>
            </div>
        </template>

        <!-- Empty State (filtered none) -->
        <template x-if="filteredOrders.length === 0 && orders.length > 0">
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No work orders found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">No orders match your search or status filter. Try adjusting your criteria.</p>
                <button @click="resetFilters()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filters
                </button>
            </div>
        </template>

        <!-- Orders Grid -->
        <div class="space-y-4" x-show="filteredOrders.length > 0">
            <template x-for="order in filteredOrders" :key="order.id">
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 hover:shadow-md transition-shadow duration-300">
                    <div class="flex flex-col gap-4">
                        <!-- Header: Order # + Brand + Status + Button (all on same row, vertically centered) -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order <span x-text="order.order_number"></span></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    From <span class="font-medium" x-text="order.brand_name"></span> • Placed <span x-text="order.created_at"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-3 flex-shrink-0">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap" 
                                    :class="getStatusColor(order.status)"
                                    x-text="capitalizeStatus(order.status)">
                                </span>
                                <a :href="`/orders/${order.id}`" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition-colors whitespace-nowrap">
                                    View & Manage
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Work Details Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Your Packages</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1 line-clamp-2" :title="order.package_names" x-text="order.package_names"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Items Count</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1" x-text="order.item_count"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Payment</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-1" x-text="'$' + parseFloat(order.total_amount).toFixed(2)"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Due Date</p>
                                <template x-if="order.due_date">
                                    <p class="text-sm font-bold mt-1" :class="order.is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">
                                        <span x-text="order.due_date"></span>
                                        <template x-if="order.is_overdue">
                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">(Overdue!)</span>
                                        </template>
                                    </p>
                                </template>
                                <template x-if="!order.due_date">
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">—</p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Pagination -->
        <div x-show="filteredOrders.length > 0" class="mt-8">
            <!-- Note: Pagination is handled client-side, showing all filtered results -->
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span x-text="filteredOrders.length"></span> of <span x-text="orders.length"></span> work orders
            </p>
        </div>
    </div>
</section>

<!-- Alpine.js Script -->
@php
    $influencerId = auth()->user()->influencer?->id;
    $ordersData = $orders
        ->map(
            fn($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'created_at' => $order->created_at->format('M d, Y'),
                'status' => $order->status,
                'brand_name' => $order->buyer->brand->name ?? 'N/A',
                'package_names' => $order->items->where('influencer_id', $influencerId)->map(fn($item) => $item->package->name ?? 'N/A')->implode(', '),
                'item_count' => $order->items->where('influencer_id', $influencerId)->count(),
                'total_amount' => $order->items->where('influencer_id', $influencerId)->sum('unit_price'),
                'due_date' => $order->items->where('influencer_id', $influencerId)->first()?->due_date?->format('M d, Y'),
                'is_overdue' => $order->items->where('influencer_id', $influencerId)->first()?->due_date?->isPast() && !in_array($order->status, ['completed', 'cancelled']),
            ],
        )
        ->values()
        ->all();
@endphp

<script>
    function influencerOrdersFilter() {
        return {
            search: '',
            status: '',
            orders: @json($ordersData),
            filteredOrders: [],

            init() {
                this.filterOrders();
            },

            filterOrders() {
                const search = this.search.toLowerCase();
                const status = this.status;

                this.filteredOrders = this.orders.filter(order => {
                    const matchesSearch = !search || order.order_number.toLowerCase().includes(search);
                    const matchesStatus = !status || order.status === status;

                    return matchesSearch && matchesStatus;
                });
            },

            resetFilters() {
                this.search = '';
                this.status = '';
                this.filterOrders();
            },

            getStatusColor(status) {
                const colors = {
                    'pending': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                    'accepted': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                    'processing': 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                    'completed': 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
                    'cancelled': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                };
                return colors[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20';
            },

            capitalizeStatus(status) {
                return status.charAt(0).toUpperCase() + status.slice(1);
            }
        }
    }
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Work Orders</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage packages you've been hired to create, track deadlines, and monitor payments</p>
    </div>

    <!-- Filters Section -->
    @if (!$orders->isEmpty())
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <form method="GET" action="{{ route('frontend.orders.index') }}" class="flex flex-1 gap-3">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search by order number..." 
                    class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                
                <select name="status" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <button type="submit" class="rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white hover:bg-purple-700">
                    Filter
                </button>
            </form>
        </div>
    @endif

    @if ($orders->isEmpty())
        <!-- Empty State -->
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No work orders yet</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-sm">When brands purchase your packages, you'll see the work orders here. Make sure your packages are published and active!</p>
            </div>
        </div>
    @else
        <!-- Orders List -->
        <div class="space-y-4">
            @foreach ($orders as $order)
                @php
                    $influencerId = auth()->user()->influencer?->id;
                    $influencerItems = $order->items->where('influencer_id', $influencerId);
                    $firstItem = $influencerItems->first();
                    $packageNames = $influencerItems->map(fn($item) => $item->package->name ?? 'N/A')->implode(', ');
                    $totalAmount = $influencerItems->sum('unit_price');
                    $itemCount = $influencerItems->count();
                    
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                        'accepted' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                        'processing' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                    ];
                    $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/20';
                    
                    $isOverdue = $firstItem?->due_date?->isPast() && !in_array($order->status, ['completed', 'cancelled']);
                @endphp
                
                <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 hover:shadow-md transition-shadow duration-300">
                    <div class="flex flex-col gap-4">
                        <!-- Header: Order # + Status -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order {{ $order->order_number }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    From <span class="font-medium">{{ $order->buyer->brand->name ?? 'N/A' }}</span> • Placed {{ $order->created_at->format('M d, Y') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap {{ $statusColor }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <!-- Work Details Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Your Packages</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1 line-clamp-2" title="{{ $packageNames }}">{{ $packageNames }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Items Count</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1">{{ $itemCount }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Payment</p>
                                <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-1">${{ number_format($totalAmount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Due Date</p>
                                @if ($firstItem?->due_date)
                                    <p class="text-sm font-bold {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }} mt-1">
                                        {{ $firstItem->due_date->format('M d, Y') }}
                                        @if ($isOverdue)
                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">(Overdue!)</span>
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">—</p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex items-center gap-2 pt-2">
                            <a href="{{ route('frontend.orders.show', $order) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition-colors">
                                View & Manage
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($orders->hasPages())
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
                                