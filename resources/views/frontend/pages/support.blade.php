@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen transition-colors duration-200"
     x-data="{ 
        step: 'main', 
        activeQuestion: null,
        // Helper to go back
        goBack() {
            if(this.step === 'questions') this.step = 'categories';
            else if(this.step === 'categories') this.step = 'main';
        }
     }">
    
    <div class="max-w-6xl mx-auto">
        
        <div class="mb-16">
            <div class="bg-pink-50 dark:bg-transparent border border-pink-100 dark:border-gray-200 rounded-xl p-6 text-center">
                <p class="text-sm font-medium text-gray-800 dark:text-white">
                    Beware of scams, QX will never contact you on Telegram or WeeChat.
                </p>
            </div>
        </div>

        <div x-show="step === 'main'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 transform scale-95" class="space-y-16">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-center mb-1">What Do You Need Help With Today?</h1>
                <p class="mt-4 text-gray-500 dark:text-gray-400  text-base">Choose from our support options below to get the help you need</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card 1: Knowledge Base -->
                <a href="#" class="group relative flex flex-col items-center text-center bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-14 rounded-xl shadow-sm hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-pink-50 dark:bg-purple-400/10 rounded-full flex items-center justify-center mb-8"><svg class="w-7 h-7 text-pink-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke-width="1.5"/></svg></div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">View Knowledge Base</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Browse help articles and tutorials to find quick answers</p>
                </a>

                <!-- Card 2: Submit Ticket -->
                <div @click="step = 'categories'" class="cursor-pointer group relative flex flex-col items-center text-center bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-14 rounded-xl shadow-sm hover:shadow-xl transition-all">
                    <div class="w-16 h-16 bg-blue-50 dark:bg-purple-400/10 rounded-full flex items-center justify-center mb-8"><svg class="w-7 h-7 text-blue-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="1.5"/></svg></div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Submit a Ticket</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Contact our support team directly for personalized assistance</p>
                </div>
            </div>
        </div>

        <div x-show="step === 'categories'" x-cloak x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 translate-x-4" class="space-y-12">
            <div class="flex items-center gap-6">
                <button @click="goBack()" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="w-4 h-4 text-gray-800 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5"/></svg>
                </button>
                <h2 class="text-3xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-4">What category does your question relate to?</h2>
            </div>

            <div class="space-y-0 divide-y divide-gray-100 dark:divide-gray-800 border-t border-gray-100 dark:border-gray-800">
                <template x-for="cat in ['Orders & Payments', 'General Question', 'Feedback', 'Other']">
                    <div @click="step = 'questions'" class="group flex items-center justify-between py-6 cursor-pointer hover:px-4 transition-all duration-300">
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-200 group-hover:text-black dark:group-hover:text-purple-400" x-text="cat"></span>
                        <svg class="w-5 h-5 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="2.5"/></svg>
                    </div>
                </template>
            </div>
        </div>

        <div x-show="step === 'questions'" x-cloak x-transition:enter="transition ease-out duration-400" x-transition:enter-start="opacity-0 translate-x-4" class="space-y-10">
            <div class="flex items-center gap-6">
                <button @click="goBack()" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <svg class="w-4 h-4 text-gray-800 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5"/></svg>
                </button>
                <div>
                    <h2 class="text-3xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-1">Common Questions</h2>
                    <p class="text-sm text-gray-400 font-medium mt-1">Don't see your question? <a href="#" class="underline hover:text-black dark:hover:text-purple-400">Contact us</a></p>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-800 border-t border-gray-100 dark:border-gray-800">
                <!-- FAQ Item 1 -->
                <div class="py-6">
                    <button @click="activeQuestion = (activeQuestion === 1 ? null : 1)" class="flex w-full items-center justify-between text-left group">
                        <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-purple-400 transition">My card was charged before my order was accepted</span>
                        <svg class="w-6 h-6 text-gray-400 transition-transform" :class="activeQuestion === 1 ? 'rotate-45 text-black dark:text-purple-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6v12m6-6H6" stroke-width="1.5"/></svg>
                    </button>
                    <div x-show="activeQuestion === 1" x-collapse x-cloak>
                        <p class="mt-4 text-gray-500 dark:text-gray-400 leading-relaxed">When you place an order on QX, we place a hold on your card for the order amount. This is not a charge, although it may show up as one on your credit card statement. If your order is declined, the charge will disappear.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="py-6">
                    <button @click="activeQuestion = (activeQuestion === 2 ? null : 2)" class="flex w-full items-center justify-between text-left group">
                        <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-purple-400 transition">My order was not accepted and I have not received my money back</span>
                        <svg class="w-6 h-6 text-gray-400 transition-transform" :class="activeQuestion === 2 ? 'rotate-45 text-black dark:text-purple-400' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 6v12m6-6H6" stroke-width="1.5"/></svg>
                    </button>
                    <div x-show="activeQuestion === 2" x-collapse x-cloak>
                        <p class="mt-4 text-gray-500 dark:text-gray-400 leading-relaxed">Refunds usually process within 3-5 business days depending on your bank.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom CTA Button -->
            <div class="pt-10">
                <a href="#" class="flex w-full items-center justify-center py-5 border border-gray-900 dark:border-purple-400/50 rounded-xl font-bold text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-purple-400/10 transition shadow-sm active:scale-[0.99]">
                    Still have questions? Contact us
                </a>
            </div>
        </div>

    </div>
</div>

<style> [x-cloak] { display: none !important; } </style>
@endsection