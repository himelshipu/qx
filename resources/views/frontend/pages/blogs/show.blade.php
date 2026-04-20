@extends('frontend.layouts.app')

@section('title', $post->title)

@section('content')
	@php
		$wordCount = str_word_count(strip_tags((string) $post->content));
		$readMinutes = max(1, (int) ceil($wordCount / 220));
		$authorName = $post->author?->name ?? 'Editorial Team';
		$authorInitials = collect(explode(' ', trim($authorName)))
			->filter()
			->take(2)
			->map(fn ($part) => strtoupper(substr($part, 0, 1)))
			->implode('');
		$articleUrl = request()->fullUrl();
	@endphp

	<div class="min-h-screen bg-white px-2 py-6 dark:bg-gray-950">
		<div class="mx-auto max-w-7xl space-y-8">
			<div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
				<a href="{{ route('home') }}" class="transition hover:text-gray-700 dark:hover:text-gray-200">Home</a>
				<span>/</span>
				<a href="{{ route('frontend.blogs.index') }}" class="transition hover:text-gray-700 dark:hover:text-gray-200">Blog</a>
				<span>/</span>
				<span class="line-clamp-1 text-gray-700 dark:text-gray-300">{{ $post->title }}</span>
			</div>

			<header class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				@if($post->featured_image_path)
					<div class="px-4 pt-4 sm:px-6 sm:pt-6">
						<div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800">
							<img src="{{ asset('storage/' . $post->featured_image_path) }}" alt="{{ $post->title }}" class="h-56 w-full object-cover sm:h-72 lg:h-96">
						</div>
					</div>
				@endif

				<div class="p-6 sm:p-8 lg:p-10">
					<div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
						@if($post->is_featured)
							<span class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">Featured</span>
						@endif
						<span>{{ optional($post->published_at)->format('M d, Y') }}</span>
						<span>•</span>
						<span>{{ $readMinutes }} min read</span>
					</div>

					<h1 class="mt-4 text-3xl font-semibold leading-tight text-gray-900 dark:text-white sm:text-4xl lg:text-5xl">{{ $post->title }}</h1>

					@if($post->excerpt)
						<p class="mt-5 max-w-4xl text-base leading-7 text-gray-600 dark:text-gray-300 sm:text-lg">{{ $post->excerpt }}</p>
					@endif

					<div class="mt-6 flex flex-wrap items-center gap-4 border-t border-gray-200 pt-6 dark:border-gray-800">
						<div class="flex items-center gap-3">
							<div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $authorInitials !== '' ? $authorInitials : 'ED' }}</div>
							<div>
								<p class="text-sm font-medium text-gray-900 dark:text-white">{{ $authorName }}</p>
								<p class="text-xs text-gray-500 dark:text-gray-400">Updated {{ optional($post->updated_at)->format('M d, Y') }}</p>
							</div>
						</div>

						<button type="button" onclick="copyBlogLink('{{ $articleUrl }}')" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Copy Link</button>
					</div>
				</div>
			</header>

			<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
				<article class="prose prose-gray max-w-none rounded-xl border border-gray-200 bg-white p-6 sm:p-8 lg:col-span-8 dark:border-gray-800 dark:bg-gray-900 dark:prose-invert">
					{!! $post->content !!}
				</article>

				<aside class="space-y-6 lg:sticky lg:top-24 lg:col-span-4 lg:self-start">
					<div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
						<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Article Snapshot</h3>
						<dl class="mt-4 space-y-3 text-sm">
							<div>
								<dt class="text-gray-500 dark:text-gray-400">Published</dt>
								<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ optional($post->published_at)->format('M d, Y h:i A') ?? '-' }}</dd>
							</div>
							<div>
								<dt class="text-gray-500 dark:text-gray-400">Reading Time</dt>
								<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $readMinutes }} minutes</dd>
							</div>
							<div>
								<dt class="text-gray-500 dark:text-gray-400">Slug</dt>
								<dd class="mt-1 break-all font-medium text-gray-900 dark:text-white">{{ $post->slug }}</dd>
							</div>
						</dl>
					</div>

					@if($related->isNotEmpty())
						<div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
							<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">More Like This</h3>
							<div class="mt-4 space-y-4">
								@foreach($related as $entry)
									<a href="{{ route('frontend.blogs.show', $entry->slug) }}" class="group block rounded-lg border border-gray-200 p-3 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:border-gray-600 dark:hover:bg-gray-800/50">
										<p class="line-clamp-2 text-sm font-semibold text-gray-900 transition group-hover:text-indigo-600 dark:text-white dark:group-hover:text-indigo-400">{{ $entry->title }}</p>
										<p class="mt-1 line-clamp-2 text-xs text-gray-500 dark:text-gray-400">{{ $entry->summary }}</p>
									</a>
								@endforeach
							</div>
						</div>
					@endif
				</aside>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		function copyBlogLink(url) {
			if (!navigator.clipboard) {
				return;
			}

			navigator.clipboard.writeText(url).then(function() {
				if (window.toast && window.toast.success) {
					window.toast.success('Article link copied.');
				}
			});
		}
	</script>
@endpush
