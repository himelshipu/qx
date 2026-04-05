@extends('frontend.layouts.app')

@section('content')
	@php
		$displayName =
		    trim((string) ($creator->display_name ?? '')) !== ''
		        ? (string) $creator->display_name
		        : (string) ($creator->user->name ?? 'Creator');
		$locationParts = array_values(
		    array_filter([
		        trim((string) ($creator->user->address_line ?? '')),
		        trim((string) ($creator->user->city ?? '')),
		        trim((string) ($creator->user->postal_code ?? '')),
		        trim((string) ($creator->user->country ?? '')),
		    ]),
		);
		$locationText = $locationParts !== [] ? implode(', ', $locationParts) : '';

		$categoryNames = $creator->categories->pluck('name')->filter()->take(5)->values();
		if ($categoryNames->isEmpty()) {
		    $categoryNames = collect(['Tech', 'Tesla', 'Health & Fitness', 'Car', 'Pet']);
		}

		// Get first 3 portfolio images for grid display
		$portfolioItems = $creator->portfolios->where('media_type', 'image')->take(3);
		$gridImages = $portfolioItems->pluck('file_path')->map(fn($path) => \App\Helpers\ImageHelper::url($path))->values();

		// Fill with defaults if not enough images
		while ($gridImages->count() < 3) {
		    $gridImages->push(asset('default.webp'));
		}

		$profileImageUrl = image_url($creator->user?->profile_image_path);

		$platformBadges = $creator->platformStats
		    ->take(2)
		    ->map(static function ($platformStat): array {
		        $followers = (int) ($platformStat->follower_count ?? 0);
		        $followersLabel =
		            $followers >= 1000000
		                ? number_format($followers / 1000000, 1) . 'M'
		                : ($followers >= 1000
		                    ? number_format($followers / 1000, 1) . 'K'
		                    : (string) $followers);
		        $platformLabel = match ($platformStat->platform) {
		            'ugc' => 'UGC',
		            'x' => 'X',
		            'tiktok' => 'TikTok',
		            'youtube' => 'YouTube',
		            'linkedin' => 'LinkedIn',
		            default => \Illuminate\Support\Str::headline((string) $platformStat->platform),
		        };
		        $icon = match ($platformStat->platform) {
		            'instagram' => 'instagram',
		            'tiktok' => 'tiktok',
		            'ugc' => 'camera',
		            default => 'group',
		        };
		        return [
		            'icon' => $icon,
		            'label' => $platformLabel,
		            'followers' => $followersLabel,
		        ];
		    })
		    ->values();

		if ($platformBadges->isEmpty()) {
		    $platformBadges = collect([
		        ['icon' => 'tiktok', 'label' => 'TikTok', 'followers' => '81.5K'],
		        ['icon' => 'instagram', 'label' => 'Instagram', 'followers' => '2.3M'],
		    ]);
		}

		$bioText = trim((string) ($creator->user->bio ?? ''));

		$packageCards = $packages
		    ->map(static function ($package): array {
		        $platformCategory = match ($package->platform) {
		            'instagram' => 'Instagram',
		            'tiktok' => 'TikTok',
		            'ugc' => 'UGC',
		            default => 'Others',
		        };
		        $icon = match ($package->platform) {
		            'instagram' => 'instagram',
		            'tiktok' => 'tiktok',
		            'ugc' => 'camera',
		            default => 'group',
		        };
		        $description = trim((string) ($package->description ?? ''));
		        return [
		            'id' => (int) $package->id,
		            'key' => 'package-' . (int) $package->id,
		            'name' => (string) $package->name,
		            'icon' => $icon,
		            'category' => $platformCategory,
		            'price' => strtoupper((string) $package->currency) . ' ' . number_format((float) $package->base_price, 2),
		            'description' =>
		                $description !== ''
		                    ? $description
		                    : 'This package includes ' .
		                        $package->name .
		                        '. The content will be created in collaboration with the brand, ensuring it aligns with creator style and audience.',
		        ];
		    })
		    ->values();

		$packageTabs = collect(['All'])
		    ->merge($packageCards->pluck('category')->unique()->values())
		    ->values();

		$initialPackageKey = $packageCards->first()['key'] ?? null;
	@endphp

	<section class="min-h-screen" x-data="creatorProfileData()">

		<main class="max-w-screen-2xl mx-auto">
			<!-- 1. TOP CATEGORIES & EDIT -->
			<div class="flex items-center justify-between mb-4">
				<div class="flex flex-wrap gap-2 text-xl font-semibold text-gray-800 dark:text-gray-300 tracking-tight">
					@foreach ($categoryNames as $categoryName)
						<span>{{ $categoryName }}@if (!$loop->last)
								,
							@endif </span>
					@endforeach
				</div>
				<div class="flex items-center gap-2">
					<button id="share-btn" type="button"
						class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-semibold text-purple-700 dark:text-purple-300 hover:bg-purple-50 dark:hover:bg-gray-800 transition"
						onclick="copyProfileUrl(event); return false;">
						<x-icons.share class="w-5 h-5" />
						Share
					</button>
					@auth
						@if (optional(Auth::user()->creator)->id === optional($creator)->id)
							<a href="{{ route('dashboard.creator.profile.edit', ['slug' => Auth::user()->slug]) }}"
								class="flex items-center gap-2 px-5 py-2 border border-gray-200 dark:border-gray-800 rounded-lg text-sm font-bold text-[#222] hover:bg-purple-50 transition active:scale-95">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
									<path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
								</svg>
								Edit
							</a>
						@endif
					@endauth
				</div>
			</div>
			@push('scripts')
				<script>
					function copyProfileUrl(e) {
						if (e) e.preventDefault();
						const url = window.location.href;
						if (navigator.clipboard) {
							navigator.clipboard.writeText(url).then(function() {
								showLinkCopied();
							}, function() {
								fallbackCopyTextToClipboard(url);
							});
						} else {
							fallbackCopyTextToClipboard(url);
						}
						return false;
					}

					function fallbackCopyTextToClipboard(text) {
						const textArea = document.createElement("textarea");
						textArea.value = text;
						document.body.appendChild(textArea);
						textArea.focus();
						textArea.select();
						try {
							document.execCommand('copy');
							showLinkCopied();
						} catch (err) {}
						document.body.removeChild(textArea);
					}

					function showLinkCopied() {
						if (window.toast) {
							window.toast.success('Link copied.');
						}
					}
				</script>
			@endpush

			<!-- 2. PORTRAIT IMAGE GRID & MOBILE SLIDER -->
			<div class="relative -mx-4 sm:-mx-6 lg:mx-0 mb-10 lg:mb-16" x-data="{
    activeImage: 1,
    total: {{ count($gridImages) }},
    handleScroll(e) {
        const width = e.target.offsetWidth;
        this.activeImage = Math.round(e.target.scrollLeft / width) + 1;
    }
}">

				<!-- Container: Flex on mobile (for scroll), Grid on desktop -->
				<div @scroll.debounce.100ms="handleScroll($event)"
					class="flex lg:grid lg:grid-cols-12 overflow-x-auto lg:overflow-x-visible snap-x snap-mandatory no-scrollbar h-[450px] lg:h-[600px] gap-0 lg:gap-4">

					@foreach ($gridImages as $index => $image)
						<!-- Removed 'hidden' class. min-w-full handles the mobile layout -->
						<div
							class="min-w-full lg:min-w-0 lg:col-span-4 snap-center relative overflow-hidden lg:rounded-xl border-gray-100 dark:border-gray-800">
							<img src="{{ $image }}"
								class="w-full h-full object-cover lg:hover:scale-105 transition-transform duration-700"
								alt="Creator showcase image {{ $index + 1 }}">
						</div>
					@endforeach
				</div>

				<!-- Show All Photos Button (Desktop Only) -->
				@if ($creator->portfolios->count() > 3)
					<div class="hidden lg:block absolute bottom-6 right-6 z-10">
						<a href="#portfolio-gallery"
							class="flex items-center gap-2 bg-white/90 backdrop-blur-md px-5 py-2.5 rounded-2xl text-sm font-bold text-gray-900 border border-gray-100 shadow-xl hover:bg-white transition active:scale-95">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2">
								<path
									d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
							</svg>
							Show All Photos
						</a>
					</div>
				@endif

				<!-- Dynamic Mobile Image Counter Badge (1/3) -->
				<div
					class="lg:hidden absolute bottom-4 right-4 bg-black/60 backdrop-blur-md text-white px-3 py-1 rounded-md text-[10px] font-medium tracking-widest z-20 pointer-events-none">
					<span x-text="activeImage"></span> / <span x-text="total"></span>
				</div>
			</div>

			<style>
				/* Critical for clean mobile experience */
				.no-scrollbar::-webkit-scrollbar {
					display: none;
				}

				.no-scrollbar {
					-ms-overflow-style: none;
					scrollbar-width: none;
				}
			</style>

			<div class="flex flex-col lg:flex-row gap-4 lg:gap-16">
				<!-- LEFT COLUMN: CREATOR INFO -->
				<div class="flex-1 space-y-6">
					<!-- Profile Identity -->
					<div class="flex items-center gap-6">
						<img src="{{ $profileImageUrl }}" class="w-24 h-24 rounded-full border-2 border-gray-50 shadow-md object-cover"
							alt="{{ $displayName }}">
						<div>
							<h1 class="text-2xl font-bold text-gray-800 dark:text-gray-300 tracking-tight leading-none mb-2">
								{{ $displayName }}
							</h1>
							<p class="text-sm text-gray-400 font-medium mb-4">{{ $locationText }}</p>
							<div class="flex gap-3">
								@foreach ($platformBadges as $platformBadge)
									<span
										class="px-2 md:px-4 py-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-full text-[10px] md:text-sm font-medium text-gray-500 flex items-center gap-2 shadow-sm">
										@if ($platformBadge['icon'] === 'instagram')
											<x-icons.instagram class="w-5 h-5 text-[#28303F] dark:text-white" />
										@elseif ($platformBadge['icon'] === 'tiktok')
											<x-icons.tiktok class="w-5 h-5 text-[#28303F] dark:text-white" />
										@elseif ($platformBadge['icon'] === 'camera')
											<x-icons.camera class="w-5 h-5 text-[#28303F] dark:text-white" />
										@else
											<x-icons.group class="w-5 h-5 text-[#28303F] dark:text-white" />
										@endif
										{{ $platformBadge['followers'] }} Followers
									</span>
								@endforeach
							</div>
						</div>
					</div>

					<!-- BADGES SYSTEM -->
					<div class="space-y-10">
						<div class="flex items-start gap-6 group">
							<div class="w-12 h-12 shrink-0 text-gray-300 transition-colors group-hover:text-purple-300">
								<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
									<path
										d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
								</svg>
							</div>
							<div>
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-lg font-bold text-gray-800 dark:text-gray-300">Top Creator</h3>
									<span
										class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-semibold uppercase tracking-widest border border-red-100">Not
										Earned</span>
								</div>
								<p class="text-sm font-normal text-gray-800 dark:text-gray-300 max-w-md">To earn this badge, you must complete
									multiple orders and have a high rating from brands.</p>
							</div>
						</div>
						<div class="flex items-start gap-6 group">
							<div class="w-12 h-12 shrink-0 text-gray-300 transition-colors group-hover:text-purple-300">
								<svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
									<path d="M13 10V3L4 14h7v7l9-11h-7z" />
								</svg>
							</div>
							<div>
								<div class="flex items-center gap-3 mb-2">
									<h3 class="text-lg font-bold text-gray-800 dark:text-gray-300">Responds Fast</h3>
									<span
										class="px-2.5 py-1 bg-red-50 text-red-600 rounded-lg text-[10px] font-semibold uppercase tracking-widest border border-red-100">Not
										Earned</span>
								</div>
								<p class="text-sm font-normal text-gray-800 dark:text-gray-300 max-w-md">To earn this badge, you must respond to
									requests within 12 hours.</p>
							</div>
						</div>
					</div>

					<!-- DESCRIPTION BIO -->
					<div class="text-gray-600 dark:text-gray-300 text-base max-w-4xl font-normal opacity-90">
						{{ $bioText }}
					</div>

					<!-- 3. NEW PACKAGES SECTION -->
					<div class="packages mt-8">
						<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 tracking-tight">Packages</h2>

						<!-- Tabs -->
						<div class="flex gap-8 border-b border-gray-100 dark:border-gray-800 mb-8">
							<template x-for="tabName in packageTabs" :key="tabName">
								<button @click="activeTab = tabName"
									:class="activeTab === tabName ? 'border-b-2 border-black dark:border-white text-black dark:text-white' :
									    'text-gray-400 hover:text-gray-600'"
									class="pb-4 text-sm font-medium transition-all" x-text="tabName">
								</button>
							</template>
						</div>

						<!-- List of Packages -->
						<div class="space-y-3">
							<template x-for="p in filteredPackages" :key="p.key">
								<div @click="selectPackage(p.key)"
									:class="selectedPackageKey === p.key ? 'border-gray-500 bg-purple-50/20 dark:bg-gray-900/10 shadow-sm' :
									    'border-gray-100 dark:border-gray-800 hover:border-gray-200'"
									class="flex items-center justify-between p-5 border rounded-2xl cursor-pointer bg-white dark:bg-transparent transition-all group">
									<div class="flex items-center gap-5">
										<div class="text-gray-800 dark:text-white">
											<template x-if="p.icon === 'instagram'">
												<x-icons.instagram class="w-6 h-6" />
											</template>
											<template x-if="p.icon === 'tiktok'">
												<x-icons.tiktok class="w-6 h-6" />
											</template>
											<template x-if="p.icon === 'camera'">
												<x-icons.camera class="w-6 h-6" />
											</template>
											<template x-if="p.icon === 'group'">
												<x-icons.group class="w-6 h-6" />
											</template>
										</div>
										<span class="text-sm md:text-base font-bold text-gray-800 dark:text-gray-200" x-text="p.name"></span>
									</div>
									<div class="flex items-center gap-6">
										<span class="text-sm md:text-lg font-bold text-gray-900 dark:text-white" x-text="p.price"></span>
										<div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
											:class="selectedPackageKey === p.key ? 'border-black bg-black dark:border-white dark:bg-white' : 'border-gray-200'">
											<div x-show="selectedPackageKey === p.key" class="w-2 h-2 rounded-full"
												:class="selectedPackageKey === p.key ? 'bg-white dark:bg-black' : ''"></div>
										</div>
									</div>
								</div>
							</template>
							<template x-if="filteredPackages.length === 0">
								<div
									class="p-5 border border-gray-100 dark:border-gray-800 rounded-2xl bg-white dark:bg-transparent text-sm text-gray-500 dark:text-gray-400">
									No active packages available for this creator.
								</div>
							</template>
						</div>
					</div>
				</div>

				<!-- RIGHT COLUMN: PRICING CARD (Synced) -->
				<div class="w-auto lg:w-2/5">
					<div
						class="sticky top-24 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-[2.5rem] p-10 shadow-2xl shadow-purple-900/5">
						<div class="flex items-center justify-between mb-4">
							<span class="text-4xl font-bold text-gray-800 dark:text-gray-300 tracking-tighter" x-text="price"></span>
						</div>

						<!-- CUSTOM DYNAMIC DROPDOWN -->
						<div class="relative mb-6">
							<button @click="openDropdown = !openDropdown"
								class="w-full flex items-center justify-between px-5 py-4 border-2 border-purple-100 dark:border-gray-700 rounded-2xl text-base font-bold text-gray-800 dark:text-gray-300 bg-white dark:bg-transparent transition-all hover:border-purple-200">
								<div class="flex items-center gap-4">
									<span class="text-gray-800 dark:text-purple-400">
										<template x-if="selectedPackage && selectedPackage.icon === 'instagram'">
											<x-icons.instagram class="w-5 h-5" />
										</template>
										<template x-if="selectedPackage && selectedPackage.icon === 'tiktok'">
											<x-icons.tiktok class="w-5 h-5" />
										</template>
										<template x-if="selectedPackage && selectedPackage.icon === 'camera'">
											<x-icons.camera class="w-5 h-5" />
										</template>
										<template x-if="selectedPackage && selectedPackage.icon === 'group'">
											<x-icons.group class="w-5 h-5" />
										</template>
									</span>
									<span x-text="selectedPackageName"></span>
								</div>
								<svg class="w-5 h-5 transition-transform" :class="openDropdown ? 'rotate-180 text-gray-800' : 'text-gray-400'"
									fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
									<path d="M19 9l-7 7-7-7" />
								</svg>
							</button>
							<div x-show="openDropdown" x-cloak @click.away="openDropdown = false"
								class="absolute top-full left-0 w-full mt-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-2xl z-50 overflow-y-auto max-h-96">
								<template x-for="p in packages" :key="p.key">
									<div @click="selectPackage(p.key)" class="px-6 py-4 cursor-pointer text-sm font-medium transition-colors"
										:class="selectedPackageKey === p.key ? 'bg-gray-100 text-gray-800' :
										    'text-gray-800 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800/40'"
										x-text="p.name">
									</div>
								</template>
							</div>
						</div>

						<!-- Show only Brand Users -->
						<div class="mb-4">
							<h3 class="text-lg font-bold text-gray-800 dark:text-gray-300 mb-2">Package Details</h3>
							<p class="text-sm text-gray-600 dark:text-gray-400 max-w-md" x-text="selectedPackageDescription"></p>
						</div>

						<!-- Show only Brand Users -->
						<div class="flex gap-3 w-full">
							<form @submit.prevent="handleAddToCart()" method="POST" class="flex-1">
								<button type="submit"
									class="bg-[#1A1A1A] hover:bg-purple-400 flex w-full items-center justify-center rounded-xl px-4 py-4 text-sm font-bold text-white transition active:scale-[0.98]">
									Add to Cart
								</button>
							</form>

							<div class="flex items-center px-3 text-gray-500 dark:text-gray-400 font-semibold">
								or
							</div>

							<button @click="negotiatePackage()" type="button"
								class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 flex items-center justify-center rounded-xl px-4 py-4 text-sm font-bold text-gray-800 dark:text-white transition active:scale-[0.98]">
								Negotiate a Package
							</button>
						</div>
					</div>
				</div>
			</div>
		</main>

		<!-- PORTFOLIO SECTION (now inside the main Alpine component) -->
		@if ($creator->portfolios->count() > 0)
			<section id="portfolio-gallery" class="py-20 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto scroll-mt-24">
				<div class="mb-12">
					<h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Portfolio</h2>
					<p class="text-gray-600 dark:text-gray-400">Check out the latest work exhibited by {{ $displayName }}</p>
				</div>

				<!-- Portfolio Grid -->
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					@foreach ($creator->portfolios as $item)
						<button @click="openGallery({{ $item->id }})" type="button"
							class="portfolio-item group relative rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-all duration-300 cursor-pointer w-full text-left bg-transparent p-0">
							@if ($item->media_type === 'image')
								<img src="{{ \App\Helpers\ImageHelper::url($item->file_path) }}" alt="{{ $item->title }}"
									class="w-full h-80 object-cover group-hover:scale-105 transition-transform duration-500">
							@else
								<div
									class="w-full h-80 bg-gray-200 dark:bg-gray-700 flex items-center justify-center group-hover:bg-gray-300 transition-colors">
									<svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
										<path d="M8 5v14l11-7z" />
									</svg>
								</div>
							@endif
							<!-- Overlay -->
							<div
								class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center">
								<div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
									<svg class="w-16 h-16 text-white" fill="currentColor" viewBox="0 0 24 24">
										<path d="M8 5v14l11-7z" />
									</svg>
								</div>
							</div>
							<!-- Title Badge -->
							@if ($item->title)
								<div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
									<p class="text-white font-semibold text-sm">{{ Str::limit($item->title, 40) }}</p>
								</div>
							@endif
						</button>
					@endforeach
				</div>

				<!-- Lightbox Modal Overlay -->
				<div x-show="showGallery" x-cloak @click.outside="closeGallery()" @keydown.escape.window="closeGallery()"
					@keydown.arrow-right.window="nextGalleryItem()" @keydown.arrow-left.window="prevGalleryItem()"
					@touchstart="getTouchPosition($event)" @touchmove.prevent @touchend="handleTouchEnd($event)"
					class="fixed inset-0 z-50 bg-black/95 flex items-center justify-center p-4" role="dialog" aria-modal="true">

					<!-- Loading state -->
					<template x-if="!currentGalleryItem">
						<div class="flex items-center justify-center">
							<div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white"></div>
						</div>
					</template>

					<!-- Modal Content -->
					<template x-if="currentGalleryItem">
						<div class="relative w-full h-full flex flex-col items-center justify-center" @click.stop>
							<!-- Media Display -->
							<div class="flex-1 flex items-center justify-center w-full max-w-5xl">
								<!-- Image -->
								<template x-if="currentGalleryItem.type === 'image'">
									<img :src="currentGalleryItem.url" :alt="currentGalleryItem.title"
										class="max-w-full max-h-[80vh] object-contain rounded-lg">
								</template>
								<!-- Video -->
								<template x-if="currentGalleryItem.type === 'video'">
									<video :src="currentGalleryItem.url" controls autoplay
										class="max-w-full max-h-[80vh] object-contain rounded-lg" controlsList="nodownload">
									</video>
								</template>
							</div>

							<!-- Bottom Info Bar -->
							<div class="mt-6 w-full max-w-5xl flex items-center justify-between">
								<!-- Left: Title and Description -->
								<div class="flex-1">
									<h3 class="text-white font-bold text-lg" x-text="currentGalleryItem.title"></h3>
									<p class="text-gray-300 text-sm mt-1" x-text="currentGalleryItem.description"></p>
								</div>
								<!-- Right: Counter -->
								<div class="text-white text-sm px-4">
									<span x-text="`${currentGalleryIndex + 1} / ${portfolioItems.length}`"></span>
								</div>
							</div>

							<!-- Navigation Controls -->
							<div class="mt-6 flex items-center gap-4">
								<!-- Previous Button -->
								<button @click="prevGalleryItem()" type="button"
									class="p-3 rounded-full bg-white/10 hover:bg-white/20 transition text-white"
									:disabled="portfolioItems.length <= 1">
									<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
									</svg>
								</button>
								<!-- Next Button -->
								<button @click="nextGalleryItem()" type="button"
									class="p-3 rounded-full bg-white/10 hover:bg-white/20 transition text-white"
									:disabled="portfolioItems.length <= 1">
									<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
									</svg>
								</button>
							</div>

							<!-- Close Button -->
							<button @click="closeGallery()" type="button"
								class="absolute top-4 right-4 p-3 rounded-full bg-white/10 hover:bg-white/20 transition text-white"
								aria-label="Close gallery">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>

							<!-- Touch swipe hint (mobile) -->
							<div class="absolute bottom-4 left-4 text-gray-400 text-xs md:hidden">
								Swipe to navigate
							</div>
						</div>
					</template>
				</div>
			</section>
		@endif
	</section>

	@push('scripts')
		<script>
			function creatorProfileData() {
				return {
					openDropdown: false,
					selectedPackageKey: @js($initialPackageKey),
					activeTab: 'All',
					packages: @js($packageCards),
					packageTabs: @js($packageTabs),
					isAuthenticated: @json(auth()->check()),
					userType: @json(auth()->check() ? auth()->user()->user_type : null),
					loginUrl: @js(route('login')),
					cartUrl: @js(route('cart.index')),
					conversationsUrl: @js(route('dashboard.conversations.index')),
					creatorId: @js($creator->id),
					startNegotiationUrl: @js(route('conversations.start-negotiation', ['creator' => $creator->id])),
					portfolioItems: @js(
    $creator->portfolios
        ->map(
            fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'url' => \App\Helpers\ImageHelper::url($p->file_path),
                'type' => $p->media_type,
            ],
        )
        ->values()
        ->toArray(),
),
					showGallery: false,
					currentGalleryIndex: 0,

					openGallery(itemId) {
						this.currentGalleryIndex = this.portfolioItems.findIndex(p => p.id === itemId);
						this.showGallery = true;
						document.body.style.overflow = 'hidden';
					},

					closeGallery() {
						this.showGallery = false;
						document.body.style.overflow = 'auto';
					},

					nextGalleryItem() {
						this.currentGalleryIndex = (this.currentGalleryIndex + 1) % this.portfolioItems.length;
					},

					prevGalleryItem() {
						this.currentGalleryIndex = (this.currentGalleryIndex - 1 + this.portfolioItems.length) % this
							.portfolioItems.length;
					},

					get currentGalleryItem() {
						return this.portfolioItems[this.currentGalleryIndex] || null;
					},

					get filteredPackages() {
						if (this.activeTab === 'All') return this.packages;
						return this.packages.filter((p) => p.category === this.activeTab);
					},

					get selectedPackage() {
						return this.packages.find((p) => p.key === this.selectedPackageKey) || this.packages[0] || null;
					},

					get selectedPackageName() {
						return this.selectedPackage ? this.selectedPackage.name : 'No package available';
					},

					get price() {
						return this.selectedPackage ? this.selectedPackage.price : '$0.00';
					},

					get selectedPackageDescription() {
						return this.selectedPackage ? this.selectedPackage.description :
							'Package details are not available right now.';
					},

					selectPackage(key) {
						this.selectedPackageKey = key;
						this.openDropdown = false;
					},

					touchStartX: 0,
					touchEndX: 0,

					getTouchPosition(e) {
						this.touchStartX = e.changedTouches[0].clientX;
					},

					handleTouchEnd(e) {
						this.touchEndX = e.changedTouches[0].clientX;
						this.handleSwipe();
					},

					handleSwipe() {
						const swipeThreshold = 50;
						const diff = this.touchStartX - this.touchEndX;
						if (Math.abs(diff) > swipeThreshold) {
							if (diff > 0) {
								this.nextGalleryItem();
							} else {
								this.prevGalleryItem();
							}
						}
					},

					async handleAddToCart() {
						if (!this.selectedPackage) {
							return;
						}

						const added = await addToCart(this.selectedPackage.id);

						if (added && this.isAuthenticated && this.userType === 'brand') {
							window.location.href = this.cartUrl;
						}
					},

					negotiatePackage() {
						if (!this.isAuthenticated) {
							window.location.href = this.startNegotiationUrl;
						} else if (this.userType === 'brand') {
							window.location.href = this.startNegotiationUrl;
						} else {
							alert('Only brands can negotiate packages.');
						}
					}
				};
			}
		</script>
	@endpush
@endsection
