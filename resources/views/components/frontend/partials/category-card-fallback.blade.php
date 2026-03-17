<a 
    href="{{ route('influencers.category', ['categorySlug' => $category['slug']]) }}" 
    class="group relative block overflow-hidden rounded-xl aspect-[4/3] bg-gray-100 dark:bg-gray-800 transition-all duration-300 hover:shadow-lg"
>
    <!-- Image with Zoom on Hover -->
    <img 
        src="{{ $category['img'] }}" 
        alt="{{ $category['name'] }}" 
        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-110"
        onerror="this.style.display='none'"
    >

    <!-- Fallback Gradient (shown if image fails to load) -->
    <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-200 to-gray-400 dark:from-gray-700 dark:to-gray-900 flex items-center justify-center">
        <x-icons.image class="w-16 h-16 opacity-50 text-gray-400" />
    </div>

    <!-- Gradient Overlay (Bottom to Top) -->
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent transition-opacity group-hover:opacity-90"></div>

    <!-- Text label -->
    <div class="absolute bottom-5 left-5">
        <span class="text-white text-lg font-bold tracking-tight">
            {{ $category['name'] }}
        </span>
    </div>
</a>
