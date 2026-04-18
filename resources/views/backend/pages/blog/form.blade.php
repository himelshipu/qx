<div class="grid grid-cols-1 gap-6 lg:grid-cols-3" x-data="blogImageUploader({ featuredImage: @js($featuredImageUrl) })">
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title *</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $post->title ?? '') }}" required
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $post->slug ?? '') }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    @error('slug')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sort_order" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $post->sort_order ?? $nextSortOrder ?? 0) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    @error('sort_order')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="excerpt" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="4"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
                    @error('excerpt')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="content" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Content *</label>
                    <div id="editor-container" class="border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-800" style="height: 400px;"></div>
                    <textarea id="content" name="content" class="hidden">{{ old('content', $post->content ?? '') }}</textarea>
                    @error('content')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="meta_description" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    @error('meta_description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="meta_keywords" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta Keywords</label>
                    <textarea id="meta_keywords" name="meta_keywords" rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('meta_keywords', $post->meta_keywords ?? '') }}</textarea>
                    @error('meta_keywords')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Publishing</h3>
            <div class="mt-4 space-y-4">
                <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                    <span>
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Published</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Make this post visible publicly</span>
                    </span>
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $isPublished) ? 'checked' : '' }} class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                </label>

                <label class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
                    <span>
                        <span class="block text-sm font-medium text-gray-900 dark:text-white">Featured</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Highlight on homepage or blog listing</span>
                    </span>
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $isFeatured) ? 'checked' : '' }} class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                </label>

                <div>
                    <label for="published_at" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Published At</label>
                    <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    @error('published_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Featured Image</h3>
            <div class="mt-4">
                <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-700">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Upload Image</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF, SVG</p>
                        </div>
                        <button type="button" @click="clearFeaturedImage()" class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Clear</button>
                    </div>
                    <label for="featured_image_file" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
                        <template x-if="featuredImagePreview"><img :src="featuredImagePreview" alt="Featured image" class="mb-3 h-28 w-full max-w-65 rounded object-cover"></template>
                        <template x-if="!featuredImagePreview"><div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800"><x-icons.image class="h-6 w-6 text-gray-400" /></div></template>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Featured Image</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 6MB</p>
                        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="featuredImageName" x-text="featuredImageName"></p>
                    </label>
                    <input id="featured_image_file" x-ref="featuredInput" name="featured_image_file" type="file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml" class="hidden" @change="onFeaturedImageSelected($event)">
                    @error('featured_image_file')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
        {{ $submitButtonText }}
    </button>
</div>

@push('scripts')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        function blogImageUploader(config) {
            return {
                featuredImagePreview: config.featuredImage || null,
                featuredImageName: '',
                onFeaturedImageSelected(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.featuredImageName = file.name;
                    this.featuredImagePreview = URL.createObjectURL(file);
                },
                clearFeaturedImage() {
                    this.featuredImageName = '';
                    this.featuredImagePreview = config.featuredImage || null;
                    if (this.$refs.featuredInput) this.$refs.featuredInput.value = '';
                }
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            const contentTextarea = document.getElementById('content');
            const initialContent = contentTextarea.value;

            const quill = new Quill('#editor-container', {
                theme: 'snow',
                placeholder: 'Enter post content...',
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

            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            const form = contentTextarea.closest('form');
            if (form) {
                form.addEventListener('submit', function() {
                    contentTextarea.value = quill.root.innerHTML;
                });
            }

            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
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
        });
    </script>
@endpush
