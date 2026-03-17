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
					class="hidden absolute top-14 left-[-24px] mt-2 w-full md:w-[420px] px-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl z-50 py-2 overflow-hidden overflow-y-scroll max-h-60">
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
					class="hidden absolute top-full left-0 md:left-24 right-0 mt-4 w-[90vw] md:w-[600px] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-[2rem] shadow-2xl z-50 p-6 transition-all">
					<p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Popular Categories</p>
					<div id="category-list" class="flex flex-wrap gap-2">
						<!-- Populated dynamically by JavaScript -->
					</div>
				</div>

				<!-- Hidden input for selected categories -->
				<input type="hidden" id="categories-input" name="categories" value="">
			</div>

			<!-- Search Button -->
			<div class="flex justify-end mt-6 md:mt-0 md:ml-3 w-full md:w-auto">
				<button type="submit"
					class="bg-[#222] hover:opacity-80 transition-all p-4 md:p-5 rounded-full text-white shadow-lg">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
							d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</button>
			</div>
		</form>
	</div>

	<!-- Quick Filters / Badges -->
	<div class="max-w-6xl flex flex-wrap items-center gap-3 justify-center mx-auto px-4 mt-6">
		<!-- Rising Instagram Stars -->
		<a href="{{ route('influencers.platform', ['platformSlug' => 'instagram']) }}?sort=followers_asc"
			class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden">
			<div
				class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity">
			</div>
			<span class="relative z-10">
				@include('components.icons.star', ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
			</span>
			<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">Rising Instagram Stars</span>
		</a>

		<!-- Rising TikTok Stars -->
		<a href="{{ route('influencers.platform', ['platformSlug' => 'tiktok']) }}?sort=followers_asc"
			class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden">
			<div
				class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity">
			</div>
			<span class="relative z-10">
				@include('components.icons.star', ['class' => 'w-4 h-4 text-gray-900 dark:text-gray-100'])
			</span>
			<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">Rising TikTok Stars</span>
		</a>

		<!-- Featured Categories (Dynamic) -->
		@php
			$featuredCategories = \App\Models\Category::where('is_featured', true)
				->where('is_active', true)
				->orderBy('featured_order')
				->limit(4)
				->get();
		@endphp

		@foreach($featuredCategories as $category)
			<a href="{{ route('influencers.category', ['categorySlug' => $category->slug]) }}"
				class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 shadow-sm hover:shadow-md hover:border-transparent transition-all group relative overflow-hidden">
				<div
					class="absolute inset-0 opacity-0 group-hover:opacity-10 bg-gradient-to-r from-[#cd9dfd] via-[#C084FC] to-[#c084fc] transition-opacity">
				</div>
				<span class="relative z-10 group-hover:text-black dark:group-hover:text-white">{{ $category->name }}</span>
			</a>
		@endforeach

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
			
			// Build query parameters
			let queryParams = new URLSearchParams();
			if (categories) {
				queryParams.append('categories', categories);
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


		// --- GLOBAL CLICK OUTSIDE ---
		document.addEventListener('click', (e) => {
			const platformContainer = platformTrigger.closest('div[class*="relative"]');
			const categoryContainer = categoryTrigger.closest('div[class*="relative"]');
			
			// Close platform menu if click is outside
			if (platformContainer && !platformContainer.contains(e.target)) {
				platformMenu.classList.add('hidden');
			}
			
			// Close category menu if click is outside
			if (categoryContainer && !categoryContainer.contains(e.target)) {
				categoryMenu.classList.add('hidden');
			}
		});

		// Initialize form with current state from URL
		function initializeFormState() {
			const urlParams = new URLSearchParams(window.location.search);
			
			// Load platform from URL (check both route param and query param)
			const pathMatch = window.location.pathname.match(/\/influencer\/([^\/]+)/);
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
		}

		// Load categories on page load
		loadCategories();
		
		// Initialize form state after categories load
		setTimeout(initializeFormState, 100);
	});

</script>
