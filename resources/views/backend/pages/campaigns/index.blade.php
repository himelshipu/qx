@extends('backend.layouts.app')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="All Campaigns" />

    <div class="mt-8 px-2">
        
        <!-- 1. Header Section with Title and Add Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
            <div class="relative flex-1 max-w-md">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                    <x-icons.search class="w-4 h-4" />
                </span>
                <input type="text" 
                    placeholder="Search campaigns by name or type..." 
                    class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-8 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
            </div>
            
            <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    <x-icons.filter class="w-4 h-4" />
                    <span class="hidden sm:inline">Filter</span>
                </button>
                
                <button href="#"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <x-icons.plus class="w-4 h-4" />
                    <span>Add Campaign</span>
                </button>
            </div>
        </div>

        <!-- 2. Campaigns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 pb-20">
            
            @php
                // Dummy Data using your provided Cloudfront links
                $campaigns = [
                    [
                        'title' => "High-quality lifestyle content for modern furniture brand",
                        'type' => 'Instagram Campaign',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/33553/imgs/1758199129030127.webp',
                        'badge' => 'New Campaign'
                    ],
                    [
                        'title' => "Vloggers needed for sustainable travel gear review",
                        'type' => 'TikTok Campaign',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/40338/imgs/1764883342509305.webp',
                        'badge' => null
                    ],
                    [
                        'title' => "Creators for tech gadgets & workspace aesthetic setup",
                        'type' => 'User Generated Content',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/39672/imgs/1765208713027384.webp',
                        'badge' => null
                    ],
                    [
                        'title' => "Skincare routine showcase with premium organic products",
                        'type' => 'Instagram Campaign',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/40004/imgs/17645957143601542.webp',
                        'badge' => null
                    ],
                    [
                        'title' => "Fitness influencers for home workout equipment launch",
                        'type' => 'TikTok Campaign',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/37563/imgs/17621975118304632.webp',
                        'badge' => 'New Campaign'
                    ],
                    [
                        'title' => "Fashion creators for seasonal wardrobe transition series",
                        'type' => 'User Generated Content',
                        'image' => 'https://d5ik1gor6xydq.cloudfront.net/campaigns/37558/imgs/17621940385474098.webp',
                        'badge' => null
                    ]
                ];
            @endphp

            @foreach($campaigns as $camp)
            <div class="group relative overflow-hidden rounded-[1.8rem] bg-gray-100 dark:bg-gray-900 aspect-[4/3] cursor-pointer shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 dark:border-gray-800">
                
                <!-- Background Image -->
                <img src="{{ $camp['image'] }}" 
                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                     alt="Campaign Preview">

                <!-- Dark Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/30 to-transparent z-10 opacity-90"></div>

                <!-- Top Left Badge -->
                @if($camp['badge'])
                <div class="absolute top-5 left-5 z-20">
                    <span class="flex items-center gap-1.5 bg-pink-500 text-white text-[10px] font-medium uppercase px-3 py-1.5 rounded-full">
                        {{ $camp['badge'] }}
                    </span>
                </div>
                @endif

                <!-- Card Content -->
                <div class="absolute bottom-0 left-0 right-0 p-8 z-20">
                    <h3 class="text-white font-bold text-base md:text-lg mb-1.5 line-clamp-2 leading-tight group-hover:underline">
                        {{ $camp['title'] }}
                    </h3>
                    <p class="text-gray-300 text-[12px] font-semibold uppercase tracking-wider">
                        {{ $camp['type'] }}
                    </p>
                </div>
            </div>
            @endforeach

        </div>
    </div>

    <style>
        /* Ensures long titles wrap correctly like the original site */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;  
            overflow: hidden;
        }
    </style>
@endsection