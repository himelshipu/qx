<div class="grid gap-6">
	<div>
		<label for="question" class="block text-sm font-medium text-gray-900 dark:text-white">Question <span class="text-red-500">*</span></label>
		<input type="text" id="question" name="question" value="{{ old('question', $item->question ?? '') }}" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('question')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="answer" class="block text-sm font-medium text-gray-900 dark:text-white">Answer <span class="text-red-500">*</span></label>
		<textarea id="answer" name="answer" rows="8" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('answer', $item->answer ?? '') }}</textarea>
		@error('answer')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
		<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('sort_order')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>
</div>
