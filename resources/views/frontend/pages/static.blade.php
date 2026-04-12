@extends('layouts.app')

@section('title', $page->title)
@section('meta_description', $page->meta_description)
@section('meta_keywords', $page->meta_keywords)

@section('content')
	<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
		<!-- Breadcrumb -->
		<div class="mb-8">
			<nav class="flex items-center space-x-2 text-sm">
				<a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Home</a>
				<span class="text-gray-400">/</span>
				<span class="text-gray-600 dark:text-gray-400">{{ $page->title }}</span>
			</nav>
		</div>

		<!-- Page Header -->
		<div class="mb-12">
			<h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $page->title }}</h1>
			<p class="text-gray-600 dark:text-gray-400">Last updated {{ $page->updated_at->format('F j, Y') }}</p>
		</div>

		<!-- Page Content -->
		<article class="prose prose-lg dark:prose-invert max-w-none mb-12">
			{!! $page->content !!}
		</article>

		<!-- Back Button -->
		<div class="pt-8 border-t border-gray-200 dark:border-gray-800">
			<a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
				</svg>
				Back to Home
			</a>
		</div>
	</div>
@endsection
