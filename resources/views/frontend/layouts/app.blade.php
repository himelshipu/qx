<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth bg-white dark:bg-gray-900">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>{{ $title ?? 'Welcome' }} | ROCKIES - Influencer Hiring Platform</title>

		<!-- Apply theme before CSS loads to avoid first-paint flash -->
		<script>
			(function() {
				let savedTheme = null;
				try {
					savedTheme = localStorage.getItem('theme');
				} catch (e) {}

				const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
				const theme = savedTheme || systemTheme;
				const isDark = theme === 'dark';

				document.documentElement.classList.toggle('dark', isDark);
				document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
				document.documentElement.style.backgroundColor = isDark ? '#111827' : '#ffffff';
			})();
		</script>

		<!-- Tailwind CSS & Alpine.js via Vite -->
		<style>
			[x-cloak] {
				display: none !important;
			}
		</style>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>

	<body class="bg-white text-gray-900 dark:bg-gray-900 dark:text-gray-100">

		<!-- Floating Theme Toggle Button -->
		<div class="fixed right-6 top-1/2 -translate-y-1/2 z-50">
			<button @click="$store.theme.toggle()"
				class="group relative inline-flex items-center justify-center w-12 h-12 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
				:class="{
				    'bg-gray-100 text-gray-800 hover:bg-gray-200': $store.theme.theme === 'light',
				    'bg-gray-800 text-gray-100 hover:bg-gray-700': $store.theme.theme === 'dark'
				}"
				:title="$store.theme.theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'" x-data="{
	    get currentIcon() {
	        return $store.theme.theme === 'light' ? '☀️' : '🌙'
	    }
	}">
				<span x-text="currentIcon" class="text-xl"></span>

				<span
					class="absolute -left-32 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap dark:bg-gray-100 dark:text-gray-900">
					<span x-text="$store.theme.theme === 'light' ? 'Dark Mode' : 'Light Mode'"></span>
				</span>
			</button>
		</div>

		<!-- Cart Modal Data Function (must load BEFORE auth-header component) -->
		<script>
			// Store initial cart data from server
			window.initialCartData = {!! json_encode($cartItemsData ?? []) !!};
			window.loginUrl = @js(route('login'));
			window.autoOpenCartSidebar = @json((bool) session('auto_open_cart_sidebar'));
			window.brandActionRequiredMessage = @json(session('brand_action_required_message'));

			// Global store for cart state (accessible from anywhere)
			window.cartStore = {
				isAdding: false,
				isRemoving: false
			};

			// Global addToCart function callable from any Alpine component
			async function addToCart(packageId) {
				if (window.cartStore.isAdding) return false;
				window.cartStore.isAdding = true;

				try {
					const response = await fetch('{{ route('cart.add') }}', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'Accept': 'application/json',
							'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
						},
						body: JSON.stringify({
							package_id: packageId
						})
					});

					if (!response.ok) {
						const errorData = await response.json().catch(() => ({}));

						if (response.status === 401 && errorData.redirect_url) {
							if (window.toast && window.toast.warning) {
								window.toast.warning(errorData.message || 'Please login first.');
							}

							window.location.href = errorData.redirect_url;
							return false;
						}

						throw new Error(errorData.message || 'Failed to add item');
					}

					const data = await response.json();
					if (data.success) {
						// Update global initial cart data
						window.initialCartData = data.cart.items || [];

						// Update cart sidebar component using Alpine's event system
						// Dispatch custom event that the component listens for
						window.dispatchEvent(new CustomEvent('cartUpdated', {
							detail: {
								items: data.cart.items || [],
								shouldOpenCart: true
							}
						}));

						if (window.toast && window.toast.success) {
							window.toast.success('Package added to cart!');
						}

						return true;
					} else {
						throw new Error(data.message || 'Failed to add item');
					}
				} catch (error) {
					if (window.toast && window.toast.error) {
						window.toast.error(error.message || 'Failed to add item to cart');
					}

					return false;
				} finally {
					window.cartStore.isAdding = false;
				}
			}

			function cartModalData(initialCartItems = null) {
				const resolvedInitialItems = Array.isArray(initialCartItems) ?
					initialCartItems :
					(Array.isArray(window.initialCartData) ? window.initialCartData : []);

				return {
					isCartOpen: false,
					isProfileOpen: false,
					cartItems: resolvedInitialItems,
					isRemoving: false,
					isAdding: false,

					init() {
						// Set up listener for real-time cart updates when component initializes
						window.addEventListener('cartUpdated', (event) => {
							const detail = event.detail || {};
							if (Array.isArray(detail.items)) {
								this.cartItems = [...detail.items];
								window.initialCartData = detail.items;
							}
							if (detail.shouldOpenCart) {
								this.isCartOpen = true;
							}
						});
					},

					get subtotal() {
						return this.cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
					},

					get projectedSpend() {
						return this.cartItems.length > 0 ? this.subtotal : 0;
					},

					get totalItemCount() {
						return this.cartItems.reduce((sum, item) => sum + Number(item.quantity || 0), 0);
					},

					get uniqueInfluencerCount() {
						const influencerIds = this.cartItems
							.map((item) => item.influencer_id)
							.filter((id) => id !== null && id !== undefined);

						return new Set(influencerIds).size;
					},

					get topAudienceLocations() {
						const locationMap = new Map();

						this.cartItems.forEach((item) => {
							const country = (item.country || '').toString().trim();
							if (!country) {
								return;
							}

							const normalized = country.toLowerCase();
							locationMap.set(normalized, {
								country,
								count: (locationMap.get(normalized)?.count || 0) + 1,
							});
						});

						return Array.from(locationMap.values())
							.sort((a, b) => b.count - a.count)
							.slice(0, 3)
							.map((entry) => ({
								country: entry.country,
								count: entry.count,
								code: this.countryCode(entry.country),
							}));
					},

					countryCode(country) {
						const value = (country || '').toString().trim();
						if (!value) {
							return '--';
						}

						if (value.length <= 3) {
							return value.toUpperCase();
						}

						const words = value.split(/\s+/).filter(Boolean);
						if (words.length >= 2) {
							return (words[0][0] + words[1][0]).toUpperCase();
						}

						return value.slice(0, 2).toUpperCase();
					},

					async addToCart(packageId) {
						if (this.isAdding) return;
						this.isAdding = true;

						try {
							const response = await fetch('{{ route('cart.add') }}', {
								method: 'POST',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute(
										'content')
								},
								body: JSON.stringify({
									package_id: packageId
								})
							});

							if (!response.ok) {
								const errorData = await response.json().catch(() => ({}));

								if (response.status === 401 && errorData.redirect_url) {
									if (window.toast && window.toast.warning) {
										window.toast.warning(errorData.message || 'Please login first.');
									}

									window.location.href = errorData.redirect_url;
									return;
								}

								throw new Error(errorData.message || 'Failed to add item');
							}

							const data = await response.json();
							if (data.success) {
								// Update component data with spread operator to trigger reactivity
								this.cartItems = data.cart.items ? [...data.cart.items] : [];
								this.isCartOpen = true;
								// Dispatch event for other components to listen
								window.dispatchEvent(new CustomEvent('cartUpdated', {
									detail: {
										items: data.cart.items || [],
										shouldOpenCart: true
									}
								}));
								if (window.toast && window.toast.success) {
									window.toast.success('Package added to cart!');
								}
							} else {
								throw new Error(data.message || 'Failed to add item');
							}
						} catch (error) {
							if (window.toast && window.toast.error) {
								window.toast.error(error.message || 'Failed to add item to cart');
							}
						} finally {
							this.isAdding = false;
						}
					},

					async removeCartItem(itemId) {
						if (this.isRemoving) return;
						this.isRemoving = true;

						try {
							const response = await fetch(`/cart/items/${itemId}`, {
								method: 'DELETE',
								headers: {
									'Content-Type': 'application/json',
									'Accept': 'application/json',
									'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute(
										'content')
								}
							});

							if (!response.ok) {
								const errorData = await response.json().catch(() => ({}));
								throw new Error(errorData.message || 'Failed to remove item');
							}

							const data = await response.json();
							if (data.success) {
								// Update component data with spread operator to trigger reactivity
								this.cartItems = data.cart.items ? [...data.cart.items] : [];
								// Dispatch event for other components to listen
								window.dispatchEvent(new CustomEvent('cartUpdated', {
									detail: {
										items: data.cart.items || [],
										shouldOpenCart: false
									}
								}));
								if (window.toast && window.toast.success) {
									window.toast.success('Item removed from cart');
								}
							} else {
								throw new Error(data.message || 'Failed to remove item');
							}
						} catch (error) {
							if (window.toast && window.toast.error) {
								window.toast.error(error.message || 'Failed to remove item from cart');
							}
						} finally {
							this.isRemoving = false;
						}
					}
				};
			}

			document.addEventListener('DOMContentLoaded', () => {
				if (window.autoOpenCartSidebar) {
					window.setTimeout(() => {
						const cartHeaderEl = document.querySelector('[x-data*="cartModalData"]');
						if (cartHeaderEl && cartHeaderEl.__x?.scope) {
							cartHeaderEl.__x.scope.isCartOpen = true;
						}
					}, 150);
				}

				if (window.brandActionRequiredMessage && window.confirmationModal) {
					window.confirmationModal.open({
						title: 'Brand Account Required',
						message: window.brandActionRequiredMessage,
						confirmText: 'OK',
						variant: 'warning'
					});
				}
			});
		</script>

		@if (auth()->user())
			<x-frontend.navigation.auth-header />
		@else
			<x-frontend.navigation.header />
		@endif


		<!-- Main Content -->
		<main class="max-w-screen-2xl mx-auto px-4 pt-4 pb-14">
			@yield('content')
		</main>
		<x-frontend.navigation.footer />

		<!-- Toast Container -->
		<div x-data="window.Alpine.store('toast')" class="fixed top-4 right-4 z-50 flex flex-col gap-2">
			<template x-for="t in toasts" :key="t.id">
				<div class="px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-[300px] max-w-md animate-slide-in"
					:class="{
					    'success': 'bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-100',
					    'error': 'bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-100',
					    'info': 'bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-100',
					    'warning': 'bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-100',
					} [t.type]">
					<div class="flex-shrink-0">
						<template x-if="t.type === 'success'">
							<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'error'">
							<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'info'">
							<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'warning'">
							<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
						</template>
					</div>
					<div class="flex-1">
						<p class="text-sm font-medium whitespace-pre-line" x-text="t.message"></p>
					</div>
					<button @click="remove(t.id)" class="text-current opacity-60 hover:opacity-100">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
			</template>
		</div>

		<x-confirmation-modal />

		@php
			$validationError = null;
			$errorsBag = session()->get('errors') ?: $errors ?? null;

			if ($errorsBag && method_exists($errorsBag, 'any') && $errorsBag->any()) {
			    $messages = $errorsBag->all();
			    if (count($messages) > 1) {
			        $validationError = '- ' . implode("\n- ", $messages);
			    } else {
			        $validationError = $messages[0] ?? null;
			    }
			}

			$toastrFlash = [
			    'success' => session('success'),
			    'error' => session('error') ?: $validationError,
			    'info' => session('info'),
			    'warning' => session('warning'),
			];
		@endphp

		<script>
			window.__toastrFlash = @json($toastrFlash);
		</script>

		@stack('scripts')

		<script>
			(function() {
				const fallback = @js(asset('default.webp'));

				const applyFallback = (img) => {
					if (!(img instanceof HTMLImageElement)) {
						return;
					}

					if (img.dataset.fallbackApplied === '1') {
						return;
					}

					img.dataset.fallbackApplied = '1';
					img.onerror = null;
					img.setAttribute('src', fallback);
				};

				document.addEventListener('error', (event) => {
					if (event.target instanceof HTMLImageElement) {
						applyFallback(event.target);
					}
				}, true);

				document.querySelectorAll('img').forEach((img) => {
					if (!(img.getAttribute('src') || '').trim()) {
						applyFallback(img);
					}
				});
			})();
		</script>
	</body>

</html>
