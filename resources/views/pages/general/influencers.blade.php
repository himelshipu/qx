@extends('layouts.general.app')

@section('content')

<div class="min-h-screen bg-white dark:bg-gray-950 px-4 sm:px-6 lg:px-8 py-20">
    <main class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <section class="w-full">

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
                                        class="category-option px-4 py-2 text-[13px] font-medium bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white rounded-lg border border-transparent hover:border-gray-200 dark:hover:border-gray-600 transition-all active:scale-95"
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
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#9333EA] via-[#C084FC] to-[#A94DFF] transition-opacity"></div>
                    
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
                    <h2 class="text-3xl font-semibold text-[#222] dark:text-white">Influencers</h2>
                    <p class="text-sm text-gray-400  font-normal dark:text-gray-400">Hire top influencers across all platforms</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach (range(1,20) as $i)
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

@endsection

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