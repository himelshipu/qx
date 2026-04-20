@extends('backend.layouts.app')

@section('title', 'Categories')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Categories" />

	<div class="space-y-6" id="categories-dashboard"
		data-csrf-token="{{ csrf_token() }}"
		data-max-featured="{{ (int) config('category.max_featured', 20) }}"
		data-filter-results-route="{{ route('dashboard.categories.table') }}"
		data-status-toggle-template="{{ route('dashboard.categories.toggle-status', ['category' => '__ID__']) }}"
		data-featured-add-template="{{ route('dashboard.categories-featured.add', ['category' => '__ID__']) }}"
		data-featured-remove-template="{{ route('dashboard.categories-featured.remove', ['category' => '__ID__']) }}"
		data-featured-list-route="{{ route('dashboard.categories-featured.list') }}"
		data-featured-search-route="{{ route('dashboard.categories-featured.search') }}"
		data-featured-reorder-route="{{ route('dashboard.categories-featured.reorder') }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['active'] }}</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Inactive</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">{{ $stats['inactive'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">In Use</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['linked'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">

			<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Category Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage Categories, status, and Featured from one
						view.</p>
				</div>
				<div class="flex flex-wrap items-center justify-end gap-2 ">
					<button type="button" id="feature-position-btn"
						class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-600">
						<x-icons.star class="h-4 w-4" />
						Feature Position
					</button>
					<a href="{{ route('dashboard.categories.create') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						New Category
					</a>
				</div>
			</div>
			


			<div class="p-5">
				<form id="category-filters-form" method="GET" action="{{ route('dashboard.categories.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-12">
					<div class="md:col-span-6">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, slug, or description"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div class="md:col-span-2">
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<select id="featured" name="featured"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $featured === 'all' ? 'selected' : '' }}>All</option>
							<option value="featured" {{ $featured === 'featured' ? 'selected' : '' }}>Featured</option>
							<option value="non-featured" {{ $featured === 'non-featured' ? 'selected' : '' }}>Non-Featured</option>
						</select>
					</div>
					<div class="flex items-end gap-2 md:col-span-2">
						<a href="{{ route('dashboard.categories.index') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.categories._results', ['categories' => $categories])
			</div>
		</div>
	</div>

	<!-- Feature Position Modal -->
	<div id="feature-position-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 dark:bg-black/70">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div class="relative flex max-h-[60vh] w-full max-w-4xl flex-col overflow-hidden rounded-lg bg-white shadow-xl dark:bg-gray-900">
				<div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-800">
					<h2 class="text-xl font-semibold text-gray-900 dark:text-white">Manage Featured Categories</h2>
					<button type="button" class="js-close-featured-modal text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-300">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>

				<div class="flex-1 overflow-y-auto p-6">
					<div class="mb-6">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
								</svg>
							</span>
							<input id="featured-search" type="text" placeholder="Search categories to add..."
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
						<div id="search-results" class="mt-2 hidden max-h-40 overflow-y-auto rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800"></div>
					</div>

					<div class="mb-4">
						<h3 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
							Featured Categories (<span id="featured-count">0</span>/{{ (int) config('category.max_featured', 20) }})
						</h3>
						<ul id="featured-list" class="divide-y divide-gray-200 rounded-lg border border-gray-200 bg-gray-50 dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800/50"></ul>
					</div>

					<div id="modal-loading" class="hidden">
						<div class="flex items-center justify-center py-8">
							<div class="h-6 w-6 animate-spin rounded-full border-2 border-gray-300 border-t-indigo-600"></div>
						</div>
					</div>
				</div>

				<div class="flex items-center justify-end gap-3 border-t border-gray-200 p-6 dark:border-gray-800">
					<button type="button" class="js-close-featured-modal rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						Close
					</button>
				</div>
			</div>
		</div>
	</div>
@endsection