@extends('backend.layouts.app')

@section('title', $post->title)

@section('content')
    <x-backend.shell.breadcrumb pageTitle="Blog Post" />

    <div class="space-y-6">
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="relative bg-gray-100 dark:bg-gray-800">
                @if ($post->featured_image_path)
                    <div class="absolute inset-0 opacity-30">
                        <img src="{{ asset('storage/' . $post->featured_image_path) }}" alt="{{ $post->title }}" class="h-full w-full object-cover blur-xl">
                    </div>
                    <div class="relative mx-auto aspect-16/7 w-full max-w-6xl p-4 sm:p-6">
                        <img src="{{ asset('storage/' . $post->featured_image_path) }}" alt="{{ $post->title }}" class="h-full w-full rounded-xl border border-white/30 object-contain shadow-lg dark:border-gray-700/70">
                    </div>
                @else
                    <div class="flex h-72 w-full items-center justify-center text-gray-400">No featured image</div>
                @endif
            </div>
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $post->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span>
                    @if ($post->is_featured)
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Featured</span>
                    @endif
                </div>
                <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $post->excerpt }}</p>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Author</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $post->author?->name ?? 'System' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Slug</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $post->slug }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Published At</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $post->published_at?->format('M d, Y h:i A') ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
                        <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $post->updated_at?->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
            <div class="lg:col-span-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Content</h2>
                    <div class="prose mt-4 max-w-none prose-gray dark:prose-invert">
                        {!! $post->content !!}
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</h3>
                    <div class="mt-4 flex flex-col gap-3">
                        <a href="{{ route('dashboard.blogs.edit', $post) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Edit Post</a>
                        <form action="{{ route('dashboard.blogs.toggle-status', $post) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">{{ $post->is_published ? 'Unpublish' : 'Publish' }}</button>
                        </form>
                        <form action="{{ route('dashboard.blogs.destroy', $post) }}" method="POST" class="js-confirmable" data-confirm-title="Delete Blog Post" data-confirm-message="Move this post to trash?" data-confirm-button="Delete" data-confirm-variant="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50 dark:border-red-900/40 dark:hover:bg-red-900/20">Move to Trash</button>
                        </form>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">SEO</h3>
                    <div class="mt-4 space-y-4 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Meta Description</p>
                            <p class="mt-1">{{ $post->meta_description ?: 'No meta description provided.' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-500">Meta Keywords</p>
                            <p class="mt-1">{{ $post->meta_keywords ?: 'No meta keywords provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
