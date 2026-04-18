@php
    $sidebarData = isset($menuItems)
        ? ['items' => $menuItems, 'activeAccordion' => $activeAccordion ?? null]
        : \App\Helpers\MenuHelper::buildSidebarMenu(\Illuminate\Support\Facades\Route::currentRouteName() ?? '');

    $menuItems = $sidebarData['items'] ?? [];
    $activeAccordion = $sidebarData['activeAccordion'] ?? null;
    $brandingLogoLight = \App\Models\Setting::fileUrl('branding.logo_light', '/images/logo/header-logo.png');
    $brandingLogoDark = \App\Models\Setting::fileUrl('branding.logo_dark', '/images/logo/header-logo.png');
@endphp

<aside id="sidebar"
    data-badges-route="{{ route('dashboard.api.sidebar-badges') }}"
    data-badges-refresh-seconds="{{ (int) config('communication.sidebar_refresh_seconds', 60) }}"
    class="fixed top-0 left-0 z-40 h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 shadow-xl"
    x-data="{ 
        openMenu: @js($activeAccordion),
        sidebarStorageKey: 'admin.sidebar.expanded',
        restoreSidebarState() {
            try {
                const savedState = localStorage.getItem(this.sidebarStorageKey);
                if (savedState !== null) {
                    $store.sidebar.isExpanded = savedState === '1';
                }
            } catch (e) {}
        },
        persistSidebarState() {
            try {
                localStorage.setItem(this.sidebarStorageKey, $store.sidebar.isExpanded ? '1' : '0');
            } catch (e) {}
        },
        toggleMenu(menuId) {
            this.openMenu = this.openMenu === menuId ? null : menuId;
        }
    }"
    :class="$store.sidebar.isExpanded ? 'w-72' : 'w-20'"
    x-init="
        restoreSidebarState();
        persistSidebarState();
        $watch('$store.sidebar.isExpanded', val => {
            persistSidebarState();
            if (!val) openMenu = null;
        });
    ">
    
    <div class="h-20 flex items-center justify-center px-4 border-b border-gray-100 dark:border-gray-800">
        <a href="/">
            <img src="{{ $brandingLogoLight }}" alt="Logo" class="h-11 dark:hidden block" x-show="$store.sidebar.isExpanded">
            <img src="{{ $brandingLogoDark }}" alt="Logo" class="h-11 dark:block hidden" x-show="$store.sidebar.isExpanded">
            <img src="/images/logo/logo-icon.png" alt="logo icon" class="h-9 w-9" x-show="!$store.sidebar.isExpanded">
        </a>
    </div>

    <nav class="p-4 h-[calc(100vh-4rem)] overflow-scroll custom-scrollbar">
        <ul class="space-y-6">
            @foreach ($menuItems as $key => $item)
                @if($key === 'dashboard')
                    <li>
                        <a href="{{ $item['url'] ?? '#' }}"
                            class="flex items-center rounded-xl transition-all duration-200 group relative"
                            :class="$store.sidebar.isExpanded
                                ? 'w-full justify-start gap-4 px-4 py-3'
                                : 'w-12 h-12 mx-auto justify-center gap-0 px-0 py-0'"
                            @class([
                                'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' => !empty($item['active']),
                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($item['active']),
                            ])>
                            <span class="shrink-0 w-5 h-5" @class([
                                'text-indigo-600 dark:text-indigo-400' => !empty($item['active']),
                                'text-gray-500 dark:text-gray-400' => empty($item['active']),
                            ])>
                                {!! \App\Helpers\MenuHelper::getIconSvg($item['icon']) !!}
                            </span>
                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm font-semibold">{{ $item['name'] }}</span>
                            @if(isset($item['badge_count']))
                                <span x-show="$store.sidebar.isExpanded" data-sidebar-badge-key="{{ $item['badge_key'] ?? '' }}" aria-hidden="{{ ($item['badge_count'] ?? 0) > 0 ? 'false' : 'true' }}" class="{{ ($item['badge_count'] ?? 0) > 0 ? '' : 'hidden' }} ml-auto px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">{{ ($item['badge_count'] ?? 0) > 99 ? '99+' : ($item['badge_count'] ?? 0) }}</span>
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
                                <li>
                                    @if(!empty($subItem['has_sub_items']))
                                        <button x-show="$store.sidebar.isExpanded" @click="toggleMenu('{{ $subItem['menu_id'] }}')"
                                            class="w-full flex items-center gap-4 px-4 py-2.5 rounded-xl transition-all duration-200 group"
                                            @class([
                                                'bg-gray-100 dark:bg-gray-800 text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($subItem['active']),
                                            ])>
                                            <span class="shrink-0 w-5 h-5" @class([
                                                'text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-500 dark:text-gray-400' => empty($subItem['active']),
                                            ])>
                                                {!! \App\Helpers\MenuHelper::getIconSvg($subItem['icon'] ?? 'home') !!}
                                            </span>
                                            <span class="flex-1 text-sm text-left">{{ $subItem['name'] }}</span>
                                            <svg class="w-4 h-4 transition-transform duration-200 text-gray-400"
                                                :class="openMenu === '{{ $subItem['menu_id'] }}' ? 'rotate-180' : ''"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <a x-show="!$store.sidebar.isExpanded" href="{{ $subItem['default_url'] ?? '#' }}"
                                            class="flex items-center justify-center w-12 h-12 mx-auto px-0 py-0 rounded-xl transition-all duration-200 group"
                                            @class([
                                                'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($subItem['active']),
                                            ])>
                                            <span class="shrink-0 w-5 h-5" @class([
                                                'text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-500 dark:text-gray-400' => empty($subItem['active']),
                                            ])>
                                                {!! \App\Helpers\MenuHelper::getIconSvg($subItem['icon'] ?? 'home') !!}
                                            </span>
                                        </a>

                                        <ul x-show="openMenu === '{{ $subItem['menu_id'] }}' && $store.sidebar.isExpanded"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 -translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            class="mt-1 ml-12 space-y-1">
                                            @foreach ($subItem['sub_items'] as $nestedItem)
                                                <li>
                                                    <a href="{{ $nestedItem['url'] ?? '#' }}"
                                                        class="flex items-center gap-3 px-4 py-2 text-sm rounded-lg transition-all duration-200"
                                                        @class([
                                                            'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' => !empty($nestedItem['active']),
                                                            'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($nestedItem['active']),
                                                        ])>
                                                        <span class="shrink-0 w-5 h-5" @class([
                                                            'text-indigo-600 dark:text-indigo-400' => !empty($nestedItem['active']),
                                                            'text-gray-500 dark:text-gray-400' => empty($nestedItem['active']),
                                                        ])>
                                                            {!! \App\Helpers\MenuHelper::getIconSvg($nestedItem['icon'] ?? ($subItem['icon'] ?? 'dashboard')) !!}
                                                        </span>
                                                        {{ $nestedItem['name'] }}
                                                        @if(isset($nestedItem['badge_count']))
                                                            <span data-sidebar-badge-key="{{ $nestedItem['badge_key'] ?? '' }}" aria-hidden="{{ ($nestedItem['badge_count'] ?? 0) > 0 ? 'false' : 'true' }}" class="{{ ($nestedItem['badge_count'] ?? 0) > 0 ? '' : 'hidden' }} ml-auto text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">{{ ($nestedItem['badge_count'] ?? 0) > 99 ? '99+' : ($nestedItem['badge_count'] ?? 0) }}</span>
                                                        @endif
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <a href="{{ $subItem['url'] ?? '#' }}"
                                            class="flex items-center rounded-xl transition-all duration-200 group relative"
                                            :class="$store.sidebar.isExpanded
                                                ? 'w-full justify-start gap-4 px-4 py-2.5'
                                                : 'w-12 h-12 mx-auto justify-center gap-0 px-0 py-0'"
                                            @class([
                                                'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($subItem['active']),
                                            ])>
                                            <span class="shrink-0 w-5 h-5" @class([
                                                'text-indigo-600 dark:text-indigo-400' => !empty($subItem['active']),
                                                'text-gray-500 dark:text-gray-400' => empty($subItem['active']),
                                            ])>
                                                {!! \App\Helpers\MenuHelper::getIconSvg($subItem['icon'] ?? 'home') !!}
                                            </span>
                                            <span x-show="$store.sidebar.isExpanded" class="flex-1 text-sm">{{ $subItem['name'] }}</span>
                                            @if(isset($subItem['badge_count']))
                                                <span x-show="$store.sidebar.isExpanded" data-sidebar-badge-key="{{ $subItem['badge_key'] ?? '' }}" aria-hidden="{{ ($subItem['badge_count'] ?? 0) > 0 ? 'false' : 'true' }}" class="{{ ($subItem['badge_count'] ?? 0) > 0 ? '' : 'hidden' }} ml-auto px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-full">{{ ($subItem['badge_count'] ?? 0) > 99 ? '99+' : ($subItem['badge_count'] ?? 0) }}</span>
                                            @endif
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @elseif($key === 'profile')
                    <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-800">
                        <a href="{{ $item['url'] ?? '#' }}"
                            class="flex items-center rounded-xl transition-all duration-200 group"
                            :class="$store.sidebar.isExpanded
                                ? 'w-full justify-start gap-4 px-4 py-3'
                                : 'w-12 h-12 mx-auto justify-center gap-0 px-0 py-0'"
                            @class([
                                'bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 text-indigo-600 dark:text-indigo-400' => !empty($item['active']),
                                'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' => empty($item['active']),
                            ])>
                            <span class="shrink-0 w-5 h-5" @class([
                                'text-indigo-600 dark:text-indigo-400' => !empty($item['active']),
                                'text-gray-500 dark:text-gray-400' => empty($item['active']),
                            ])>
                                {!! \App\Helpers\MenuHelper::getIconSvg($item['icon']) !!}
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