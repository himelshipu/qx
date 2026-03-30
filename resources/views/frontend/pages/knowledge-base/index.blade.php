@extends('frontend.layouts.app')

@section('content')
	<div class="mx-auto max-w-6xl space-y-10">
		<div class="text-center">
			<h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white">Knowledge Base</h1>
			<p class="mt-3 text-gray-500 dark:text-gray-400">Helpful guides and answers from the ROCKIES support team.</p>
		</div>

		@if($featured->isNotEmpty())
			<div class="space-y-4">
				<h2 class="text-xl font-semibold text-gray-900 dark:text-white">Featured Articles</h2>
				<div class="grid grid-cols-1 gap-4 md:grid-cols-3">
					@foreach($featured as $article)
						<a href="{{ route('knowledge-base.show', $article->slug) }}" class="rounded-xl border border-indigo-200 bg-indigo-50 p-5 transition hover:shadow-md dark:border-indigo-900/40 dark:bg-indigo-900/20">
							<p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">{{ $article->badge ?? 'Guide' }}</p>
							<h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $article->title }}</h3>
							<p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $article->summary ?? \Illuminate\Support\Str::limit(strip_tags($article->content), 120) }}</p>
							<p class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ $article->read_time_minutes }} min read</p>
						</a>
					@endforeach
				</div>
			</div>
		@endif

		<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h2 class="text-lg font-semibold text-gray-900 dark:text-white">All Articles</h2>
			</div>
			<div class="divide-y divide-gray-200 dark:divide-gray-800">
				@forelse($articles as $article)
					<a href="{{ route('knowledge-base.show', $article->slug) }}" class="block px-6 py-5 transition hover:bg-gray-50 dark:hover:bg-gray-800/40">
						<div class="flex flex-wrap items-center gap-2">
							@if($article->badge)
								<span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $article->badge }}</span>
							@endif
							<span class="text-xs text-gray-500 dark:text-gray-400">{{ $article->read_time_minutes }} min read</span>
						</div>
						<h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $article->title }}</h3>
						<p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $article->summary ?? \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}</p>
					</a>
				@empty
					<p class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">No knowledge base article available yet.</p>
				@endforelse
			</div>
			@if($articles->hasPages())
				<div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">{{ $articles->links() }}</div>
			@endif
		</div>
	</div>
@endsection
