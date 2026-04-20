@extends('frontend.layouts.app')

@section('title', 'Blog')

@section('content')
	<div class="min-h-screen bg-white px-2 py-6 dark:bg-gray-950">
		<div class="mx-auto max-w-screen-2xl space-y-8">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Resources</p>
						<h1 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">Platform Blog</h1>
						<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Insights, campaign playbooks, influencer strategy guides, and product updates.</p>
					</div>
					<form method="GET" class="grid w-full grid-cols-1 gap-3 sm:grid-cols-3 lg:w-auto lg:min-w-[640px]">
						<div class="sm:col-span-2">
							<input type="search" name="q" value="{{ $search }}" placeholder="Search blog posts..."
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
						</div>
						<div class="flex gap-2">
							<select name="featured" class="h-12 flex-1 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
								<option value="all" @selected($featuredFilter === 'all')>All Posts</option>
								<option value="yes" @selected($featuredFilter === 'yes')>Featured Only</option>
								<option value="no" @selected($featuredFilter === 'no')>Non-Featured</option>
							</select>
							<button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Filter</button>
						</div>
					</form>
				</div>
			</div>

			@if($featuredPosts->isNotEmpty())
				<div class="space-y-4">
					<div class="flex items-center justify-between">
						<h2 class="text-xl font-semibold text-gray-900 dark:text-white">Featured Stories</h2>
					</div>
					<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
						@foreach($featuredPosts as $entry)
							<a href="{{ route('frontend.blogs.show', $entry->slug) }}" class="group overflow-hidden rounded-xl border border-indigo-200 bg-indigo-50 transition hover:shadow-md dark:border-indigo-900/40 dark:bg-indigo-900/20">
								@if($entry->featured_image_path)
									<div class="h-40 w-full overflow-hidden">
										<img src="{{ asset('storage/' . $entry->featured_image_path) }}" alt="{{ $entry->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
									</div>
								@endif
								<div class="p-5">
									<p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">Featured</p>
									<h3 class="mt-2 line-clamp-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $entry->title }}</h3>
									<p class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-300">{{ $entry->summary }}</p>
								</div>
							</a>
						@endforeach
					</div>
				</div>
			@endif

			<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
				<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
					<h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Blog Posts</h2>
				</div>
				<div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-3">
					@forelse($posts as $entry)
						<article class="group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-gray-50 transition hover:border-gray-300 hover:bg-white hover:shadow-sm dark:border-gray-700 dark:bg-gray-800/30 dark:hover:border-gray-600 dark:hover:bg-gray-800">
							<a href="{{ route('frontend.blogs.show', $entry->slug) }}" class="flex h-full flex-col">
								<div class="h-48 w-full overflow-hidden bg-gray-200 dark:bg-gray-700">
									@if($entry->featured_image_path)
										<img src="{{ asset('storage/' . $entry->featured_image_path) }}" alt="{{ $entry->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
									@else
										<div class="flex h-full items-center justify-center text-sm font-medium text-gray-500 dark:text-gray-400">No image</div>
									@endif
								</div>
								<div class="flex flex-1 flex-col p-5">
									<div class="mb-3 flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
										@if($entry->is_featured)
											<span class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Featured</span>
										@endif
										<span>{{ optional($entry->published_at)->format('M d, Y') }}</span>
									</div>
									<h3 class="line-clamp-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $entry->title }}</h3>
									<p class="mt-2 line-clamp-3 text-sm text-gray-600 dark:text-gray-300">{{ $entry->summary }}</p>
									<div class="mt-4 text-xs text-gray-500 dark:text-gray-400">By {{ $entry->author?->name ?? 'System' }}</div>
								</div>
							</a>
						</article>
					@empty
						<p class="col-span-full py-8 text-center text-sm text-gray-500 dark:text-gray-400">No blog posts found.</p>
					@endforelse
				</div>
				@if($posts->hasPages())
					<div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
						{{ $posts->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
