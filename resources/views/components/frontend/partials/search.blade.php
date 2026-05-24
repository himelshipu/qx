 <section id="how-it-works" class="search-section-wrapper bg-white dark:bg-transparent overflow-hidden">
            <div class="max-w-screen-2xl mx-auto px-2">

                <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-12 mb-8">

                    <div class="w-full lg:w-1/2">
                        <!-- Search Badge -->
                        <span class="inline-block px-6 py-2 rounded-full text-white font-bold text-sm bg-gradient-to-r from-[#9333EA] to-[#c084fc] shadow-md">
                            Search
                        </span>

                        <h2 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight mt-8">
                            Connect with Creators Who Have Sales Records
                        </h2>

                        <p class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg mt-4">
                           Purchase and chat with creators.  Place a deposit so you can quickly access creators and their profiles
                        </p>
                    </div>


                    <div class="w-full lg:w-1/2 relative group">
                        <div class="relative z-10 drop-shadow-2xl transition-transform duration-700 group-hover:-translate-y-2">
                            <img src="{{ asset('images/marketplace.png') }}" alt="Marketplace Preview" class="w-full h-auto rounded-2xl">
                        </div>

                        <div class="absolute -top-10 -right-10 w-64 h-64 bg-purple-100 dark:bg-purple-900/10 rounded-full blur-3xl opacity-50 -z-1"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $benefits = [
                            [
                                'icon' => '$',
                                'title' => 'Free to Use',
                                'desc' => 'Find ecommerce creators for free. No subscription, or hidden fee.'
                            ],
                            [
                                'icon' => '✓',
                                'title' => 'E-commerce and Exposure creators',
                                'desc' => 'Creators are vetted by us and they are separated into categories for e-commerce and exposure (high views)'
                            ],
                            [
                                'icon' => '💬',
                                'title' => 'Directly chat',
                                'desc' => 'Chat with creators to go through the task you need done'
                            ],
                            [
                                'icon' => '🔒',
                                'title' => 'Pay at completion',
                                'desc' => "You only need to pay the creator once you are satisfied"
                            ],
                        ];
                    @endphp

                    @foreach($benefits as $item)
                    <div class="relative bg-white dark:bg-gray-900 px-6 py-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-xl transition-all group overflow-hidden cursor-pointer">
                        <!-- Left Accent Border -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-[#dec0f8] to-[#d5adfd] opacity-50 group-hover:opacity-100 transition-opacity"></div>

                        <div class="relative z-10">
                            <div class="text-2xl mb-2 text-[#c084fc] font-bold">
                                {{ $item['icon'] }}
                            </div>
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                                {{ $item['title'] }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                {{ $item['desc'] }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </section>
