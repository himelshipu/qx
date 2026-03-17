<section class="w-full">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($featuredCategories as $category)
            <x-frontend.partials.category-card :$category />
        @empty
            @forelse($fallbackCategories as $category)
                <x-frontend.partials.category-card-fallback :$category />
            @empty
                <p class="text-gray-500">No categories available</p>
            @endforelse
        @endforelse
    </div>
</section>