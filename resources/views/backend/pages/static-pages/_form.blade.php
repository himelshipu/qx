<div class="space-y-6" id="static-page-form-root">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Page Title *</label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', data_get($page, 'title', '')) }}"
            placeholder="e.g., Privacy Policy"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            required>
        @error('title')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug (URL)</label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <span class="inline-flex items-center border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400">/</span>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', data_get($page, 'slug', '')) }}"
                placeholder="privacy-policy"
                class="block min-w-0 flex-1 rounded-none rounded-r-md border border-gray-300 px-3 py-2 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-generated from title when left blank.</p>
        @error('slug')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Description (for SEO)</label>
        <textarea
            id="meta_description"
            name="meta_description"
            rows="2"
            placeholder="Brief description for search engines"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            maxlength="{{ (int) config('static-page.meta_description_max', 500) }}">{{ old('meta_description', data_get($page, 'meta_description', '')) }}</textarea>
        @error('meta_description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Keywords (for SEO)</label>
        <input
            type="text"
            id="meta_keywords"
            name="meta_keywords"
            value="{{ old('meta_keywords', data_get($page, 'meta_keywords', '')) }}"
            placeholder="keyword1, keyword2, keyword3"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            maxlength="{{ (int) config('static-page.meta_keywords_max', 500) }}">
        @error('meta_keywords')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="content" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Page Content *</label>
        <div id="editor-container" class="rounded-md border border-gray-300 bg-white shadow-sm dark:border-gray-600 dark:bg-gray-800" style="height: 400px;"></div>
        <textarea id="content" name="content" class="hidden" required>{{ old('content', data_get($page, 'content', '')) }}</textarea>
        @error('content')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            {{ old('is_active', data_get($page, 'is_active', true)) ? 'checked' : '' }}
            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800">
        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Publish this page (make it visible)</label>
    </div>
</div>

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
@endpush
