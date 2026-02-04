<!-- Theme Toggle Button Component -->
<button
    @click="$store.theme.toggle()"
    class="relative inline-flex items-center gap-2 px-3 py-2 rounded-lg transition-colors duration-200"
    :class="{
        'bg-gray-100 text-gray-800 hover:bg-gray-200': $store.theme.theme === 'light',
        'bg-gray-800 text-gray-100 hover:bg-gray-700': $store.theme.theme === 'dark'
    }"
    :title="$store.theme.theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
    x-data="{ 
        get currentIcon() { 
            return $store.theme.theme === 'light' ? '☀️' : '🌙'
        }
    }"
>
    <span x-text="currentIcon" class="text-lg"></span>
    <span class="hidden sm:inline text-sm font-medium" x-text="$store.theme.theme === 'light' ? 'Light' : 'Dark'"></span>
</button>
