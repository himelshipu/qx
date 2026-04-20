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

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900" id="blog-dashboard"
            data-filter-results-route="{{ route('dashboard.blogs.table') }}"
            data-status-toggle-template="{{ route('dashboard.blogs.toggle-status', ['blogPost' => '__ID__']) }}"
            data-featured-toggle-template="{{ route('dashboard.blogs.toggle-featured', ['blogPost' => '__ID__']) }}"
            data-csrf-token="{{ csrf_token() }}">

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

            <div class="p-5">
                <form id="blog-filters-form" method="GET" action="{{ route('dashboard.blogs.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-6">
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <x-icons.search class="h-4 w-4" />
                            </span>
                            <input id="q" name="q" type="text" value="{{ $search }}"
                                placeholder="Search by title, slug, or excerpt"
                                class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <select id="status" name="status"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                            <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="trashed" {{ $status === 'trashed' ? 'selected' : '' }}>Trash</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 md:col-span-3">
                        <a href="{{ route('dashboard.blogs.index') }}"
                            class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                            Reset
                        </a>
                    </div>
                </form>

                @include('backend.pages.blog._results', ['posts' => $posts, 'status' => $status])
            </div>
        </div>
    </div>
@endsection