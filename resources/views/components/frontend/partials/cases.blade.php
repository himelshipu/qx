<section>
	<div class="flex flex-col gap-8 mt-8">
		<div>
			<h2 class="text-2xl font-semibold text-[#222] dark:text-white mb-4">
				<a href="{{ route('case-studies') }}" class="hover:text-blue-600 transition">
					Case Studiesaaaaaaaaaaaaaaa
				</a>
			</h2>

			@php
				$caseStudies = \App\Models\CaseStudy::where('is_published', true)
				    ->orderBy('sort_order', 'asc')
				    ->orderBy('published_at', 'desc')
				    ->limit(6)
				    ->get();
			@endphp

			@if ($caseStudies->isEmpty())
				<div class="text-center py-12">
					<p class="text-gray-500 dark:text-gray-400">No case studies available at the moment.</p>
				</div>
			@else
				<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
					@foreach ($caseStudies as $case)
						<a href="{{ route('case-studies.show', $case) }}"
							class="group relative rounded-xl overflow-hidden h-64 hover:opacity-90">
							@if ($case->cover_image_path)
								<img src="{{ \App\Helpers\ImageHelper::url($case->cover_image_path) }}" alt="{{ $case->title }}"
									class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
							@else
								<div class="w-full h-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center">
									<span class="text-white text-lg font-semibold text-center px-4">{{ $case->title }}</span>
								</div>
							@endif
							<div class="absolute inset-0 bg-black/40 flex items-end p-6">
								<p class="text-white font-medium leading-snug">
									{{ $case->title }}
								</p>
							</div>
						</a>
					@endforeach
				</div>
			@endif
		</div>

	</div>
</section>
