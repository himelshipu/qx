@extends('backend.layouts.app')

@section('title', $page->title)

@section('content')
	<x-backend.shell.breadcrumb pageTitle="{{ $page->title }}" />

	<div class="space-y-6">
		<!-- Header -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-6 dark:border-gray-800">
				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $page->title }}</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
							<code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">{{ $page->slug }}</code>
						</p>
					</div>
					@if($page->is_active)
						<span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">
							Published
						</span>
					@else
						<span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-800 dark:bg-gray-800 dark:text-gray-400">
							Draft
						</span>
					@endif
				</div>
			</div>

			<!-- Meta Info -->
			<div class="grid grid-cols-1 gap-4 border-b border-gray-200 p-6 sm:grid-cols-3 dark:border-gray-800">
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Created</p>
					<p class="mt-2 text-sm text-gray-900 dark:text-white">{{ $page->created_at->format('M d, Y') }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400">{{ $page->created_at->format('h:i A') }}</p>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Last Updated</p>
					<p class="mt-2 text-sm text-gray-900 dark:text-white">{{ $page->updated_at->format('M d, Y') }}</p>
					<p class="text-xs text-gray-500 dark:text-gray-400">{{ $page->updated_at->format('h:i A') }}</p>
				</div>
				<div>
					<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Slug</p>
					<p class="mt-2 break-all font-mono text-sm text-gray-900 dark:text-white">{{ $page->slug }}</p>
				</div>
			</div>
		</div>

		<!-- SEO Info -->
		@if($page->meta_description || $page->meta_keywords)
			<div class="rounded-xl border border-blue-200 bg-blue-50 shadow-sm dark:border-blue-900/40 dark:bg-blue-900/20">
				<div class="border-b border-blue-200 p-6 dark:border-blue-900/40">
					<h3 class="flex items-center text-lg font-semibold text-blue-900 dark:text-blue-300">
						<svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						SEO Information
					</h3>
				</div>
				<div class="space-y-4 p-6">
					@if($page->meta_description)
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Meta Description</p>
							<p class="mt-2 text-sm text-blue-900 dark:text-blue-300">{{ $page->meta_description }}</p>
						</div>
					@endif
					@if($page->meta_keywords)
						<div>
							<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Meta Keywords</p>
							<div class="mt-2 flex flex-wrap gap-2">
								@foreach(explode(',', $page->meta_keywords) as $keyword)
									<span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
										{{ trim($keyword) }}
									</span>
								@endforeach
							</div>
						</div>
					@endif
				</div>
			</div>
		@endif

		<!-- Content -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-6 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Page Content</h3>
			</div>
			<div class="prose prose-sm max-w-none p-6 dark:prose-invert">
				{!! $page->content !!}
			</div>
		</div>

		<!-- Actions -->
		<div class="flex flex-col gap-3 sm:flex-row sm:justify-between">
			<a href="{{ route('dashboard.static-pages.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
				<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
				</svg>
				Back to Pages
			</a>
			<div class="flex gap-3">
				<a href="{{ route('dashboard.static-pages.edit', $page) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
					</svg>
					Edit
				</a>
				<form method="POST" action="{{ route('dashboard.static-pages.destroy', $page) }}" onsubmit="return confirm('Delete this page?');" class="inline">
					@csrf
					@method('DELETE')
					<button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-700">
						<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
						</svg>
						Delete
					</button>
				</form>
			</div>
		</div>
	</div>
@endsection
