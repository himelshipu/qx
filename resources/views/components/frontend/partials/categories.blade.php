<section class="w-full">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $categories = [
                ['name' => 'Fashion', 'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/creatorMarketplace/categories/fashion.png'],
                ['name' => 'Music & Dance', 'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/creatorMarketplace/categories/music%20&%20dance.png'],
                ['name' => 'Beauty', 'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/creatorMarketplace/categories/beauty.png'],
                ['name' => 'Travel', 'img' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/creatorMarketplace/categories/travel.png'],
            ];
        @endphp

        @foreach($categories as $category)
        <a href="#" class="group relative block overflow-hidden rounded-xl aspect-[4/3] bg-gray-100">
            <!-- Image with Zoom on Hover -->
            <img 
                src="{{ $category['img'] }}" 
                alt="{{ $category['name'] }}" 
                class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-110"
            >

            <!-- Gradient Overlay (Bottom to Top) -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent transition-opacity group-hover:opacity-90"></div>

            <!-- Text label -->
            <div class="absolute bottom-5 left-5">
                <span class="text-white text-lg font-bold tracking-tight">
                    {{ $category['name'] }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
</section>