 <section class="w-full">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-[#222] dark:text-white">Social Media</h2>
            <p class="text-gray-500 dark:text-gray-400 leading-relaxed mt-1">Hire top influencers across all platforms</p>
        </div>
        <a href="#" class="text-sm font-medium text-[#222] dark:text-gray-400 hover:underline">
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
                    <x-icons.heart-badge class="w-4 h-4 text-purple-400" /> Top Creator
                    </span>
                    <span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
                        <x-icons.checkmark class="w-4 h-4 text-green-500" /> Responds Fast
                    </span>
                </div>

                <!-- Profile Info -->
                <div class="absolute bottom-3 left-3 right-3">
                    <div class="flex flex-row items-center gap-2">
                        <div class="bg-white text-black text-[10px] font-medium px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
                            <x-icons.instagram class="w-4 h-4 text-purple-500" /> 11.1K
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
                    <h3 class="text-gray-800 dark:text-gray-300 text-[15px] leading-tight font-medium line-clamp-1">
                        Skincare Influencer, Ugc Crea...
                    </h3>
                    <span class="text-[#222] dark:text-white font-medium text-lg leading-none">$60</span>
                </div>
                <p class="text-sm text-gray-400 dark:text-gray-400 font-normal mt-1">Los Angeles, CA, US</p>
            </div>
        </div>
        @endforeach
    </div>
</section>