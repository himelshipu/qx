@props(['conversation'])

@php
    $getStatusColor = function($status) {
        return match($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'shipped' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
            'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
        };
    };
@endphp

@if($conversation->order_id)
    <div id="orderDetailsPanel" class="flex-shrink-0 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-950/20 dark:to-purple-950/20 hidden transition-all duration-300">
        <div class="p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icons.shopping-bag class="w-4 h-4 text-indigo-500" />
                    Order Information
                </h4>
                <a href="{{ route('dashboard.orders.show', $conversation->order_id) }}" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                    View Full Order
                    <x-icons.chevron-right class="w-3 h-3" />
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Order ID</p>
                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">#{{ $conversation->order_id }}</p>
                </div>
                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($conversation->order?->total_amount ?? 0, 2) }}</p>
                </div>
                <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg px-3 py-2 backdrop-blur-sm">
                    <p class="text-[10px] uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $getStatusColor($conversation->order?->status ?? 'pending') }}">
                        {{ ucfirst($conversation->order?->status ?? 'Pending') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif