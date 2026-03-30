@extends('frontend.layouts.app')

@section('content')
	<div class="mx-auto max-w-4xl space-y-8">
		<div>
			<a href="{{ route('knowledge-base.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">← Back to Knowledge Base</a>
			<div class="mt-4 flex flex-wrap items-center gap-3">
				@if($article->badge)
					<span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $article->badge }}</span>
				@endif
				<span class="text-xs text-gray-500 dark:text-gray-400">{{ $article->read_time_minutes }} min read</span>
			</div>
			<h1 class="mt-3 text-3xl font-semibold text-[#222] dark:text-white">{{ $article->title }}</h1>
			@if($article->summary)
				<p class="mt-3 text-gray-600 dark:text-gray-300">{{ $article->summary }}</p>
			@endif
		</div>

		<article class="prose max-w-none rounded-xl border border-gray-200 bg-white p-6 text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:prose-invert">
			{!! nl2br(e($article->content)) !!}
		</article>

		@if($related->isNotEmpty())
			<div class="space-y-4">
				<h2 class="text-xl font-semibold text-gray-900 dark:text-white">Related articles</h2>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
					@foreach($related as $entry)
						<a href="{{ route('knowledge-base.show', $entry->slug) }}" class="rounded-xl border border-gray-200 bg-white p-5 transition hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
							<p class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->read_time_minutes }} min read</p>
							<h3 class="mt-2 text-base font-semibold text-gray-900 dark:text-white">{{ $entry->title }}</h3>
							<p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $entry->summary ?? \Illuminate\Support\Str::limit(strip_tags($entry->content), 120) }}</p>
						</a>
					@endforeach
				</div>
			</div>
		@endif
	</div>
@endsection
