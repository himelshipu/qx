@props(['regionOptions' => [], 'genderOptions' => [], 'followerRangeOptions' => []])

<div>
	<!-- Search Bar -->
	<div class="w-full mx-auto">
		<form id="filterForm" method="GET" action="{{ route('influencers') }}"
			class="bg-white dark:bg-gray-800 w-full rounded-xl md:rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:p-2 md:pl-10 p-6 relative items-start md:items-center gap-0">
			<!-- Platform Selector -->
			<div class="relative flex-1 min-w-0 md:border-r border-gray-100 dark:border-gray-700 md:pr-6">
				<div id="platform-trigger"
					class="flex flex-col items-start cursor-pointer group">
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
						Any Platform
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
					<div class="flex flex-wrap gap-2 items-center mt-1">
						<input type="text" id="category-input" placeholder="Enter keywords, niches or categories"
							class="flex-1 min-w-0 bg-transparent border-none p-0 outline-none focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400"
							autocomplete="off">
						<div id="selected-categories-display" class="flex flex-wrap gap-2">
							<!-- Selected category chips will appear here -->
						</div>
					</div>
				</div>

				<!-- Category Dropdown -->
				<div id="category-menu"
					class="hidden absolute top-full left-0 right-0 mt-4 w-[250px] md:w-[420px] xl:w-[500px] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-4xl shadow-2xl z-50 p-6 transition-all">
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
		<div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-4">
			<div id="gender-filter-container" class="relative">
				<button id="gender-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.user class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-gender" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Any Gender</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="gender-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="gender" data-value="" data-label="Any Gender">Any Gender</button>
					@foreach (($genderOptions ?: [['value' => 'male', 'label' => 'Male'], ['value' => 'female', 'label' => 'Female'], ['value' => 'other', 'label' => 'Other']]) as $genderOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="gender" data-value="{{ $genderOption['value'] ?? '' }}" data-label="{{ $genderOption['label'] ?? '' }}">{{ $genderOption['label'] ?? '' }}</button>
					@endforeach
				</div>
				<input type="hidden" id="gender-input" value="">
			</div>

			<div id="region-filter-container" class="relative">
				<button id="region-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.navigator class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-region" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Any Region</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="region-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="region" data-value="" data-label="Any Region">Any Region</button>
					@foreach (($regionOptions ?: ['United States', 'United Kingdom', 'Canada', 'Australia', 'India']) as $regionOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="region" data-value="{{ $regionOption }}" data-label="{{ $regionOption }}">{{ $regionOption }}</button>
					@endforeach
				</div>
				<input type="hidden" id="region-input" value="">
			</div>

			<div id="followers-filter-container" class="relative">
				<button id="followers-trigger" type="button" class="flex w-full items-center gap-2 rounded-lg border border-transparent bg-gray-50 px-2.5 py-2 text-left transition hover:border-gray-200 dark:bg-gray-700/60 dark:hover:border-gray-600">
					<x-icons.trending-up class="h-4 w-4 text-gray-500 dark:text-gray-300" />
					<span id="selected-followers" class="flex-1 truncate text-xs text-gray-700 dark:text-gray-100">Any Followers</span>
					<x-icons.chevron-down class="h-3.5 w-3.5 text-gray-400 dark:text-gray-300" />
				</button>
				<div id="followers-menu" class="hidden absolute left-0 right-0 top-[calc(100%+8px)] z-50 max-h-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-1.5 shadow-lg dark:border-gray-600 dark:bg-gray-800">
					<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="followers" data-value="" data-label="Any Followers">Any Followers</button>
					@foreach (($followerRangeOptions ?: [['value' => '0-10000', 'label' => '0 - 10K'], ['value' => '10001-50000', 'label' => '10K - 50K'], ['value' => '50001-100000', 'label' => '50K - 100K'], ['value' => '100001-500000', 'label' => '100K - 500K'], ['value' => '500001+', 'label' => '500K+']]) as $followerRangeOption)
						<button type="button" class="advanced-option w-full rounded-lg px-3 py-2 text-left text-xs text-gray-700 transition hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700" data-target="followers" data-value="{{ $followerRangeOption['value'] ?? '' }}" data-label="{{ $followerRangeOption['label'] ?? '' }}">{{ $followerRangeOption['label'] ?? '' }}</button>
					@endforeach
				</div>
				<input type="hidden" id="followers-input" value="">
			</div>

			<div class="relative flex items-center gap-2 rounded-lg">
				<button id="advanced-filter-submit" type="button" class="w-full rounded-lg bg-[#222] px-4 py-2 text-xs font-semibold text-white transition-opacity hover:opacity-85">
					Filter
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
		const genderFilterContainer = document.getElementById('gender-filter-container');
		const regionFilterContainer = document.getElementById('region-filter-container');
		const followersFilterContainer = document.getElementById('followers-filter-container');
		const genderTrigger = document.getElementById('gender-trigger');
		const regionTrigger = document.getElementById('region-trigger');
		const followersTrigger = document.getElementById('followers-trigger');
		const genderMenu = document.getElementById('gender-menu');
		const regionMenu = document.getElementById('region-menu');
		const followersMenu = document.getElementById('followers-menu');
		const selectedGender = document.getElementById('selected-gender');
		const selectedRegion = document.getElementById('selected-region');
		const selectedFollowers = document.getElementById('selected-followers');
		const genderInput = document.getElementById('gender-input');
		const regionInput = document.getElementById('region-input');
		const followersInput = document.getElementById('followers-input');
		const advancedFilterSubmit = document.getElementById('advanced-filter-submit');

		let allCategories = [];
		let selectedCategories = [];
		let categorySearchText = '';

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

		const advancedMenus = [genderMenu, regionMenu, followersMenu].filter(Boolean);
		const advancedContainers = [genderFilterContainer, regionFilterContainer, followersFilterContainer].filter(Boolean);

		function closeAdvancedMenus() {
			advancedMenus.forEach(menu => menu.classList.add('hidden'));
		}

		function setAdvancedFilterValue(target, value, label) {
			if (target === 'gender') {
				if (genderInput) {
					genderInput.value = value;
				}
				if (selectedGender) {
					selectedGender.textContent = label || 'Any Gender';
				}
				return;
			}

			if (target === 'region') {
				if (regionInput) {
					regionInput.value = value;
				}
				if (selectedRegion) {
					selectedRegion.textContent = label || 'Any Region';
				}
				return;
			}

			if (followersInput) {
				followersInput.value = value;
			}
			if (selectedFollowers) {
				selectedFollowers.textContent = label || 'Any Followers';
			}
		}

		function bindAdvancedTrigger(trigger, menu) {
			if (!trigger || !menu) {
				return;
			}

			trigger.addEventListener('click', (e) => {
				e.preventDefault();
				e.stopPropagation();
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

		bindAdvancedTrigger(genderTrigger, genderMenu);
		bindAdvancedTrigger(regionTrigger, regionMenu);
		bindAdvancedTrigger(followersTrigger, followersMenu);
		bindAdvancedMenu(genderMenu, 'gender');
		bindAdvancedMenu(regionMenu, 'region');
		bindAdvancedMenu(followersMenu, 'followers');

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
				platformMenu.classList.add('hidden');
			}
		});

		// Keep the old forEach for any existing handlers (for safety)
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
			const gender = genderInput?.value || '';
			const region = regionInput?.value || '';
			const followers = followersInput?.value || '';
			
			// Build query parameters
			let queryParams = new URLSearchParams();
			if (categories) {
				queryParams.append('categories', categories);
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
		}

		// Load categories on page load
		loadCategories();
		
		// Initialize form state after categories load
		setTimeout(initializeFormState, 100);
	});

</script>
