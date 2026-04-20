<div id="blog-results" data-pagination-container>
    <div class="overflow-x-auto">
        <table class="w-full min-w-245">
            <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Post</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Author</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Featured</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Published</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
                        <td class="px-4 py-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ (int) ($posts->firstItem() ?? 1) + $loop->index }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
                                    @if ($post->featured_image_path)
                                        <img src="{{ asset('storage/' . $post->featured_image_path) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold text-gray-400">No Image</div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $post->title }}</p>
                                    <p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $post->summary }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $post->author?->name ?? 'System' }}</td>
                        <td class="px-4 py-4">
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" data-status-toggle data-id="{{ $post->id }}" {{ $post->is_published ? 'checked' : '' }} class="peer sr-only" />
                                <div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
                            </label>
                        </td>
                        <td class="px-4 py-4">
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" data-featured-toggle data-id="{{ $post->id }}" {{ $post->is_featured ? 'checked' : '' }} class="peer sr-only" />
                                <div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-indigo-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-indigo-300 dark:bg-gray-700 dark:peer-focus:ring-indigo-800"></div>
                            </label>
                        </td>
                        <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $post->published_at?->format('M d, Y') ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('dashboard.blogs.show', $post) }}" class="rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800" title="View">
                                    <x-icons.eye class="h-4 w-4" />
                                </a>
                                <a href="{{ route('dashboard.blogs.edit', $post) }}" class="rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800" title="Edit">
                                    <x-icons.edit class="h-4 w-4" />
                                </a>
                                @if ($status === 'trashed')
                                    <form action="{{ route('dashboard.blogs.restore', $post->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-gray-200 p-2 text-emerald-600 transition hover:bg-emerald-50 dark:border-gray-700 dark:hover:bg-emerald-900/20" title="Restore">
                                            <span class="text-xs font-semibold">Restore</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('dashboard.blogs.destroy', $post) }}" method="POST" class="js-confirmable" data-confirm-title="Delete Blog Post" data-confirm-message="Move this post to trash?" data-confirm-button="Delete" data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-red-50 hover:text-red-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-red-900/20 dark:hover:text-red-300" title="Delete">
                                            <x-icons.trash class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                            No blog posts found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($posts->hasPages())
        <div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
            {{ $posts->links() }}
        </div>
    @endif
</div>