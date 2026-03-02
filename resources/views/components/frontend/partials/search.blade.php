 <section class="search-section-wrapper bg-white dark:bg-transparent overflow-hidden">
            <div class="max-w-screen-2xl mx-auto px-2">
                
                <div class="flex flex-col lg:flex-row items-center gap-16 lg:gap-12 mb-8">  
                   
                    <div class="w-full lg:w-1/2">
                        <!-- Search Badge -->
                        <span class="inline-block px-6 py-2 rounded-full text-white font-bold text-sm bg-gradient-to-r from-[#9333EA] to-[#c084fc] shadow-md">
                            Search
                        </span>

                        <h2 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight mt-8">
                            Find and Hire Influencers in Seconds on the Marketplace
                        </h2>

                        <div class="mt-8 space-y-8">          
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Search Influencers</h3>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg">
                                    Search thousands of vetted Instagram, TikTok, and YouTube influencers.
                                </p>
                            </div>

                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Purchase & Chat Securely</h3>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg">
                                    Safely purchase and communicate through Collabstr. We hold your payment until the work is completed.
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Receive Quality Content</h3>
                                <p class="text-gray-500 dark:text-gray-400 leading-relaxed max-w-lg">
                                    Receive your high-quality content from influencers directly through the platform.
                                </p>
                            </div>
                        </div>
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
                                'title' => 'No Upfront Cost',
                                'desc' => 'Search influencers for free. No subscriptions, contracts, or hidden fees.'
                            ],
                            [
                                'icon' => '✓',
                                'title' => 'Vetted Influencers',
                                'desc' => 'Every influencer is vetted by us. Always receive high-quality, professional content.'
                            ],
                            [
                                'icon' => '💬',
                                'title' => 'Instant Chat',
                                'desc' => 'Instantly chat with influencers and stay in touch throughout the whole transaction.'
                            ],
                            [
                                'icon' => '🔒',
                                'title' => 'Secure Purchases',
                                'desc' => "Your money is held safely until you approve the influencer's work."
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