<!-- Header Navigation -->
<header class="w-full bg-white dark:bg-gray-900 transition-colors duration-200">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}">
                    <img src="/images/logo/logo-dark.png" alt="Logo" class="h-11 dark:block hidden">
                    <img src="/images/logo/logo.png" alt="Logo" class="h-11 dark:hidden block">
                </a>
            </div>

            <nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-8 text-sm lg:text-[14px] font-medium text-gray-600 dark:text-gray-300">
                <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Search</a>
                <a href="{{ route('faq') }}" class="hover:text-black dark:hover:text-white transition-colors">Faq</a>
                <a href="{{ route('support') }}" class="hover:text-black dark:hover:text-white transition-colors">Support</a>
                
                @auth
                    <a href="{{ route('dashboard.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-black dark:hover:text-white transition-colors">Login</a>
                    <a href="{{ route('register', ['user-type' => 'brand']) }}" class="hover:text-black dark:hover:text-white transition-colors">Join as Brand</a>
                @endauth
                
                <!-- Gradient hover or text for Creator link -->
                <a href="{{ route('register', ['user-type' => 'creator']) }}"
                class="font-bold inline-block bg-clip-text text-transparent
                        bg-[length:300%_300%]
                        bg-[linear-gradient(90deg,rgb(255,132,160)_0%,rgb(251,102,157)_20%,rgb(179,45,194)_95%,rgb(136,95,183)_100%)]
                        transition-all duration-700 ease-out
                        hover:bg-[position:80%_0%]">
                    Join as Creator
                </a>
            </nav>
        </div>
    </div>
</header>