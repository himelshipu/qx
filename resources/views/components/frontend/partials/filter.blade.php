@props(['selectedPlatform' => null, 'regionOptions' => [], 'genderOptions' => [], 'followerRangeOptions' => [], 'contentTypeOptions' => [], 'contentTypeOptionsByPlatform' => [], 'selectedContentTypes' => [], 'priceRange' => ['min' => 0, 'max' => 0], 'selectedPriceLabel' => null])

@php
	$platformSelected = !empty($selectedPlatform);
	$initialContentTypeOptions = $platformSelected ? $contentTypeOptions : [];
@endphp

<div>
	<!-- Search Bar -->
	<div class="w-full mx-auto">
		<form id="filterForm" method="GET" action="{{ route('influencers') }}"
			class="bg-white dark:bg-gray-800 w-full rounded-xl md:rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:p-2 md:pl-10 p-6 relative items-start md:items-center gap-0">
			<!-- Platform Selector -->
			<div class="relative flex-1 min-w-0 md:border-r border-gray-100 dark:border-gray-700 md:pr-6">
				<div id="platform-trigger"
					class="flex flex-col gap-[1px] items-start cursor-pointer group">
					<span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Platform</span>
					<span id="selected-platform" class="text-gray-400 text-sm truncate">Choose a platform</span>
					<input type="hidden" id="platform-input" name="platformSlug" value="">
				</div>

				<!-- Platform Dropdown Menu -->
				<div id="platform-menu"
					class="hidden absolute top-14 left-[-24px] mt-2 w-[320px] md:w-[420px] px-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl z-50 py-2 overflow-hidden overflow-y-scroll max-h-60">
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="">
						Platform
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="instagram">
						Instagram
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="tiktok">
						TikTok
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="youtube">
						YouTube
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="twitter">
						X (Twitter)
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="linkedin">
						LinkedIn
					</div>
					<div
						class="platform-option px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl cursor-pointer transition-colors"
						data-value="facebook">
						Facebook
					</div>
				</div>
			</div>

			<!-- Category Section -->
			<div class="relative flex-[1.5] min-w-0 md:pl-6 mt-6 md:mt-0 group">
				<div id="category-trigger" class="w-full cursor-pointer">
					<span class="text-[11px] font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Category</span>
					<div class="flex flex-wrap gap-2 items-center">
						<input type="text" id="category-input" placeholder="Enter keywords, niches or categories"
							class="flex-1 w-full min-w-[320px] sm:min-w-0 bg-transparent border-none p-0 outline-none focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400"
							autocomplete="off">
						<div id="selected-categories-display" class="flex flex-wrap gap-2">
							<!-- Selected category chips will appear here -->
						</div>
					</div>
				</div>

				<!-- Category Dropdown -->
				<div id="category-menu"
					class="hidden absolute top-full left-0 right-0 mt-4 w-[320px] md:w-[420px] xl:w-[500px] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-4xl shadow-2xl z-50 p-6 transition-all">
					<p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Popular Categories</p>
					<div id="category-list" class="flex flex-wrap overflow-x-hidden gap-2">
						<!-- Populated dynamically by JavaScript -->
					</div>
				</div>

				<!-- Hidden input for selected categories -->
				<input type="hidden" id="categories-input" name="categories" value="">
			</div>

			<!-- Search Button -->
			<div class="flex items-center mt-6 md:mt-0 md:ml-3 w-full md:w-auto">
				<button type="submit"
					class="bg-[#222] hover:opacity-80 transition-all px-6 py-3 md:py-3.5 rounded-full w-full flex justify-center items-center gap-2 text-white text-sm font-semibold shadow-lg">
					
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
							d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</button>
			</div>
		</form>
	</div>

	<div class="mt-4 rounded-2xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-800">
		<div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-gray-800 dark:text-gray-100">
			<x-icons.filter class="h-3.5 w-3.5 text-gray-600 dark:text-gray-300" />
			Refine Results
		</div>
		<div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-7">
			<div id="content-type-filter-container" class="relative sm:col-span-2 xl:col-span-2">
				<button id="content-type-trigger" type="button" @disabled(!$platformSelected) aria-disabled="{{ $platformSelected ? 'false' : 'true' }}" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600 disabled:cursor-not-allowed disabled:opacity-50">
					<x-icons.package class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-content-type" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">
						@if (!empty($selectedContentTypes))
							@if (count($selectedContentTypes) === 1)
								{{ $selectedContentTypes[0]['label'] ?? 'Content Type' }}
							@else
								{{ count($selectedContentTypes) }} Content Types
							@endif
						@elseif (!$platformSelected)
							Select a platform first
						@else
							Content Type
						@endif
					</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="content-type-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<div class="max-h-64 overflow-y-auto p-1.5" id="content-type-options-list">
						@forelse (($initialContentTypeOptions ?: []) as $contentType)
							<button type="button" class="content-type-option flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-value="{{ $contentType['value'] ?? '' }}" data-label="{{ $contentType['label'] ?? '' }}">
								<span class="content-type-checkbox inline-flex h-4 w-4 shrink-0 items-center justify-center rounded border border-gray-300 bg-white transition dark:border-gray-500 dark:bg-gray-900">
									<x-icons.check class="checkmark-icon h-3 w-3 opacity-0 transition-opacity" />
								</span>
								<span class="flex-1 truncate">{{ $contentType['label'] ?? '' }}</span>
							</button>
						@empty
							<div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400" id="content-type-empty-state">{{ $platformSelected ? 'No content types available' : 'Select a platform first' }}</div>
						@endforelse
					</div>
					<div class="flex items-center justify-between border-t border-gray-100 px-3 py-2 dark:border-gray-700">
						<button id="content-type-clear" type="button" class="text-[11px] font-semibold text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">Clear</button>
						<span class="text-[11px] text-gray-400 dark:text-gray-500">Pick a platform first</span>
					</div>
				</div>
				<input type="hidden" id="content-types-input" value="">
			</div>

			<div id="followers-filter-container" class="relative">
				<button id="followers-trigger" type="button" @disabled(!$platformSelected) aria-disabled="{{ $platformSelected ? 'false' : 'true' }}" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600 disabled:cursor-not-allowed disabled:opacity-50">
					<x-icons.trending-up class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-followers" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Followers</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="followers-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="followers" data-value="" data-label="Followers">Followers</button>
					@foreach (($followerRangeOptions ?: [['value' => '0-10000', 'label' => '0 - 10K'], ['value' => '10001-50000', 'label' => '10K - 50K'], ['value' => '50001-100000', 'label' => '50K - 100K'], ['value' => '100001-500000', 'label' => '100K - 500K'], ['value' => '500001+', 'label' => '500K+']]) as $followerRangeOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="followers" data-value="{{ $followerRangeOption['value'] ?? '' }}" data-label="{{ $followerRangeOption['label'] ?? '' }}">{{ $followerRangeOption['label'] ?? '' }}</button>
					@endforeach
				</div>
				<input type="hidden" id="followers-input" value="">
			</div>

			<div id="price-filter-container" class="relative">
				<button id="price-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.dollar-sign class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-price" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">{{ $selectedPriceLabel ?: 'Price' }}</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
			</div>

			<div id="gender-filter-container" class="relative">
				<button id="gender-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.user class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-gender" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Gender</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="gender-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="gender" data-value="" data-label="Gender">Gender</button>
					@foreach (($genderOptions ?: [['value' => 'male', 'label' => 'Male'], ['value' => 'female', 'label' => 'Female'], ['value' => 'other', 'label' => 'Other']]) as $genderOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="gender" data-value="{{ $genderOption['value'] ?? '' }}" data-label="{{ $genderOption['label'] ?? '' }}">{{ $genderOption['label'] ?? '' }}</button>
					@endforeach
				</div>
				<input type="hidden" id="gender-input" value="">
			</div>

			<div id="region-filter-container" class="relative">
				<button id="region-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.navigator class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-region" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Region</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="region-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="region" data-value="" data-label="Region">Region</button>
					@foreach (($regionOptions ?: ['United States', 'United Kingdom', 'Canada', 'Australia', 'India']) as $regionOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="region" data-value="{{ $regionOption }}" data-label="{{ $regionOption }}">{{ $regionOption }}</button>
					@endforeach
				</div>
				<input type="hidden" id="region-input" value="">
			</div>

			<div class="relative flex items-center gap-2 rounded-lg">
				<button id="advanced-filter-submit" type="button" class="w-full rounded-lg bg-[#222] px-4 py-2 text-xs font-semibold text-white transition-opacity hover:opacity-85">
					Filter
				</button>
			</div>
		</div>
	</div>

	<style>
		#price-modal .price-track-line {
			height: 3px;
		}

		#price-modal .price-thumb {
			transform: translate(-50%, -50%);
			touch-action: none;
			cursor: grab;
		}

		#price-modal .price-thumb:active {
			cursor: grabbing;
		}
	</style>

	@php
		$modalMinPrice = (float) ($priceRange['min'] ?? 50);
		$modalMaxPrice = (float) ($priceRange['max'] ?? 3000);

		if ($modalMaxPrice <= $modalMinPrice) {
			$modalMinPrice = 50;
			$modalMaxPrice = 3000;
		}
	@endphp

	<div id="price-modal" class="fixed inset-0 z-[60] hidden items-center justify-center px-4 py-6">
		<div id="price-modal-backdrop" class="absolute inset-0 bg-black/70"></div>
		<div class="relative z-10 w-full max-w-[550px] rounded-[20px] bg-white px-[58px] pb-5 pt-5 shadow-2xl dark:bg-gray-800 max-sm:px-6" data-min-price="{{ $modalMinPrice }}" data-max-price="{{ $modalMaxPrice }}">
			<button id="price-modal-close" type="button" class="absolute right-5 top-5 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-[#222] shadow-[0_4px_14px_rgba(0,0,0,0.12)] transition hover:bg-gray-50 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700">
				<x-icons.close class="h-3.5 w-3.5" />
			</button>

			<h3 class="text-center text-xl font-semibold leading-7 text-[#222] dark:text-white">Price</h3>

			<div class="mt-10 grid grid-cols-2 gap-6">
				<div>
					<p class="text-sm font-normal leading-5 text-[#222] dark:text-gray-200">Min Price</p>
					<p id="price-min-label" class="mt-0.5 text-[26px] font-semibold leading-8 tracking-normal text-[#222] dark:text-white">$0</p>
				</div>
				<div class="text-left sm:text-right">
					<p class="text-sm font-normal leading-5 text-[#222] dark:text-gray-200">Max Price</p>
					<p id="price-max-label" class="mt-0.5 text-[26px] font-semibold leading-8 tracking-normal text-[#222] dark:text-white">$0</p>
				</div>
			</div>

			<div id="price-track" class="relative mt-5 h-8">
				<div class="price-track-line absolute left-0 right-0 top-1/2 -translate-y-1/2 rounded-full bg-gray-200 dark:bg-gray-700"></div>
				<div id="price-range-fill" class="price-track-line absolute top-1/2 -translate-y-1/2 rounded-full bg-[#222]" style="left: 0%; right: 0%;"></div>
				<button id="price-min-thumb" type="button" class="price-thumb absolute top-1/2 z-20 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#222] shadow-[0_2px_8px_rgba(0,0,0,0.22)]">
					<span class="sr-only">Adjust minimum price</span>
				</button>
				<button id="price-max-thumb" type="button" class="price-thumb absolute top-1/2 z-30 inline-flex h-5 w-5 items-center justify-center rounded-full bg-[#222] shadow-[0_2px_8px_rgba(0,0,0,0.22)]">
					<span class="sr-only">Adjust maximum price</span>
				</button>
			</div>

			<div class="mt-10">
				<button id="price-save" type="button" class="inline-flex h-[52px] w-full items-center justify-center rounded-[7px] bg-[#222] px-5 text-sm font-semibold text-white transition hover:opacity-90">
					Save
				</button>
			</div>
		</div>
	</div>


	<div class="w-full px-2 py-4 overflow-x-auto">
    
		<div class="flex justify-start lg:justify-center flex-nowrap gap-3 w-[320px] lg:w-full">

			<!-- Rising Instagram Stars -->
			<a href="{{ route('influencers.platform', ['platformSlug' => 'instagram']) }}?sort=followers_asc"
				class="flex-shrink-0 flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden whitespace-nowrap">

				<div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity"></div>

				<span class="relative z-10">
					@include('components.icons.star', ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
				</span>

				<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">
					Rising Instagram Stars
				</span>
			</a>

			<!-- Rising TikTok Stars -->
			<a href="{{ route('influencers.platform', ['platformSlug' => 'tiktok']) }}?sort=followers_asc"
				class="flex-shrink-0 flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden whitespace-nowrap">

				<div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity"></div>

				<span class="relative z-10">
					@include('components.icons.star', ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
				</span>

				<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">
					Rising TikTok Stars
				</span>
			</a>

				<!-- Featured Categories -->
				@php
					$featuredCategories = \App\Models\Category::where('is_featured', true)
						->where('is_active', true)
						->orderBy('featured_order')
						->limit(4)
						->get();
				@endphp

				@foreach($featuredCategories as $category)
				<a href="{{ route('influencers.category', ['categorySlug' => $category->slug]) }}"
					class="flex-shrink-0 flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden whitespace-nowrap">

					<div class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity"></div>

					<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">
						{{ $category->name }}
					</span>
				</a>
				@endforeach
			
		</div>
	</div>
</div>


<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Selectors
		const filterForm = document.getElementById('filterForm');
		const platformTrigger = document.getElementById('platform-trigger');
		const platformMenu = document.getElementById('platform-menu');
		const platformLabel = document.getElementById('selected-platform');
		const platformInput = document.getElementById('platform-input');
		const platformOptions = document.querySelectorAll('.platform-option');

		const categoryTrigger = document.getElementById('category-trigger');
		const categoryMenu = document.getElementById('category-menu');
		const categoryInput = document.getElementById('category-input');
		const categoryList = document.getElementById('category-list');
		const categoriesInput = document.getElementById('categories-input');
		const selectedCategoriesDisplay = document.getElementById('selected-categories-display');
		const contentTypeFilterContainer = document.getElementById('content-type-filter-container');
		const priceFilterContainer = document.getElementById('price-filter-container');
		const genderFilterContainer = document.getElementById('gender-filter-container');
		const regionFilterContainer = document.getElementById('region-filter-container');
		const followersFilterContainer = document.getElementById('followers-filter-container');
		const contentTypeTrigger = document.getElementById('content-type-trigger');
		const priceTrigger = document.getElementById('price-trigger');
		const genderTrigger = document.getElementById('gender-trigger');
		const regionTrigger = document.getElementById('region-trigger');
		const followersTrigger = document.getElementById('followers-trigger');
		const contentTypeMenu = document.getElementById('content-type-menu');
		const priceModal = document.getElementById('price-modal');
		const priceModalBackdrop = document.getElementById('price-modal-backdrop');
		const priceModalClose = document.getElementById('price-modal-close');
		const priceTrack = document.getElementById('price-track');
		const priceMinThumb = document.getElementById('price-min-thumb');
		const priceMaxThumb = document.getElementById('price-max-thumb');
		const priceMinLabel = document.getElementById('price-min-label');
		const priceMaxLabel = document.getElementById('price-max-label');
		const priceRangeFill = document.getElementById('price-range-fill');
		const priceSave = document.getElementById('price-save');
		const contentTypeClear = document.getElementById('content-type-clear');
		const selectedContentTypeLabel = document.getElementById('selected-content-type');
		const selectedPriceLabel = document.getElementById('selected-price');
		const genderMenu = document.getElementById('gender-menu');
		const regionMenu = document.getElementById('region-menu');
		const followersMenu = document.getElementById('followers-menu');
		const selectedGender = document.getElementById('selected-gender');
		const selectedRegion = document.getElementById('selected-region');
		const selectedFollowers = document.getElementById('selected-followers');
		const contentTypesInput = document.getElementById('content-types-input');
		const contentTypeOptionsList = document.getElementById('content-type-options-list');
		const genderInput = document.getElementById('gender-input');
		const regionInput = document.getElementById('region-input');
		const followersInput = document.getElementById('followers-input');
		const advancedFilterSubmit = document.getElementById('advanced-filter-submit');
		const contentTypeOptionsByPlatform = @json($contentTypeOptionsByPlatform ?? []);
		const fallbackContentTypeOptionsByPlatform = {
			facebook: [
				{ value: 'posts', label: 'Facebook Posts' },
			],
			instagram: [
				{ value: 'stories', label: 'Instagram Stories' },
				{ value: 'reels', label: 'Instagram Reels' },
				{ value: 'photo-feed-post', label: 'Instagram Photo Feed Post' },
			],
			tiktok: [
				{ value: 'stories', label: 'TikTok Stories' },
				{ value: 'videos', label: 'TikTok Videos' },
				{ value: 'live', label: 'TikTok Live' },
			],
			youtube: [
				{ value: 'shorts', label: 'YouTube Shorts' },
				{ value: 'videos', label: 'YouTube Videos' },
			],
			linkedin: [
				{ value: 'posts', label: 'LinkedIn Posts' },
			],
			x: [
				{ value: 'posts', label: 'X Posts' },
			],
			ugc: [
				{ value: 'content', label: 'UGC Content' },
			],
			other: [
				{ value: 'content', label: 'Other Content' },
			],
		};
		const priceBoundsPanel = priceModal?.querySelector('[data-min-price]');
		const rawPriceBounds = {
			min: Number(priceBoundsPanel?.getAttribute('data-min-price') || 50),
			max: Number(priceBoundsPanel?.getAttribute('data-max-price') || 3000),
		};
		const priceBounds = {
			min: rawPriceBounds.max > rawPriceBounds.min ? rawPriceBounds.min : 50,
			max: rawPriceBounds.max > rawPriceBounds.min ? rawPriceBounds.max : 3000,
		};

		let allCategories = [];
		let selectedCategories = [];
		let categorySearchText = '';
		let selectedContentTypeIds = [];
		let activePlatformKey = '';
		let priceMinValue = priceBounds.min;
		let priceMaxValue = priceBounds.max;
		let priceSelectionActive = false;
		let priceRangeCommitted = false;
		let activePriceThumb = null;
		const minimumPriceGap = 1;

		function formatPriceValue(value, showPlus = false) {
			const numericValue = Number(value || 0);
			const fractionDigits = Number.isInteger(numericValue) ? 0 : 2;
			const formattedValue = numericValue.toLocaleString('en-US', { maximumFractionDigits: fractionDigits, minimumFractionDigits: fractionDigits });
			return `$${formattedValue}${showPlus ? '+' : ''}`;
		}

		function getPriceValueFromClientX(clientX) {
			if (!priceTrack) {
				return priceBounds.min;
			}

			const rect = priceTrack.getBoundingClientRect();
			if (rect.width <= 0) {
				return priceBounds.min;
			}

			const ratio = Math.max(0, Math.min(1, (clientX - rect.left) / rect.width));
			const rawValue = priceBounds.min + (ratio * Math.max(priceBounds.max - priceBounds.min, 0));
			return Math.round(rawValue);
		}

		// Fetch categories from server
		async function loadCategories() {
			try {
				const response = await fetch('/api/categories');
				const data = await response.json();
				allCategories = data.categories || [];
				renderCategories(allCategories);
			} catch (error) {
				console.error('Error loading categories:', error);
			}
		}

		function updateSelectedCategoriesDisplay() {
			selectedCategoriesDisplay.innerHTML = '';
			selectedCategories.forEach(catId => {
				const category = allCategories.find(cat => String(cat.id) === String(catId));
				if (category) {
					const chip = document.createElement('span');
					chip.className = 'inline-flex items-center gap-1 px-2 py-1 bg-black dark:bg-purple-500 text-white text-xs font-medium rounded-md';
					chip.innerHTML = `
						${category.name}
						<button type="button" class="ml-1 hover:scale-110 transition-transform" data-remove-cat="${catId}">
							<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
							</svg>
						</button>
					`;
					selectedCategoriesDisplay.appendChild(chip);
				}
			});

			// Add event listeners to remove buttons
			selectedCategoriesDisplay.querySelectorAll('[data-remove-cat]').forEach(btn => {
				btn.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					const catId = btn.getAttribute('data-remove-cat');
					selectedCategories = selectedCategories.filter(id => id !== catId);
					categoriesInput.value = selectedCategories.join(',');
					renderCategories(getCategoriesForDisplay());
					updateSelectedCategoriesDisplay();
				});
			});
		}

		function syncContentTypeUI() {
			if (contentTypesInput) {
				contentTypesInput.value = selectedContentTypeIds.join(',');
			}

			if (selectedContentTypeLabel) {
				if (selectedContentTypeIds.length === 0) {
					selectedContentTypeLabel.textContent = 'Content Type';
				} else if (selectedContentTypeIds.length === 1) {
					const option = Array.from(contentTypeMenu?.querySelectorAll('.content-type-option') || []).find((node) => node.getAttribute('data-value') === selectedContentTypeIds[0]);
					selectedContentTypeLabel.textContent = option?.getAttribute('data-label') || '1 Content Type';
				} else {
					selectedContentTypeLabel.textContent = `${selectedContentTypeIds.length} Content Types`;
				}
			}

			contentTypeMenu?.querySelectorAll('.content-type-option').forEach((option) => {
				const value = option.getAttribute('data-value') || '';
				const checkbox = option.querySelector('.content-type-checkbox');
				const isSelected = selectedContentTypeIds.includes(value);

				option.classList.toggle('bg-gray-100', isSelected);
				option.classList.toggle('dark:bg-gray-700', isSelected);
				option.classList.toggle('text-gray-900', isSelected);
				option.classList.toggle('dark:text-white', isSelected);
				option.classList.toggle('font-medium', isSelected);

				if (checkbox) {
					checkbox.classList.toggle('bg-black', isSelected);
					checkbox.classList.toggle('border-black', isSelected);
					const checkmark = checkbox.querySelector('.checkmark-icon');
					if (checkmark) {
						checkmark.classList.toggle('opacity-100', isSelected);
						checkmark.classList.toggle('opacity-0', !isSelected);
					}
				}
			});
		}

		function normalizePlatformKey(platformValue) {
			const aliases = {
				'twitter': 'x',
				'twitter-x': 'x',
				'user-generated-content': 'ugc',
			};

			const normalized = String(platformValue || '').trim().toLowerCase();
			return aliases[normalized] || normalized;
		}

		function setDependentControlsEnabled(enabled) {
			if (contentTypeTrigger) {
				contentTypeTrigger.disabled = !enabled;
				contentTypeTrigger.setAttribute('aria-disabled', enabled ? 'false' : 'true');
			}

			if (followersTrigger) {
				followersTrigger.disabled = !enabled;
				followersTrigger.setAttribute('aria-disabled', enabled ? 'false' : 'true');
			}

			if (contentTypeFilterContainer) {
				contentTypeFilterContainer.classList.toggle('opacity-50', !enabled);
			}

			if (followersFilterContainer) {
				followersFilterContainer.classList.toggle('opacity-50', !enabled);
			}
		}

		function renderContentTypeOptions(platformKey) {
			if (!contentTypeOptionsList) {
				return;
			}

			const options = contentTypeOptionsByPlatform[platformKey] || fallbackContentTypeOptionsByPlatform[platformKey] || [];
			contentTypeOptionsList.innerHTML = '';

			if (options.length === 0) {
				const emptyState = document.createElement('div');
				emptyState.className = 'px-3 py-2 text-xs text-gray-500 dark:text-gray-400';
				emptyState.textContent = 'Select a platform first';
				contentTypeOptionsList.appendChild(emptyState);
				return;
			}

			options.forEach((contentType) => {
				const option = document.createElement('button');
				option.type = 'button';
				option.className = 'content-type-option flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700';
				option.setAttribute('data-value', contentType.value || '');
				option.setAttribute('data-label', contentType.label || '');
				option.innerHTML = `
					<span class="content-type-checkbox inline-flex h-4 w-4 shrink-0 items-center justify-center rounded border border-gray-300 bg-white transition dark:border-gray-500 dark:bg-gray-900">
						<svg class="checkmark-icon h-3 w-3 opacity-0 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
					</span>
					<span class="flex-1 truncate"></span>
				`;
				option.querySelector('span.flex-1')?.replaceChildren(document.createTextNode(contentType.label || ''));
				contentTypeOptionsList.appendChild(option);
			});
		}

		function clearDependentFilters() {
			selectedContentTypeIds = [];
			if (followersInput) {
				followersInput.value = '';
			}
			if (selectedFollowers) {
				selectedFollowers.textContent = 'Followers';
			}
		}

		function syncPlatformDependentFilters(platformValue, { preserveSelections = false } = {}) {
			const platformKey = normalizePlatformKey(platformValue);
			const hasPlatform = platformKey !== '';
			const platformChanged = platformKey !== activePlatformKey;

			activePlatformKey = platformKey;
			setDependentControlsEnabled(hasPlatform);
			renderContentTypeOptions(platformKey);

			if (!hasPlatform) {
				clearDependentFilters();
				syncContentTypeUI();
				if (selectedContentTypeLabel) {
					selectedContentTypeLabel.textContent = 'Select a platform first';
				}
				return;
			}

			if (platformChanged && !preserveSelections) {
				clearDependentFilters();
			}

			syncContentTypeUI();
		}

		function syncPriceUI() {
			const hasCustomRange = priceMinValue > priceBounds.min || priceMaxValue < priceBounds.max;
			const hasActivePriceRange = priceRangeCommitted || hasCustomRange;
			priceSelectionActive = hasActivePriceRange;

			if (priceMinLabel) {
				priceMinLabel.textContent = formatPriceValue(priceMinValue);
			}

			if (priceMaxLabel) {
				priceMaxLabel.textContent = formatPriceValue(priceMaxValue, priceMaxValue >= priceBounds.max);
			}

			if (selectedPriceLabel) {
				selectedPriceLabel.textContent = hasActivePriceRange
					? `${formatPriceValue(priceMinValue)} - ${formatPriceValue(priceMaxValue, priceMaxValue >= priceBounds.max)}`
					: 'Price';
			}

			if (priceRangeFill) {
				const range = Math.max(priceBounds.max - priceBounds.min, 1);
				const start = ((priceMinValue - priceBounds.min) / range) * 100;
				const end = ((priceBounds.max - priceMaxValue) / range) * 100;
				priceRangeFill.style.left = `${Math.max(0, Math.min(100, start))}%`;
				priceRangeFill.style.right = `${Math.max(0, Math.min(100, end))}%`;
			}

			if (priceMinThumb) {
				const range = Math.max(priceBounds.max - priceBounds.min, 1);
				const start = ((priceMinValue - priceBounds.min) / range) * 100;
				priceMinThumb.style.left = `${Math.max(0, Math.min(100, start))}%`;
			}

			if (priceMaxThumb) {
				const range = Math.max(priceBounds.max - priceBounds.min, 1);
				const end = ((priceMaxValue - priceBounds.min) / range) * 100;
				priceMaxThumb.style.left = `${Math.max(0, Math.min(100, end))}%`;
			}
		}

		function setPriceRange(minValue, maxValue) {
			const clampedMin = Math.max(priceBounds.min, Math.min(minValue, priceBounds.max));
			const clampedMax = Math.max(priceBounds.min, Math.min(maxValue, priceBounds.max));
			const normalizedMin = Math.min(clampedMin, clampedMax);
			const normalizedMax = Math.max(clampedMin, clampedMax);

			if (normalizedMax - normalizedMin < minimumPriceGap) {
				if (normalizedMax >= priceBounds.max) {
					priceMaxValue = priceBounds.max;
					priceMinValue = Math.max(priceBounds.min, priceBounds.max - minimumPriceGap);
				} else {
					priceMinValue = normalizedMin;
					priceMaxValue = Math.min(priceBounds.max, normalizedMin + minimumPriceGap);
				}
			} else {
				priceMinValue = normalizedMin;
				priceMaxValue = normalizedMax;
			}
			syncPriceUI();
		}

		function clearContentTypes() {
			selectedContentTypeIds = [];
			syncContentTypeUI();
		}

		function updatePriceFromPointer(clientX) {
			if (!activePriceThumb) {
				return;
			}

			const nextValue = getPriceValueFromClientX(clientX);

			if (activePriceThumb === 'min') {
				setPriceRange(Math.min(nextValue, priceMaxValue - minimumPriceGap), priceMaxValue);
			} else if (activePriceThumb === 'max') {
				setPriceRange(priceMinValue, Math.max(nextValue, priceMinValue + minimumPriceGap));
			}
		}

		function beginPriceDrag(clientX) {
			const nextValue = getPriceValueFromClientX(clientX);
			const minDistance = Math.abs(nextValue - priceMinValue);
			const maxDistance = Math.abs(nextValue - priceMaxValue);

			activePriceThumb = minDistance <= maxDistance ? 'min' : 'max';
			updatePriceFromPointer(clientX);
		}

		function getCategoriesForDisplay() {
			const searchText = categorySearchText.toLowerCase();
			if (searchText === '') {
				return allCategories;
			}
			return allCategories.filter(cat => cat.name.toLowerCase().includes(searchText));
		}

		function renderCategories(categories) {
			categoryList.innerHTML = '';
			categories.forEach(cat => {
				const button = document.createElement('button');
				button.type = 'button';
				const isSelected = selectedCategories.includes(String(cat.id));
				button.className =
					'category-option px-4 py-2 text-[13px] font-medium rounded-lg border border-transparent transition-all active:scale-95 ' +
					(isSelected 
						? 'bg-black dark:bg-purple-500 text-white' 
						: 'bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white hover:border-gray-200 dark:hover:border-gray-600');
				button.setAttribute('data-value', cat.id);
				button.setAttribute('data-name', cat.name);
				button.textContent = cat.name;

				button.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					const catId = String(cat.id);

					if (selectedCategories.includes(catId)) {
						selectedCategories = selectedCategories.filter(id => id !== catId);
						button.classList.remove('bg-black', 'text-white', 'dark:bg-purple-500');
						button.classList.add('bg-gray-50', 'text-gray-700', 'dark:bg-gray-700');
					} else {
						selectedCategories.push(catId);
						button.classList.add('bg-black', 'text-white', 'dark:bg-purple-500');
						button.classList.remove('bg-gray-50', 'text-gray-700', 'dark:bg-gray-700');
					}

					categoriesInput.value = selectedCategories.join(',');
					updateSelectedCategoriesDisplay();
				});

				categoryList.appendChild(button);
			});
		}

		// Filter categories by search input
		categoryInput.addEventListener('input', (e) => {
			categorySearchText = e.target.value;
			const filtered = getCategoriesForDisplay();
			renderCategories(filtered);
		});

		const advancedMenus = [genderMenu, regionMenu, followersMenu, contentTypeMenu].filter(Boolean);
		const advancedContainers = [contentTypeFilterContainer, genderFilterContainer, regionFilterContainer, followersFilterContainer].filter(Boolean);

		function closeAdvancedMenus() {
			advancedMenus.forEach(menu => menu.classList.add('hidden'));
		}

		function setAdvancedFilterValue(target, value, label) {
			if (target === 'gender') {
				if (genderInput) {
					genderInput.value = value;
				}
				if (selectedGender) {
					selectedGender.textContent = label || 'Gender';
				}
				return;
			}

			if (target === 'region') {
				if (regionInput) {
					regionInput.value = value;
				}
				if (selectedRegion) {
					selectedRegion.textContent = label || 'Region';
				}
				return;
			}

			if (followersInput) {
				followersInput.value = value;
			}
			if (selectedFollowers) {
				selectedFollowers.textContent = label || 'Followers';
			}
		}

		function bindAdvancedTrigger(trigger, menu) {
			if (!trigger || !menu) {
				return;
			}

			trigger.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				if (trigger.disabled) {
					return;
				}
				platformMenu.classList.add('hidden');
				categoryMenu.classList.add('hidden');
				const shouldOpen = menu.classList.contains('hidden');
				closeAdvancedMenus();
				if (shouldOpen) {
					menu.classList.remove('hidden');
				}
			});
		}

		function bindAdvancedMenu(menu, target) {
			if (!menu) {
				return;
			}

			menu.addEventListener('click', (e) => {
				e.stopPropagation();
				const option = e.target.closest('.advanced-option');
				if (!option) {
					return;
				}

				e.preventDefault();
				const value = option.getAttribute('data-value') || '';
				const label = option.getAttribute('data-label') || option.textContent.trim();
				setAdvancedFilterValue(target, value, label);
				closeAdvancedMenus();
			});
		}

		function setAdvancedFromQuery(menu, target, queryValue) {
			if (!queryValue || !menu) {
				return;
			}

			const option = Array.from(menu.querySelectorAll('.advanced-option')).find((node) => {
				return node.getAttribute('data-value') === queryValue;
			});

			if (option) {
				setAdvancedFilterValue(target, queryValue, option.getAttribute('data-label') || option.textContent.trim());
				return;
			}

			setAdvancedFilterValue(target, queryValue, queryValue);
		}

		function toggleContentType(value) {
			const normalizedValue = String(value || '');
			if (normalizedValue === '') {
				return;
			}

			if (selectedContentTypeIds.includes(normalizedValue)) {
				selectedContentTypeIds = selectedContentTypeIds.filter((id) => id !== normalizedValue);
			} else {
				selectedContentTypeIds.push(normalizedValue);
			}

			syncContentTypeUI();
		}

		function setContentTypesFromQuery(queryValue) {
			if (!queryValue) {
				clearContentTypes();
				return;
			}

			const availableValues = Array.from(contentTypeMenu?.querySelectorAll('.content-type-option') || []).map((node) => node.getAttribute('data-value') || '');
			selectedContentTypeIds = queryValue
				.split(',')
				.map((value) => value.trim())
				.filter((value) => value !== '' && availableValues.includes(value));
			selectedContentTypeIds = Array.from(new Set(selectedContentTypeIds));
			syncContentTypeUI();
		}

		function setPriceFromQuery(queryValue) {
			if (!queryValue) {
				priceRangeCommitted = false;
				setPriceRange(priceBounds.min, priceBounds.max);
				return;
			}

			const match = String(queryValue).match(/^(\d+(?:\.\d+)?)\-(\d+(?:\.\d+)?)$/);
			if (!match) {
				priceRangeCommitted = false;
				setPriceRange(priceBounds.min, priceBounds.max);
				return;
			}

			priceRangeCommitted = true;
			setPriceRange(Number(match[1]), Number(match[2]));
		}

		function openPriceModal() {
			if (!priceModal) {
				return;
			}

			priceModal.classList.remove('hidden');
			priceModal.classList.add('flex');
			closeAdvancedMenus();
			platformMenu.classList.add('hidden');
			categoryMenu.classList.add('hidden');
			syncPriceUI();
		}

		function closePriceModal() {
			if (!priceModal) {
				return;
			}

			priceModal.classList.add('hidden');
			priceModal.classList.remove('flex');
		}

		bindAdvancedTrigger(genderTrigger, genderMenu);
		bindAdvancedTrigger(regionTrigger, regionMenu);
		bindAdvancedTrigger(followersTrigger, followersMenu);
		bindAdvancedMenu(genderMenu, 'gender');
		bindAdvancedMenu(regionMenu, 'region');
		bindAdvancedMenu(followersMenu, 'followers');

		if (contentTypeTrigger && contentTypeMenu) {
			contentTypeTrigger.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				if (contentTypeTrigger.disabled) {
					return;
				}
				platformMenu.classList.add('hidden');
				categoryMenu.classList.add('hidden');
				closeAdvancedMenus();
				closePriceModal();
				contentTypeMenu.classList.toggle('hidden');
			});

			contentTypeMenu.addEventListener('click', (e) => {
				e.stopPropagation();
				const option = e.target.closest('.content-type-option');
				if (option) {
					e.preventDefault();
					toggleContentType(option.getAttribute('data-value') || '');
					return;
				}

				const clearButton = e.target.closest('#content-type-clear');
				if (clearButton) {
					e.preventDefault();
					clearContentTypes();
				}
			});
		}

		if (priceTrigger) {
			priceTrigger.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				contentTypeMenu?.classList.add('hidden');
				closeAdvancedMenus();
				openPriceModal();
			});
		}

		priceModalBackdrop?.addEventListener('click', closePriceModal);
		priceModalClose?.addEventListener('click', closePriceModal);
		priceSave?.addEventListener('click', () => {
			priceRangeCommitted = true;
			syncPriceUI();
			closePriceModal();
		});

		priceMinThumb?.addEventListener('pointerdown', (e) => {
			e.preventDefault();
			e.stopPropagation();
			activePriceThumb = 'min';
			priceMinThumb.setPointerCapture(e.pointerId);
		});

		priceMaxThumb?.addEventListener('pointerdown', (e) => {
			e.preventDefault();
			e.stopPropagation();
			activePriceThumb = 'max';
			priceMaxThumb.setPointerCapture(e.pointerId);
		});

		priceTrack?.addEventListener('pointerdown', (e) => {
			e.preventDefault();
			beginPriceDrag(e.clientX);
		});

		document.addEventListener('pointermove', (e) => {
			if (!activePriceThumb || !priceModal || priceModal.classList.contains('hidden')) {
				return;
			}

			updatePriceFromPointer(e.clientX);
		});

		document.addEventListener('pointerup', () => {
			activePriceThumb = null;
		});

		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && priceModal && !priceModal.classList.contains('hidden')) {
				closePriceModal();
			}
		});

		// Function to rebind platform option handlers
		function rebindPlatformOptions() {
			const options = document.querySelectorAll('.platform-option');
			options.forEach(option => {
				option.addEventListener('click', (e) => {
					e.preventDefault();
					e.stopPropagation();
					const val = option.getAttribute('data-value');
					platformInput.value = val;

					if (val === '') {
						platformLabel.textContent = 'Choose a platform';
						platformLabel.classList.add('text-gray-400');
						platformLabel.classList.remove('text-gray-900', 'dark:text-white');
					} else {
						platformLabel.textContent = option.textContent;
						platformLabel.classList.remove('text-gray-400');
						platformLabel.classList.add('text-gray-900', 'dark:text-white');
					}
					platformMenu.classList.add('hidden');
				});
			});
		}

		// --- PLATFORM LOGIC ---
		platformTrigger.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			closeAdvancedMenus();
			categoryMenu.classList.add('hidden');
			platformMenu.classList.toggle('hidden');
		});

		// Use event delegation for platform options
		platformMenu.addEventListener('click', (e) => {
			e.stopPropagation();
			const option = e.target.closest('.platform-option');
			if (option) {
				e.preventDefault();
				const val = option.getAttribute('data-value');
				platformInput.value = val;

				if (val === '') {
					platformLabel.textContent = 'Choose a platform';
					platformLabel.classList.add('text-gray-400');
					platformLabel.classList.remove('text-gray-900', 'dark:text-white');
				} else {
					platformLabel.textContent = option.textContent;
					platformLabel.classList.remove('text-gray-400');
					platformLabel.classList.add('text-gray-900', 'dark:text-white');
				}
				syncPlatformDependentFilters(val);
				platformMenu.classList.add('hidden');
			}
		});

		// Keep the old forEach for existing handlers (for safety)
		platformOptions.forEach(option => {
			option.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
				const val = option.getAttribute('data-value');
				platformInput.value = val;

				if (val === '') {
					platformLabel.textContent = 'Choose a platform';
					platformLabel.classList.add('text-gray-400');
					platformLabel.classList.remove('text-gray-900', 'dark:text-white');
				} else {
					platformLabel.textContent = option.textContent;
					platformLabel.classList.remove('text-gray-400');
					platformLabel.classList.add('text-gray-900', 'dark:text-white');
				}
				syncPlatformDependentFilters(val);
				platformMenu.classList.add('hidden');
			});
		});

		// --- CATEGORY LOGIC ---
		categoryTrigger.addEventListener('click', (e) => {
			e.preventDefault();
			e.stopPropagation();
			closeAdvancedMenus();
			platformMenu.classList.add('hidden');
			categoryMenu.classList.toggle('hidden');
		});

		// Also open category menu when input is focused
		categoryInput.addEventListener('focus', (e) => {
			e.stopPropagation();
			platformMenu.classList.add('hidden');
			categoryMenu.classList.remove('hidden');
		});

		// Form submission handler
		filterForm.addEventListener('submit', (e) => {
			e.preventDefault();
			
			const platformSlug = platformInput.value;
			const categories = categoriesInput.value;
			const platformKey = normalizePlatformKey(platformSlug);
			const contentTypes = platformKey ? (contentTypesInput?.value || '') : '';
			const gender = genderInput?.value || '';
			const region = regionInput?.value || '';
			const followers = platformKey ? (followersInput?.value || '') : '';
			const price = priceSelectionActive ? `${priceMinValue}-${priceMaxValue}` : '';
			
			// Build query parameters
			let queryParams = new URLSearchParams();
			if (categories) {
				queryParams.append('categories', categories);
			}
			if (contentTypes) {
				queryParams.append('contentTypes', contentTypes);
			}
			if (gender) {
				queryParams.append('gender', gender);
			}
			if (region) {
				queryParams.append('region', region);
			}
			if (followers) {
				queryParams.append('followers', followers);
			}
			if (price) {
				queryParams.append('price', price);
			}
			
			// Determine the action URL
			let actionUrl = '{{ route('influencers') }}';
			
			if (platformSlug) {
				// If platform is selected, use the platform route
				actionUrl = `{{ route('influencers.platform', ['platformSlug' => '__PLATFORM__']) }}`.replace('__PLATFORM__', platformSlug);
			}
			
			// Append query parameters if any
			if (queryParams.toString()) {
				actionUrl += '?' + queryParams.toString();
			}
			
			window.location.href = actionUrl;
		});

		if (advancedFilterSubmit) {
			advancedFilterSubmit.addEventListener('click', () => {
				filterForm.dispatchEvent(new Event('submit', { cancelable: true }));
			});
		}


		// --- GLOBAL CLICK OUTSIDE ---
		document.addEventListener('click', (e) => {
			const platformContainer = platformTrigger.closest('div[class*="relative"]');
			const categoryContainer = categoryTrigger.closest('div[class*="relative"]');
			const clickedInsideAdvanced = advancedContainers.some(container => container.contains(e.target));
			
			// Close platform menu if click is outside
			if (platformContainer && !platformContainer.contains(e.target)) {
				platformMenu.classList.add('hidden');
			}
			
			// Close category menu if click is outside
			if (categoryContainer && !categoryContainer.contains(e.target)) {
				categoryMenu.classList.add('hidden');
			}

			if (!clickedInsideAdvanced) {
				closeAdvancedMenus();
			}
		});

		// Initialize form with current state from URL
		function initializeFormState() {
			const urlParams = new URLSearchParams(window.location.search);
			
			// Load platform from URL (check both route param and query param)
			const pathMatch = window.location.pathname.match(/\/influencers\/([^\/?]+)/);
			const platformSlug = pathMatch ? pathMatch[1] : urlParams.get('platformSlug');
			
			if (platformSlug) {
				platformInput.value = platformSlug;
				
				// Find and set platform label
				const option = document.querySelector(`.platform-option[data-value="${platformSlug}"]`);
				if (option) {
					platformLabel.textContent = option.textContent;
					platformLabel.classList.remove('text-gray-400');
					platformLabel.classList.add('text-gray-900', 'dark:text-white');
				}
				syncPlatformDependentFilters(platformSlug, { preserveSelections: true });
			} else {
				syncPlatformDependentFilters('', { preserveSelections: false });
			}
			
			// Load categories from URL
			const categoriesParam = urlParams.get('categories');
			if (categoriesParam) {
				selectedCategories = categoriesParam.split(',').filter(c => c !== '');
				categoriesInput.value = selectedCategories.join(',');
				// Re-render to highlight selected categories
				renderCategories(getCategoriesForDisplay());
				updateSelectedCategoriesDisplay();
			}

			setAdvancedFromQuery(genderMenu, 'gender', urlParams.get('gender'));
			setAdvancedFromQuery(regionMenu, 'region', urlParams.get('region'));
			setAdvancedFromQuery(followersMenu, 'followers', urlParams.get('followers'));
			setContentTypesFromQuery(urlParams.get('contentTypes'));
			setPriceFromQuery(urlParams.get('price'));
		}

		// Load categories on page load
		loadCategories();
		
		// Initialize form state after categories load
		setTimeout(initializeFormState, 100);
	});

</script>
