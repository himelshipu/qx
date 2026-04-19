@php
	/** @var \App\Models\Campaign|null $campaign */
	$campaign = $campaign ?? null;
	$targeting = $campaign?->targeting;

	$selectedCategoryIds = array_values(
	    array_unique(array_map('intval', (array) old('categories', $campaign?->categories?->pluck('id')->all() ?? []))),
	);
	$selectedFollowerRangeIds = array_values(
	    array_unique(
	        array_map('intval', (array) old('follower_ranges', $campaign?->followerRanges?->pluck('id')->all() ?? [])),
	    ),
	);
	$selectedCountryCodes = array_values(
	    array_unique(
	        array_map(static function ($code): string {
	            return strtoupper((string) $code);
	        }, (array) old('target_countries', $campaign?->targetCountries?->pluck('country_code')->all() ?? [])),
	    ),
	);

	$budgetMin = old('budget_min', $campaign?->budget_min !== null ? (string) $campaign->budget_min : '');
	$budgetMax = old('budget_max', $campaign?->budget_max !== null ? (string) $campaign->budget_max : '');
	$selectedBrandId = (int) old('brand_id', $campaign?->brand_id ?? ($defaultBrandId ?? 0));
@endphp

<div x-data="{
			<div>
				<x-form.date-picker
					id="start_date"
					name="start_date"
					label="Start Date"
					placeholder="Select start date"
					:default-date="old('start_date', $campaign?->start_date?->format('Y-m-d'))"
				/>
	        }
	    },
	    getCountryName(code) {
	        const option = this.countryOptions.find(c => c.code.toUpperCase() === code.toUpperCase());
	        return option ? option.name : code;
			<div>
				<x-form.date-picker
					id="end_date"
					name="end_date"
					label="End Date"
					placeholder="Select end date"
					:default-date="old('end_date', $campaign?->end_date?->format('Y-m-d'))"
				/>
						<option value="{{ $brandOption['id'] }}" {{ $selectedBrandId === (int) $brandOption['id'] ? 'selected' : '' }}>
							{{ $brandOption['name'] }}
						</option>
					@endforeach
				</select>
			<div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
				<label class="flex min-h-11 cursor-pointer items-center justify-between gap-3">
					<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
					<span class="flex h-11 items-center">
						<input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($campaign) ? $campaign->is_active : true) ? 'checked' : '' }}
							class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
					</span>
				</label>
		@endif

		<div>
			<label for="title" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Campaign Title <span class="text-red-500">*</span>
			</label>
			<input id="title" name="title" type="text" value="{{ old('title', $campaign?->title) }}" required
				placeholder="e.g., Summer Product Awareness Push"
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('title')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="grid grid-cols-1 gap-5 md:grid-cols-3">
			<div>
				<label for="campaign_type" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Campaign Type <span class="text-red-500">*</span>
				</label>
				<select id="campaign_type" name="campaign_type" required
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					@foreach ($campaignTypeOptions as $option)
						<option value="{{ $option['value'] }}"
							{{ old('campaign_type', $campaign?->campaign_type) === $option['value'] ? 'selected' : '' }}>
							{{ $option['label'] }}
						</option>
					@endforeach
				</select>
				@error('campaign_type')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="status" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Status <span class="text-red-500">*</span>
				</label>
				<select id="status" name="status" required
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					@foreach ($statusOptions as $option)
						<option value="{{ $option['value'] }}"
							{{ old('status', $campaign?->status ?? 'draft') === $option['value'] ? 'selected' : '' }}>
							{{ $option['label'] }}
						</option>
					@endforeach
				</select>
				@error('status')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="currency" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Currency <span class="text-red-500">*</span>
				</label>
				<input id="currency" name="currency" type="text" maxlength="3"
					value="{{ old('currency', $campaign?->currency ?? 'USD') }}" required
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 uppercase text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('currency')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div>
			<label for="description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
			<textarea id="description" name="description" rows="4" placeholder="Brief campaign summary"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description', $campaign?->description) }}</textarea>
			@error('description')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="instructions"
				class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Instructions</label>
			<textarea id="instructions" name="instructions" rows="6" placeholder="Detailed influencer instructions"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('instructions', $campaign?->instructions) }}</textarea>
			@error('instructions')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="budget_min" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum
					Budget</label>
				<input id="budget_min" name="budget_min" type="number" min="0" step="0.01" value="{{ $budgetMin }}"
					placeholder="e.g., 500.00"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('budget_min')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="budget_max" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum
					Budget</label>
				<input id="budget_max" name="budget_max" type="number" min="0" step="0.01" value="{{ $budgetMax }}"
					placeholder="e.g., 1500.00"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('budget_max')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<x-form.date-picker
					id="start_date"
					name="start_date"
					label="Start Date"
					placeholder="Select start date"
					:default-date="old('start_date', $campaign?->start_date?->format('Y-m-d'))"
				/>
				@error('start_date')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<x-form.date-picker
					id="end_date"
					name="end_date"
					label="End Date"
					placeholder="Select end date"
					:default-date="old('end_date', $campaign?->end_date?->format('Y-m-d'))"
				/>
				@error('end_date')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div>
			<label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target Categories</label>
			<div class="grid grid-cols-1 gap-2 rounded-lg border border-gray-200 p-3 sm:grid-cols-2 dark:border-gray-700">
				@forelse ($categoryOptions as $option)
					<label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
						<input type="checkbox" name="categories[]" value="{{ $option['id'] }}"
							{{ in_array((int) $option['id'], $selectedCategoryIds, true) ? 'checked' : '' }}
							class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
						<span>{{ $option['name'] }}</span>
					</label>
				@empty
					<p class="text-sm text-gray-500 dark:text-gray-400">No active categories available.</p>
				@endforelse
			</div>
			@error('categories')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
			@error('categories.*')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target Follower Ranges</label>
			<div class="grid grid-cols-1 gap-2 rounded-lg border border-gray-200 p-3 sm:grid-cols-2 dark:border-gray-700">
				@forelse ($followerRangeOptions as $option)
					<label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
						<input type="checkbox" name="follower_ranges[]" value="{{ $option['id'] }}"
							{{ in_array((int) $option['id'], $selectedFollowerRangeIds, true) ? 'checked' : '' }}
							class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
						<span>{{ $option['label'] }}</span>
					</label>
				@empty
					<p class="text-sm text-gray-500 dark:text-gray-400">No follower ranges available.</p>
				@endforelse
			</div>
			@error('follower_ranges')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
			@error('follower_ranges.*')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="relative" @click.away="countryDropdownOpen = false">
			<label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Target Countries <span class="text-gray-400 font-normal text-sm">(optional)</span>
			</label>
			<div @click="countryDropdownOpen = !countryDropdownOpen"
				class="flex min-h-11.5 w-full cursor-pointer flex-wrap gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs transition focus-within:border-pink-50 focus-within:ring-1 focus-within:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
				<template x-if="selectedCountryCodes.length === 0">
					<span class="py-1 text-sm text-gray-400">Select countries...</span>
				</template>
				<template x-for="code in selectedCountryCodes" :key="`selected-country-${code}`">
					<span class="flex items-center gap-2 rounded-md bg-purple-400 px-3 py-1 text-sm font-normal text-white dark:text-gray-800">
						<span x-text="getCountryName(code)"></span>
						<svg class="h-3 w-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"
							@click.stop="toggleCountry(code)">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</span>
				</template>
				<div class="ml-auto flex items-center text-gray-500 dark:text-gray-400">
					<svg class="h-4 w-4 transition-transform" :class="countryDropdownOpen ? 'rotate-180' : ''" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
					</svg>
				</div>
			</div>
			<div x-show="countryDropdownOpen" x-cloak
				class="custom-scrollbar absolute left-0 top-full z-50 mt-2 max-h-48 w-full overflow-y-auto rounded-lg border border-purple-100 bg-white shadow-md dark:border-gray-800 dark:bg-gray-900">
				<div class="flex flex-wrap gap-2 p-3">
					<template x-for="option in countryOptions" :key="`country-option-${option.code}`">
						<button type="button" class="rounded-md px-3 py-2 text-sm font-medium leading-tight transition-all"
							@click="toggleCountry(option.code)"
							:class="isCountrySelected(option.code) ?
							    'bg-black text-white dark:bg-purple-400 dark:text-gray-800' :
							    'bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
							<span x-text="option.name"></span>
						</button>
					</template>
				</div>
			</div>
			<template x-for="code in selectedCountryCodes" :key="`hidden-country-${code}`">
				<input type="hidden" name="target_countries[]" :value="code">
			</template>
			<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select one or multiple countries with chips.</p>
			@error('target_countries')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
			@error('target_countries.*')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>

	<div class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
		<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Targeting and Flags</h4>

		<div>
			<label for="influencer_count" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Influencer
				Count</label>
			<input id="influencer_count" name="influencer_count" type="number" min="1"
				value="{{ old('influencer_count', $targeting?->influencer_count) }}"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('influencer_count')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="target_gender" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Target
				Gender</label>
			<select id="target_gender" name="target_gender"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
				@foreach ($genderOptions as $option)
					<option value="{{ $option['value'] }}"
						{{ old('target_gender', $targeting?->target_gender ?? 'any') === $option['value'] ? 'selected' : '' }}>
						{{ $option['label'] }}
					</option>
				@endforeach
			</select>
			@error('target_gender')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
			<div>
				<label for="age_min" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Age</label>
				<input id="age_min" name="age_min" type="number" min="13" max="100"
					value="{{ old('age_min', $targeting?->age_min) }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
				@error('age_min')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
			<div>
				<label for="age_max" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum Age</label>
				<input id="age_max" name="age_max" type="number" min="13" max="100"
					value="{{ old('age_max', $targeting?->age_max) }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
				@error('age_max')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div>
			<label for="targeting_notes" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Targeting
				Notes</label>
			<textarea id="targeting_notes" name="targeting_notes" rows="4" placeholder="Optional targeting notes"
			 class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('targeting_notes', $targeting?->notes) }}</textarea>
			@error('targeting_notes')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex min-h-11 cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<span class="flex h-11 items-center">
					<input id="is_active" name="is_active" type="checkbox" value="1"
						{{ old('is_active', $campaign?->is_active ?? true) ? 'checked' : '' }}
						class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
				</span>
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>
</div>
