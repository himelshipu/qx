<section class="cta-section-wrapper px-8 py-24 rounded-2xl" style="
background-size: cover;
background-position: center;
background-image: url('{{ asset('images/rockies-banner.webp'); }}')">
	<div class="flex flex-col justify-start items-start gap-2 w-full bg-[#1a1a1a17] rounded-2xl overflow-hidden">

		<div class="flex flex-col gap-3 justify-start items-start max-w-none md:max-w-[520px]">
			<h2 class="text-2xl md:text-4xl font-bold text-white">
				Find and Hire Influencers
			</h2>
			<p class="text-gray-100 text-lg mb-4 font-normal">
				Search Instagram, TikTok, and YouTube influencers.
			</p>
			
		</div>
		<a href="{{ route('influencers') }}"
			class="bg-white hover:bg-purple-500 hover:text-white text-gray-800 font-bold px-8 py-4 rounded-xl transition-all shadow-lg active:scale-95">
			Search Influencers
		</a>
	</div>
</section>
