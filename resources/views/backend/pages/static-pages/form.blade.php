<!-- Static Page Form Component -->
<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <!-- Title Field -->
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Page Title *
        </label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $page->title ?? '') }}"
            placeholder="e.g., Privacy Policy"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror"
            required
        >
        @error('title')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Slug Field -->
    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Slug (URL) *
        </label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <span class="inline-flex items-center px-3 bg-gray-50 dark:bg-gray-900 border border-r-0 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 text-sm">
                /
            </span>
            <input
                type="text"
                id="slug"
                name="slug"
                value="{{ old('slug', $page->slug ?? '') }}"
                placeholder="privacy-policy"
                class="flex-1 min-w-0 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-none rounded-r-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('slug') border-red-500 @enderror"
                required
            >
        </div>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-generated from title if left blank</p>
        @error('slug')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Meta Description -->
    <div>
        <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Meta Description (for SEO)
        </label>
        <textarea
            id="meta_description"
            name="meta_description"
            rows="2"
            placeholder="Brief description for search engines (160 characters max)"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('meta_description') border-red-500 @enderror"
            maxlength="500"
        >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
        @error('meta_description')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Meta Keywords -->
    <div>
        <label for="meta_keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Meta Keywords (for SEO)
        </label>
        <input
            type="text"
            id="meta_keywords"
            name="meta_keywords"
            value="{{ old('meta_keywords', $page->meta_keywords ?? '') }}"
            placeholder="keyword1, keyword2, keyword3"
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-white @error('meta_keywords') border-red-500 @enderror"
            maxlength="500"
        >
        @error('meta_keywords')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Rich Text Editor -->
    <div>
        <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Page Content *
        </label>
        <div id="editor-container" class="border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800" style="height: 400px;"></div>
        <textarea
            id="content"
            name="content"
            style="display: none;"
            required
        >{{ old('content', $page->content ?? '') }}</textarea>
        @error('content')
            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <!-- Active Status -->
    <div class="flex items-center">
        <input
            type="checkbox"
            id="is_active"
            name="is_active"
            value="1"
            {{ old('is_active', $page->is_active ?? true) ? 'checked' : '' }}
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-800"
        >
        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
            Publish this page (make it visible)
        </label>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
        <a
            href="{{ route('dashboard.static-pages.index') }}"
            class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
        >
            Cancel
        </a>
        <button
            type="submit"
            class="inline-flex items-center px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-900"
        >
            {{ $submitButtonText }}
        </button>
    </div>
</form>

@push('scripts')
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const initialContent = contentTextarea.value;

            // Initialize Quill editor
            const quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Enter page content...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'header': 1 }, { 'header': 2 }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [false, 1, 2, 3, 4, 5, 6] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'font': [] }],
                        [{ 'align': [] }],
                        ['clean'],
                        ['link', 'image', 'video']
                    ]
                }
            });

            // Set initial content
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            // Update hidden textarea on form submit
            const form = contentTextarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    contentTextarea.value = quill.root.innerHTML;
                });
            }

            // Apply dark mode styles if needed
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                const editorContainer = document.getElementById('editor-container');
                editorContainer.classList.add('dark');
                const styleSheet = document.createElement('style');
                styleSheet.textContent = `
                    .ql-toolbar.ql-snow {
                        background-color: #111827;
                        border-color: #374151;
                    }
                    .ql-container.ql-snow {
                        background-color: #1f2937;
                        border-color: #374151;
                    }
                    .ql-editor {
                        background-color: #1f2937;
                        color: #f3f4f6;
                    }
                    .ql-editor.ql-blank::before {
                        color: #9ca3af;
                    }
                    .ql-toolbar.ql-snow .ql-picker-label,
                    .ql-toolbar.ql-snow button:hover,
                    .ql-toolbar.ql-snow button.ql-active,
                    .ql-toolbar.ql-snow .ql-picker-label:hover,
                    .ql-toolbar.ql-snow .ql-picker-item:hover,
                    .ql-toolbar.ql-snow .ql-picker-item.ql-selected {
                        color: #f3f4f6;
                    }
                    .ql-toolbar.ql-snow button {
                        color: #d1d5db;
                    }
                    .ql-toolbar.ql-snow .ql-stroke {
                        stroke: #d1d5db;
                    }
                    .ql-toolbar.ql-snow .ql-fill {
                        fill: #d1d5db;
                    }
                `;
                document.head.appendChild(styleSheet);
            }

            // Listen for theme changes
            document.addEventListener('theme-changed', function() {
                location.reload();
            });
        });
    </script>
@endpush
