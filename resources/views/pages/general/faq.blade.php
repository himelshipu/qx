@extends('layouts.general.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-20">
    <main class="max-w-5xl flex flex-col gap-16 mx-auto">
        
        <!-- SECTION: FOR INFLUENCERS -->
        <section x-data="{ activeAccordion: null }">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-10 px-4">For Influencers</h2>

            <div class="border-t border-gray-200 dark:border-gray-800">
                
                <!-- FAQ Item 1 -->
                <div class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <div class="py-7 px-4">
                        <button @click="activeAccordion = (activeAccordion === 1 ? null : 1)" 
                                class="flex w-full items-center justify-between text-left">
                            <span class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                How does QX work?
                            </span>
                            <span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
                                <svg class="h-6 w-6 transition-transform duration-500" :class="activeAccordion === 1 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="activeAccordion === 1" x-collapse x-cloak>
                            <div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl">
                                QX allows influencers to create a profile and list their services for brands to purchase directly. You set your own prices and manage your collaborations all in one place.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <div class="py-7 px-4">
                        <button @click="activeAccordion = (activeAccordion === 2 ? null : 2)" 
                                class="flex w-full items-center justify-between text-left">
                            <span class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                How do I get paid?
                            </span>
                            <span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
                                <svg class="h-6 w-6 transition-transform duration-500" :class="activeAccordion === 2 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="activeAccordion === 2" x-collapse x-cloak>
                            <div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl">
                                Payments are made directly through our website. Once you complete an order, the funds are released to your wallet where you can choose your preferred payout method.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <div class="py-7 px-4">
                        <button @click="activeAccordion = (activeAccordion === 3 ? null : 3)" 
                                class="flex w-full items-center justify-between text-left">
                            <span class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                What platforms does QX support?
                            </span>
                            <span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
                                <svg class="h-6 w-6 transition-transform duration-500" :class="activeAccordion === 3 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="activeAccordion === 3" x-collapse x-cloak>
                            <div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl">
                                Currently, you can list your services for Instagram, TikTok, YouTube, Twitch, Twitter and UGC.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- SECTION: FOR BRANDS -->
        <section x-data="{ activeAccordion: null }">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-10 px-4">For Brands</h2>
            <div class="border-t border-gray-200 dark:border-gray-800">
                
                <div class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <div class="py-7 px-4">
                        <button @click="activeAccordion = (activeAccordion === 1 ? null : 1)" 
                                class="flex w-full items-center justify-between text-left">
                            <span class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                How do I find influencers?
                            </span>
                            <span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
                                <svg class="h-6 w-6 transition-transform duration-500" :class="activeAccordion === 1 ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
                                </svg>
                            </span>
                        </button>
                        <div x-show="activeAccordion === 1" x-collapse x-cloak>
                            <div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl">
                                You can browse our marketplace and filter by platform, niche, location, and audience size to find the perfect match for your campaign.
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </main>
</div>
@endsection