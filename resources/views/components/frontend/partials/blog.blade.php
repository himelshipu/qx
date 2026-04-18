<section class="w-full">
	<div class="space-y-5">
		<div class="flex flex-row items-end justify-between mb-6 gap-3">
			<div>
				<h2 class="text-2xl font-semibold text-[#222] dark:text-white">
					Latest Blog Posts
				</h2>
				<p class="text-gray-500 dark:text-gray-400 leading-relaxed mt-1">
					Insights and strategies for influencer marketing
				</p>
			</div>
			<a href="{{ route('frontend.blogs.index') }}"
				class="text-sm font-medium text-[#222] dark:text-gray-400 hover:underline hover:text-purple-300 pb-1 whitespace-nowrap">
				See All
			</a>
		</div>

		@php
			$blogPosts = \App\Models\BlogPost::where('is_published', true)
				->orderBy('is_featured', 'desc')
				->orderBy('sort_order', 'asc')
				->orderBy('published_at', 'desc')
				->limit(6)
				->get();
		@endphp

		@if ($blogPosts->isEmpty())
			<div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
				<p class="text-sm text-gray-500 dark:text-gray-400">No blog posts available at the moment.</p>
			</div>
		@else
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
				@foreach ($blogPosts as $post)
					<a href="{{ route('frontend.blogs.show', $post->slug) }}"
						class="group overflow-hidden rounded-xl border border-gray-200 bg-white hover:border-gray-300 hover:shadow-lg transition-all dark:border-gray-700 dark:bg-gray-800/50 dark:hover:border-gray-600">
						
						<!-- Image -->
						<div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
							@if ($post->featured_image_path)
								<img src="{{ asset('storage/' . $post->featured_image_path) }}"
									alt="{{ $post->title }}"
									class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
							@else
								<div class="w-full h-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center">
									<svg class="w-12 h-12 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
											d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
									</svg>
								</div>
							@endif

							<!-- Featured Badge -->
							@if ($post->is_featured)
								<div class="absolute top-3 right-3">
									<span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
										Featured
									</span>
								</div>
							@endif
						</div>

						<!-- Content -->
						<div class="p-5">
							<div class="flex items-center gap-2 mb-2">
								<span class="inline-block text-xs font-semibold text-gray-500 dark:text-gray-400">
									{{ $post->published_at?->format('M d, Y') ?? 'Draft' }}
								</span>
								@if ($post->author)
									<span class="text-xs text-gray-400 dark:text-gray-500">•</span>
									<span class="text-xs text-gray-500 dark:text-gray-400">
										{{ $post->author->name }}
									</span>
								@endif
							</div>

							<h3 class="line-clamp-2 text-lg font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
								{{ $post->title }}
							</h3>

							<p class="mt-3 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
								{{ $post->summary }}
							</p>

							<div class="mt-4 inline-flex text-xs font-semibold text-indigo-600 dark:text-indigo-400 group-hover:gap-2 gap-1 transition-all">
								Read More
								<svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
								</svg>
							</div>
						</div>
					</a>
				@endforeach
			</div>
		@endif
	</div>
</section>
