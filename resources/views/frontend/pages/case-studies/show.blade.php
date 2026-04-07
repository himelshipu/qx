@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-white dark:bg-gray-950">
		<!-- Header with Breadcrumb -->
		<div class="mx-auto max-w-4xl space-y-8 px-4 py-12 sm:px-6 lg:px-8">
			<div>
				<a href="{{ route('case-studies') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">← Back to Case Studies</a>
			</div>

			<!-- Cover Image Section -->
			<div class="relative overflow-hidden rounded-2xl bg-gray-200 dark:bg-gray-800 h-96 w-full">
				@if ($caseStudy->cover_image_path)
					<img src="{{ \App\Helpers\ImageHelper::url($caseStudy->cover_image_path) }}"
						alt="{{ $caseStudy->title }}"
						class="w-full h-full object-cover" />
				@else
					<div class="w-full h-full bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-600 flex items-center justify-center p-6">
						<div class="text-center">
							<svg class="w-20 h-20 text-white/50 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
							<span class="text-white text-lg font-semibold text-center">{{ $caseStudy->title }}</span>
						</div>
					</div>
				@endif
			</div>

			<!-- Title and Meta Information -->
			<div>
				<div class="space-y-3">
					<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">
						Case Study
					</span>

					<h1 class="text-4xl font-bold text-gray-900 dark:text-white">
						{{ $caseStudy->title }}
					</h1>

					@if ($caseStudy->summary)
						<p class="text-lg text-gray-600 dark:text-gray-300">
							{{ $caseStudy->summary }}
						</p>
					@endif

					<div class="flex flex-wrap items-center gap-4 pt-2">
						<span class="text-sm text-gray-500 dark:text-gray-400">
							Published {{ $caseStudy->published_at?->format('F d, Y') }}
						</span>

						@if ($caseStudy->external_url)
							<a href="{{ $caseStudy->external_url }}"
								target="_blank"
								rel="noopener noreferrer"
								class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition">
								<span>View Case Study</span>
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
								</svg>
							</a>
						@endif
					</div>
				</div>
			</div>
		</div>

		<!-- Divider -->
		<div class="border-t border-gray-200 dark:border-gray-800"></div>

		<!-- Main Content -->
		<div class="mx-auto max-w-4xl space-y-8 px-4 py-12 sm:px-6 lg:px-8">
			<!-- Summary Section -->
			@if ($caseStudy->summary)
				<section>
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Overview</h2>
					<div class="prose prose-lg max-w-none text-gray-700 dark:prose-invert dark:text-gray-300">
						<p class="whitespace-pre-wrap leading-relaxed">{{ $caseStudy->summary }}</p>
					</div>
				</section>
			@endif

			<!-- Case Study Details Grid -->
			<section>
				<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Details</h2>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-6">
						<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">About This Case</h3>
						<dl class="space-y-3 text-sm">
							<div>
								<dt class="text-gray-600 dark:text-gray-400 font-medium">Title</dt>
								<dd class="text-gray-900 dark:text-white mt-1">{{ $caseStudy->title }}</dd>
							</div>
							<div>
								<dt class="text-gray-600 dark:text-gray-400 font-medium">Published</dt>
								<dd class="text-gray-900 dark:text-white mt-1">{{ $caseStudy->published_at?->format('M d, Y') }}</dd>
							</div>
							<div>
								<dt class="text-gray-600 dark:text-gray-400 font-medium">Status</dt>
								<dd class="text-gray-900 dark:text-white mt-1">
									<span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700 dark:bg-green-900/30 dark:text-green-300">
										Published
									</span>
								</dd>
							</div>
						</dl>
					</div>

					@if ($caseStudy->external_url)
						<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 p-6">
							<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">External Resources</h3>
							<div>
								<dt class="text-gray-600 dark:text-gray-400 font-medium text-sm mb-2">External Link</dt>
								<dd class="mt-2">
									<a href="{{ $caseStudy->external_url }}"
										target="_blank"
										rel="noopener noreferrer"
										class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 break-all text-sm fade-link">
										{{ \Illuminate\Support\Str::limit($caseStudy->external_url, 50) }}
									</a>
								</dd>
							</div>
						</div>
					@endif
				</div>
			</section>

			<!-- CTA Section -->
			<section class="rounded-xl border border-indigo-200 dark:border-indigo-900/40 bg-indigo-50 dark:bg-indigo-900/20 p-8">
				<div class="text-center">
					<h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
						Inspired by this case study?
					</h3>
					<p class="text-gray-600 dark:text-gray-300 mb-6">
						Join our influencer community and start collaborating with brands today.
					</p>
					<a href="{{ route('influencers') }}"
						class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition">
						Browse Influencers
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
						</svg>
					</a>
				</div>
			</section>
		</div>

		<!-- Related Case Studies Section -->
		@if ($related->isNotEmpty())
			<div class="border-t border-gray-200 dark:border-gray-800">
				<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">More Case Studies</h2>

					<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
						@foreach ($related as $case)
							<a href="{{ route('case-studies.show', $case->slug) }}"
								class="group relative rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 hover:shadow-lg transition-all duration-300">

								<!-- Cover Image -->
								<div class="relative overflow-hidden bg-gray-200 dark:bg-gray-700 h-48">
									@if ($case->cover_image_path)
										<img src="{{ \App\Helpers\ImageHelper::url($case->cover_image_path) }}"
											alt="{{ $case->title }}"
											class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
									@else
										<div class="w-full h-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center p-6">
											<span class="text-white text-center text-sm font-semibold line-clamp-2">{{ $case->title }}</span>
										</div>
									@endif

									<!-- Overlay -->
									<div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300"></div>
								</div>

								<!-- Content -->
								<div class="p-4">
									<h3 class="text-base font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
										{{ $case->title }}
									</h3>

									<p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-3">
										{{ $case->summary }}
									</p>

									<!-- Footer -->
									<div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
										<span class="text-xs text-gray-500 dark:text-gray-400">
											{{ $case->published_at?->format('M d, Y') }}
										</span>
										<svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
										</svg>
									</div>
								</div>
							</a>
						@endforeach
					</div>
				</div>
			</div>
		@endif
	</div>
@endsection
