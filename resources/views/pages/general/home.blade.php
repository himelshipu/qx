@extends('layouts.general.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-200">
    <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    
        <section class="w-full">
            <!-- Header Navigation -->
            <div class="mx-auto py-6 flex flex-col sm:px-0 px-4 sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                <div class="flex items-center gap-2">
                    <img src="/images/logo/logo-dark.png" alt="Logo" class="h-11 dark:block hidden">
                    <img src="/images/logo/logo.png" alt="Logo" class="h-11 dark:hidden block">
                </div>

                <nav class="flex flex-wrap items-center justify-center gap-4 md:gap-6 lg:gap-8 text-sm lg:text-[14px] font-medium text-gray-600 dark:text-gray-300">
                    <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Search</a>
                    <a href="#" class="hover:text-black dark:hover:text-white transition-colors">How It Works</a>
                    <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Pricing</a>
                    <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Login</a>
                    <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Join as Brand</a>
                    <!-- Gradient hover or text for Creator link -->
                    <a href="#"
                    class="font-bold inline-block bg-clip-text text-transparent
                            bg-[length:300%_300%]
                            bg-[linear-gradient(90deg,rgb(255,132,160)_0%,rgb(251,102,157)_20%,rgb(179,45,194)_95%,rgb(136,95,183)_100%)]
                            transition-all duration-700 ease-out
                            hover:bg-[position:80%_0%]">
                        Join as Creator
                    </a>


                </nav>
            </div>

            <!-- Hero Content -->
            <div class="max-w-6xl mx-auto px-4 pt-4 lg:pt-16 pb-8 text-center">
                <h1 class="pb-4 text-4xl md:text-5xl font-bold tracking-tight bg-clip-text text-transparent bg-[linear-gradient(90deg,#FF84A0_0%,#D84EB7_50%,#9553BF_100%)] leading-[1.1]">
                    Influencer Marketing Made Easy
                </h1>

                <p class="text-base text-gray-500 dark:text-gray-400 mx-auto leading-relaxed mt-4">
                    Find and collaborate with top Instagram, TikTok, YouTube, and UGC influencers to create authentic content for your brand.
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-6xl mx-auto px-4 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-md md:rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row p-5 md:p-2 md:pl-10 relative items-start md:items-center">
                    
                    <!-- Platform Section -->
                    <div class="flex-1 flex flex-col items-start cursor-pointer border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 pb-4 md:pb-0 md:pr-4">
                        <span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Platform</span>
                        <span class="text-gray-400 text-sm truncate">Choose a platform</span>
                    </div>

                    <!-- Category Section -->
                    <div class="flex-[1.5] flex flex-col items-start cursor-pointer pt-4 md:pt-0 md:pl-8">
                        <span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Category</span>
                        <span class="text-gray-400 text-sm truncate">Enter keywords, niches or categories</span>
                    </div>

                    <!-- Search Button Container -->
                    <div class="flex justify-end mt-4 md:mt-0 md:items-center">
                        <button class="bg-[#222] hover:opacity-80 transition-all p-4 md:p-5 rounded-full text-white shadow-lg md:ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                    
                </div>
            </div>

            <!-- Quick Filters / Badges -->
            <div class="max-w-6xl flex flex-wrap items-center gap-3 justify-center mb-16 mx-auto px-4">
                @php
                    $badges = [
                        ['icon' => 'star', 'label' => 'Rising Instagram Stars'],
                        ['icon' => 'star', 'label' => 'Rising TikTok Stars'],
                        ['icon' => 'trending-up', 'label' => 'Most Viewed'],
                        ['icon' => 'camera', 'label' => 'UGC'],
                        ['icon' => 'shopping-bag', 'label' => 'Fashion'],
                        ['icon' => 'happy-face-plus', 'label' => 'Beauty'],
                        ['icon' => 'heart-plus', 'label' => 'Health & Fitness']
                    ];
                @endphp

                @foreach($badges as $badge)
                <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden">
                    <!-- Subtle Hover Gradient Overlay -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#FF84A0] via-[#D84EB7] to-[#9553BF] transition-opacity"></div>
                    
                    <span class="relative z-10">
                        @include('components.icons.' . $badge['icon'], ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
                    </span>
                    <span class="relative z-10 text-sm group-hover:text-black dark:group-hover:text-white">{{ $badge['label'] }}</span>
                </button>
                @endforeach
            </div>
        </section>

        

        <section class="w-full pb-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Featured</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hire top influencers across all platforms</p>
                </div>
                <a href="#" class="text-sm font-medium text-gray-900 dark:text-gray-200 hover:underline">
                    See All
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach (range(1,4) as $i)
                <div class="group overflow-hidden font-sans cursor-pointer">
                    <div class="relative overflow-hidden rounded-xl"> <img 
                            src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" 
                            class="w-full h-48 sm:h-64 object-cover transition-transform duration-500 ease-out group-hover:scale-110 inset-0" 
                            alt="Creator"
                        >
                        
                        
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
                               <x-icons.heart-badge class="w-4 h-4 text-pink-400" /> Top Creator
                            </span>
                            <span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
                                <x-icons.checkmark class="w-4 h-4 text-green-500" /> Responds Fast
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3">
                            <div class="flex flex-row items-center gap-2">
                                <div class="bg-white text-black text-[10px] font-bold px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
                                <x-icons.instagram class="w-4 h-4 text-gradient-to-r from-purple-500 to-pink-500" /> 11.1K
                                </div>
                                <div class="bg-white text-black text-[10px] font-bold px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
                                    <x-icons.tiktok class="w-4 h-4 text-gradient-to-r from-purple-500 to-pink-500" /> 11.3K
                                </div>
                            </div>
                            <div class="flex items-center gap-1 text-white drop-shadow-md">
                                <span class="font-bold text-sm">Oleksa</span>
                                <span class="flex items-center text-xs gap-0.5">
                                    <x-icons.star class="w-4 h-4 text-yellow-400" /> 5.0
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 px-1">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-gray-800 dark:text-gray-200 text-[15px] leading-tight font-medium line-clamp-1">
                                Skincare Influencer, Ugc Crea...
                            </h3>
                            <span class="text-gray-900 dark:text-white font-bold text-lg leading-none">$60</span>
                        </div>
                        <p class="text-[13px] text-gray-400 font-medium mt-1">Los Angeles, CA, US</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="w-full bg-white dark:bg-gray-950">
            <div class=" mx-auto px-6 py-20 space-y-20">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-8">
                        Case Studies
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Wealthsimple Launches "Wealthsimple Cash" With Instagram and TikTok Influencers on Collabstr
                                </p>
                            </div>
                        </a>

                         <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Wealthsimple Launches "Wealthsimple Cash" With Instagram and TikTok Influencers on Collabstr
                                </p>
                            </div>
                        </a>

                        <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 grayscale" />
                            <div class="absolute inset-0 bg-black/50 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Advertising Agency Gets 100+ Influencers Per Month on Autopilot with Collabstr
                                </p>
                            </div>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-12">
                        330,000+ Brands Work With Influencers on Collabstr
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div>
                            <span class="text-pink-500 text-5xl leading-none">“</span>
                            <h4 class="font-semibold text-gray-900 dark:text-white mt-4">
                                5 stars from a creator and a brand
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 leading-relaxed">
                                I've used Collabstr from both the Creator side and the Brand side! It is extremely user-friendly and has lead to some great relationships with creators/brands I wouldn't have been connected to otherwise. Love the platform!
                            </p>
                            <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                Layla - Influencer & Founder
                            </p>
                        </div>

                        <div>
                            <span class="text-pink-500 text-5xl leading-none">“</span>
                            <h4 class="font-semibold text-gray-900 dark:text-white mt-4">
                                Best platform to connect with influencers
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 leading-relaxed">
                                Best platform to connect with influencers and content creators. I've signed up to many platforms, collabstr is the easiest to use and gives the best results for my brand.
                            </p>
                            <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                Myriam - Founder of BBeyond
                            </p>
                        </div>

                        <div>
                            <span class="text-pink-500 text-5xl leading-none">“</span>
                            <h4 class="font-semibold text-gray-900 dark:text-white mt-4">
                                Great way to generate content
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 leading-relaxed">
                                Been using Collabstr to generate content for our seasonal clothing lines. Super easy for us to search for relevant influencers and pay them. We save at least 10–20 hours a month on this.
                            </p>
                            <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">
                                Courtney - Marketer
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>





        <footer class="w-full bg-white border-t border-gray-100 dark:bg-gray-900 dark:border-gray-800 mt-10">
            <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-16">
                    <div class="flex flex-col items-start">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-6">Resources</h4>
                        <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Pricing</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Blog</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Resource Hub</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">TikTok Ebook For Brands</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">2026 Influencer Marketing Report</a></li>
                        </ul>
                    </div>

                    <div class="flex flex-col items-start">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-6">Tools</h4>
                        <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Influencer Price Calculator</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Instagram Fake Follower Checker</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">TikTok Fake Follower Checker</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Instagram Engagement Rate Calculator</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">TikTok Engagement Rate Calculator</a></li>
                        </ul>
                    </div>

                    <div class="flex flex-col items-start">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-6">Discover</h4>
                        <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Find Influencers</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Top Influencers</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Search Influencers</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Buy Shoutouts</a></li>
                        </ul>
                    </div>

                    <div class="flex flex-col items-start">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-6">Support</h4>
                        <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Contact Us</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">How It Works</a></li>
                            <li><a href="#" class="hover:text-gray-900 dark:hover:text-white">Frequently Asked Questions</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-4 md:gap-6 text-sm text-gray-600 dark:text-gray-400">
                        <span>© QX Inc.</span>
                        <a href="#" class="hover:text-gray-900 dark:hover:text-white">Privacy</a>
                        <a href="#" class="hover:text-gray-900 dark:hover:text-white">Terms</a>
                        <a href="#" class="hover:text-gray-900 dark:hover:text-white">Sitemap</a>
                    </div>

                    <div class="flex items-center gap-4 text-gray-800 dark:text-gray-300">
                        <a href="#" aria-label="Instagram" class="hover:text-black dark:hover:text-white">
                            <x-icons.instagram class="w-4 h-4" />
                        </a>

                        <a href="#" aria-label="TikTok" class="hover:text-black dark:hover:text-white">
                            <x-icons.tiktok class="w-4 h-4" />
                        </a>

                        <a href="#" aria-label="Twitter" class="hover:text-black dark:hover:text-white">
                            <x-icons.twitter class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            </div>
        </footer>

    </main>
</div>
@endsection