<!-- Shopping Cart Modal Sidebar -->
<div x-show="isCartOpen" x-cloak class="fixed inset-0 z-[200] overflow-hidden" role="dialog" aria-modal="true">

	<!-- Backdrop Blur -->
	<div x-show="isCartOpen" x-transition.opacity @click="isCartOpen = false"
		class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

	<div class="fixed inset-y-0 right-0 flex max-w-full">
		<div x-show="isCartOpen" x-transition:enter="transform transition ease-in-out duration-500"
			x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
			x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0"
			x-transition:leave-end="translate-x-full" class="w-screen max-w-5xl flex shadow-2xl">

			<!-- LEFT PANEL (Estimated Results) -->
			<div class="hidden md:flex flex-col w-[38%] bg-black p-4 lg:p-12 text-white justify-between">
				<div>
					<h2 class="text-3xl font-bold mb-6 tracking-tight">Estimated Results</h2>
					<p class="text-sm text-gray-400 leading-relaxed mb-16">
						Not all influencers will accept your order. Here is a projection of your actual outcome based on acceptance rates.
					</p>

					<div class="space-y-12">
						<!-- Influencer Progress -->
						<div>
							<div class="flex justify-between items-end mb-3">
								<span class="text-xl font-bold" x-text="(cartItems.length > 0 ? 1 : 0) + ' Influencer'"></span>
							</div>
							<div class="h-2 w-full bg-gray-800 rounded-sm overflow-hidden">
								<div class="h-full bg-white transition-all duration-1000"
									:style="`width: ${cartItems.length > 0 ? (1 / cartItems.length) * 100 : 0}%`"></div>
							</div>
							<div class="text-right mt-3 text-sm font-bold text-gray-500" x-text="cartItems.length + ' Influencers'"></div>
						</div>

						<!-- Spend Progress -->
						<div>
							<div class="flex justify-between items-end mb-3">
								<span class="text-xl font-bold" x-text="'$' + projectedSpend + ' Spend'"></span>
							</div>
							<div class="h-2 w-full bg-gray-800 rounded-sm overflow-hidden">
								<div class="h-full bg-white transition-all duration-1000"
									:style="`width: ${subtotal > 0 ? (projectedSpend / subtotal) * 100 : 0}%`"></div>
							</div>
							<div class="text-right mt-3 text-sm font-bold text-gray-500" x-text="'$' + subtotal.toLocaleString()"></div>
						</div>

						<!-- Top Locations -->
						<div x-show="cartItems.length > 0" class="pt-4">
							<h3 class="text-xl font-bold mb-6">Top Audience Locations</h3>
							<ul class="space-y-5">
								<li class="flex items-center gap-4"><span
										class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">1</span><span
										class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">US</span> United
										States</span></li>
								<li class="flex items-center gap-4"><span
										class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">2</span><span
										class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">GB</span> United
										Kingdom</span></li>
								<li class="flex items-center gap-4"><span
										class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">3</span><span
										class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">TH</span> Thailand</span>
								</li>
							</ul>
						</div>
					</div>
				</div>

				<div class="flex items-start gap-4">
					<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
							stroke-width="2" stroke-linecap="round" />
					</svg>
					<p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight leading-4">Payment Protection <br><span
							class="text-gray-600 font-medium normal-case">If an order is declined, funds will be refunded.</span></p>
				</div>
			</div>

			<!-- RIGHT PANEL (Cart List) -->
			<div class="flex-1 flex flex-col bg-white p-4 lg:p-12 justify-between overflow-hidden">
				<div class="flex items-center justify-between border-b pb-8 border-gray-50">
					<h2 class="text-3xl font-bold text-gray-900">Cart</h2>
					<button @click="isCartOpen = false" class="p-2 text-gray-300 hover:text-black transition-all hover:rotate-90">
						<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round" />
						</svg>
					</button>
				</div>

				<!-- Cart Content -->
				<div class="flex-1 overflow-y-auto py-4">
					<!-- POPULATED STATE -->
					<div x-show="cartItems.length > 0" class="space-y-8">
						<template x-for="item in cartItems" :key="item.id">
							<div class="flex items-start gap-4">
								<img :src="item.image" class="w-16 h-16 rounded-xl object-cover shadow-sm">
								<div class="flex-1">
									<div class="flex justify-between items-start">
										<h4 class="font-bold text-gray-900 text-[15px]" x-text="item.package"></h4>
										<span class="font-bold text-gray-900" x-text="'$' + (item.price * item.quantity).toLocaleString()"></span>
									</div>
									<p class="text-sm text-gray-400" x-text="'by ' + item.name"></p>
									<p class="text-xs text-gray-500 mt-1" x-text="'Qty: ' + item.quantity"></p>
									<button @click="removeCartItem(item.id)"
										class="text-[11px] font-bold text-gray-300 hover:text-red-500 mt-2 uppercase tracking-widest transition-colors flex justify-self-end border-b border-gray-300 pb-1 bg-none border-none p-0 cursor-pointer hover:bg-none">Remove</button>
								</div>
							</div>
						</template>
					</div>

					<!-- EMPTY STATE -->
					<div x-show="cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-center">
						<svg class="w-24 h-24 text-gray-900 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2"
								d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
							</path>
						</svg>
						<p class="text-gray-600 font-medium mb-6">Your cart is empty</p>
						<a href="{{ route('influencers') }}"
							class="text-sm font-bold text-blue-600 hover:text-blue-700 uppercase tracking-widest">Browse Influencers</a>
					</div>
				</div>

				<!-- Footer / Checkout -->
				<div class="pt-8 space-y-4">
					<div x-show="cartItems.length > 0" class="border-t border-gray-50 pt-8 space-y-4 mb-6">
						<div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Subtotal</span><span
								class="font-bold text-gray-900" x-text="'$' + subtotal.toLocaleString() + '.00'"></span></div>
						<div class="flex justify-between text-sm items-center">
							<div class="flex items-center gap-1.5 text-gray-900 font-bold">Projected Spend <svg
									class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<circle cx="12" cy="12" r="10" stroke-width="2" />
									<path d="M12 16v-4m0-4h.01" stroke-width="2" stroke-linecap="round" />
								</svg></div><span class="font-bold text-gray-900"
								x-text="'$' + projectedSpend.toLocaleString() + '.00'"></span>
						</div>
					</div>

					<form x-show="cartItems.length > 0" action="{{ route('cart.checkout') }}" method="POST">
						@csrf
						<button type="submit"
							class="w-full bg-[#1A1A1A] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-black transition-all shadow-xl active:scale-95 uppercase">Checkout</button>
					</form>
					<button x-show="cartItems.length === 0" @click="isCartOpen = false"
						class="w-full bg-[#1A1A1A] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-black transition-all uppercase">Discover
						Influencers</button>
				</div>
			</div>

		</div>
	</div>
</div>
