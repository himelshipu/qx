<div class="grid gap-6">
	<div>
		<label for="title" class="block text-sm font-medium text-gray-900 dark:text-white">Title <span class="text-red-500">*</span></label>
		<input type="text" id="title" name="title" value="{{ old('title', $article->title ?? '') }}" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('title')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="slug" class="block text-sm font-medium text-gray-900 dark:text-white">Slug</label>
		<input type="text" id="slug" name="slug" value="{{ old('slug', $article->slug ?? '') }}" placeholder="auto-generated-from-title"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('slug')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="badge" class="block text-sm font-medium text-gray-900 dark:text-white">Badge</label>
		<input type="text" id="badge" name="badge" value="{{ old('badge', $article->badge ?? '') }}" placeholder="e.g., Billing, Account, Security"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
		@error('badge')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="summary" class="block text-sm font-medium text-gray-900 dark:text-white">Summary</label>
		<textarea id="summary" name="summary" rows="3" maxlength="500"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('summary', $article->summary ?? '') }}</textarea>
		@error('summary')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="content" class="block text-sm font-medium text-gray-900 dark:text-white">Article Content <span class="text-red-500">*</span></label>
		<textarea id="content" name="content" rows="10" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('content', $article->content ?? '') }}</textarea>
		@error('content')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
		<div>
			<label for="read_time_minutes" class="block text-sm font-medium text-gray-900 dark:text-white">Read Time (minutes) <span class="text-red-500">*</span></label>
			<input type="number" id="read_time_minutes" name="read_time_minutes" min="1" max="60" value="{{ old('read_time_minutes', $article->read_time_minutes ?? 5) }}" required
				class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('read_time_minutes')
				<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
			@enderror
		</div>
		<div>
			<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
			<input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $article->sort_order ?? ($nextSortOrder ?? 0)) }}"
				class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('sort_order')
				<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
			@enderror
		</div>
		<div>
			<label for="published_at" class="block text-sm font-medium text-gray-900 dark:text-white">Published At</label>
			<input type="datetime-local" id="published_at" name="published_at"
				value="{{ old('published_at', isset($article) && $article->published_at ? $article->published_at->format('Y-m-d\\TH:i') : '') }}"
				class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('published_at')
				<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
			@enderror
		</div>
	</div>

	<div class="flex items-center gap-6">
		<label class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
			<input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $article->is_featured ?? false) ? 'checked' : '' }}
				class="h-4 w-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-blue-600">
			Featured article
		</label>
		<label class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white">
			<input type="checkbox" name="is_published" value="1" {{ old('is_published', $article->is_published ?? true) ? 'checked' : '' }}
				class="h-4 w-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-blue-600">
			Published
		</label>
	</div>
</div>
