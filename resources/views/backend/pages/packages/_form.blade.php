@php
	/** @var \App\Models\Package|null $package */
	$package = $package ?? null;
	$resolvedCurrency = strtoupper((string) old('currency', $package?->currency ?? 'USD'));
	$isInfluencer = $isInfluencer ?? false;
	$influencers = $influencers ?? null;
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<!-- Influencer Selection (Admin/Moderator Only) -->
		@if (!$isInfluencer && $influencers)
			<div>
				<label for="created_for" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Create For (Select Influencer) <span class="text-red-500">*</span>
				</label>
				<select id="created_for" name="created_for" required
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					<option value="" disabled {{ old('created_for', $package?->influencer_id) ? '' : 'selected' }}>Select an influencer</option>
					@foreach ($influencers as $influencer)
						<option value="{{ $influencer->id }}"
							{{ old('created_for', $package?->influencer_id) == $influencer->id ? 'selected' : '' }}>
							{{ $influencer->user?->name ?? $influencer->display_name ?? 'Unknown' }}
						</option>
					@endforeach
				</select>
				@error('created_for')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		@elseif ($isInfluencer && $package)
			<!-- Show Influencer Info (Read-only for influencers) -->
			<div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Your Package</p>
				<p class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
					This package belongs to you
				</p>
			</div>
		@endif

		<div>
			<label for="platform" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Platform <span class="text-red-500">*</span>
			</label>
			<select id="platform" name="platform" required
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				<option value="" disabled {{ old('platform', $package?->platform) ? '' : 'selected' }}>Select platform</option>
				@foreach ($platformOptions as $option)
					<option value="{{ $option['value'] }}"
						{{ old('platform', $package?->platform) === $option['value'] ? 'selected' : '' }}>
						{{ $option['label'] }}
					</option>
				@endforeach
			</select>
			@error('platform')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Package Name <span class="text-red-500">*</span>
			</label>
			<input id="name" name="name" type="text" value="{{ old('name', $package?->name) }}"
				placeholder="e.g., 3 Instagram Reels + Story" required
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('name')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
			<textarea id="description" name="description" rows="5" placeholder="Optional package details"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description', $package?->description) }}</textarea>
			@error('description')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>

	<div class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
		<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Pricing and Delivery</h4>

		<div>
			<label for="base_price" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Base Price <span class="text-red-500">*</span>
			</label>
			<input id="base_price" name="base_price" type="number" step="0.01" min="0"
				value="{{ old('base_price', $package?->base_price) }}" required
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('base_price')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="currency" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Currency (3-letter code) <span class="text-red-500">*</span>
			</label>
			<input id="currency" name="currency" type="text" maxlength="3" value="{{ $resolvedCurrency }}" required
				oninput="this.value = this.value.toUpperCase()" placeholder="USD"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm uppercase text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('currency')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="delivery_days" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery
				Days</label>
			<input id="delivery_days" name="delivery_days" type="number" min="1"
				value="{{ old('delivery_days', $package?->delivery_days) }}" placeholder="e.g., 7"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('delivery_days')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="revisions_included" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Revisions
				Included</label>
			<input id="revisions_included" name="revisions_included" type="number" min="0"
				value="{{ old('revisions_included', $package?->revisions_included) }}" placeholder="e.g., 2"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('revisions_included')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex min-h-11 cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<span class="flex h-11 items-center">
					<input id="is_active" name="is_active" type="checkbox" value="1"
						{{ old('is_active', $package?->is_active ?? true) ? 'checked' : '' }}
						class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
				</span>
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>
</div>
