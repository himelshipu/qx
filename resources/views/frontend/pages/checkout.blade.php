{{-- checkout.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Checkout</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review and confirm your order</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Order Items Section -->
        <div class="flex-1 min-w-0">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden mb-6">
                <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Order Items</h2>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach ($items as $item)
                        @php
                            $creator = $item->package->creator;
                            $creatorName = $creator->display_name ?? $creator->user->name;
                        @endphp
                        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-sm font-semibold text-indigo-700 dark:text-indigo-300 flex-shrink-0">
                                    {{ strtoupper(substr($creatorName, 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $item->package->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">by {{ $creatorName }}</p>
                                    @if ($item->package->description)
                                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1 line-clamp-1">{{ $item->package->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right sm:text-left">
                                <p class="text-sm text-gray-600 dark:text-gray-400">Quantity: {{ $item->quantity }}</p>
                                <p class="font-semibold text-gray-900 dark:text-white">${{ number_format($item->unit_price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Next Steps Info -->
            <div class="rounded-2xl border border-blue-200 bg-blue-50 dark:border-blue-900/40 dark:bg-blue-900/20 p-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-900 dark:text-blue-100">What happens next?</h3>
                        <ul class="mt-2 text-sm text-blue-800 dark:text-blue-200 space-y-1">
                            <li>✓ Your order will be created</li>
                            <li>✓ A conversation will open with each creator</li>
                            <li>✓ You can discuss details and negotiate terms</li>
                            <li>✓ Track progress until delivery</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Sidebar -->
        <div class="lg:w-80 flex-shrink-0">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm sticky top-24">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>

                    <div class="space-y-3 pb-4 border-b border-gray-200 dark:border-gray-800">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">${{ number_format($cart->total_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Items</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $items->sum('quantity') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Creators</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $items->groupBy('package.creator_id')->count() }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-4 pb-6">
                        <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($cart->total_price, 2) }}</span>
                    </div>

                    <form action="{{ route('cart.complete-checkout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                            Place Order
                        </button>
                    </form>

                    <a href="{{ route('cart.index') }}"
                        class="block text-center text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 text-sm font-medium py-3 mt-2 transition-colors">
                        ← Back to Cart
                    </a>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            After placing your order, you'll be redirected to your conversations where you can message creators directly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection