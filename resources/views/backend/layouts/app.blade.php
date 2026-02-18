<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Dashboard' }} | QX - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine stores are defined in resources/js/app.js; avoid duplicating here -->

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            const apply = () => {
                const savedTheme = localStorage.getItem('theme');
                const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                const theme = savedTheme || systemTheme;
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    if (document.body) document.body.classList.add('dark', 'bg-gray-900');
                } else {
                    document.documentElement.classList.remove('dark');
                    if (document.body) document.body.classList.remove('dark', 'bg-gray-900');
                }
            };

            if (document.body) {
                apply();
            } else {
                document.addEventListener('DOMContentLoaded', apply);
            }
        })();
    </script>
</head>

<body x-data="{ 'loaded': true }"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);"
    class="transition-colors duration-200">

    <div class="min-h-screen xl:flex">
        @include('backend.layouts.backdrop')
        @include('backend.layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded,
                'xl:ml-[90px]': !$store.sidebar.isExpanded,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('backend.layouts.app-header')
            <!-- app header end -->
            <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-gray-50">
                @yield('content')
            </main>
        </div>

    </div>

</body>

@stack('scripts')

</html>
