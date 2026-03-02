<section class="w-full">
    <div class="flex flex-col gap-6">
        
        <!-- Header -->
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-300 mb-2">Trusted by 330,000+ Brands</h2>
            <p class="text-gray-500 dark:text-gray-400 font-medium">
                View collaborations from brands like Wealthsimple, Hopper, Deezer, and more.
            </p>
        </div>

        <!-- Collaborations Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @php
                $collabs = [
                    ['url' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/content/1.mp4#t=0.1', 'type' => 'video'],
                    ['url' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/content/2.png', 'type' => 'image'],
                    ['url' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/content/3.mp4#t=0.1', 'type' => 'video'],
                    ['url' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/content/4.png', 'type' => 'image'],
                    ['url' => 'https://d5ik1gor6xydq.cloudfront.net/websiteImages/content/5.mp4#t=0.1', 'type' => 'video'],
                ];
            @endphp

            @foreach($collabs as $collab)
                <div class="relative aspect-[3/4.2] rounded-xl overflow-hidden group bg-gray-100 dark:bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300">
                    
                    @if($collab['type'] === 'video')
                        <!-- Video Handling: Set to play on hover or keep static with #t=0.1 -->
                        <video class="w-full h-full object-cover" muted loop playsinline onmouseover="this.play()" onmouseout="this.pause(); this.currentTime = 0.1;">
                            <source src="{{ $collab['url'] }}" type="video/mp4">
                        </video>
                        
                        <!-- Play Icon Overlay (Matching Screenshot) -->
                        <div class="absolute bottom-4 left-4 z-10 pointer-events-none transition-opacity group-hover:opacity-0">
                            <svg class="w-6 h-6 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z" />
                            </svg>
                        </div>
                    @else
                        <!-- Image Handling -->
                        <img src="{{ $collab['url'] }}" 
                             alt="Brand Collaboration" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @endif

                    <!-- Subtle bottom vignette for better contrast -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-60 pointer-events-none"></div>
                </div>
            @endforeach
        </div>

    </div>
</section>