@extends('frontend.layouts.app')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-950">
	<!-- Hero Section -->
	<section class="relative py-4 px-4 sm:px-6 lg:px-8">
		<div class="max-w-screen-2xl mx-auto">
			<div class="text-center mb-6">
				<h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">
					Case Studies
				</h1>
				<p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
					Discover how brands have successfully collaborated with influencers on our platform.
				</p>
			</div>
		</div>
	</section>

	<!-- Case Studies Grid -->
	<section class="py-6 px-4 sm:px-6 lg:px-8">
		<div class="max-w-screen-2xl mx-auto">
			@if ($caseStudies->count() > 0)
				<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
					@foreach ($caseStudies as $case)
						<a href="{{ route('case-studies.show', $case) }}"
							target="_self"
							rel=""
							class="group relative rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-800 hover:shadow-xl transition-all duration-300">

							<!-- Cover Image Container -->
							<div class="relative overflow-hidden bg-gray-200 dark:bg-gray-700 h-80">
								@if ($case->cover_image_path)
									<img src="{{ \App\Helpers\ImageHelper::url($case->cover_image_path) }}"
										alt="{{ $case->title }}"
										class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
								@else
									<div class="w-full h-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center p-6">
										<span class="text-white text-center text-lg font-semibold">{{ $case->title }}</span>
									</div>
								@endif

								<!-- Overlay -->
								<div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors duration-300"></div>
							</div>

							<!-- Content -->
							<div class="p-6">
								<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
									{{ $case->title }}
								</h3>

								<p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-4">
									{{ $case->summary }}
								</p>

								<!-- Footer -->
								<div class="flex items-center justify-between">
									<span class="text-xs text-gray-500 dark:text-gray-400">
										{{ $case->published_at?->format('M d, Y') }}
									</span>
								</div>
							</div>
						</a>
					@endforeach
				</div>

				<!-- Pagination -->
				<div class="flex justify-center">
					{{ $caseStudies->links() }}
				</div>
			@else
				<!-- Empty State -->
				<div class="text-center py-16">
					<div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full mb-4">
						<svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
					</div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Case Studies</h3>
					<p class="text-gray-600 dark:text-gray-400">
						There are no case studies available at the moment. Check back soon!
					</p>
				</div>
			@endif
		</div>
	</section>

	<!-- CTA Section -->
	@if ($caseStudies->count() > 0)
		<section class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
			<div class="max-w-screen-2xl mx-auto text-center">
				<h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
					Ready to collaborate?
				</h2>
				<p class="text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
					Join thousands of influencers and brands building successful partnerships.
				</p>
				<a href="{{ route('influencers') }}"
					class="inline-flex items-center gap-2 px-8 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold hover:opacity-90 transition">
					Browse Influencers
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>
			</div>
		</section>
	@endif
</div>
@endsection
