@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen transition-colors duration-200">
		<main class="max-w-5xl flex flex-col gap-16 mx-auto px-4 py-8">

			@php
				// Get FAQ sections filtered by audience type
				$influencerSections = \App\Models\FaqSection::where('is_active', true)
				    ->whereIn('audience_type', ['all', 'influencer'])
				    ->orderBy('sort_order', 'asc')
				    ->with('items')
				    ->get();

				$brandSections = \App\Models\FaqSection::where('is_active', true)
				    ->whereIn('audience_type', ['all', 'brand'])
				    ->orderBy('sort_order', 'asc')
				    ->with('items')
				    ->get();
			@endphp

			@foreach ($influencerSections as $section)
				@if ($section->audience_type === 'influencer' || $section->audience_type === 'all')
					<!-- SECTION: {{ $section->section_title }} -->
					<section x-data="{ activeAccordion: null }">
						<h2 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-8">
							{{ $section->section_title }}
						</h2>

						<div class="border-b border-gray-200 dark:border-gray-800">
							@forelse($section->items()->where('is_active', true)->orderBy('sort_order', 'asc')->get() as $index => $item)
								<div
									class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
									<div class="py-7 px-4">
										<button @click="activeAccordion = (activeAccordion === {{ $index + 1 }} ? null : {{ $index + 1 }})"
											class="flex w-full items-center justify-between text-left">
											<span
												class="text-lg font-bold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
												{{ $item->question }}
											</span>
											<span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
												<svg class="h-6 w-6 transition-transform duration-500"
													:class="activeAccordion === {{ $index + 1 }} ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24"
													stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
												</svg>
											</span>
										</button>
										<div x-show="activeAccordion === {{ $index + 1 }}" x-collapse x-cloak>
											<div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl prose prose-invert">
												{!! nl2br(e($item->answer)) !!}
											</div>
										</div>
									</div>
								</div>
							@empty
								<div class="py-7 px-4 text-center text-gray-500 dark:text-gray-400">
									No FAQs available for this section.
								</div>
							@endforelse
						</div>
					</section>
				@endif
			@endforeach

			@foreach ($brandSections as $section)
				@if (
					$section->audience_type === 'brand' ||
						($section->audience_type === 'all' && !$influencerSections->contains($section)))
					<!-- SECTION: {{ $section->section_title }} -->
					<section x-data="{ activeAccordion: null }">
						<h2 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white leading-tight text-left mb-8">
							{{ $section->section_title }}
						</h2>

						<div class="border-b border-gray-200 dark:border-gray-800">
							@forelse($section->items()->where('is_active', true)->orderBy('sort_order', 'asc')->get() as $index => $item)
								<div
									class="group border-b border-gray-200 dark:border-gray-800 transition-all duration-300 hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
									<div class="py-7 px-4">
										<button @click="activeAccordion = (activeAccordion === {{ $index + 1 }} ? null : {{ $index + 1 }})"
											class="flex w-full items-center justify-between text-left">
											<span
												class="text-lg font-bold text-gray-800 dark:text-white transition-colors duration-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
												{{ $item->question }}
											</span>
											<span class="ml-6 flex-shrink-0 text-gray-400 group-hover:text-gray-600">
												<svg class="h-6 w-6 transition-transform duration-500"
													:class="activeAccordion === {{ $index + 1 }} ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24"
													stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
												</svg>
											</span>
										</button>
										<div x-show="activeAccordion === {{ $index + 1 }}" x-collapse x-cloak>
											<div class="mt-5 text-gray-500 dark:text-gray-400 text-base leading-relaxed max-w-3xl prose prose-invert">
												{!! nl2br(e($item->answer)) !!}
											</div>
										</div>
									</div>
								</div>
							@empty
								<div class="py-7 px-4 text-center text-gray-500 dark:text-gray-400">
									No FAQs available for this section.
								</div>
							@endforelse
						</div>
					</section>
				@endif
			@endforeach

			@if ($influencerSections->isEmpty() && $brandSections->isEmpty())
				<div class="text-center py-16">
					<p class="text-gray-500 dark:text-gray-400 text-lg">No FAQs available at the moment.</p>
				</div>
			@endif

		</main>
	</div>
@endsection
