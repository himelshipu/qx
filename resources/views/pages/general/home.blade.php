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
                    
                    <!-- Chooses Platform -->
                    <div class="relative flex-1 w-full md:w-auto">
                        <div id="platform-trigger" class="flex flex-col items-start cursor-pointer border-b md:border-b-0 md:border-r border-gray-100 dark:border-gray-700 pb-4 md:pb-0 md:pr-4 group">
                            <span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Platform</span>
                            <span id="selected-platform" class="text-gray-400 text-sm truncate">Choose a platform</span>
                        </div>

                        <!-- Dropdown Menu -->
                        <div id="platform-menu" class="hidden absolute top-14 left-[-24px] mt-2 w-full md:w-[420px] px-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl z-50 py-2 overflow-hidden overflow-y-scroll max-h-60">
                            @php
                                $platforms = ['Any', 'Instagram', 'TikTok', 'User Generated Content', 'YouTube', 'Twitter', 'Twitch', 'Facebook', 'LinkedIn'];
                            @endphp
                            
                            @foreach($platforms as $platform)
                                <div class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors" data-value="{{ $platform }}">
                                    {{ $platform }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Category Section -->
                    <div class="relative flex-[1.5] flex flex-col items-start pt-4 md:pt-0 md:pl-8 w-full md:w-auto group">
                        <div id="category-trigger" class="w-full cursor-pointer">
                            <span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Category</span>
                            <input type="text" id="category-input" placeholder="Enter keywords, niches or categories" 
                                class="w-full bg-transparent  border-none p-0 outline-none focus:ring-0 text-sm text-gray-900 placeholder-gray-400"
                                autocomplete="off">
                        </div>

                        <!--Category Dropdown -->
                        <div id="category-menu" class="hidden absolute top-full left-[24px] right-0 mt-4 w-[90vw] md:w-[600px] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-[2rem] shadow-2xl z-50 p-6 transition-all">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Popular</p>
                            
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $categories = [
                                        'Lifestyle', 'Beauty', 'Fashion', 'Travel', 'Health & Fitness', 
                                        'Family & Children', 'Food & Drink', 'Comedy & Entertainment', 
                                        'Art & Photography', 'Music & Dance', 'Animals & Pets', 'Model', 
                                        'Adventure & Outdoors', 'Education', 'Entrepreneur & Business', 
                                        'Athlete & Sports', 'Technology', 'Gaming', 'Healthcare', 
                                        'LGBTQ2+', 'Actor', 'Automotive', 'Celebrity & Public Figure', 
                                        'Vegan', 'Skilled Trades', 'Cannabis'
                                    ];
                                @endphp

                                @foreach($categories as $category)
                                    <button type="button" 
                                        class="category-option px-4 py-2 text-[13px] font-medium bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all active:scale-95"
                                        data-value="{{ $category }}">
                                        {{ $category }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Search Button Container -->
                    <div class="flex justify-end mt-4 md:mt-0 md:items-center w-full md:w-auto">
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
                    <h2 class="text-2xl font-semibold text-[#222] dark:text-white">Featured</h2>
                    <p class="text-sm text-gray-400  font-normal dark:text-gray-400">Hire top influencers across all platforms</p>
                </div>
                <a href="#" class="text-sm font-medium text-[#222] dark:text-gray-200 hover:underline">
                    See All
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach (range(1,4) as $i)
                <div class="group overflow-hidden font-sans cursor-pointer creator-card" data-creator-id="{{ $i }}">
                    <div class="relative overflow-hidden rounded-xl">
                        <!-- Wishlist Button -->
                        <button class="wishlist-btn absolute top-3 right-3 z-30 p-1.5 transition-all duration-300 hover:scale-110 drop-shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 wishlist-heart-icon fill-none stroke-white stroke-[2px]" viewBox="0 0 24 24">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>

                        <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" 
                            class="w-full h-48 sm:h-64 object-cover transition-transform duration-500 ease-out group-hover:scale-110" 
                            alt="Creator">
                        
                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                            <span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
                            <x-icons.heart-badge class="w-4 h-4 text-pink-400" /> Top Creator
                            </span>
                            <span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
                                <x-icons.checkmark class="w-4 h-4 text-green-500" /> Responds Fast
                            </span>
                        </div>

                        <!-- Profile Info -->
                        <div class="absolute bottom-3 left-3 right-3">
                            <div class="flex flex-row items-center gap-2">
                                <div class="bg-white text-black text-[10px] font-medium px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
                                    <x-icons.instagram class="w-4 h-4 text-pink-500" /> 11.1K
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
                            <span class="text-[#222] dark:text-white font-medium text-lg leading-none">$60</span>
                        </div>
                        <p class="text-[13px] text-gray-400 font-normal mt-1">Los Angeles, CA, US</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="w-full bg-[#F8F6F2] dark:bg-gray-950 p-8 ">
            <div class="flex flex-col gap-8 mt-8">
                <div>
                    <h2 class="text-xl font-semibold text-[#222] dark:text-white mb-4">
                        Case Studies
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Wealthsimple Launches "Wealthsimple Cash" With Instagram and TikTok Influencers on QX Influencer Platform
                                </p>
                            </div>
                        </a>

                         <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Wealthsimple Launches "Wealthsimple Cash" With Instagram and TikTok Influencers on QX Influencer Platform
                                </p>
                            </div>
                        </a>

                        <a href="#" class="group relative rounded-xl overflow-hidden h-64">
                            <img src="https://fastly.picsum.photos/id/64/367/267.jpg?hmac=D-dgjsVmMZqhGCO6oL4mSQ_n2oLNkFTPHl7JEbjf1Gs" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 grayscale" />
                            <div class="absolute inset-0 bg-black/50 flex items-end p-6">
                                <p class="text-white font-semibold leading-snug">
                                    Advertising Agency Gets 100+ Influencers Per Month on Autopilot with QX Influencer Platform
                                </p>
                            </div>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-12">
                        330,000+ Brands Work With Influencers on QX Influencer Platform
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div>
                            <span class="text-pink-500 text-5xl leading-none">“</span>
                            <h4 class="font-semibold text-gray-900 dark:text-white mt-4">
                                5 stars from a creator and a brand
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4 leading-relaxed">
                                I've used QX Influencer Platform from both the Creator side and the Brand side! It is extremely user-friendly and has lead to some great relationships with creators/brands I wouldn't have been connected to otherwise. Love the platform!
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
                                Best platform to connect with influencers and content creators. I've signed up to many platforms, QX Influencer Platform is the easiest to use and gives the best results for my brand.
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
                                Been using QX Influencer Platform to generate content for our seasonal clothing lines. Super easy for us to search for relevant influencers and pay them. We save at least 10–20 hours a month on this.
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

<!-- Modal Overlay -->
<div id="wishlist-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <!-- Backdrop shadow -->
    <div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content Card -->
    <div class="relative w-auto lg:w-[500px] max-w-md bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300" id="modal-container">
        
        <!-- Header -->
        <div class="relative p-6 text-center border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Add to List</h3>
            <button id="close-modal" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 space-y-4">
            <!-- Create New List Option -->
            <button class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group">
                <div class="w-14 h-14 bg-black dark:bg-white flex items-center justify-center rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Create new list</span>
            </button>

            <!-- Existing Lists (Mockup) -->
            <div id="existing-lists" class="space-y-2">
                <button class="list-item w-full flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <div class="w-14 h-14 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center overflow-hidden">
                        <!-- Creator Profile Picture from the clicked card will go here -->
                        <img src="" class="list-preview-img hidden w-full h-full object-cover">
                        <svg class="placeholder-icon w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="text-left">
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Brain Capita</p>
                        <p class="text-sm text-gray-500">0 influencers</p>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Selectors
    const platformTrigger = document.getElementById('platform-trigger');
    const platformMenu = document.getElementById('platform-menu');
    const platformLabel = document.getElementById('selected-platform');
    const platformOptions = document.querySelectorAll('.platform-option');

    const categoryTrigger = document.getElementById('category-trigger');
    const categoryMenu = document.getElementById('category-menu');
    const categoryInput = document.getElementById('category-input');
    const categoryOptions = document.querySelectorAll('.category-option');

    let selectedCategories = [];

    // --- PLATFORM LOGIC ---
    platformTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        categoryMenu.classList.add('hidden'); // Close Category
        platformMenu.classList.toggle('hidden'); // Toggle Platform
    });

    platformOptions.forEach(option => {
        option.addEventListener('click', () => {
            const val = option.getAttribute('data-value');
            platformLabel.textContent = val;
            platformLabel.classList.remove('text-gray-400');
            platformLabel.classList.add('text-gray-900', 'dark:text-white');
            platformMenu.classList.add('hidden');
        });
    });

    // --- CATEGORY LOGIC ---
    categoryTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        platformMenu.classList.add('hidden'); // Close Platform
        categoryMenu.classList.toggle('hidden'); // Toggle Category
    });

    categoryOptions.forEach(option => {
        option.addEventListener('click', (e) => {
            e.stopPropagation();
            const val = option.getAttribute('data-value');

            if (selectedCategories.includes(val)) {
                selectedCategories = selectedCategories.filter(item => item !== val);
                option.classList.remove('bg-black', 'text-white', 'dark:bg-white', 'dark:text-black');
                option.classList.add('bg-gray-50', 'text-gray-700');
            } else {
                selectedCategories.push(val);
                option.classList.add('bg-black', 'text-white', 'dark:bg-white', 'dark:text-black');
                option.classList.remove('bg-gray-50', 'text-gray-700');
            }
            categoryInput.value = selectedCategories.join(', ');
        });
    });

    // --- GLOBAL CLICK OUTSIDE ---
    document.addEventListener('click', (e) => {
        if (!platformTrigger.contains(e.target) && !platformMenu.contains(e.target)) {
            platformMenu.classList.add('hidden');
        }
        if (!categoryTrigger.contains(e.target) && !categoryMenu.contains(e.target)) {
            categoryMenu.classList.add('hidden');
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('wishlist-modal');
    const modalContainer = document.getElementById('modal-container');
    const closeBtn = document.getElementById('close-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');

    let currentCard = null;

    // Open Modal Function
    const openModal = (card, btn) => {
        currentCard = card;
        const heart = btn.querySelector('.wishlist-heart-icon');
        const imgUrl = card.querySelector('img').src;

        heart.classList.remove('fill-none', 'stroke-white');
        heart.classList.add('fill-red-500', 'stroke-red-500');

        const previewImg = modal.querySelector('.list-preview-img');
        const placeholder = modal.querySelector('.placeholder-icon');
        previewImg.src = imgUrl;
        previewImg.classList.remove('hidden');
        placeholder.classList.add('hidden');

        // Show Modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Trigger Animation
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    // Close Modal Function
    const closeModal = () => {
        modalContainer.classList.add('scale-95', 'opacity-0');
        modalContainer.classList.remove('scale-100', 'opacity-100');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    };

    // Attach click events to all cards
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent card navigation
            const card = this.closest('.creator-card');
            
            // Toggle Logic
            const heart = this.querySelector('.wishlist-heart-icon');
            if (heart.classList.contains('fill-red-500')) {
                // If already red, just turn it off (un-wishlist)
                heart.classList.add('fill-none', 'stroke-white');
                heart.classList.remove('fill-red-500', 'stroke-red-500');
            } else {
                openModal(card, this);
            }
        });
    });

    // Close on backdrop or close button
    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    // Close on ESC key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
});
</script>
@endsection