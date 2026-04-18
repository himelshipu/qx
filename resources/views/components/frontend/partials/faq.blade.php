@props(['faqItems' => collect()])

<section class="faq-section-wrapper" x-data="{ activeAccordion: null }">
	<div class="flex flex-row items-end justify-between mb-6 gap-3">
		<div>
			<h2 class="text-2xl font-semibold text-[#222] dark:text-white">FAQ</h2>
			<p class="text-gray-500 dark:text-gray-400 leading-relaxed mt-1">
				Common questions answered
			</p>
		</div>
		<a href="{{ route('faq') }}"
			class="text-sm font-medium text-[#222] dark:text-gray-400 hover:underline hover:text-purple-300 pb-1 whitespace-nowrap">
			See All
		</a>
	</div>

	<div class="divide-y divide-gray-200 dark:divide-gray-800 border-b border-gray-200 dark:border-gray-800">

		@forelse ($faqItems as $item)
			<div class="py-6">
				<button @click="activeAccordion = (activeAccordion === {{ $loop->index }} ? null : {{ $loop->index }})"
					class="flex w-full items-center justify-between text-left group">
					<span class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-gray-600 transition-colors">
						{{ $item->question }}
					</span>
					<span class="ml-6 flex-shrink-0 text-gray-400">
						<svg class="h-6 w-6 transition-transform duration-300"
							:class="activeAccordion === {{ $loop->index }} ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24"
							stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m6-6H6" />
						</svg>
					</span>
				</button>
				<div x-show="activeAccordion === {{ $loop->index }}" x-collapse x-cloak>
					<div class="mt-4 text-gray-500 dark:text-gray-400 text-base">
						{!! nl2br(e($item->answer)) !!}
					</div>
				</div>
			</div>
		@empty
			<p class="py-6 text-gray-400 dark:text-gray-500 text-sm">No FAQs available yet.</p>
		@endforelse

	</div>
</section>
