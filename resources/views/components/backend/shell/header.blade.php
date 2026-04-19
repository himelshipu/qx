@php
    $brandingLogoLight = \App\Models\Setting::fileUrl('branding.logo_light', '/images/logo/header-logo.png');
    $brandingLogoDark = \App\Models\Setting::fileUrl('branding.logo_dark', '/images/logo/header-logo.png');
@endphp

<header class="sticky top-0 z-50 w-full bg-white border-b border-gray-200 dark:bg-gray-900 dark:border-gray-800" x-data="{ appMenuOpen: false }">
    <div class="flex flex-col xl:flex-row xl:items-center xl:px-6">
        <div class="flex items-center justify-between w-full gap-2 px-3 py-3 border-b border-gray-200 dark:border-gray-800 xl:border-b-0 xl:px-0">
            <div class="flex items-center gap-2">
                <button @click="$store.sidebar.toggleExpanded()" class="hidden lg:flex items-center justify-center w-11 h-11 text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-100 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800" :class="{ 'bg-gray-100 dark:bg-white/5': !$store.sidebar.isExpanded }">
                    <x-icons.menu class="w-5 h-5" />
                </button>

                <button @click="$store.sidebar.toggleMobileOpen()" class="flex lg:hidden items-center justify-center w-11 h-11 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800" :class="{ 'bg-gray-100 dark:bg-white/5': $store.sidebar.isMobileOpen }">
                    <x-icons.menu class="w-5 h-5" />
                </button>

                <a href="/" class="lg:hidden">
                    <img src="{{ $brandingLogoDark }}" alt="Logo" class="h-12 dark:block hidden">
                    <img src="{{ $brandingLogoLight }}" alt="Logo" class="h-12 dark:hidden block">
                </a>

                <!-- Search Bar (desktop only) - Now positioned after logo/menu on left -->
                <div class="hidden xl:block ml-2">
                    <form>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2">
                                <x-icons.search class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            </span>
                            <input type="text" placeholder="Search or type command..." class="w-107.5 h-11 pl-12 pr-14 text-sm bg-transparent border border-gray-200 rounded-lg dark:border-gray-700 dark:text-white placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 px-2 py-1 text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">⌘ K</span>
                        </div>
                    </form>
                </div>
            </div>

            <button @click="appMenuOpen = !appMenuOpen" class="xl:hidden flex items-center justify-center w-11 h-11 text-gray-700 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                <x-icons.dots class="w-6 h-6" />
            </button>
        </div>

        <div :class="appMenuOpen ? 'flex' : 'hidden'" class="items-center justify-between w-full gap-4 px-5 py-4 xl:flex xl:w-auto xl:px-0 xl:py-0">
            <div class="flex items-center gap-2">
                <button @click="$store.theme.toggle()" class="flex items-center justify-center w-11 h-11 text-gray-500 bg-white border border-gray-200 rounded-full hover:bg-gray-100 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                    <x-icons.sun class="w-5 h-5 hidden dark:block" />
                    <x-icons.moon class="w-5 h-5 block dark:hidden" />
                </button>

                <a href="{{ route('dashboard.conversations.index') }}"
                    class="relative flex items-center justify-center w-11 h-11 text-gray-500 bg-white border border-gray-200 rounded-full hover:bg-gray-50 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition-colors duration-200"
                    title="Conversations">
                    @if (($dashboardUnreadConversations ?? 0) > 0)
                        <span class="absolute top-1 right-1 min-w-4 h-4 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold leading-4 text-center">
                            {{ ($dashboardUnreadConversations ?? 0) > 99 ? '99+' : $dashboardUnreadConversations }}
                        </span>
                    @endif
                    <x-icons.message-square class="w-5 h-5" />
                </a>

                <x-backend.dropdowns.notification />
            </div>
            
                <x-backend.dropdowns.user />
        </div>
    </div>
</header>