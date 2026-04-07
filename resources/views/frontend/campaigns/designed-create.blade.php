@extends('frontend.layouts.app')

@section('title', 'Create Campaign')

@section('content')
	@php
		$stepTwoFields = [
			'title',
			'description',
			'instructions',
			'status',
			'currency',
			'budget_min',
			'budget_max',
			'start_date',
			'end_date',
		];

		$requestedStep = (int) old('wizard_step', 1);
		$hasStepTwoErrors = collect($stepTwoFields)->contains(static fn(string $field): bool => $errors->has($field));
		$initialStep = $hasStepTwoErrors ? max($requestedStep, 2) : max($requestedStep, 1);
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Create Campaign" />

	<div class="max-w-6xl px-2 py-2 transition-colors duration-300"
		x-data="campaignDesignedWizard({
			step: @js($initialStep),
			campaignType: @js(old('campaign_type', 'instagram')),
			campaignTypeOptions: @js($campaignTypeOptions),
			statusOptions: @js($statusOptions),
			genderOptions: @js($genderOptions),
			categoryOptions: @js($categoryOptions->values()),
			followerRangeOptions: @js($followerRangeOptions->values()),
			countryOptions: @js($countryOptions),
			selectedCategoryIds: @js(array_values(array_unique(array_map('intval', (array) old('categories', []))))),
			selectedFollowerRangeIds: @js(array_values(array_unique(array_map('intval', (array) old('follower_ranges', []))))),
			selectedCountryCodes: @js(array_values(array_unique(array_map(static function ($code): string { return strtoupper((string) $code); }, (array) old('target_countries', []))))),
			influencerCount: @js((string) old('influencer_count', '1')),
			isAdvancedOpen: @js(old('target_gender') || old('age_min') || old('age_max') || old('targeting_notes')),
		})">
		<div class="mb-6">
			@include('backend.pages.campaigns._alerts')
		</div>

		<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
			<div class="flex items-center gap-12 border-b border-gray-100 pb-4 dark:border-gray-800">
				<div class="flex items-center gap-3">
					<span class="flex h-8 w-8 items-center justify-center rounded-full text-base font-bold"
						:class="step === 1 ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'bg-gray-200 text-gray-500 dark:bg-gray-800'">1</span>
					<span class="text-base font-bold text-gray-800 dark:text-gray-400">Set Campaign Targeting</span>
				</div>
				<div class="flex items-center gap-3">
					<span class="flex h-8 w-8 items-center justify-center rounded-full border-2 text-base font-bold transition-all"
						:class="step === 2 ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'border-gray-300 text-gray-400 dark:border-gray-800'">2</span>
					<span class="text-base font-bold text-gray-400 dark:text-gray-600" :class="step === 2 ? 'text-gray-800 dark:text-gray-400' : ''">Enter Campaign Details</span>
				</div>
			</div>

			<div class="flex flex-wrap items-center gap-2">
				<a href="{{ route('frontend.campaigns.index') }}"
					class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Back to Campaign List
				</a>
			</div>
		</div>

		<form action="{{ route('frontend.campaigns.store') }}" method="POST" novalidate>
			@csrf
			<input type="hidden" name="ui_variant" value="designed">
			<input type="hidden" name="wizard_step" x-model="step">
			<input type="hidden" name="is_active" value="0">

			@php
				$selectedBrandId = auth()->user()->brand?->id ?? 0;
			@endphp

			<input type="hidden" name="brand_id" value="{{ $selectedBrandId }}">

			<div x-show="step === 1" x-cloak x-transition class="grid grid-cols-1 items-start gap-8 md:grid-cols-[1fr_380px]">
				<div class="space-y-6">
					<header>
						<h1 class="text-2xl font-medium text-gray-800 dark:text-white">Let's set your targeting details.</h1>
						<p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">Provide some details on influencers you're looking to target.</p>
					</header>

					<div class="space-y-6">
						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What type of campaign do you want to run?</label>
							<select name="campaign_type" x-model="campaignType"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								@foreach ($campaignTypeOptions as $option)
									<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
								@endforeach
							</select>
							@error('campaign_type')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">How many influencers do you want to hire for this campaign?
								<span class="text-sm font-normal text-gray-400">(optional)</span>
							</label>
							<input type="number" min="1" name="influencer_count" x-model="influencerCount"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
							@error('influencer_count')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="relative" @click.away="showCategoryDropdown = false">
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What niches do you want to target?
								<span class="text-sm font-normal text-gray-400">(optional)</span>
							</label>
							<div @click="showCategoryDropdown = !showCategoryDropdown"
								class="flex w-full flex-wrap gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs focus-within:border-pink-50 focus-within:ring-1 focus-within:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 cursor-pointer min-h-[48px]">
								<template x-if="selectedCategoryIds.length === 0">
									<span class="py-1 text-sm text-gray-400">Select niches...</span>
								</template>
								<template x-for="categoryId in selectedCategoryIds" :key="`category-${categoryId}`">
									<span class="flex items-center gap-4 rounded-md bg-purple-400 px-3 py-1 text-sm font-normal text-white dark:text-gray-800">
										<span x-text="getCategoryName(categoryId)"></span>
										<svg @click.stop="toggleSelection('selectedCategoryIds', categoryId)" class="h-3 w-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
									</span>
								</template>
							</div>

							<div x-show="showCategoryDropdown" x-cloak
								class="custom-scrollbar absolute top-full z-50 mt-2 max-h-32 w-full overflow-y-auto rounded-lg border border-purple-100 bg-white shadow-md dark:border-gray-800 dark:bg-gray-900">
								<div class="flex flex-wrap gap-4 p-4">
									<template x-for="option in categoryOptions" :key="`option-category-${option.id}`">
										<button type="button" @click="toggleSelection('selectedCategoryIds', option.id)"
											:class="selectedCategoryIds.includes(Number(option.id)) ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'"
											class="rounded-md px-2 py-2 text-center text-sm font-medium leading-tight transition-all">
											<span x-text="option.name"></span>
										</button>
									</template>
								</div>
							</div>

							<template x-for="categoryId in selectedCategoryIds" :key="`category-input-${categoryId}`">
								<input type="hidden" name="categories[]" :value="categoryId">
							</template>
							@error('categories')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
							@error('categories.*')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="relative" @click.away="showFollowerDropdown = false">
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What follower ranges do you want to target?
								<span class="text-sm font-normal text-gray-400">(optional)</span>
							</label>
							<div @click="showFollowerDropdown = !showFollowerDropdown"
								class="flex min-h-[46px] w-full cursor-pointer flex-wrap gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs focus-within:border-pink-50 focus-within:ring-1 focus-within:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<template x-if="selectedFollowerRangeIds.length === 0">
									<span class="py-1 text-sm text-gray-400">Select follower ranges...</span>
								</template>
								<template x-for="rangeId in selectedFollowerRangeIds" :key="`range-${rangeId}`">
									<span class="flex items-center gap-3 rounded-md bg-purple-400 px-3 py-1 text-sm font-normal text-white dark:text-gray-800">
										<span x-text="getFollowerRangeLabel(rangeId)"></span>
										<svg @click.stop="toggleSelection('selectedFollowerRangeIds', rangeId)" class="h-3 w-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
									</span>
								</template>
							</div>

							<div x-show="showFollowerDropdown" x-cloak
								class="custom-scrollbar absolute top-full z-50 mt-2 max-h-40 w-full overflow-y-auto rounded-lg border border-purple-100 bg-white shadow-md dark:border-gray-800 dark:bg-gray-900">
								<div class="flex flex-wrap gap-3 p-4">
									<template x-for="option in followerRangeOptions" :key="`option-range-${option.id}`">
										<button type="button" @click="toggleSelection('selectedFollowerRangeIds', option.id)"
											:class="selectedFollowerRangeIds.includes(Number(option.id)) ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'"
											class="rounded-md px-3 py-2 text-center text-sm font-medium leading-tight transition-all">
											<span x-text="option.label"></span>
										</button>
									</template>
								</div>
							</div>

							<template x-for="rangeId in selectedFollowerRangeIds" :key="`range-input-${rangeId}`">
								<input type="hidden" name="follower_ranges[]" :value="rangeId">
							</template>
							@error('follower_ranges')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
							@error('follower_ranges.*')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="relative" @click.away="showCountryDropdown = false">
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What countries do you want to target?
								<span class="text-sm font-normal text-gray-400">(optional)</span>
							</label>
							<div @click="showCountryDropdown = !showCountryDropdown"
								class="flex min-h-[46px] w-full cursor-pointer flex-wrap gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs focus-within:border-pink-50 focus-within:ring-1 focus-within:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<template x-if="selectedCountryCodes.length === 0">
									<span class="py-1 text-sm text-gray-400">Select countries...</span>
								</template>
								<template x-for="countryCode in selectedCountryCodes" :key="`country-${countryCode}`">
									<span class="flex items-center gap-3 rounded-md bg-purple-400 px-3 py-1 text-sm font-normal text-white dark:text-gray-800">
										<span x-text="getCountryName(countryCode)"></span>
										<svg @click.stop="toggleSelection('selectedCountryCodes', countryCode)" class="h-3 w-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
									</span>
								</template>
							</div>

							<div x-show="showCountryDropdown" x-cloak
								class="custom-scrollbar absolute top-full z-50 mt-2 max-h-40 w-full overflow-y-auto rounded-lg border border-purple-100 bg-white shadow-md dark:border-gray-800 dark:bg-gray-900">
								<div class="flex flex-wrap gap-3 p-4">
									<template x-for="option in countryOptions" :key="`option-country-${option.code}`">
										<button type="button" @click="toggleSelection('selectedCountryCodes', option.code)"
											:class="selectedCountryCodes.includes(option.code) ? 'bg-black text-white dark:bg-purple-400 dark:text-gray-800' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'"
											class="rounded-md px-3 py-2 text-center text-sm font-medium leading-tight transition-all">
											<span x-text="option.name"></span>
										</button>
									</template>
								</div>
							</div>

							<template x-for="countryCode in selectedCountryCodes" :key="`country-input-${countryCode}`">
								<input type="hidden" name="target_countries[]" :value="countryCode">
							</template>
							@error('target_countries')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
							@error('target_countries.*')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="border-t border-gray-100 pt-6 dark:border-gray-800">
							<button type="button" @click="isAdvancedOpen = !isAdvancedOpen" class="group flex w-full items-center justify-between">
								<span class="text-base font-bold uppercase tracking-widest text-gray-800 dark:text-gray-400">Advanced Filters</span>
								<svg class="h-5 w-5 text-purple-500 transition-transform" :class="isAdvancedOpen ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 4v16m8-8H4" /></svg>
							</button>
							<div x-show="isAdvancedOpen" x-collapse class="mt-6 space-y-6">
								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">What genders do you want to target?</label>
									<select name="target_gender"
										class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
										@foreach ($genderOptions as $option)
											<option value="{{ $option['value'] }}" {{ old('target_gender', 'any') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
										@endforeach
									</select>
									@error('target_gender')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>

								<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
									<div>
										<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Minimum age</label>
										<input type="number" name="age_min" min="13" max="100" value="{{ old('age_min') }}"
											class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
										@error('age_min')
											<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
										@enderror
									</div>
									<div>
										<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Maximum age</label>
										<input type="number" name="age_max" min="13" max="100" value="{{ old('age_max') }}"
											class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
										@error('age_max')
											<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
										@enderror
									</div>
								</div>

								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Targeting notes</label>
									<textarea name="targeting_notes" rows="4" placeholder="Optional targeting notes"
										class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('targeting_notes') }}</textarea>
									@error('targeting_notes')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="space-y-6 md:sticky md:top-24">
					<div class="rounded-lg border border-gray-100 bg-gray-100 p-8 shadow-md shadow-gray-900/5 dark:border-gray-900/30 dark:bg-gray-950/20">
						<h3 class="mb-8 text-lg font-medium text-gray-800 dark:text-gray-300">Estimated Results</h3>

						<div class="space-y-10">
							<div class="flex items-center gap-5">
								<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-500 shadow-sm dark:bg-gray-800">
									<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
								</div>
								<div>
									<p class="text-2xl font-bold leading-none text-gray-800 dark:text-white" x-text="getEstimate().influencers"></p>
									<p class="mt-1 text-[10px] font-medium uppercase text-gray-500">Influencers Match</p>
								</div>
							</div>

							<div class="flex items-center gap-5">
								<div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-gray-500 shadow-sm dark:bg-gray-800">
									<svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
								</div>
								<div>
									<p class="text-2xl font-bold leading-none text-gray-800 dark:text-white" x-text="getEstimate().reach"></p>
									<p class="mt-1 text-[10px] font-medium uppercase text-gray-500">Followers Reached</p>
								</div>
							</div>
						</div>

						<button type="button" @click="step = 2" class="mt-4 w-full rounded-lg bg-[#222] py-4 text-lg font-bold text-white shadow-sm transition hover:bg-purple-400 active:scale-95">
							Continue
						</button>
					</div>

					<p class="px-6 text-center text-sm leading-relaxed text-gray-400">Adjust your targeting to see real-time updates on potential reach and influencer matches.</p>
				</div>
			</div>

			<div x-show="step === 2" x-cloak x-transition class="max-w-4xl space-y-12">
				<header>
					<h1 class="text-2xl font-medium text-gray-800 dark:text-white">Campaign Details</h1>
					<p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">Define the specifics of your campaign content.</p>
				</header>

				<div class="space-y-6">
					<div>
						<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Campaign Title</label>
						<input type="text" name="title" value="{{ old('title') }}" placeholder="Summer 2026 Influencer Push"
							class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
						@error('title')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Campaign Description</label>
						<textarea name="description" rows="4" placeholder="Describe your product and the value of this campaign..."
							class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description') }}</textarea>
						@error('description')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Product Instructions</label>
						<textarea name="instructions" rows="6" placeholder="Describe what you want influencers to do, key messages, and deliverables..."
							class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('instructions') }}</textarea>
						@error('instructions')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
						<div class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800">
							<p class="mb-2 text-xs font-bold uppercase text-gray-400">Selected Type</p>
							<p class="text-xl font-bold capitalize text-gray-800 dark:text-purple-400" x-text="getCampaignTypeLabel()"></p>
						</div>
						<div class="rounded-xl border border-gray-100 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900">
							<p class="mb-2 text-xs font-bold uppercase tracking-widest text-gray-400">Hiring Limit</p>
							<p class="text-xl font-bold text-gray-800 dark:text-purple-400"><span x-text="influencerCount || 'Not set'"></span> Influencers</p>
						</div>
					</div>

					<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Status</label>
							<select name="status"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								@foreach ($statusOptions as $option)
									<option value="{{ $option['value'] }}" {{ old('status', 'draft') === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
								@endforeach
							</select>
							@error('status')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Currency</label>
							<input type="text" name="currency" maxlength="3" value="{{ old('currency', 'USD') }}"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 uppercase text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
							@error('currency')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
							<label class="flex cursor-pointer items-center justify-between gap-3">
								<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
								<input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
									class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
							</label>
						</div>
					</div>

					<div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Minimum Budget</label>
							<input type="number" name="budget_min" min="0" step="0.01" value="{{ old('budget_min') }}"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
							@error('budget_min')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Maximum Budget</label>
							<input type="number" name="budget_max" min="0" step="0.01" value="{{ old('budget_max') }}"
								class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
							@error('budget_max')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">Start Date</label>
							<div class="relative">
								<input type="date" name="start_date" value="{{ old('start_date') }}"
									class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-10 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
									<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
									</svg>
								</span>
							</div>
							@error('start_date')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">End Date</label>
							<div class="relative">
								<input type="date" name="end_date" value="{{ old('end_date') }}"
									class="dark:bg-dark-900 shadow-theme-xs focus:border-pink-50 focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 pl-10 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
									<svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" fill="none" class="size-5">
										<path fill-rule="evenodd" clip-rule="evenodd" d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V9V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V9V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2ZM8 5.25H5.5C5.08579 5.25 4.75 5.58579 4.75 6V8.25H19.25V6C19.25 5.58579 18.9142 5.25 18.5 5.25H16H8ZM19.25 9.75H4.75V19C4.75 19.4142 5.08579 19.75 5.5 19.75H18.5C18.9142 19.75 19.25 19.4142 19.25 19V9.75Z" fill="currentColor"></path>
									</svg>
								</span>
							</div>
							@error('end_date')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="flex flex-col gap-4 pt-10">
						<button type="submit" class="w-full rounded-lg bg-[#222] py-4 text-xlg font-bold uppercase tracking-[0.2em] text-white shadow-sm transition hover:bg-purple-500 hover:opacity-90 active:scale-95 dark:bg-purple-400 dark:text-gray-800">
							Publish Campaign
						</button>
						<button type="button" @click="step = 1" class="text-sm font-medium text-gray-500 transition hover:text-gray-800 dark:hover:text-gray-200">Back to Edit Targeting</button>
					</div>
				</div>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function campaignDesignedWizard(config) {
				return {
					step: Number(config.step || 1),
					campaignType: config.campaignType || 'instagram',
					campaignTypeOptions: Array.isArray(config.campaignTypeOptions) ? config.campaignTypeOptions : [],
					statusOptions: Array.isArray(config.statusOptions) ? config.statusOptions : [],
					genderOptions: Array.isArray(config.genderOptions) ? config.genderOptions : [],
					categoryOptions: Array.isArray(config.categoryOptions) ? config.categoryOptions : [],
					followerRangeOptions: Array.isArray(config.followerRangeOptions) ? config.followerRangeOptions : [],
					countryOptions: Array.isArray(config.countryOptions) ? config.countryOptions : [],
					selectedCategoryIds: Array.isArray(config.selectedCategoryIds) ? config.selectedCategoryIds.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0) : [],
					selectedFollowerRangeIds: Array.isArray(config.selectedFollowerRangeIds) ? config.selectedFollowerRangeIds.map((id) => Number(id)).filter((id) => Number.isInteger(id) && id > 0) : [],
					selectedCountryCodes: Array.isArray(config.selectedCountryCodes) ? config.selectedCountryCodes.map((code) => String(code).toUpperCase()) : [],
					influencerCount: config.influencerCount || '',
					isAdvancedOpen: Boolean(config.isAdvancedOpen),
					showCategoryDropdown: false,
					showFollowerDropdown: false,
					showCountryDropdown: false,

					toggleSelection(arrayName, value) {
						if (!Array.isArray(this[arrayName])) {
							return;
						}

						const normalizedValue = arrayName === 'selectedCountryCodes'
							? String(value).toUpperCase()
							: Number(value);

						if (this[arrayName].includes(normalizedValue)) {
							this[arrayName] = this[arrayName].filter((item) => item !== normalizedValue);
							return;
						}

						this[arrayName] = [...this[arrayName], normalizedValue];
					},

					getCategoryName(categoryId) {
						const option = this.categoryOptions.find((item) => Number(item.id) === Number(categoryId));
						return option ? option.name : `Category ${categoryId}`;
					},

					getFollowerRangeLabel(rangeId) {
						const option = this.followerRangeOptions.find((item) => Number(item.id) === Number(rangeId));
						return option ? option.label : `Range ${rangeId}`;
					},

					getCountryName(countryCode) {
						const option = this.countryOptions.find((item) => String(item.code).toUpperCase() === String(countryCode).toUpperCase());
						return option ? `${option.name} (${option.code})` : countryCode;
					},

					getCampaignTypeLabel() {
						const option = this.campaignTypeOptions.find((item) => item.value === this.campaignType);
						return option ? option.label : this.campaignType;
					},

					getEstimate() {
						const count = Math.max(parseInt(this.influencerCount || '1', 10) || 1, 1);
						const nicheFactor = Math.max(this.selectedCategoryIds.length, 1);
						const rangeFactor = Math.max(this.selectedFollowerRangeIds.length, 1);
						const countryFactor = Math.max(this.selectedCountryCodes.length, 1);

						// Realistic estimation based on typical influencer marketplace data
						// Average: 15-45 influencers per niche, adjusted by targeting specificity
						const baseInfluencersPerNiche = 25;
						const nicheMultiplier = Math.min(nicheFactor, 3); // Diminishing returns after 3 niches
						const rangeMultiplier = Math.min(rangeFactor, 2); // Diminishing returns after 2 ranges
						const countryMultiplier = Math.min(countryFactor, 5); // Cap at 5 major markets

						// Calculate estimated matches
						const minInfluencers = Math.round(count * baseInfluencersPerNiche * nicheMultiplier * 0.4);
						const maxInfluencers = Math.round(count * baseInfluencersPerNiche * nicheMultiplier * rangeMultiplier * countryMultiplier * 0.9);

						// Average followers per influencer: 35K-280K depending on range
						const avgFollowersMin = 35000;
						const avgFollowersMax = 280000;
						const reachMin = Math.round((minInfluencers * avgFollowersMin) / 1000000 * 10) / 10;
						const reachMax = Math.round((maxInfluencers * avgFollowersMax) / 1000000 * 10) / 10;

						return {
							influencers: minInfluencers === maxInfluencers ? `${minInfluencers}` : `${minInfluencers}-${maxInfluencers}`,
							reach: reachMin === reachMax ? `${reachMin}M` : `${reachMin}-${reachMax}M`
						};
					}
				};
			}
		</script>
	@endpush

	<style>
		[x-cloak] {
			display: none !important;
		}

		.custom-scrollbar::-webkit-scrollbar {
			width: 4px;
		}

		.custom-scrollbar::-webkit-scrollbar-track {
			background: transparent;
		}

		.custom-scrollbar::-webkit-scrollbar-thumb {
			background: #e5e7eb;
			border-radius: 10px;
		}

		.dark .custom-scrollbar::-webkit-scrollbar-thumb {
			background: #374151;
		}
	</style>
@endsection
