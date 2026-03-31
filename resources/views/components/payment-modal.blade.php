@props([
    'show' => 'showCardModal',
    'title' => 'Add New Card',
    'description' => 'Your card information is secure and encrypted with Stripe',
])

<div x-show="{{ $show }}" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden p-8 md:p-10 w-full max-w-xl">
        <button @click="closePaymentModal()" type="button" class="absolute top-6 right-6 text-gray-400 hover:text-black dark:hover:text-white transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $title }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-8">{{ $description }}</p>

        <div x-show="payment?.error" class="mb-4 p-4 bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800 rounded-lg">
            <p class="text-sm text-red-700 dark:text-red-300" x-text="payment?.error"></p>
        </div>

        <form @submit.prevent="submitPaymentMethod()" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-800 dark:text-gray-300 mb-2">Card Details <span class="text-red-500">*</span></label>
                <div id="card-element" class="p-4 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800"></div>
                <div id="card-errors" class="text-xs text-red-500 mt-2"></div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" id="is_default" x-model="payment.isDefault" class="rounded border-gray-300 dark:border-gray-700">
                <label for="is_default" class="text-sm font-medium text-gray-800 dark:text-gray-300">
                    Set as default payment method
                </label>
            </div>

            <button type="submit" :disabled="payment?.isProcessing" class="w-full bg-[#222] dark:bg-white text-white dark:text-black font-bold py-3 rounded-lg text-base hover:opacity-90 transition active:scale-95 mt-8 disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-text="payment?.isProcessing ? 'Processing...' : 'Save Card'"></span>
            </button>

            <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                🔒 Secured by Stripe
            </p>
        </form>
    </div>
</div>
