@extends('frontend.layouts.app')

@section('content')
	@php
		$displayName =
		    trim((string) ($influencer->display_name ?? '')) !== ''
		        ? (string) $influencer->display_name
		        : (string) ($influencer->user->name ?? 'Influencer');
		$locationParts = array_values(
		    array_filter([
		        trim((string) ($influencer->user->address_line ?? '')),
		        trim((string) ($influencer->user->city ?? '')),
		        trim((string) ($influencer->user->postal_code ?? '')),
		        trim((string) ($influencer->user->country ?? '')),
		    ]),
		);
		$locationText = $locationParts !== [] ? implode(', ', $locationParts) : '';

		$categoryNames = $influencer->categories->pluck('name')->filter()->take(5)->values();
		if ($categoryNames->isEmpty()) {
		    $categoryNames = collect(['Tech', 'Tesla', 'Health & Fitness', 'Car', 'Pet']);
		}

		// Get the latest 3 portfolio images for the top grid display
		$gridImages = $portfolioTopImages
		    ->where('media_type', 'image')
		    ->pluck('url')
		    ->values();

		// Fill with defaults if not enough images
		while ($gridImages->count() < 3) {
		    $gridImages->push(asset('default.webp'));
		}

		$profileImageUrl = image_url($influencer->user?->profile_image_path);
		$socialLinks = $influencer->socialLinks;

		$platformBadges = $influencer->platformStats
		    ->map(static function ($platformStat): array {
		        $platformKey = \Illuminate\Support\Str::of((string) ($platformStat->platform ?? ''))
		            ->trim()
		            ->lower()
		            ->value();
		        $followers = (int) ($platformStat->follower_count ?? 0);
		        $followersLabel =
		            $followers >= 1000000
		                ? number_format($followers / 1000000, 1) . 'M'
		                : ($followers >= 1000
		                    ? number_format($followers / 1000, 1) . 'K'
		                    : (string) $followers);
		        $platformLabel = match ($platformKey) {
		            'ugc' => 'UGC',
		            'x' => 'X',
		            'tiktok' => 'TikTok',
		            'youtube' => 'YouTube',
		            'linkedin' => 'LinkedIn',
		            default => \Illuminate\Support\Str::headline((string) $platformKey),
		        };
		        $icon = match ($platformKey) {
		            'facebook' => 'facebook',
		            'instagram' => 'instagram',
		            'tiktok' => 'tiktok',
		            'youtube' => 'youtube',
		            'linkedin' => 'linkedin',
		            'x' => 'x',
		            'ugc' => 'camera',
		            default => 'group',
		        };

		        $url = trim((string) ($platformStat->profile_url ?? ''));

		        return [
		            'platform' => $platformKey,
		            'icon' => $icon,
		            'label' => $platformLabel,
		            'followers_count' => $followers,
		            'followers' => $followersLabel,
		            'url' => $url !== '' ? $url : null,
		        ];
		    })
		    ->values();

		if ($platformBadges->isNotEmpty() && $socialLinks !== null) {
		    $socialLinksByPlatform = [
		        'instagram' => trim((string) ($socialLinks->instagram_url ?? '')),
		        'tiktok' => trim((string) ($socialLinks->tiktok_url ?? '')),
		        'facebook' => trim((string) ($socialLinks->facebook_url ?? '')),
		        'x' => trim((string) ($socialLinks->x_url ?? '')),
		        'youtube' => trim((string) ($socialLinks->youtube_url ?? '')),
		        'linkedin' => trim((string) ($socialLinks->linkedin_url ?? '')),
		    ];

		    $platformBadges = $platformBadges
		        ->map(static function (array $badge) use ($socialLinksByPlatform): array {
		            if (($badge['url'] ?? null) !== null) {
		                return $badge;
		            }

		            $platformKey = (string) ($badge['platform'] ?? '');

		            $fallbackUrl = $platformKey !== '' ? ($socialLinksByPlatform[$platformKey] ?? '') : '';

		            if ($fallbackUrl === '') {
		                return $badge;
		            }

		            $badge['url'] = $fallbackUrl;

		            return $badge;
		        })
		        ->values();
		}

		if ($platformBadges->isEmpty()) {
		    $platformBadges = collect([
		        ['platform' => 'tiktok', 'icon' => 'tiktok', 'label' => 'TikTok', 'followers_count' => 81500, 'followers' => '81.5K', 'url' => null],
		        ['platform' => 'instagram', 'icon' => 'instagram', 'label' => 'Instagram', 'followers_count' => 2300000, 'followers' => '2.3M', 'url' => null],
		    ]);
		}

		$platformBadges = $platformBadges
		    ->sortByDesc(fn(array $badge): int => (int) ($badge['followers_count'] ?? 0))
		    ->take(4)
		    ->values();

		$bioText = trim((string) ($influencer->user->bio ?? ''));

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
		                        '. The content will be created in collaboration with the brand, ensuring it aligns with influencer style and audience.',
		        ];
		    })
		    ->values();

		$packageTabs = collect(['All'])
		    ->merge($packageCards->pluck('category')->unique()->values())
		    ->values();

		$initialPackageKey = $packageCards->first()['key'] ?? null;
		$reviewsTotal = $reviewsTotalCount;
		$avgRating = $reviewsAverageRating;
		$ratingLabel = $avgRating !== null ? number_format($avgRating, 1) : '0.0';

		// Get earned badges
		$earnedBadges = $influencer->badges->keyBy('code');
		$hasTopInfluencer = $earnedBadges->has('top_influencer');
		$hasResponsesFast = $earnedBadges->has('responds_fast');
	@endphp

	<section class="min-h-screen" x-data="influencerProfileData()">

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
					<button type="button"
						class="wishlist-btn flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-semibold text-purple-700 dark:text-purple-300 hover:bg-purple-50 dark:hover:bg-gray-800 transition"
						data-wishlist-trigger
						data-wishlist-active="false"
						data-influencer-id="{{ $influencer->id }}"
						data-wishlist-image="{{ $profileImageUrl }}"
						aria-label="Add {{ $displayName }} to wishlist"
						aria-pressed="false">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 wishlist-heart-icon fill-none stroke-current stroke-[2px]" viewBox="0 0 24 24">
							<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
						</svg>
						Save
					</button>

					<button id="share-btn" type="button"
						class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-semibold text-purple-700 dark:text-purple-300 hover:bg-purple-50 dark:hover:bg-gray-800 transition"
						onclick="copyProfileUrl(event); return false;">
						<x-icons.share class="w-5 h-5" />
						Share
					</button>
					@auth
						@if (optional(Auth::user()->influencer)->id === optional($influencer)->id)
							<a href="{{ route('influencer.profile.edit', ['slug' => Auth::user()->slug]) }}"
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
						textArea.style.position = "fixed";
						textArea.style.top = "-9999px";
						textArea.style.left = "-9999px";
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
					class="flex lg:grid lg:grid-cols-12 overflow-x-auto lg:overflow-x-visible snap-x snap-mandatory no-scrollbar h-112.5 lg:h-150 gap-0 lg:gap-4">

					@foreach ($gridImages as $index => $image)
						<!-- Removed 'hidden' class. min-w-full handles the mobile layout -->
						<div
							class="min-w-full lg:min-w-0 lg:col-span-4 snap-center relative overflow-hidden lg:rounded-xl border-gray-100 dark:border-gray-800">
							<img src="{{ $image }}"
								class="w-full h-full object-cover lg:hover:scale-105 transition-transform duration-700"
								alt="Influencer showcase image {{ $index + 1 }}">
						</div>
					@endforeach
				</div>

				<!-- Show All Photos Button (Desktop Only) -->
				@if (($portfolioRemainingMedia ?? collect())->isNotEmpty())
					<div class="hidden lg:block absolute bottom-6 right-6 z-10">
						<button type="button" @click="openGallery()"
							class="flex items-center gap-2 bg-white/90 backdrop-blur-md px-5 py-2.5 rounded-2xl text-sm font-bold text-gray-900 border border-gray-100 shadow-xl hover:bg-white transition active:scale-95">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
								stroke="currentColor" stroke-width="2">
								<path
									d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
							</svg>
							Show All Photos
						</button>
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

			<div class="flex flex-col lg:flex-row gap-4 lg:gap-8">
				<!-- LEFT COLUMN: INFLUENCER INFO -->
				<div class="flex-1 space-y-6">
					<!-- Profile Identity -->
					<div class="flex items-center gap-6">
						<img src="{{ $profileImageUrl }}" class="w-24 h-24 rounded-full border-2 border-gray-50 shadow-md object-cover"
							alt="{{ $displayName }}">
						<div>
							<div class="mb-1 flex flex-wrap items-center gap-2">
								<h1 class="text-2xl font-bold text-gray-800 dark:text-gray-300 tracking-tight leading-none">
									{{ $displayName }}
								</h1>
								<span class="inline-flex items-center gap-1 text-base font-semibold text-gray-800 dark:text-gray-200">
									<x-icons.star class="h-4 w-4 text-amber-400" />
									{{ $ratingLabel }}
									<span class="text-gray-400">·</span>
									<a href="#reviews-holder"
										class="underline decoration-gray-400 underline-offset-2 hover:text-gray-900 dark:hover:text-white">{{ number_format($reviewsTotal) }}
										Reviews</a>
								</span>
							</div>
							<p class="text-sm text-gray-500 font-medium mb-4">{{ $locationText }}</p>
							<div class="flex gap-3">
								@foreach ($platformBadges as $platformBadge)
									@if (!empty($platformBadge['url']))
										<a href="{{ $platformBadge['url'] }}" target="_blank" rel="noopener noreferrer"
											class="px-2 md:px-4 py-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-full text-[10px] md:text-sm font-medium text-gray-500 flex items-center gap-2 shadow-sm hover:border-gray-300 dark:hover:border-gray-600 transition"
											title="Open {{ $platformBadge['label'] }} profile">
											@if ($platformBadge['icon'] === 'facebook')
												<x-icons.facebook class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'instagram')
												<x-icons.instagram class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'tiktok')
												<x-icons.tiktok class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'youtube')
												<x-icons.youtube class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'linkedin')
												<x-icons.linkedin class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'x')
												<x-icons.x class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'camera')
												<x-icons.camera class="w-5 h-5 text-[#28303F] dark:text-white" />
											@else
												<x-icons.group class="w-5 h-5 text-[#28303F] dark:text-white" />
											@endif
											{{ $platformBadge['followers'] }} Followers
										</a>
									@else
										<span
											class="px-2 md:px-4 py-1.5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-full text-[10px] md:text-sm font-medium text-gray-500 flex items-center gap-2 shadow-sm">
											@if ($platformBadge['icon'] === 'facebook')
												<x-icons.facebook class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'instagram')
												<x-icons.instagram class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'tiktok')
												<x-icons.tiktok class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'youtube')
												<x-icons.youtube class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'linkedin')
												<x-icons.linkedin class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'x')
												<x-icons.x class="w-5 h-5 text-[#28303F] dark:text-white" />
											@elseif ($platformBadge['icon'] === 'camera')
												<x-icons.camera class="w-5 h-5 text-[#28303F] dark:text-white" />
											@else
												<x-icons.group class="w-5 h-5 text-[#28303F] dark:text-white" />
											@endif
											{{ $platformBadge['followers'] }} Followers
										</span>
									@endif
								@endforeach
							</div>
						</div>
					</div>



					<!-- BADGES SYSTEM -->
					<div class="space-y-3">
						<div class="flex items-start gap-4 group">
							<div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center transition-colors"
								:class="@js($hasTopInfluencer) ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-400'">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
									<path
										d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
								</svg>
							</div>
							<div class="flex-1">
								<div class="flex items-center gap-2 mb-1">
									<h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Top Influencer</h3>
									@if ($hasTopInfluencer)
										<span
											class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full text-[9px] font-bold uppercase tracking-widest">Earned</span>
									@else
										<span
											class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-[9px] font-bold uppercase tracking-widest">Not
											Earned</span>
									@endif
								</div>
								<p class="text-xs text-gray-600 dark:text-gray-400">Complete multiple orders with high brand ratings</p>
							</div>
						</div>

						<div class="flex items-start gap-4 group">
							<div class="w-10 h-10 shrink-0 rounded-lg flex items-center justify-center transition-colors"
								:class="@js($hasResponsesFast) ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400'">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
									<path d="M13 10V3L4 14h7v7l9-11h-7z" />
								</svg>
							</div>
							<div class="flex-1">
								<div class="flex items-center gap-2 mb-1">
									<h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Responds Fast</h3>
									@if ($hasResponsesFast)
										<span
											class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[9px] font-bold uppercase tracking-widest">Earned</span>
									@else
										<span
											class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-[9px] font-bold uppercase tracking-widest">Not
											Earned</span>
									@endif
								</div>
								<p class="text-xs text-gray-600 dark:text-gray-400">Consistently respond to requests within 12 hours</p>
							</div>
						</div>
					</div>

					<!-- DESCRIPTION BIO -->
					<div class="text-gray-600 dark:text-gray-300 text-base max-w-4xl font-normal opacity-90">
						@if ($bioText !== '')
							<p class="leading-relaxed">{{ $bioText }}</p>
						@else
							<p class="leading-relaxed">No bio available.</p>
						@endif
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
									No active packages available for this influencer.
								</div>
							</template>
						</div>
					</div>
				</div>

				<!-- RIGHT COLUMN: PRICING CARD (Synced) -->
				<div class="w-auto lg:w-2/5">
					<div
						class="sticky top-24 rounded-2xl border border-gray-300 bg-white p-4 shadow-md dark:border-gray-700 dark:bg-gray-900">
						<div class="mb-4">
							<span class="block text-4xl font-bold text-gray-900 dark:text-white" x-text="price"></span>
						</div>

						<!-- CUSTOM DYNAMIC DROPDOWN -->
						<div class="relative mb-4">
							<button @click="openDropdown = !openDropdown"
								class="w-full flex items-center justify-between rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-900 transition hover:border-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">
								<div class="flex items-center gap-4">
									<span class="text-gray-700 dark:text-gray-200">
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
								class="absolute top-full left-0 z-50 mt-2 max-h-96 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-xl dark:border-gray-700 dark:bg-gray-800">

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
							<p class="text-base text-gray-500 dark:text-gray-400" x-text="selectedPackageDescription"></p>
						</div>

						<div class="w-full">
							<form @submit.prevent="handleAddToCart()" method="POST">
								<button type="submit"
									class="flex w-full items-center justify-center rounded-xl bg-linear-to-r from-rose-400 to-fuchsia-500 px-6 py-3.5 text-base font-bold text-white transition hover:from-rose-500 hover:to-fuchsia-600 active:scale-[0.98] shadow-lg hover:shadow-xl">
									Add to Cart
								</button>
							</form>

							<div class="my-4 flex items-center gap-3 text-gray-400 dark:text-gray-600">
								<div class="h-px flex-1 bg-gray-300 dark:bg-gray-700"></div>
								<span class="text-sm font-semibold">or</span>
								<div class="h-px flex-1 bg-gray-300 dark:bg-gray-700"></div>
							</div>

							<button @click="negotiatePackage()" type="button"
								class="w-full text-center text-base font-bold text-gray-800 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white transition">
								Negotiate a Package
							</button>


						</div>
					</div>
				</div>
			</div>
		</main>

		<!-- PORTFOLIO SECTION (remaining media) -->
		@if (($portfolioRemainingMedia ?? collect())->isNotEmpty())
			<section class="py-4 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
				<div class="mb-12 flex items-center justify-between gap-4">
					<div>
						<h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Portfolio</h2>
						<p class="text-gray-600 dark:text-gray-400">Showing {{ $portfolioRemainingMedia->count() }} of
							{{ number_format($portfolioTotalCount) }} media items</p>
					</div>
					<button type="button" @click="openGallery()"
						class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-900 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800">
						See all
					</button>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
					@foreach ($portfolioRemainingMedia as $item)
						<div class="portfolio-item group relative overflow-hidden rounded-2xl bg-black">
							@if ($item['media_type'] === 'image')
								<img src="{{ $item['url'] }}" alt="{{ $item['title'] !== '' ? $item['title'] : 'Portfolio media' }}"
									class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-105">
							@else
								<div class="relative">
									<video src="{{ $item['url'] }}" data-video-id="media-{{ $item['id'] }}" @play="pauseOtherVideos($event)"
										@pause="updateVideoState($event)" poster="{{ $item['poster_url'] ?? '' }}" muted playsinline preload="metadata"
										class="w-full h-80 object-cover"></video>
									<button type="button" @click="toggleVideoPlayback($event)" data-video-toggle
										class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20">
										<div class="flex h-14 w-14 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm">
											<svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
												<path d="M8 5v14l11-7z" />
											</svg>
										</div>
									</button>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			</section>
		@endif

		<div x-show="showGallery" x-cloak @click.self="closeGallery()" @keydown.escape.window="closeGallery()"
			class="fixed inset-0 z-50 bg-black/90 p-4 sm:p-6" role="dialog" aria-modal="true">
			<div x-ref="galleryModal" class="mx-auto flex h-full w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
				<div class="flex justify-end p-4 sm:p-6 pb-0">
					<button @click="closeGallery()" type="button" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Close gallery">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>

				<div class="flex-1 overflow-y-auto p-4 sm:p-6">
					<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
						<template x-for="item in mediaGalleryItems" :key="item.id">
							<div class="group overflow-hidden rounded-2xl bg-black">
								<template x-if="item.media_type === 'image'">
									<img :src="item.url" :alt="item.title || 'Portfolio media'" class="h-60 w-full object-cover">
								</template>
								<template x-if="item.media_type === 'video'">
									<div class="relative">
										<video :src="item.url" :poster="item.poster_url || ''" :data-video-id="'gallery-' + item.id" muted playsinline preload="metadata"
											@play="pauseOtherVideos($event)" @pause="updateVideoState($event)" class="h-60 w-full object-cover"></video>
										<button type="button" @click="toggleVideoPlayback($event)" data-video-toggle
											class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20">
											<div class="flex h-14 w-14 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm">
												<svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
													<path d="M8 5v14l11-7z" />
												</svg>
											</div>
										</button>
									</div>
								</template>
							</div>
						</template>
					</div>
				</div>
			</div>
		</div>

		<!-- REVIEWS SECTION -->
		<section id="reviews-holder" class="py-2 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto scroll-mt-24">
			<div class="mb-12">
				<div class="flex items-center justify-between mb-6">
					<div>
						<h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($reviewsTotal) }} Reviews</h2>
						<div class="flex items-center gap-2 mt-2">
							<div class="flex items-center gap-1">
								@for ($star = 1; $star <= 5; $star++)
									<x-icons.star
										class="h-5 w-5 {{ $avgRating !== null && $star <= floor($avgRating) ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600' }}" />
								@endfor
							</div>
							<span class="text-2xl font-bold text-amber-400">{{ $ratingLabel }}</span>
						</div>
					</div>
				</div>

				<!-- Rating Categories -->
				<div class="grid grid-cols-3 gap-4 mb-8">
					<div class="flex items-start gap-3">
						<svg class="h-6 w-6 text-gray-800 dark:text-gray-200 shrink-0 mt-1" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
								d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
						</svg>
						<div>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ratingLabel }}</p>
							<p class="text-sm text-gray-600 dark:text-gray-400">Communication</p>
						</div>
					</div>
					<div class="flex items-start gap-3">
						<svg class="h-6 w-6 text-gray-800 dark:text-gray-200 shrink-0 mt-1" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
								d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<div>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ratingLabel }}</p>
							<p class="text-sm text-gray-600 dark:text-gray-400">Timeliness</p>
						</div>
					</div>
					<div class="flex items-start gap-3">
						<svg class="h-6 w-6 text-gray-800 dark:text-gray-200 shrink-0 mt-1" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
								d="M9 12l2 2 4-4m5 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
						<div>
							<p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ratingLabel }}</p>
							<p class="text-sm text-gray-600 dark:text-gray-400">Satisfaction</p>
						</div>
					</div>
				</div>
			</div>

			@if ($reviewsPage->count() > 0)
				<div class="border-t border-gray-200 dark:border-gray-800 pt-8 space-y-6">
					@foreach ($reviewsPage as $review)
						@php
							// Determine if this is a campaign or package review
							$isCampaignReview = !empty($review->sub_order_id);

							if ($isCampaignReview) {
							    $reviewTitle = $review->subOrder?->order?->campaign?->title ?? 'Campaign';
							    $orderDetail = $review->subOrder?->order?->order_number ?? 'Order';
							} else {
							    $reviewTitle = $review->orderItem?->package?->name ?: $review->orderItem?->title;
							    $orderDetail = $review->orderItem?->order?->order_number ?? 'Order';
							}
						@endphp
						<div class="pb-6 border-b border-gray-100 dark:border-gray-800 last:border-b-0">
							<!-- Review Header -->
							<div class="flex items-center justify-between mb-3">
								<div class="flex items-center gap-3">
									<div
										class="flex h-10 w-10 items-center justify-center rounded-full bg-linear-to-br from-purple-100 to-pink-100 text-sm font-bold text-gray-700 dark:from-purple-900 dark:to-pink-900 dark:text-gray-100">
										{{ \Illuminate\Support\Str::substr($review->brand?->brand_name ?? 'B', 0, 1) }}
									</div>
									<div>
										<p class="font-semibold text-gray-900 dark:text-white text-sm">From
											{{ $review->brand?->brand_name ?? 'Brand' }}</p>
										@if ($reviewTitle)
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ $isCampaignReview ? 'Campaign' : 'Task' }}:
												{{ $reviewTitle }}</p>
										@endif
										<p class="text-xs text-gray-500 dark:text-gray-400">Order #{{ $orderDetail }}</p>
									</div>
								</div>
								<div class="text-right">
									<div class="flex items-center gap-1 mb-1 justify-end">
										@for ($i = 1; $i <= 5; $i++)
											<span class="text-lg">{{ $i <= $review->rating ? '★' : '☆' }}</span>
										@endfor
									</div>
									<p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($review->created_at)->format('M Y') }}</p>
								</div>
							</div>

							<!-- Review Comment -->
							@if ($review->comment)
								<p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed">{{ $review->comment }}</p>
							@endif
						</div>
					@endforeach
				</div>

				@if ($reviewsPage->hasMorePages())
					<div class="mt-8">
						<a href="{{ $reviewsPage->nextPageUrl() }}"
							class="inline-flex items-center rounded-xl border border-gray-300 dark:border-gray-700 px-6 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100 transition hover:bg-gray-50 dark:hover:bg-gray-800">
							Show all reviews
						</a>
					</div>
				@endif
			@else
				<div
					class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
					No reviews available yet.
				</div>
			@endif
		</section>

		@if (collect($similarInfluencers ?? [])->isNotEmpty())
			<section class="py-2 px-4 sm:px-6 lg:px-8 max-w-screen-2xl mx-auto">
				<div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4 sm:p-6">
					<div class="flex items-center justify-between mb-5">
						<div>
							<h3 class="text-2xl font-semibold text-gray-900 dark:text-white">Influencers similar to {{ $similarRegionLabel }}</h3>
						</div>
						<div class="hidden sm:flex items-center gap-2">
							<button type="button" @click="scrollSimilar('prev')"
								class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
								<x-icons.chevron-left class="h-4 w-4" />
							</button>
							<button type="button" @click="scrollSimilar('next')"
								class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-300 text-gray-700 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
								<x-icons.chevron-right class="h-4 w-4" />
							</button>
						</div>
					</div>

					<div x-ref="similarCarousel" class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory scroll-smooth no-scrollbar">
						@foreach ($similarInfluencers as $similar)
							<a href="{{ !empty($similar['slug']) ? route('influencer.profile', ['slug' => $similar['slug']]) : '#' }}"
								class="group snap-start shrink-0 w-[80%] sm:w-[44%] lg:w-[31%] overflow-hidden font-sans cursor-pointer influencer-card block"
								data-influencer-id="{{ $similar['id'] }}">
								<div class="relative overflow-hidden rounded-xl">
									<button
										type="button"
										class="wishlist-btn absolute top-3 right-3 z-30 p-1.5 text-white transition-all duration-300 hover:scale-110 drop-shadow-md"
										data-wishlist-trigger
										data-wishlist-active="false"
										data-influencer-id="{{ $similar['id'] }}"
										data-wishlist-image="{{ image_url($similar['image_url']) }}"
										aria-label="Add {{ $similar['name'] }} to wishlist"
										aria-pressed="false">
										<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 wishlist-heart-icon fill-none stroke-current stroke-[2px]" viewBox="0 0 24 24">
											<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l8.84-8.84 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
										</svg>
									</button>

									<img src="{{ image_url($similar['image_url']) }}"
										class="w-full h-56 object-cover transition-transform duration-500 ease-out group-hover:scale-110"
										alt="{{ $similar['name'] }}">

									<div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
										@if (!empty($similar['has_top_influencer']))
											<span
												class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
												<x-icons.heart-badge class="w-4 h-4 text-pink-400" /> Top Creator
											</span>
										@endif
										@if (!empty($similar['has_responses_fast']))
											<span
												class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
												<x-icons.active class="w-3.5 h-3.5 text-green-400" /> Responds Fast
											</span>
										@endif
									</div>

									<div class="absolute bottom-3 left-3 right-3">
										<div class="flex flex-row items-center gap-2 mb-1">
											<div
												class="bg-white text-black text-[10px] font-medium px-2 py-0.5 rounded-md w-fit flex items-center gap-1">
												@if ($similar['platform'] === 'facebook')
													<x-icons.facebook class="w-4 h-4 text-blue-600" />
												@elseif ($similar['platform'] === 'instagram')
													<x-icons.instagram class="w-4 h-4 text-pink-500" />
												@elseif ($similar['platform'] === 'tiktok')
													<x-icons.tiktok class="w-4 h-4 text-black" />
												@elseif ($similar['platform'] === 'youtube')
													<x-icons.youtube class="w-4 h-4 text-red-600" />
												@elseif ($similar['platform'] === 'linkedin')
													<x-icons.linkedin class="w-4 h-4 text-blue-700" />
												@elseif ($similar['platform'] === 'x')
													<x-icons.x class="w-4 h-4 text-black" />
												@elseif ($similar['platform'] === 'ugc')
													<x-icons.camera class="w-4 h-4 text-gray-700" />
												@else
													<x-icons.group class="w-4 h-4 text-gray-700" />
												@endif
												{{ $similar['followers_label'] }}
											</div>
										</div>

										<div class="flex items-center gap-1 text-white drop-shadow-md">
											<span class="font-bold text-sm">{{ $similar['name'] }}</span>
											<span class="text-xs">
												<x-icons.star class="w-4 h-4 text-yellow-400" />
											</span>
											<span class="text-xs mt-1">{{ $similar['rating_label'] }}</span>
										</div>
									</div>
								</div>

								<div class="pt-3 px-1">
									<div class="flex items-start justify-between gap-3">
										<h4 class="text-gray-800 dark:text-gray-200 text-[15px] leading-tight font-medium line-clamp-1">
											{{ $similar['title'] !== '' ? $similar['title'] : $similar['platform_label'] . ' Influencer' }}
										</h4>
									</div>
									<p class="text-[13px] text-gray-400 font-normal mt-1">{{ $similar['location'] }}</p>
								</div>
							</a>
						@endforeach
					</div>
				</div>
			</section>
		@endif
	</section>

	@push('scripts')
		<script>
			function influencerProfileData() {
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
					influencerId: @js($influencer->id),
					startNegotiationUrl: @js(route('conversations.start-negotiation', ['influencer' => $influencer->id])),
					startAddToCartUrl(packageId) {
						return @js(route('cart.start-add-to-cart', ['package' => ':id'])).replace(':id', packageId);
					},
					mediaGalleryItems: @js($portfolioMediaItems),
					showGallery: false,
					activeVideoId: null,

					openGallery() {
						this.showGallery = true;
						document.body.style.overflow = 'hidden';
					},

					closeGallery() {
						this.showGallery = false;
						this.pauseGalleryVideos();
						this.activeVideoId = null;
						document.body.style.overflow = 'auto';
					},

					pauseGalleryVideos() {
						const container = this.$refs.galleryModal;
						if (!container) {
							return;
						}

						container.querySelectorAll('video').forEach((video) => {
							video.pause();
						});
					},

					pauseOtherVideos(event) {
						const currentVideo = event.target;
						if (!(currentVideo instanceof HTMLVideoElement)) {
							return;
						}

						this.activeVideoId = currentVideo.dataset.videoId || null;
						this.$el.querySelectorAll('video').forEach((video) => {
							if (video !== currentVideo) {
								video.pause();
							}
						});
					},

					updateVideoState(event) {
						const currentVideo = event.target;
						if (!(currentVideo instanceof HTMLVideoElement)) {
							return;
						}

						if (currentVideo.paused && this.activeVideoId === currentVideo.dataset.videoId) {
							this.activeVideoId = null;
						}
					},

					toggleVideoPlayback(event) {
						const trigger = event.currentTarget;
						const video = trigger.parentElement ? trigger.parentElement.querySelector('video') : null;

						if (!video) {
							return;
						}

						if (video.paused) {
							this.pauseGalleryVideos();
							this.activeVideoId = video.dataset.videoId || null;
								const playPromise = video.play();
								if (playPromise && typeof playPromise.catch === 'function') {
									playPromise.catch(() => {
										if (this.activeVideoId === (video.dataset.videoId || null)) {
											this.activeVideoId = null;
										}
									});
								}
						} else {
							this.activeVideoId = null;
							video.pause();
						}
					},

					scrollSimilar(direction) {
						const container = this.$refs.similarCarousel;
						if (!container) {
							return;
						}

						const offset = Math.max(280, Math.floor(container.clientWidth * 0.8));
						container.scrollBy({
							left: direction === 'next' ? offset : -offset,
							behavior: 'smooth'
						});
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
					},

					get normalizedUserType() {
						return (this.userType || '').toString().trim().toLowerCase();
					},

					get isBrandUser() {
						return this.normalizedUserType === 'brand';
					},

					async handleAddToCart() {
						if (!this.selectedPackage) {
							return;
						}

						// Use global addToCart function for real-time cart updates
						if (!this.isAuthenticated) {
							// Redirect via pending-action endpoint so post-login add-to-cart completes automatically.
							window.location.href = this.startAddToCartUrl(this.selectedPackage.id);
							return;
						}

						if (!this.isBrandUser) {
							// Show error modal for non-brand users
							if (window.confirmationModal) {
								window.confirmationModal.open({
									title: 'Brand Account Required',
									message: 'Only brand accounts can add to cart or negotiate packages.',
									confirmText: 'OK',
									variant: 'warning'
								});
							}
							return;
						}

						// Call global addToCart function for real-time sidebar open
						const success = await addToCart(this.selectedPackage.id);
						if (success) {
							// Success - cart is now open with real-time data
							// Toast notification is shown by global function
						}
					},

					negotiatePackage() {
						if (!this.isAuthenticated) {
							window.location.href = this.startNegotiationUrl;
						} else if (this.isBrandUser) {
							window.location.href = this.startNegotiationUrl;
						} else {
							window.confirmationModal.open({
								title: 'Brand Account Required',
								message: 'Only brand accounts can add to cart or negotiate packages.',
								confirmText: 'OK',
								variant: 'warning'
							});
						}
					}
				};
			}
		</script>
	@endpush
@endsection
