<div class="grid gap-6">
	<div>
		<label for="section_code" class="block text-sm font-medium text-gray-900 dark:text-white">Section Code <span class="text-red-500">*</span></label>
		<input type="text" id="section_code" name="section_code" value="{{ old('section_code', $section->section_code ?? '') }}" required placeholder="e.g., for_influencers, for_brands"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier for this section</p>
		@error('section_code')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="section_title" class="block text-sm font-medium text-gray-900 dark:text-white">Section Title <span class="text-red-500">*</span></label>
		<input type="text" id="section_title" name="section_title" value="{{ old('section_title', $section->section_title ?? '') }}" required placeholder="e.g., For Influencers"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('section_title')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="audience_type" class="block text-sm font-medium text-gray-900 dark:text-white">Audience Type <span class="text-red-500">*</span></label>
		<select id="audience_type" name="audience_type" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			<option value="">Select audience type</option>
			<option value="all" {{ old('audience_type', $section->audience_type ?? '') === 'all' ? 'selected' : '' }}>All (visible to everyone)</option>
			<option value="brand" {{ old('audience_type', $section->audience_type ?? '') === 'brand' ? 'selected' : '' }}>Brands only</option>
			<option value="influencer" {{ old('audience_type', $section->audience_type ?? '') === 'influencer' ? 'selected' : '' }}>Influencers only</option>
		</select>
		@error('audience_type')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
		<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $section->sort_order ?? 0) }}" min="0"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('sort_order')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>
</div>
