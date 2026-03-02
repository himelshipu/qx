 <section class="faq-section-wrapper" x-data="{ activeAccordion: null }">
            <h2 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white mb-6">FAQ</h2>

            <div class="divide-y divide-gray-200 dark:divide-gray-800 border-b border-gray-200 dark:border-gray-800">
                
                <!-- FAQ Item 1 -->
                <div class="py-6">
                    <button @click="activeAccordion = (activeAccordion === 1 ? null : 1)" 
                            class="flex w-full items-center justify-between text-left group">
                        <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 transition-colors">
                            How does rockies work?
                        </span>
                        <span class="ml-6 flex-shrink-0 text-gray-400">
                            <svg class="h-6 w-6 transition-transform duration-300" :class="activeAccordion === 1 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                            </svg>
                        </span>
                    </button>
                    <div x-show="activeAccordion === 1" x-collapse x-cloak>
                        <div class="mt-4 text-gray-500 dark:text-gray-400  text-base">
                            rockies allows influencers to create a profile and list their services for brands to purchase directly. You set your own prices and manage your collaborations all in one place.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="py-6">
                    <button @click="activeAccordion = (activeAccordion === 2 ? null : 2)" 
                            class="flex w-full items-center justify-between text-left group">
                        <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 transition-colors">
                            How do I get paid?
                        </span>
                        <span class="ml-6 flex-shrink-0 text-gray-400">
                            <svg class="h-6 w-6 transition-transform duration-300" :class="activeAccordion === 2 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                            </svg>
                        </span>
                    </button>
                    <div x-show="activeAccordion === 2" x-collapse x-cloak>
                        <div class="mt-4 text-gray-500 dark:text-gray-400  text-[15px]">
                            Payments are made directly through our website. Once you complete an order, the funds are released to your wallet where you can choose your preferred payout method.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="py-6">
                    <button @click="activeAccordion = (activeAccordion === 3 ? null : 3)" 
                            class="flex w-full items-center justify-between text-left group">
                        <span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 transition-colors">
                            What platforms does rockies support?
                        </span>
                        <span class="ml-6 flex-shrink-0 text-gray-400">
                            <svg class="h-6 w-6 transition-transform duration-300" :class="activeAccordion === 3 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                            </svg>
                        </span>
                    </button>
                    <div x-show="activeAccordion === 3" x-collapse x-cloak>
                        <div class="mt-4 text-gray-500 dark:text-gray-400  text-[15px]">
                            Currently, you can list services for Instagram, TikTok, YouTube, Twitch, Twitter, and UGC.
                        </div>
                    </div>
                </div>

            </div>
        </section>