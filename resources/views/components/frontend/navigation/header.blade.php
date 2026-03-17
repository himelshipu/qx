<!-- Header Navigation -->
<header class="w-full bg-white dark:bg-gray-900 transition-colors duration-200">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}">
                    <img src="/images/logo/header-logo.png" alt="Logo" class="h-12 dark:block hidden">
                    <img src="/images/logo/header-logo.png" alt="Logo" class="h-12 dark:hidden block">
                </a>
            </div>

            <nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-10 text-sm font-medium">

                <a href="{{ route('influencers') }}" class="nav-link">Search</a>
                <a href="{{ route('faq') }}" class="nav-link">Faq</a>
                <a href="{{ route('support') }}" class="nav-link">Support</a>

                @auth
                    <a href="{{ route('dashboard.index') }}" class="nav-link">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register', ['user-type' => 'brand']) }}" class="nav-link">Join as Brand</a>
                @endauth

                <!-- Creator link -->
                <a href="{{ route('register', ['user-type' => 'creator']) }}"
                class="nav-link nav-gradient font-bold">
                    Join as Creator
                </a>

            </nav>
        </div>
    </div>
</header>