@extends('frontend.layouts.app')

@section('content')

<div class="min-h-screen bg-white dark:bg-gray-950 flex flex-col gap-4 px-4 sm:px-6 lg:px-8 py-20">


    <div class="bg-[#1A1A1A] text-white p-8">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

            <div class="max-w-2xl">
                <h1 class="text-2xl font-bold mb-2">Complete Your Profile</h1>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Your profile is the first thing creators view to learn about your brand.
                    Having a complete, detailed profile helps creators decide if you're a fit to collaborate with.
                </p>
            </div>

            @auth
                @if(optional(Auth::user()->brand)->id === optional($brand)->id)
                    <a href="{{ route('dashboard.brand.profile.edit', ['slug' => Auth::user()->slug]) }}"
                        class="bg-white text-black px-6 py-2.5 rounded-xl font-bold text-sm
                                hover:bg-gray-100 transition shadow-md whitespace-nowrap">
                        Edit Profile
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="px-4 flex flex-col gap-6">

        <!-- ================= PROFILE SECTION ================= -->
        <section class="py-14 max-w-svw mx-auto">

            <!-- Edit Button -->
            <div class="flex justify-end mb-6">
                @auth
                    @if(optional(Auth::user()->brand)->id === optional($brand)->id)
                        <a href="{{ route('dashboard.brand.profile.edit', ['slug' => Auth::user()->slug]) }}" class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-50 hover:text-black transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Edit
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Cover -->
            <div class="relative mb-20">
                @if($brand->user->cover_image_path)
                    <img src="{{ asset('storage/' . $brand->user->cover_image_path) }}" alt="Cover" class="w-full h-80 md:h-[320px] rounded-3xl object-cover">
                
                    @else
                    
                    <div class="w-full h-80 md:h-[320px] bg-gray-200 rounded-3xl"></div>
                @endif

                <!-- Avatar -->
                <div class="absolute -bottom-14 left-1/2 -translate-x-1/2">
                    @if($brand->user->profile_image_path)
                        <img src="{{ asset('storage/' . $brand->user->profile_image_path) }}" alt="{{ $brand->brand_name }}"
                             class="w-32 h-32 rounded-full border-[6px] border-white shadow-md object-cover">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-purple-500
                                    border-[6px] border-white shadow-md
                                    flex items-center justify-center
                                    text-4xl font-bold text-white">
                            {{ strtoupper(substr($brand->brand_name ?? $brand->user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Name + Description -->
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $brand->brand_name ?? $brand->user->name }}</h2>

                <p class="text-sm text-gray-500 dark:text-white max-w-2xl mx-auto leading-relaxed">
                    {{ $brand->description ?? 'No description provided yet.' }}
                </p>

                @if($brand->user->city || $brand->user->country)
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-3">
                        📍 {{ $brand->user->city }}{{ $brand->user->city && $brand->user->country ? ', ' : '' }}{{ $brand->user->country }}
                    </p>
                @endif

                @if($brand->industry)
                    <p class="text-sm text-gray-500 dark:text-gray-300 mt-2">
                        <span class="font-semibold">Industry:</span> {{ $brand->industry }}
                    </p>
                @endif

                <!-- Social Links -->
                @if($brand->socialLinks && ($brand->socialLinks->instagram_url || $brand->socialLinks->facebook_url || $brand->socialLinks->youtube_url || $brand->socialLinks->tiktok_url || $brand->socialLinks->x_url || $brand->socialLinks->linkedin_url))
                    <div class="flex flex-wrap gap-3 mt-6 justify-center">
                        @if($brand->socialLinks->instagram_url)
                            <a href="{{ $brand->socialLinks->instagram_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.015-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z"/></svg>
                            </a>
                        @endif
                        @if($brand->socialLinks->facebook_url)
                            <a href="{{ $brand->socialLinks->facebook_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($brand->socialLinks->youtube_url)
                            <a href="{{ $brand->socialLinks->youtube_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        @endif
                        @if($brand->socialLinks->tiktok_url)
                            <a href="{{ $brand->socialLinks->tiktok_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.498 5.091a4.921 4.921 0 00-3.84-1.889 4.899 4.899 0 00-4.999 4.899 4.898 4.898 0 00.077.836c-2.706-.13-5.164-1.413-6.887-3.463-.284.469-.448 1.016-.448 1.597 0 1.702.867 3.201 2.182 4.082-.81-.026-1.572-.247-2.234-.618v.061c0 2.379 1.694 4.363 3.942 4.817-.413.111-.849.171-1.296.171-.317 0-.627-.03-.929-.087.628 1.953 2.445 3.376 4.6 3.416-1.681 1.319-3.809 2.105-6.112 2.105-.397 0-.789-.023-1.175-.068 2.179 1.397 4.768 2.212 7.548 2.212 9.056 0 13.99-7.502 13.99-13.99 0-.213-.005-.425-.014-.637.961-.694 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                        @endif
                        @if($brand->socialLinks->x_url)
                            <a href="{{ $brand->socialLinks->x_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24h-6.514l-5.106-6.554-5.835 6.554H2.556l7.73-8.835L1.488 2.25h6.69l4.713 6.231 5.579-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                        @endif
                        @if($brand->socialLinks->linkedin_url)
                            <a href="{{ $brand->socialLinks->linkedin_url }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.475-2.236-1.986-2.236-1.81 0-2.594 1.214-3.011 2.391-.155.381-.194.914-.194 1.446v3.968h-3.554s.004-6.446 0-7.112h3.554v1.001c.312-.48 2.359-5.824 5.822-5.824 4.936 0 8.679 3.226 8.679 10.159v3.776zM5.337 9.341c-1.113 0-1.846-.738-1.846-1.66 0-.922.74-1.66 1.846-1.66 1.114 0 1.847.738 1.847 1.66 0 .922-.733 1.66-1.847 1.66zm1.581 11.111H3.756V9.229h3.162v11.223zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.225 0z"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </section>


        <!-- ================= CAMPAIGNS SECTION ================= -->
        <section class="border-t py-4 border-gray-100">

            <h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Campaigns</h3>

            @if($campaigns && count($campaigns) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($campaigns as $campaign)
                        <a href="#" class="group relative overflow-hidden rounded-2xl bg-gray-200 aspect-video flex items-center justify-center hover:shadow-xl transition duration-300 cursor-pointer">
                            <!-- Campaign background or image -->
                            <div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400"></div>

                            <!-- Campaign info overlay -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition duration-300 flex flex-col justify-end p-4">
                                <div class="bg-black/70 backdrop-blur-sm text-white rounded-lg p-3">
                                    <h4 class="font-semibold text-sm mb-1">{{ $campaign->title }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-gray-300">
                                        <span class="px-2 py-1 bg-blue-600 rounded">
                                            {{ ucfirst($campaign->campaign_type) }}
                                        </span>
                                        <span class="capitalize">{{ $campaign->status }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="max-w-xl relative group overflow-hidden rounded-2xl bg-gray-200 aspect-video flex items-center justify-center hover:shadow-xl transition duration-300 cursor-pointer">
                    <svg class="w-20 h-20 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>

                    <div class="absolute bottom-4 left-4">
                        <span class="bg-black/70 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
                            No campaigns
                        </span>
                    </div>
                </div>
            @endif

        </section>


        <!-- ================= REVIEWS SECTION ================= -->
        <section class="border-t py-4 border-gray-100">

            <h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Reviews</h3>

            <p class="text-sm text-gray-500 italic">
                You have no reviews yet.
            </p>

        </section>

    </div>
</div>

@endsection
