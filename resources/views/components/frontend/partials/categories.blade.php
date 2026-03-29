<section class="w-full">
    <h2 class="text-2xl font-semibold text-[#222] dark:text-white mb-4">
        Categories
    </h2>

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