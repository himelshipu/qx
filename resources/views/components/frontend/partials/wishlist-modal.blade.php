<div
	class="fixed inset-0 z-100 hidden items-center justify-center p-4"
	data-wishlist-modal
	data-wishlist-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
	data-wishlist-login-url="{{ route('login') }}"
	data-wishlist-status-url="{{ route('frontend.wishlists.status') }}"
	data-wishlist-lists-url="{{ route('frontend.wishlists.index') }}"
	data-wishlist-base-url="{{ url('/wishlist/lists') }}"
	role="dialog"
	aria-modal="true"
	aria-hidden="true">
	<div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity" data-wishlist-modal-backdrop></div>

	<div class="relative w-full max-w-md overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-all duration-300 scale-95 opacity-0 dark:bg-gray-900 lg:w-125" data-wishlist-modal-panel>
		<div class="relative border-b border-gray-100 p-6 text-center dark:border-gray-800">
			<h3 class="text-xl font-bold text-gray-900 dark:text-white" data-wishlist-modal-title>Add to List</h3>
			<button type="button" class="absolute right-6 top-6 text-gray-400 hover:text-gray-600" data-wishlist-modal-close aria-label="Close wishlist modal">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<div class="space-y-4 p-6" data-wishlist-modal-default-view>
			<button type="button" class="group flex w-full items-center gap-4 rounded-2xl p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800" data-wishlist-create-list>
				<div class="flex h-14 w-14 items-center justify-center rounded-xl bg-black dark:bg-white">
					<svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white dark:text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
					</svg>
				</div>
				<span class="text-lg font-bold text-gray-900 dark:text-white">Create new list</span>
			</button>

			<div class="space-y-2" data-wishlist-empty-state>
				<p class="rounded-2xl border border-dashed border-gray-200 px-4 py-5 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">No lists yet. Create your first list.</p>
			</div>

			<div class="hidden space-y-2" data-wishlist-existing-lists></div>
			<div class="hidden text-sm text-gray-500 dark:text-gray-400" data-wishlist-loading-state>Loading your lists...</div>
		</div>

		<div class="hidden p-6" data-wishlist-modal-create-view>
			<div class="space-y-6">
				<div class="space-y-3">
					<input
						type="text"
						class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-4 text-base text-gray-900 outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 dark:border-gray-700 dark:bg-gray-950 dark:text-white"
						placeholder="Name"
						data-wishlist-create-input>

					<button
						type="button"
						class="w-full rounded-2xl bg-[#222] px-4 py-4 text-base font-semibold text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-60"
						data-wishlist-create-submit>
						Create
					</button>
				</div>

				<div class="flex items-center justify-between text-sm">
					<button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" data-wishlist-back-to-lists>
						Back to lists
					</button>
					<a href="{{ route('frontend.wishlist.index') }}" class="font-semibold text-[#222] hover:underline dark:text-white">
						View List
					</a>
				</div>
			</div>
		</div>
	</div>
</div>