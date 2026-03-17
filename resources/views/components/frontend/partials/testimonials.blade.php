@props(['testimonials' => collect()])

<section class="w-full bg-[#F8F6F2] dark:bg-gray-950 p-8">
    <div class="flex flex-col gap-8 mt-8">
        <div>
            <h3 class="text-xl font-semibold text-[#222] dark:text-white mb-12">
                330,000+ Brands Work With Influencers on Rockies Influencer Platform
            </h3>

            @if ($testimonials->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                @foreach ($testimonials as $testimonial)
                <div>
                    <span class="text-purple-500 text-5xl leading-none">"</span>
                    <h4 class="text-base font-semibold text-[#222] dark:text-white">
                        {{ $testimonial->quote }}
                    </h4>
                    <p class="mt-4 text-sm font-semibold text-[#222] dark:text-white">
                        {{ $testimonial->author_name }}
                        @if ($testimonial->author_role || $testimonial->company_name)
                            &mdash; {{ implode(', ', array_filter([$testimonial->author_role, $testimonial->company_name])) }}
                        @endif
                    </p>
                    @if ($testimonial->rating)
                    <p class="mt-1 text-yellow-500 text-sm">
                        @for ($i = 1; $i <= 5; $i++){{ $i <= $testimonial->rating ? '★' : '☆' }}@endfor
                    </p>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-400 dark:text-gray-500 text-sm">No testimonials available yet.</p>
            @endif
        </div>
    </div>
</section>
