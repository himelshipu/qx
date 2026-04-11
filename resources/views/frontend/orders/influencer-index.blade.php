{{-- frontend/pages/orders/influencer-index.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'My Work Orders')

@section('content')
@php
    $influencerId = auth()->user()->influencer?->id;
    $ordersData = $orders->map(function ($order) use ($influencerId) {
        $items = $order->items->where('influencer_id', $influencerId);
        $firstItem = $items->first();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'created_at' => $order->created_at->format('M d, Y'),
            'status' => $order->status,
            'brand_name' => $order->buyer->brand->name ?? 'N/A',
            'package_names' => $items->map(fn ($item) => $item->package->name ?? 'N/A')->implode(', '),
            'item_count' => $items->count(),
            'total_amount' => $items->sum('line_total'),
            'due_date' => $firstItem?->due_date?->format('M d, Y'),
            'is_overdue' => (bool) ($firstItem?->due_date?->isPast() && ! in_array($order->status, ['completed', 'cancelled'], true)),
        ];
    })->values()->all();
@endphp

<section class="py-10" x-data="influencerOrdersFilter()">
    <div class="max-w-screen-2xl mx-auto px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Work Orders</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Track assigned package work, deadlines, and payment status.</p>
        </div>

        @if (! empty($ordersData))
            <div class="grid grid-cols-1 gap-3 mb-8 md:grid-cols-3 lg:grid-cols-3">
                <div>
                    <input type="text" x-model="search" placeholder="Search by order number..." @keyup="filterOrders()"
                        class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                </div>
                <div class="relative">
                    <select x-model="status" @change="filterOrders()"
                        class="w-full appearance-none rounded-lg border border-gray-200 bg-white px-4 py-2.5 pr-10 text-sm text-gray-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="delivered">Delivered</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
                <button @click="resetFilters()" class="w-full rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    Reset
                </button>
            </div>
        @endif

        <template x-if="orders.length === 0">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-col items-center justify-center px-4 py-16 text-center">
                    <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                        <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">No work orders yet</h3>
                    <p class="max-w-sm text-gray-600 dark:text-gray-400">When brands purchase your packages, the assigned work orders will appear here.</p>
                </div>
            </div>
        </template>

        <template x-if="filteredOrders.length === 0 && orders.length > 0">
            <div class="py-16 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">No work orders found</h3>
                <p class="mb-6 text-gray-600 dark:text-gray-400">No orders match your search or status filter. Try adjusting your criteria.</p>
                <button @click="resetFilters()" class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-purple-700">
                    Reset Filters
                </button>
            </div>
        </template>

        <div class="space-y-4" x-show="filteredOrders.length > 0">
            <template x-for="order in filteredOrders" :key="order.id">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow duration-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0 flex-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Order <span x-text="order.order_number"></span></h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">From <span class="font-medium" x-text="order.brand_name"></span> • Placed <span x-text="order.created_at"></span></p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold whitespace-nowrap" :class="getStatusColor(order.status)" x-text="capitalizeStatus(order.status)"></span>
                                <a :href="`/orders/${order.id}`" class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-700">
                                    View & Manage
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800/50 md:grid-cols-4">
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Your Packages</p>
                                <p class="mt-1 line-clamp-2 text-sm font-bold text-gray-900 dark:text-white" :title="order.package_names" x-text="order.package_names"></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Items Count</p>
                                <p class="mt-1 text-sm font-bold text-gray-900 dark:text-white" x-text="order.item_count"></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Subtotal</p>
                                <p class="mt-1 text-sm font-bold text-green-600 dark:text-green-400" x-text="'$' + parseFloat(order.total_amount).toFixed(2)"></p>
                            </div>
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-600 dark:text-gray-400">Due Date</p>
                                <template x-if="order.due_date">
                                    <p class="mt-1 text-sm font-bold" :class="order.is_overdue ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white'">
                                        <span x-text="order.due_date"></span>
                                        <span x-show="order.is_overdue" class="text-xs font-medium text-red-600 dark:text-red-400">(Overdue)</span>
                                    </p>
                                </template>
                                <template x-if="!order.due_date">
                                    <p class="mt-1 text-sm text-gray-400 dark:text-gray-500">—</p>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div x-show="filteredOrders.length > 0" class="mt-8 text-sm text-gray-600 dark:text-gray-400">
            Showing <span x-text="filteredOrders.length"></span> of <span x-text="orders.length"></span> work orders
        </div>
    </div>
</section>

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
                    pending: 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                    accepted: 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                    processing: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                    in_progress: 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300',
                    delivered: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                    completed: 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-300',
                    cancelled: 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300',
                };
                return colors[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-500/20';
            },
            capitalizeStatus(status) {
                return status.charAt(0).toUpperCase() + status.slice(1).replaceAll('_', ' ');
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