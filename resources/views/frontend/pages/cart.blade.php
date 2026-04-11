{{-- cart.blade.php --}}
@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="px-4 py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Shopping Cart</h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review your items before proceeding to checkout</p>
    </div>

    @if ($items->count() > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items -->
            <div class="flex-1 min-w-0">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50/50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Package</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencer</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Price</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quantity</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @foreach ($items as $item)
                                    @php
                                        $influencer = $item->package->influencer;
                                        $influencerName = $influencer->display_name ?? $influencer->user->name;
                                        $avatarPath = $influencer->user->profile_image_path ?? ($influencer->profile_image_path ?? '/default.webp');
                                        $avatarUrl = image_url($avatarPath);
                                        $itemTotal = $item->unit_price * $item->quantity;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->package->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $avatarUrl }}" alt="{{ $influencerName }}"
                                                    class="h-8 w-8 rounded-full object-cover"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div style="display:none" class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-xs font-semibold text-indigo-700 dark:text-indigo-300">
                                                    {{ strtoupper(substr($influencerName, 0, 2)) }}
                                                </div>
                                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $influencerName }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900 dark:text-white">${{ number_format($item->unit_price, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="999"
                                                    class="w-16 rounded-lg border border-gray-300 dark:border-gray-700 bg-transparent px-2 py-1.5 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500"
                                                    onchange="this.form.submit()">
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($itemTotal, 2) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <form action="{{ route('cart.remove', $item) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-800 px-6 py-4 bg-gray-50/30 dark:bg-gray-900/30 flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('influencers') }}"
                            class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Continue Shopping
                        </a>
                        <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium transition-colors">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:w-80 flex-shrink-0">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm sticky top-24">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Order Summary</h3>
                        
                        @php
                            $subtotal = (float) $cart->total_price;
                            $pricing = \App\Support\PlatformPricing::calculateFromNet($subtotal);
                            $fee = $pricing['platform_charge'];
                            $total = $pricing['gross_total'];
                        @endphp

                        <div class="space-y-3 pb-4 border-b border-gray-200 dark:border-gray-800">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Platform Charge (20%)</span>
                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($fee, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-4 pb-6">
                            <span class="text-base font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($total, 2) }}</span>
                        </div>

                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                                Proceed to Checkout
                            </button>
                        </form>

                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-4 text-center">
                            You'll be able to message influencers after checkout
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm overflow-hidden">
            <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                <div class="w-20 h-20 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Your cart is empty</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Browse influencers and packages to get started</p>
                <a href="{{ route('influencers') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Browse Influencers
                </a>
            </div>
        </div>
    @endif
</div>
@endsection