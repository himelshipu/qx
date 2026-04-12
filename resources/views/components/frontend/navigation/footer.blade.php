<footer class="w-full border-t border-gray-100 dark:border-gray-800">
    <div class="max-w-screen-2xl mx-auto px-4 py-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-16">
            <div class="flex flex-col items-start">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-6">Resources</h4>
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Pricing</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Blog</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Resource Hub</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">TikTok For Brands</a></li>
                </ul>
            </div>

            <div class="flex flex-col items-start">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-6">Discover</h4>
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Find Influencers</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Top Influencers</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Search Influencers</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Buy Shoutouts</a></li>
                </ul>
            </div>

            <div class="flex flex-col items-start">
                <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-6">Support</h4>
                <ul class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('support') }}" class="hover:text-gray-800 dark:hover:text-white">Contact Us</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">How It Works</a></li>
                    <li><a href="#" class="hover:text-gray-800 dark:hover:text-white">Frequently Asked Questions</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-2 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4 md:gap-6 text-sm text-gray-600 dark:text-gray-400">
                <span>© Rockies Inc.</span>
                @php
                    $footerPages = \App\Models\Setting::get('footer_pages', []);
                    $pages = \App\Models\StaticPage::whereIn('id', $footerPages)->where('is_active', true)->get();
                @endphp
                @forelse($pages as $page)
                    <a href="{{ route('pages.show', $page->slug) }}" class="hover:text-gray-800 dark:hover:text-white">{{ $page->title }}</a>
                @empty
                    <a href="#" class="hover:text-gray-800 dark:hover:text-white">Privacy</a>
                    <a href="#" class="hover:text-gray-800 dark:hover:text-white">Terms & Conditions</a>
                @endforelse
            </div>

            <div class="flex items-center gap-4 text-gray-800 dark:text-gray-300">
                <a href="#" aria-label="Instagram" class="hover:text-black dark:hover:text-white">
                    <x-icons.instagram class="w-4 h-4" />
                </a>

                <a href="#" aria-label="TikTok" class="hover:text-black dark:hover:text-white">
                    <x-icons.tiktok class="w-4 h-4" />
                </a>

                <a href="#" aria-label="Twitter" class="hover:text-black dark:hover:text-white">
                    <x-icons.twitter class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>
</footer>