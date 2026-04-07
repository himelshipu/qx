@extends('frontend.layouts.app')

@section('content')

	<div class="min-h-screen bg-white dark:bg-gray-950 flex flex-col gap-4 px-4 sm:px-6 lg:px-8 py-20">


		<div class="bg-[#1A1A1A] text-white p-8">
			<div class="flex flex-col md:flex-row items-center justify-between gap-6">

				<div class="max-w-2xl">
					<h1 class="text-2xl font-bold mb-2">Complete Your Profile</h1>
					<p class="text-gray-400 text-sm leading-relaxed">
						Your profile is the first thing influencers view to learn about your brand.
						Having a complete, detailed profile helps influencers decide if you're a fit to collaborate with.
					</p>
				</div>

				@auth
					@if (optional(Auth::user()->brand)->id === optional($brand)->id)
						<a href="{{ route('brand.profile.edit', ['slug' => Auth::user()->slug]) }}"
							class="bg-white text-black px-6 py-2.5 rounded-xl font-bold text-sm
                                hover:bg-gray-100 transition shadow-md whitespace-nowrap">
							Edit Profile
						</a>
					@endif
				@endauth
			</div>
		</div>

		<div class="px-4 flex flex-col gap-6">

			<!-- ================= PROFILE SECTION ================= -->
			<section class="py-14 max-w-svw mx-auto">

				<!-- Edit Button -->
				<div class="flex justify-end mb-6">
					@auth
						@if (optional(Auth::user()->brand)->id === optional($brand)->id)
							<a href="{{ route('brand.profile.edit', ['slug' => Auth::user()->slug]) }}"
								class="flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-50 hover:text-black transition">

								<x-icons.edit class="w-4 h-4" />
								Edit
							</a>
						@endif
					@endauth
				</div>

				<!-- Cover -->
				<div class="relative mb-20">
					@if ($brand->user->cover_image_path)
						<img src="{{ \App\Helpers\ImageHelper::url($brand->user->cover_image_path) }}" alt="Cover"
							class="w-full h-80 md:h-[320px] rounded-3xl object-cover">
					@else
						<div class="w-full h-80 md:h-[320px] bg-gray-200 rounded-3xl"></div>
					@endif

					<!-- Avatar -->
					<div class="absolute -bottom-14 left-1/2 -translate-x-1/2">
						@if ($brand->user->profile_image_path)
							<img src="{{ \App\Helpers\ImageHelper::url($brand->user->profile_image_path) }}" alt="{{ $brand->brand_name }}"
								class="w-32 h-32 rounded-full border-[6px] border-white shadow-md object-cover">
						@else
							<div
								class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-400 to-purple-500
                                    border-[6px] border-white shadow-md
                                    flex items-center justify-center
                                    text-4xl font-bold text-white">
								{{ strtoupper(substr($brand->brand_name ?? $brand->user->name, 0, 1)) }}
							</div>
						@endif
					</div>
				</div>

				<!-- Name + Description -->
				<div class="text-center">
					@php
						$locationParts = array_filter([
						    $brand->user->address_line,
						    $brand->user->city,
						    $brand->user->country,
						    $brand->user->postal_code,
						]);
					@endphp

					<h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $brand->brand_name ?? $brand->user->name }}
					</h2>

					<p class="text-sm text-gray-500 dark:text-white max-w-2xl mx-auto leading-relaxed">
						{{ $brand->user->bio ?? 'No bio provided yet.' }}
					</p>

					@if (count($locationParts) > 0)
						<p class="text-sm text-gray-500 dark:text-gray-300 mt-3">
							📍 {{ implode(', ', $locationParts) }}
						</p>
					@endif

					@if ($brand->industry)
						<p class="text-sm text-gray-500 dark:text-gray-300 mt-2">
							<span class="font-semibold">Industry:</span> {{ $brand->industry }}
						</p>
					@endif

					<!-- Social Links -->
					@if (
						$brand->website ||
							($brand->socialLinks &&
								($brand->socialLinks->instagram_url ||
									$brand->socialLinks->facebook_url ||
									$brand->socialLinks->youtube_url ||
									$brand->socialLinks->tiktok_url ||
									$brand->socialLinks->x_url ||
									$brand->socialLinks->linkedin_url)))
						<div class="flex flex-wrap gap-3 mt-6 justify-center">
							@if ($brand->website)
								<a href="{{ $brand->website }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.website class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->instagram_url)
								<a href="{{ $brand->socialLinks?->instagram_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.instagram class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->facebook_url)
								<a href="{{ $brand->socialLinks?->facebook_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.facebook class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->youtube_url)
								<a href="{{ $brand->socialLinks?->youtube_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.youtube class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->tiktok_url)
								<a href="{{ $brand->socialLinks?->tiktok_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.tiktok class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->x_url)
								<a href="{{ $brand->socialLinks?->x_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.x class="w-5 h-5" />
								</a>
							@endif
							@if ($brand->socialLinks?->linkedin_url)
								<a href="{{ $brand->socialLinks?->linkedin_url }}" target="_blank" rel="noopener noreferrer"
									class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
									<x-icons.linkedin class="w-5 h-5" />
								</a>
							@endif
						</div>
					@endif
				</div>

			</section>


			<!-- ================= CAMPAIGNS SECTION ================= -->
			<section class="border-t py-4 border-gray-100">

				<h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Campaigns</h3>

				@if ($campaigns && count($campaigns) > 0)
					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
						@foreach ($campaigns as $campaign)
							<a href="{{ route('frontend.campaigns.show', ['campaign' => $campaign->id]) }}"
								class="group relative overflow-hidden rounded-2xl bg-gray-200 aspect-video flex items-center justify-center hover:shadow-xl transition duration-300 cursor-pointer">
								<!-- Campaign background or image -->
								<div class="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400"></div>

								<!-- Campaign info overlay -->
								<div
									class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition duration-300 flex flex-col justify-end p-4">
									<div class="bg-black/70 backdrop-blur-sm text-white rounded-lg p-3">
										<h4 class="font-semibold text-sm mb-1">{{ $campaign->title }}</h4>
										<div class="flex items-center gap-2 text-xs text-gray-300">
											<span class="px-2 py-1 bg-blue-600 rounded">
												{{ ucfirst($campaign->campaign_type) }}
											</span>
											<span class="capitalize">{{ $campaign->status }}</span>
										</div>
									</div>
								</div>
							</a>
						@endforeach
					</div>
				@else
					<div
						class="max-w-xl relative group overflow-hidden rounded-2xl bg-gray-200 aspect-video flex items-center justify-center hover:shadow-xl transition duration-300 cursor-pointer">
						<svg class="w-20 h-20 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>

						<div class="absolute bottom-4 left-4">
							<span class="bg-black/70 backdrop-blur-sm text-white text-xs font-semibold px-3 py-1.5 rounded-lg">
								No campaigns
							</span>
						</div>
					</div>
				@endif

			</section>


			<!-- ================= REVIEWS SECTION ================= -->
			<section class="border-t py-4 border-gray-100">

				<h3 class="text-xl font-medium text-[#222] dark:text-white mb-4">Reviews</h3>

				<p class="text-sm text-gray-500 italic">
					You have no reviews yet.
				</p>

			</section>

		</div>
	</div>

@endsection
