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
				use App\Models\FeaturedCollaboration;
				$collabs = FeaturedCollaboration::published();
			@endphp

			@forelse($collabs as $collab)
				<div
					class="relative aspect-[3/4.2] rounded-xl overflow-hidden group bg-gray-100 dark:bg-gray-900 shadow-sm hover:shadow-xl transition-all duration-300"
					title="{{ $collab->brand_name }}">

					@if ($collab->asset_type === 'video' && $collab->video_path)
						<!-- Video Handling: Set to play on hover or keep static with #t=0.1 -->
						<video class="w-full h-full object-cover" muted loop playsinline onmouseover="this.play()"
							onmouseout="this.pause(); this.currentTime = 0.1;">
							<source src="{{ $collab->getVideoUrl() }}#t=0.1" type="video/mp4">
						</video>

						<!-- Play Icon Overlay (Matching Screenshot) -->
						<div class="absolute bottom-4 left-4 z-10 pointer-events-none transition-opacity group-hover:opacity-0">
							<svg class="w-6 h-6 text-white drop-shadow-md" fill="currentColor" viewBox="0 0 24 24">
								<path d="M8 5v14l11-7z" />
							</svg>
						</div>
					@elseif($collab->asset_type === 'image' && $collab->image_path)
						<!-- Image Handling -->
						<img src="{{ $collab->getImageUrl() }}" alt="{{ $collab->brand_name }}"
							class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
					@else
						<!-- Fallback: Show placeholder -->
						<div
							class="w-full h-full bg-gradient-to-br from-indigo-300 to-purple-300 dark:from-indigo-700 dark:to-purple-700 flex items-center justify-center">
							<svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
								</path>
							</svg>
						</div>
					@endif

					<!-- Subtle bottom vignette for better contrast -->
					<div
						class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-60 pointer-events-none">
					</div>
				</div>
			@empty
				<!-- Fallback: Show message if no collaborations exist -->
				<div class="col-span-2 sm:col-span-3 md:col-span-4 lg:col-span-5 text-center py-12">
					<p class="text-gray-500 dark:text-gray-400">No featured collaborations yet</p>
				</div>
			@endforelse
		</div>

	</div>
</section>
