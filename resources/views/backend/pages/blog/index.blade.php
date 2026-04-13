@extends('backend.layouts.app')

@section('title', 'Blog Posts')

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Blog Posts" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
                        <p class="mt-1 text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300"><i class="fas fa-newspaper"></i></span>
                </div>
            </div>
            <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 dark:border-green-900/40 dark:bg-green-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
                        <p class="mt-1 text-xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300"><i class="fas fa-check-circle"></i></span>
                </div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-900/40 dark:bg-amber-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">Drafts</p>
                        <p class="mt-1 text-xl font-semibold text-amber-700 dark:text-amber-200">{{ $stats['draft'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"><i class="fas fa-pen"></i></span>
                </div>
            </div>
            <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-900/40 dark:bg-indigo-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Featured</p>
                        <p class="mt-1 text-xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['featured'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300"><i class="fas fa-star"></i></span>
                </div>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-900/40 dark:bg-rose-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-rose-700 dark:text-rose-300">Trash</p>
                        <p class="mt-1 text-xl font-semibold text-rose-700 dark:text-rose-200">{{ $stats['trashed'] }}</p>
                    </div>
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300"><i class="fas fa-trash"></i></span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 border-b border-gray-200 p-5 lg:flex-row lg:items-center lg:justify-between dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Blog Management</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Publish articles, feature stories, and manage the editorial queue.</p>
                </div>
                <a href="{{ route('dashboard.blogs.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <x-icons.plus class="h-4 w-4" />
                    New Post
                </a>
            </div>

            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <form method="GET" class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <input type="search" name="q" value="{{ $search }}" placeholder="Search title, slug, or excerpt..."
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                    <div class="flex gap-3">
                        <select name="status" class="h-11 flex-1 rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" @selected($status === 'all')>All</option>
                            <option value="published" @selected($status === 'published')>Published</option>
                            <option value="draft" @selected($status === 'draft')>Draft</option>
                            <option value="trashed" @selected($status === 'trashed')>Trash</option>
                        </select>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Filter</button>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-245">
                    <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Post</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Author</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Published</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($posts as $post)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/40">
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
                                    <div class="flex flex-wrap gap-2">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $post->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">
                                            {{ $post->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        @if ($post->is_featured)
                                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Featured</span>
                                        @endif
                                    </div>
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
                                            <form action="{{ route('dashboard.blogs.restore', $post->slug) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-gray-200 p-2 text-emerald-600 transition hover:bg-emerald-50 dark:border-gray-700 dark:hover:bg-emerald-900/20" title="Restore">
                                                    <span class="text-xs font-semibold">Restore</span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('dashboard.blogs.toggle-status', $post) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                                    {{ $post->is_published ? 'Unpublish' : 'Publish' }}
                                                </button>
                                            </form>
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
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No blog posts found.</td>
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
    </div>
@endsection
