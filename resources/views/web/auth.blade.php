<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Welcome' }} | QX - Influencer Hiring Platform</title>

    <!-- Tailwind CSS & Alpine.js via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                if (document.body) {
                    document.body.classList.add('dark', 'bg-gray-900', 'text-gray-100');
                }
            } else {
                document.documentElement.classList.remove('dark');
                if (document.body) {
                    document.body.classList.add('bg-white', 'text-gray-900');
                }
            }
        })();
    </script>
</head>

<body class="transition-colors duration-200 font-sans antialiased">
    
    <!-- Floating Theme Toggle Button -->
    <div class="fixed right-6 top-1/2 -translate-y-1/2 z-50">
        <button
            @click="$store.theme.toggle()"
            class="group relative inline-flex items-center justify-center w-12 h-12 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
            :class="{
                'bg-gray-100 text-gray-800 hover:bg-gray-200': $store.theme.theme === 'light',
                'bg-gray-800 text-gray-100 hover:bg-gray-700': $store.theme.theme === 'dark'
            }"
            :title="$store.theme.theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
            x-data="{ 
                get currentIcon() { 
                    return $store.theme.theme === 'light' ? '☀️' : '🌙'
                }
            }">
            <span x-text="currentIcon" class="text-xl"></span>
            
            <span class="absolute -left-32 px-2 py-1 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-lg pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap dark:bg-gray-100 dark:text-gray-900">
                <span x-text="$store.theme.theme === 'light' ? 'Dark Mode' : 'Light Mode'"></span>
            </span>
        </button>
    </div>

    <!-- Header Component -->
    <x-web.header />

    <!-- Main Content with Centered Auth Form -->
    <main class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </main>

    @stack('scripts')
</body>

</html>
