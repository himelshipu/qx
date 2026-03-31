@php
	$currentUser = Auth::user();
	$avatarPath = $currentUser?->brand?->profile_image_path
		?? $currentUser?->creator?->profile_image_path
		?? $currentUser?->profile_image_path;
	$avatarUrl = filled($avatarPath) ? image_url($avatarPath) : null;
	$userInitials = $currentUser
		? Str::of($currentUser->name)->explode(' ')->map(fn($word) => Str::upper($word[0] ?? ''))->filter()->take(2)->join('')
		: 'SM';
@endphp

<div x-data="{
		isCartOpen: false,
		isProfileOpen: false,
		// DUMMY DATA FROM REFERENCE
		cartItems: [
			{ id: 1, name: 'Kevin Cuenca', package: '1 Instagram Reel (90 Seconds)', price: 6000, image: 'https://i.pravatar.cc/150?u=kevin' },
			{ id: 2, name: 'Lauren Hassall', package: '1 UGC Unboxing', price: 120, image: 'https://i.pravatar.cc/150?u=lauren' },
			{ id: 3, name: 'Quita The Kitty', package: '1 Instagram Photo Feed Post', price: 250, image: 'https://i.pravatar.cc/150?u=cat' }
		],
		get subtotal() { return this.cartItems.reduce((acc, item) => acc + item.price, 0) },
		// Projected spend usually mirrors the first item or a specific calculation (Reference shows $120)
		get projectedSpend() { return this.cartItems.length > 0 ? 120 : 0 }
	}"

	class=" max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">

	<div x-data="{ isCartOpen: false, isProfileOpen: false }" class="relative">
		<!-- After login menu -->
		<div class="max-w-screen-2xl mx-auto py-3 flex flex-col sm:px-0 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">

			<!-- Logo Section -->
			<div class="flex items-center gap-2">
				<a href="{{ route('home') }}">
					<img src="/images/logo/header-logo.png" alt="Logo" class="h-11 dark:hidden block">
					<img src="/images/logo/header-logo.png" alt="Logo" class="h-11 dark:block hidden">
				</a>
			</div>

			<!-- Desktop Navigation (Hidden on mobile if needed, or flex-wrap) -->
			<div class="mb-0">
				<nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-10 text-sm font-medium mb-0">
					<a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
					<a href="{{ route('dashboard.content-library') }}" class="nav-link {{ request()->routeIs('dashboard.content-library') ? 'active' : '' }}">Library</a>
					<a href="#how-it-works" class="nav-link">How it Works</a>
					<a href="{{ route('influencers') }}" class="nav-link">Search</a>
					<a href="{{ route('faq') }}" class="nav-link {{ request()->routeIs('faq') ? 'active' : '' }}">Faq</a>
				</nav>
			</div>

			<!-- Action Icons Section -->
			<div class="flex items-center justify-between gap-3 sm:gap-5 w-full sm:w-auto">

				<!-- Shopping Cart Icon (THIS ONLY opens the Cart Modal) -->
				<div @click="isCartOpen = true" class="relative cursor-pointer hover:opacity-70 transition-opacity p-2">
					<svg class="w-7 h-7 text-gray-800 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
					</svg>
					<div class="absolute bottom-1.5 left-1/2 -translate-x-1/2 flex items-center gap-0.5">
						<div class="w-1 h-1 bg-black dark:bg-white rounded-full"></div>
						<div class="w-1 h-1 bg-black dark:bg-white rounded-full"></div>
					</div>
				</div>

				<!-- Profile Dropdown Wrapper (Updated for better Mobile support) -->
				<div class="relative" @click.away="isProfileOpen = false">
					<button @click="isProfileOpen = !isProfileOpen"
						class="flex items-center gap-3 border border-gray-100 dark:border-gray-800 rounded-full p-1 pl-4 bg-white dark:bg-gray-900 hover:shadow-md transition-all duration-300 active:scale-95">

						<!-- Hamburger Icon (Now toggles Profile, NOT Cart) -->
						<svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
						</svg>

						<!-- Profile Image/Initials -->
						@auth
							@if ($avatarUrl)
								<img src="{{ $avatarUrl }}" alt="{{ $currentUser?->name ?? 'Profile' }}" class="w-10 h-10 rounded-full object-cover">
							@else
								<div class="w-10 h-10 rounded-full bg-[#FFE4C4] flex items-center justify-center text-base font-bold text-black uppercase tracking-tighter">
									{{ $userInitials }}
								</div>
							@endif
						@else
							<div class="w-10 h-10 rounded-full bg-[#FFE4C4] flex items-center justify-center text-base font-bold text-black uppercase tracking-tighter">SM</div>
						@endauth
					</button>

					<!-- Profile Dropdown Content (Converted from group-hover to Alpine show for mobile reliability) -->
					<div x-show="isProfileOpen"
						x-cloak
						x-transition:enter="transition ease-out duration-200"
						x-transition:enter-start="opacity-0 scale-95"
						x-transition:enter-end="opacity-100 scale-100"
						class="absolute right-0 top-full mt-3 w-56 bg-white dark:bg-gray-800 rounded-[20px] shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-50 dark:border-gray-700 z-50 overflow-hidden">
						<div class="py-2 flex flex-col">
							@auth
								@if (Auth::user()->brand)
									<a href="{{ route('brand.profile', Auth::user()->slug) }}" class="px-7 py-3.5 text-[15px] font-bold text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">View profile</a>
									<a href="{{ route('dashboard.brand.profile.edit', ['slug' => Auth::user()->slug]) }}" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Edit profile</a>
								@elseif(Auth::user()->creator)
									<a href="{{ route('creator.profile', Auth::user()->slug) }}" class="px-7 py-3.5 text-[15px] font-bold text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">View profile</a>
									<a href="{{ route('dashboard.creator.profile.edit', ['slug' => Auth::user()->slug]) }}" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Edit profile</a>
								@endif

											<a href="{{ route('dashboard.index') }}" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Dashboard</a>
										@endauth

							<div class="border-t border-gray-100 dark:border-gray-700 my-1 mx-2"></div>
									<a href="{{ route('dashboard.account.edit', ['slug' => Auth::user()->slug]) }}" class="px-7 py-3.5 text-[15px] font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Account</a>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<button type="submit" class="text-left px-7 py-3.5 text-[15px] font-medium text-red-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-red-700 transition-colors w-full">Log Out</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>


		<!-- ========================= CART SIDEBAR MODAL ========================= -->
		<div x-show="isCartOpen" x-cloak class="fixed inset-0 z-[200] overflow-hidden" role="dialog" aria-modal="true">

			<!-- Backdrop Blur -->
			<div x-show="isCartOpen" x-transition.opacity @click="isCartOpen = false" class="absolute inset-0 bg-black/40 backdrop-blur-[2px]"></div>

			<div class="fixed inset-y-0 right-0 flex max-w-full">
				<div x-show="isCartOpen"
					x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
					x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
					class="w-screen max-w-5xl flex shadow-2xl">

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
										<div class="h-full bg-white transition-all duration-1000" :style="`width: ${cartItems.length > 0 ? (1 / cartItems.length) * 100 : 0}%`"></div>
									</div>
									<div class="text-right mt-3 text-sm font-bold text-gray-500" x-text="cartItems.length + ' Influencers'"></div>
								</div>

								<!-- Spend Progress -->
								<div>
									<div class="flex justify-between items-end mb-3">
										<span class="text-xl font-bold" x-text="'$' + projectedSpend + ' Spend'"></span>
									</div>
									<div class="h-2 w-full bg-gray-800 rounded-sm overflow-hidden">
										<div class="h-full bg-white transition-all duration-1000" :style="`width: ${subtotal > 0 ? (projectedSpend / subtotal) * 100 : 0}%`"></div>
									</div>
									<div class="text-right mt-3 text-sm font-bold text-gray-500" x-text="'$' + subtotal.toLocaleString()"></div>
								</div>

								<!-- Top Locations -->
								<div x-show="cartItems.length > 0" class="pt-4">
									<h3 class="text-xl font-bold mb-6">Top Audience Locations</h3>
									<ul class="space-y-5">
										<li class="flex items-center gap-4"><span class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">1</span><span class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">US</span> United States</span></li>
										<li class="flex items-center gap-4"><span class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">2</span><span class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">GB</span> United Kingdom</span></li>
										<li class="flex items-center gap-4"><span class="w-8 h-8 rounded-full border border-gray-700 flex items-center justify-center text-xs font-bold">3</span><span class="text-sm font-bold uppercase tracking-widest"><span class="text-gray-500 mr-2">TH</span> Thailand</span></li>
									</ul>
								</div>
							</div>
						</div>

						<div class="flex items-start gap-4">
							<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round"/></svg>
							<p class="text-[11px] font-bold text-gray-500 uppercase tracking-tight leading-4">Payment Protection <br><span class="text-gray-600 font-medium normal-case">If an order is declined, funds will be refunded.</span></p>
						</div>
					</div>

					<!-- RIGHT PANEL (Cart List) -->
					<div class="flex-1 flex flex-col bg-white p-4 lg:p-12 justify-between overflow-hidden">
						<div class="flex items-center justify-between border-b pb-8 border-gray-50">
							<h2 class="text-3xl font-bold text-gray-900">Cart</h2>
							<button @click="isCartOpen = false" class="p-2 text-gray-300 hover:text-black transition-all hover:rotate-90">
								<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"/></svg>
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
												<span class="font-bold text-gray-900" x-text="'$' + item.price.toLocaleString()"></span>
											</div>
											<p class="text-sm text-gray-400" x-text="item.name"></p>
											<button @click="cartItems = cartItems.filter(i => i.id !== item.id)" class="text-[11px] font-bold text-gray-300 hover:text-red-500 mt-2 uppercase tracking-widest transition-colors flex justify-self-end border-b border-gray-300 pb-1">Remove</button>
										</div>
									</div>
								</template>
							</div>

							<!-- EMPTY STATE -->
							<div x-show="cartItems.length === 0" class="h-full flex flex-col items-center justify-center text-center">
								<svg class="w-24 h-24 text-gray-900 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
								<h3 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
								<p class="text-gray-400 max-w-[240px]">Start adding influencers by clicking the button below</p>
							</div>
						</div>

						<!-- Footer / Checkout -->
						<div class="pt-8 space-y-4">
							<div x-show="cartItems.length > 0" class="border-t border-gray-50 pt-8 space-y-4 mb-6">
								<div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Subtotal</span><span class="font-bold text-gray-900" x-text="'$' + subtotal.toLocaleString() + '.00'"></span></div>
								<div class="flex justify-between text-sm items-center"><div class="flex items-center gap-1.5 text-gray-900 font-bold">Projected Spend <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 16v-4m0-4h.01" stroke-width="2" stroke-linecap="round"/></svg></div><span class="font-bold text-gray-900" x-text="'$' + projectedSpend.toLocaleString() + '.00'"></span></div>
							</div>

							<button x-show="cartItems.length > 0" class="w-full bg-[#1A1A1A] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-black transition-all shadow-xl active:scale-95 uppercase">Checkout</button>
							<button x-show="cartItems.length === 0" @click="isCartOpen = false" class="w-full bg-[#1A1A1A] text-white py-5 rounded-2xl font-bold text-sm tracking-widest hover:bg-black transition-all uppercase">Discover Influencers</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>
