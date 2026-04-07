<footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 mt-12">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
		<div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
			<!-- Brand Section -->
			<div>
				<h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">QX Marketplace</h3>
				<p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
					Connect brands with influencers for authentic influencer marketing campaigns.
				</p>
				<div class="flex gap-4">
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
						<i class="fab fa-facebook w-5 h-5"></i>
					</a>
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
						<i class="fab fa-twitter w-5 h-5"></i>
					</a>
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
						<i class="fab fa-instagram w-5 h-5"></i>
					</a>
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
						<i class="fab fa-linkedin w-5 h-5"></i>
					</a>
				</div>
			</div>

			<!-- For Brands -->
			<div>
				<h4 class="font-semibold text-gray-900 dark:text-white mb-4">For Brands</h4>
				<ul class="space-y-3 text-sm">
					<li><a href="{{ route('influencers') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Browse
							Influencers</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">How
							It Works</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Pricing</a>
					</li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Case
							Studies</a></li>
				</ul>
			</div>

			<!-- For Influencers -->
			<div>
				<h4 class="font-semibold text-gray-900 dark:text-white mb-4">For Influencers</h4>
				<ul class="space-y-3 text-sm">
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Get
							Started</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Influencer
							Guide</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Earnings</a>
					</li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Support</a>
					</li>
				</ul>
			</div>

			<!-- Resources -->
			<div>
				<h4 class="font-semibold text-gray-900 dark:text-white mb-4">Resources</h4>
				<ul class="space-y-3 text-sm">
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Blog</a>
					</li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Contact
							Us</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy
							Policy</a></li>
					<li><a href="{{ route('home') }}"
							class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Terms
							of Service</a></li>
				</ul>
			</div>
		</div>

		<!-- Bottom Section -->
		<div class="border-t border-gray-200 dark:border-gray-800 pt-8">
			<div class="flex flex-col md:flex-row items-center justify-between">
				<p class="text-sm text-gray-600 dark:text-gray-400">
					&copy; {{ date('Y') }} QX Marketplace. All rights reserved.
				</p>
				<div class="mt-4 md:mt-0 flex gap-6 text-sm">
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Privacy</a>
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Terms</a>
					<a href="#"
						class="text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Cookies</a>
				</div>
			</div>
		</div>
	</div>
</footer>
