{{-- frontend/pages/orders/brand-index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'My Orders')

@section('content')
<section class="py-10" x-data="ordersFilter()">
    <div class="max-w-screen-2xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Orders</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Track and manage all your influencer package purchases</p>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No orders yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-sm">You haven't purchased any influencer packages yet. Browse available packages to get started.</p>
                    <a href="{{ route('frontend.packages.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Browse Packages
                    </a>
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No orders found</h3>
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
                        <!-- Header: Order # + Status + Button (all on same row, vertically centered) -->
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order <span x-text="order.order_number"></span></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Placed on <span x-text="order.created_at"></span>
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap" 
                                    :class="getStatusColor(order.status)"
                                    x-text="capitalizeStatus(order.status)">
                                </span>
                                <a :href="`/orders/${order.id}`" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-600 text-white text-sm font-medium hover:bg-purple-700 transition-colors whitespace-nowrap">
                                    View Details
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Order Details Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Influencers</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1 line-clamp-1" :title="order.influencers" x-text="order.influencers"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Packages</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1" x-text="order.package_count + ' item' + (order.package_count > 1 ? 's' : '')"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Total Cost</p>
                                <p class="text-sm font-bold text-purple-600 dark:text-purple-400 mt-1" x-text="'$' + parseFloat(order.total_amount).toFixed(2)"></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 uppercase font-medium">Order Status</p>
                                <p class="text-sm font-bold mt-1" :class="getStatusTextColor(order.status)" x-text="getStatusText(order.status)"></p>
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
                Showing <span x-text="filteredOrders.length"></span> of <span x-text="orders.length"></span> orders
            </p>
        </div>
    </div>
</section>

<!-- Alpine.js Script -->
@php
    $ordersData = $orders
        ->map(function ($order) {
            $items = $order->items;

            if ($items->isEmpty() && $order->relationLoaded('childOrders')) {
                $items = $order->childOrders->flatMap(fn ($childOrder) => $childOrder->items);
            }

            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'created_at' => $order->created_at->format('M d, Y'),
                'status' => $order->status,
                'influencers' => $items->map(fn ($item) => $item->influencer->user->name ?? 'N/A')->unique()->implode(', '),
                'package_count' => $items->count(),
                'total_amount' => $order->total_amount,
            ];
        })
        ->values()
        ->all();
@endphp

<script>
    function ordersFilter() {
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

            getStatusTextColor(status) {
                const colors = {
                    'pending': 'text-amber-600 dark:text-amber-400',
                    'accepted': 'text-blue-600 dark:text-blue-400',
                    'processing': 'text-purple-600 dark:text-purple-400',
                    'completed': 'text-green-600 dark:text-green-400',
                    'cancelled': 'text-red-600 dark:text-red-400',
                };
                return colors[status] || 'text-gray-700 dark:text-gray-300';
            },

            getStatusText(status) {
                const texts = {
                    'pending': 'Awaiting',
                    'accepted': 'In Progress',
                    'processing': 'In Progress',
                    'completed': 'Done',
                    'cancelled': 'Cancelled',
                };
                return texts[status] || status.charAt(0).toUpperCase() + status.slice(1);
            },

            capitalizeStatus(status) {
                return status.charAt(0).toUpperCase() + status.slice(1);
            }
        }
    }
</script>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection