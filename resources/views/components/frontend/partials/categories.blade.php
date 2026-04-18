<section class="w-full">
    <div class="flex flex-row items-end justify-between mb-6 gap-3">
        <div>
            <h2 class="text-2xl font-semibold text-[#222] dark:text-white">
                Categories
            </h2>
            <p class="text-gray-500 dark:text-gray-400 leading-relaxed mt-1">
                Browse influencers by category
            </p>
        </div>
        <a href="{{ route('influencers') }}"
            class="text-sm font-medium text-[#222] dark:text-gray-400 hover:underline hover:text-purple-300 pb-1 whitespace-nowrap">
            View All
        </a>
    </div>

    <div class="flex overflow-x-auto gap-4 pb-3 lg:grid lg:grid-cols-4 sm:grid-cols-2 lg:gap-6 lg:px-0 scroll-smooth">

        @forelse($featuredCategories as $category)
            <div class="flex-shrink-0 w-[80%] sm:w-[60%] lg:w-auto">
                <x-frontend.partials.category-card :$category />
            </div>
        @empty
            @forelse($fallbackCategories as $category)
                <div class="flex-shrink-0 w-[80%] sm:w-[60%] lg:w-auto">
                    <x-frontend.partials.category-card-fallback :$category />
                </div>
            @empty
                <p class="text-gray-500">No categories available</p>
            @endforelse
        @endforelse

    </div>
</section>