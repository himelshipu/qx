@php
    use App\Helpers\MenuHelper;
    use Illuminate\Support\Facades\Route;

    $menuItems = MenuHelper::getMainNavItems();
    $currentRoute = Route::currentRouteName();
@endphp

<aside id="sidebar"
    class="fixed top-0 left-0 z-40 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 shadow-xl"
    x-data="{ 
        openMenus: [],
        isActive(route) {
            return '{{ $currentRoute }}' === route || '{{ $currentRoute }}' === 'dashboard.' + route;
        },
        toggleMenu(index) {
            if(this.openMenus.includes(index)) {
                this.openMenus = this.openMenus.filter(i => i !== index);
            } else {
                this.openMenus = [...this.openMenus, index];
            }
        }
    }"
    :class="$store.sidebar.isExpanded ? 'w-72' : 'w-20'"
    x-init="$watch('$store.sidebar.isExpanded', val => { if (!val) openMenus = [] })">
    
    <div class="h-20 flex items-center justify-center px-4 border-b border-gray-100 dark:border-gray-800">
        <!-- <a href="/" class="flex items-center gap-3">
            <div class="h-10 w-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <span class="text-white font-bold text-xl">Q</span>
            </div>
            <span x-show="$store.sidebar.isExpanded" class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">QX</span>
        </a> -->
        <a href="/">
            <img src="/images/logo/header-logo.png" alt="Logo" class="h-11 dark:hidden block">
            <img src="/images/logo/header-logo.png" alt="Logo" class="h-11 dark:block hidden">
        </a>
    </div>

    <nav class="p-4 h-[calc(100vh-4rem)] overflow-scroll custom-scrollbar">
        <ul class="space-y-6">
            @foreach ($menuItems as $key => $item)
                @if($key === 'dashboard')
                    @php
                        $route = $item['route'] ?? '#';
                        $url = $route === '/dashboard' ? route('dashboard.index') : '#';
                    @endphp
                    <li>
                        <a href="{{ $url }}"
                            class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group relative"
                            :class="isActive('{{ str_replace('/', '', $route) }}') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                            <span class="flex-shrink-0 w-5 h-5" :class="isActive('{{ str_replace('/', '', $route) }}') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                            </span>
                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm font-semibold">{{ $item['name'] }}</span>
                            @if(!empty($item['count']))
                                <span x-show="$store.sidebar.isExpanded" class="ml-auto px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">22</span>
                            @endif
                        </a>
                    </li>
                @elseif(isset($item['type']) && $item['type'] === 'group')
                    <li class="space-y-2">
                        <div x-show="$store.sidebar.isExpanded" class="px-4 py-1">
                            <span class="text-xs font-semibold tracking-wider text-gray-400 dark:text-gray-500">{{ $item['name'] }}</span>
                        </div>
                        <ul class="space-y-1">
                            @foreach ($item['items'] as $index => $subItem)
                                @php
                                    $hasSubItems = isset($subItem['subItems']);
                                    $itemIcon = $subItem['icon'] ?? 'home';
                                @endphp
                                <li>
                                    @if($hasSubItems)
                                        <button @click="toggleMenu('{{ $key }}_{{ $index }}')"
                                            class="w-full flex items-center gap-4 px-4 py-2.5 rounded-xl transition-all duration-200 group"
                                            :class="openMenus.includes('{{ $key }}_{{ $index }}') ? 'bg-gray-100 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                            <span class="flex-shrink-0 w-5 h-5" :class="openMenus.includes('{{ $key }}_{{ $index }}') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                                                {!! MenuHelper::getIconSvg($itemIcon) !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm text-left">{{ $subItem['name'] }}</span>
                                            <svg x-show="$store.sidebar.isExpanded" 
                                                class="w-4 h-4 transition-transform duration-200 text-gray-400"
                                                :class="openMenus.includes('{{ $key }}_{{ $index }}') ? 'rotate-180' : ''"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <ul x-show="openMenus.includes('{{ $key }}_{{ $index }}') && $store.sidebar.isExpanded"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="mt-1 ml-12 space-y-1">
                                            @foreach ($subItem['subItems'] as $nestedItem)
                                                @php
                                                    $nestedRoute = $nestedItem['route'] ?? '#';
                                                    $nestedFullRoute = 'dashboard.' . $nestedRoute;
                                                    $nestedUrl = Route::has($nestedFullRoute) ? route($nestedFullRoute) : '#';
                                                @endphp
                                                <li>
                                                    <a href="{{ $nestedUrl }}"
                                                        class="flex items-center gap-3 px-4 py-2 text-sm rounded-lg transition-all duration-200"
                                                        :class="isActive('{{ $nestedRoute }}') ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                                        <span class="w-1 h-1 rounded-full" :class="isActive('{{ $nestedRoute }}') ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-gray-400 dark:bg-gray-600'"></span>
                                                        {{ $nestedItem['name'] }}
                                                        @if(!empty($nestedItem['count']))
                                                            <span class="ml-auto text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">42</span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        @php
                                            $route = $subItem['route'] ?? '#';
                                            $fullRoute = 'dashboard.' . $route;
                                            $url = Route::has($fullRoute) ? route($fullRoute) : '#';
                                        @endphp
                                        <a href="{{ $url }}"
                                            class="flex items-center gap-4 px-4 py-2.5 rounded-xl transition-all duration-200 group relative"
                                            :class="isActive('{{ $route }}') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                            <span class="flex-shrink-0 w-5 h-5" :class="isActive('{{ $route }}') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                                                {!! MenuHelper::getIconSvg($itemIcon) !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm">{{ $subItem['name'] }}</span>
                                            @if(!empty($subItem['count']))
                                                <span x-show="$store.sidebar.isExpanded" class="ml-auto px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">22</span>
                                            @endif
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @elseif($key === 'profile')
                    <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href=""
                            class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 group"
                            :class="isActive('profile') ? 'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800'">
                            <span class="flex-shrink-0 w-5 h-5" :class="isActive('profile') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-500 dark:text-gray-400'">
                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                            </span>
                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm font-semibold">{{ $item['name'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>

    <style>
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
        width: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
        }

        /* Firefox */
        .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
        }
    </style>
</aside>

<div x-show="$store.sidebar.isMobileOpen" 
     @click="$store.sidebar.setMobileOpen(false)"
     class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm lg:hidden">
</div>