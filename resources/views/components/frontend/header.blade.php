<header
	class="sticky top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-800 shadow-sm">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
		<div class="flex items-center justify-between h-16">
			<!-- Logo -->
			<div class="flex-shrink-0">
				<a href="{{ route('home') }}" class="flex items-center gap-2">
					<img src="/images/logo/header-logo.png" alt="QX Logo" class="h-8 dark:hidden">
					<img src="/images/logo/header-logo.png" alt="QX Logo" class="h-8 hidden dark:block">
					<span class="hidden sm:inline-block text-lg font-bold text-gray-900 dark:text-white">QX Marketplace</span>
				</a>
			</div>

			<!-- Main Navigation -->
			<nav class="hidden md:flex items-center gap-8">
				@auth
					@if (auth()->user()->user_type === 'brand')
						{{-- Brand Navigation --}}
						<a href="{{ route('frontend.campaigns.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.campaigns.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-briefcase mr-2"></i>Campaigns
						</a>

						<a href="{{ route('frontend.packages.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.packages.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-box mr-2"></i>Packages
						</a>

						<a href="{{ route('cart.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors relative
                            {{ request()->routeIs('cart.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-shopping-cart mr-2"></i>Cart
							@if (session('cart_count', 0) > 0)
								<span
									class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
									{{ session('cart_count', 0) }}
								</span>
							@endif
						</a>

						<a href="{{ route('frontend.orders.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.orders.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-receipt mr-2"></i>Orders
						</a>

						@if (auth()->user()->user_type === 'brand')
							<a href="{{ route('frontend.conversations.index') }}"
								class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.conversations.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
								<i class="fas fa-comments mr-2"></i>Messages
							</a>
						@endif
					@elseif(auth()->user()->user_type === 'influencer')
						{{-- Influencer Navigation --}}
						<a href="{{ route('frontend.packages.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.packages.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-box mr-2"></i>Packages
						</a>

						<a href="{{ route('frontend.orders.index') }}"
							class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                            {{ request()->routeIs('frontend.orders.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
							<i class="fas fa-receipt mr-2"></i>Orders
						</a>
					@else
						{{-- Logged in as something else (shouldn't normally happen with new structure) --}}
						<a href="{{ route('home') }}"
							class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors">
							<i class="fas fa-home mr-2"></i>Home
						</a>
					@endif
				@else
					{{-- Guest Navigation --}}
					<a href="{{ route('frontend.packages.index') }}"
						class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('frontend.packages.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
						<i class="fas fa-box mr-2"></i>Packages
					</a>

					<a href="{{ route('influencers') }}"
						class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                        {{ request()->routeIs('influencers*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
						<i class="fas fa-users mr-2"></i>Influencers
					</a>
				@endauth
			</nav>

			<!-- Right Side Actions -->
			<div class="flex items-center gap-4">
				<!-- Theme Toggle -->
				<button
					onclick="
                    const isDark = document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', !isDark);
                    localStorage.setItem('theme', isDark ? 'light' : 'dark');
                "
					class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
					<i class="fas fa-sun hidden dark:inline w-5 h-5"></i>
					<i class="fas fa-moon inline dark:hidden w-5 h-5"></i>
				</button>

				@auth
					<!-- User Menu -->
					<div class="relative group">
						<button
							class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
							<div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
								{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
							</div>
							<span class="hidden sm:inline text-sm font-medium text-gray-900 dark:text-gray-100">
								{{ auth()->user()->name }}
							</span>
							<svg class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:rotate-180 transition-transform" fill="none"
								stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
							</svg>
						</button>

						<!-- Dropdown Menu -->
						<div
							class="absolute right-0 mt-0 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">

							<!-- View Profile -->
							<a
								href="@if (auth()->user()->user_type === 'influencer') {{ route('influencer.profile', auth()->user()->slug) }}@else{{ route('brand.profile', auth()->user()->slug) }} @endif"
								class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 first:rounded-t-lg">
								<i class="fas fa-eye mr-2"></i>View Profile
							</a>

							<!-- Edit Profile -->
							<a href="@if (auth()->user()->user_type === 'influencer') {{ route('influencer.profile.edit', auth()->user()->slug) }}@else{{ route('brand.profile.edit', auth()->user()->slug) }} @endif"
								class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
								<i class="fas fa-edit mr-2"></i>Edit Profile
							</a>

						<!-- Account Settings -->
						<a href="{{ route('frontend.account.edit', auth()->user()->slug) }}" 
							class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
							<i class="fas fa-cog mr-2"></i>Account Settings
						</a>							<!-- Orders -->
							<a href="{{ route('frontend.orders.index') }}"
								class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
								<i class="fas fa-receipt mr-2"></i>Orders
							</a>

							<!-- Cart (Brand Only) -->
							@if (auth()->user()->user_type === 'brand')
								<a href="{{ route('cart.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-shopping-cart mr-2"></i>View Cart
								</a>

								<!-- Conversations/Messages (Brand Only) -->
								<a href="{{ route('frontend.conversations.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-comments mr-2"></i>Conversations
								</a>
							@endif

							<!-- Earnings (Influencer Only) -->
							@if (auth()->user()->user_type === 'influencer')
								<a href="{{ route('earnings.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-wallet mr-2"></i>Earnings
								</a>

								<!-- Payment Queue (Influencer Only) -->
								<a href="{{ route('payment-queue.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-hourglass-half mr-2"></i>Payment Queue
								</a>

								<!-- Payment Audit Log (Influencer Only) -->
								<a href="{{ route('payment-audit.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-list-ul mr-2"></i>Payment Audit Log
								</a>

								<!-- Payment Statements (Influencer Only) -->
								<a href="{{ route('payment-statements.index') }}"
									class="block w-full text-left px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
									<i class="fas fa-file-invoice mr-2"></i>Statements
								</a>
							@endif

							<hr class="my-1 border-gray-200 dark:border-gray-700">

							<!-- Logout -->
							<form method="POST" action="{{ route('logout') }}" class="block">
								@csrf
								<button type="submit"
									class="w-full text-left px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 last:rounded-b-lg">
									<i class="fas fa-sign-out-alt mr-2"></i>Logout
								</button>
							</form>
						</div>
					</div>
				@else
					<!-- Auth Buttons -->
					<a href="{{ route('login') }}"
						class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
						Login
					</a>
					<a href="{{ route('register') }}"
						class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
						Sign Up
					</a>
				@endauth
			</div>
		</div>
	</div>
</header>
