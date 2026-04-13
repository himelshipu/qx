<!-- Header Navigation -->
<header class="w-full bg-white dark:bg-gray-900 transition-colors duration-200">
	@php
		$brandingLogoLight = \App\Models\Setting::fileUrl('branding.logo_light', '/images/logo/header-logo.png');
		$brandingLogoDark = \App\Models\Setting::fileUrl('branding.logo_dark', '/images/logo/header-logo.png');
	@endphp
	<div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
		@php
			$authRedirect = request()->fullUrl();
		@endphp
		<div class="py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
			<div class="flex items-center gap-2">
				<a href="{{ route('home') }}">
					<img src="{{ $brandingLogoDark }}" alt="Logo" class="h-12 dark:block hidden">
					<img src="{{ $brandingLogoLight }}" alt="Logo" class="h-12 dark:hidden block">
				</a>
			</div>

			<nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-10 text-sm font-medium">

				<a href="{{ route('frontend.blogs.index') }}" class="nav-link">Blog</a>
				<a href="{{ route('influencers') }}" class="nav-link">Search</a>
				<a href="{{ route('faq') }}" class="nav-link">Faq</a>
				<a href="#how-it-works" class="nav-link">How it Works</a>
				<a href="{{ route('support') }}" class="nav-link">Support</a>
				@auth
					<a href="{{ route('dashboard.index') }}" class="nav-link">Dashboard</a>
				@else
					<a href="{{ route('login', ['redirect' => $authRedirect]) }}" class="nav-link">Login</a>
					<a href="{{ route('register', ['user-type' => 'brand', 'redirect' => $authRedirect]) }}" class="nav-link">Join as Brand</a>
				@endauth

				<!-- Influencer link -->
				<a href="{{ route('register', ['user-type' => 'influencer', 'redirect' => $authRedirect]) }}" class="nav-link nav-gradient font-bold">
					Join as Influencer
				</a>

			</nav>
		</div>
	</div>
</header>
