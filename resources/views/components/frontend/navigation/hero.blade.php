<div class="flex flex-col gap-8 items-center">
     <!-- Hero Content -->
    <div class="flex flex-col gap-3 items-center text-center">
        <h1 class="font-bold text-4xl lg:text-5xl inline-block bg-clip-text text-transparent bg-[length:300%_300%] bg-[linear-gradient(90deg,rgba(175,120,229,1)_1%,rgba(205,157,253,1)_73%,rgba(192,132,252,1)_100%)] transition-all duration-700 ease-out leading-14" >
            The Creator Network Built 
            for E-Commerce
        </h1>

        <p class="text-base text-gray-500 dark:text-gray-400 leading-relaxed max-w-4xl">
            Rockies brings brands and creators together into a single workflow -- talent discovery, task assignments, approvals, timeline tracking, and reporting -- plus automated commissions, payouts, invoices and contracts
        </p>
    </div>

    <!-- Search Bar -->
    <div class="w-full md:w-2/3 mx-auto">
        <div class="bg-white dark:bg-gray-800 w-full rounded-md md:rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row p-5 md:p-2 md:pl-10 relative items-start md:items-center">
            
            <!-- Chooses Platform -->
            <div class="relative flex-1">
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
            <div class="relative flex-[1.5] flex flex-col items-start pt-4 md:pt-0 md:pl-8 group">
                <div id="category-trigger" class="w-full cursor-pointer">
                    <span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Category</span>
                    <input type="text" id="category-input" placeholder="Enter keywords, niches or categories" 
                        class="w-full bg-transparent  border-none p-0 outline-none focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400"
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
    <div class="max-w-6xl flex flex-wrap items-center gap-3 justify-center mx-auto px-4">
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
            <div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity"></div>
            
            <span class="relative z-10">
                @include('components.icons.' . $badge['icon'], ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
            </span>
            <span class="relative z-10 text-sm group-hover:text-black dark:group-hover:text-white">{{ $badge['label'] }}</span>
        </button>
        @endforeach
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
                option.classList.remove('bg-black', 'text-white', 'dark:bg-purple-500', 'dark:text-white');
                option.classList.add('bg-gray-50', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-white');
            } else {
                selectedCategories.push(val);
                option.classList.add('bg-black', 'text-white', 'dark:bg-purple-500', 'dark:text-white');
                option.classList.remove('bg-gray-50', 'text-gray-700', 'dark:bg-gray-700', 'dark:text-white');
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


</script>