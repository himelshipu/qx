@props([
    'title' => 'Please confirm',
    'message' => 'Are you sure you want to continue?',
    'confirmText' => 'Confirm',
    'variant' => 'danger',
])

<div x-data="window.Alpine.store('confirmModal')"
     x-show="isOpen"
     x-cloak
     x-on:keydown.escape.window="close()"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700"
         @click.outside="close()">
        <div class="flex items-start justify-between p-6 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="title || @js($title)"></h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line" x-text="message || @js($message)"></p>
            </div>
            <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 flex justify-end gap-3">
            <button type="button"
                    @click="close()"
                    class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                Cancel
            </button>
            <button type="button"
                    @click="confirm()"
                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white transition"
                    :class="{
                        'bg-red-600 hover:bg-red-700': (variant || @js($variant)) === 'danger',
                        'bg-amber-500 hover:bg-amber-600': (variant || @js($variant)) === 'warning',
                        'bg-blue-600 hover:bg-blue-700': (variant || @js($variant)) === 'info'
                    }">
                <span x-text="confirmText || @js($confirmText)"></span>
            </button>
        </div>
    </div>
</div>
