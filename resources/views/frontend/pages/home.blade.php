@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-200">
    <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    
        @include('frontend.partials.hero')
        
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
                        <span class="text-pink-500 text-5xl leading-none">"</span>
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
                        <span class="text-pink-500 text-5xl leading-none">"</span>
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
                        <span class="text-pink-500 text-5xl leading-none">"</span>
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
    const modal = document.getElementById('wishlist-modal');
    const modalContainer = document.getElementById('modal-container');
    const closeBtn = document.getElementById('close-modal');
    const backdrop = document.getElementById('modal-backdrop');
    const wishlistBtns = document.querySelectorAll('.wishlist-btn');
    const listItems = document.querySelectorAll('.list-item');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }

    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    });

    closeBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    // Close on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>
@endsection