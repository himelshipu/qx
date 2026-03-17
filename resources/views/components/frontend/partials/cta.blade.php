<section class="cta-section-wrapper">
	<div class="relative w-full bg-[#1A1A1A] rounded-2xl overflow-hidden min-h-[380px] flex items-center">

		<div class="relative z-20 w-full lg:w-1/2 px-10 md:px-20 py-16">
			<h2 class="text-2xl md:text-4xl font-bold text-white mb-4">
				Find and Hire Influencers
			</h2>
			<p class="text-gray-300 text-lg mb-10 font-normal">
				Search Instagram, TikTok, and YouTube influencers.
			</p>
			<a href="{{ route('influencers') }}"
				class="inline-block bg-white hover:bg-gray-100 text-black font-bold px-8 py-4 rounded-xl transition-all shadow-lg active:scale-95">
				Search Influencers
			</a>
		</div>

		<div class="absolute right-0 top-0 bottom-0 w-full lg:w-[70%] z-0">
			<div class="absolute inset-0 z-10 bg-gradient-to-r from-[#1A1A1A] via-[#1A1A1A]/80 to-transparent"></div>
			<img src="{{ asset('images/cta.png') }}" alt="Influencer Grid"
				class="w-full h-full object-cover object-right opacity-50 lg:opacity-100 transition-opacity duration-700">
		</div>

	</div>
</section>
