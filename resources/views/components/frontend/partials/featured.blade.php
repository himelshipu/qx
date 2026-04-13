@props([
    'featuredInfluencers' => collect(),
])

<section class="w-full">
	<div class="flex flex-row items-end justify-between mb-6 gap-3">
		<div>
			<h2 class="text-2xl font-semibold text-[#222] dark:text-white">Featured</h2>
			<p class="text-gray-500 dark:text-gray-400 leading-relaxed mt-1">Hire top influencers across all platforms</p>
		</div>
		<a href="{{ route('influencers', ['platformSlug' => 'featured']) }}"
			class="text-sm font-medium text-[#222] dark:text-gray-400 hover:text-purple-300 hover:underline pb-1">
			See All
		</a>
	</div>

	@if ($featuredInfluencers->isEmpty())
		<div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
			<p class="text-sm text-gray-500 dark:text-gray-400">No featured influencers available right now.</p>
		</div>
	@else
		<div class="flex overflow-x-auto gap-4 pb-2 lg:grid lg:grid-cols-4 sm:grid-cols-2 lg:gap-8 lg:px-0 scroll-smooth">

			@foreach ($featuredInfluencers as $influencer)
				@php
					$profileUrl = !empty($influencer['slug']) ? route('influencer.profile', ['slug' => $influencer['slug']]) : '#';
					$locationDisplay = trim((string) ($influencer['location'] ?? '')) ?: 'N/A';
				@endphp

			<a href="{{ $profileUrl }}"
				class="flex-shrink-0 w-[80%] sm:w-[60%] lg:w-auto group overflow-hidden font-sans cursor-pointer influencer-card block"
				data-influencer-id="{{ $influencer['id'] }}">

				<div class="relative overflow-hidden rounded-xl">
					<img src="{{ image_url($influencer['image_url']) }}"
						class="w-full h-48 sm:h-64 object-cover transition-transform duration-500 ease-out group-hover:scale-110"
						alt="{{ $influencer['name'] }}">

					<div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
					<span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
						<x-icons.heart-badge class="w-4 h-4 text-purple-400" /> Featured Influencer
					</span>
						<span class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
							<x-icons.checkmark class="w-4 h-4 text-green-500" /> {{ $influencer['engagement_label'] }} ER
						</span>
					</div>

					<div class="absolute bottom-3 left-3 right-3">
						<div class="flex flex-row items-center gap-2">
							<div class="bg-white text-black text-[10px] font-medium px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
								@if ($influencer['platform'] === 'facebook')
									<x-icons.facebook class="w-4 h-4 text-blue-600" />
								@elseif ($influencer['platform'] === 'instagram')
									<x-icons.instagram class="w-4 h-4 text-pink-500" />
								@elseif ($influencer['platform'] === 'tiktok')
									<x-icons.tiktok class="w-4 h-4 text-black" />
								@elseif ($influencer['platform'] === 'x')
									<x-icons.x class="w-4 h-4 text-black" />
								@elseif ($influencer['platform'] === 'ugc')
									<x-icons.camera class="w-4 h-4 text-gray-700" />
								@else
									<x-icons.group class="w-4 h-4 text-gray-700" />
								@endif
								{{ $influencer['followers_label'] }}
							</div>
						</div>
						<div class="flex items-center gap-1 text-white drop-shadow-md">
							<span class="font-bold text-sm">{{ $influencer['name'] }}</span>
							<span class="flex items-center text-xs gap-0.5">
								<x-icons.star class="w-4 h-4 text-yellow-400" /> {{ $influencer['rating_label'] }}
							</span>
						</div>
					</div>
				</div>

				<div class="pt-3 px-1">
					<div class="flex items-start justify-between gap-3">
						<h3 class="text-gray-800 dark:text-gray-300 text-[15px] leading-tight font-medium line-clamp-1">
							{{ $influencer['title'] }}
						</h3>
							<span class="text-[#222] dark:text-white font-medium text-sm leading-none max-w-[120px] inline-block truncate" title="{{ $influencer['slug'] ?? '' }}">
								{{ $influencer['handle'] ?? (!empty($influencer['slug']) ? '@' . $influencer['slug'] : 'N/A') }}
						</span>
					</div>
					<p class="text-sm text-gray-400 font-normal mt-1">{{ $locationDisplay }}</p>
				</div>

			</a>
			@endforeach

		</div>
	@endif
</section>
