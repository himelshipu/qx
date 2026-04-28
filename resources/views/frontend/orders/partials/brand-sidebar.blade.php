<div class="space-y-6 lg:col-span-1">
	@if ($brandInfo)
		<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h3 class="font-semibold text-gray-900 dark:text-white">Brand Info</h3>
			</div>
			<div class="p-6">
				<div class="flex items-center gap-4">
					<div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-sm font-bold text-indigo-700">
						{{ $brandInitial }}</div>
					<div class="min-w-0">
						<p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $brandInfo['name'] }}</p>
						<p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $brandInfo['email'] }}</p>
					</div>
				</div>
				@if ($brandSlug && !$isBrand)
					<a href="{{ route('brand.profile', ['slug' => $brandSlug]) }}"
						class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-black dark:bg-indigo-600 dark:hover:bg-indigo-500">
						<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M5.121 17.804A8 8 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
						</svg>
						View brand profile
					</a>
				@endif
			</div>
		</div>

		<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
				<h3 class="font-semibold text-gray-900 dark:text-white">Review Brand</h3>
			</div>
			<div class="space-y-3 p-6 text-sm text-gray-600 dark:text-gray-400">
				@if ($canLeaveReview)
					<form method="POST" action="{{ route('frontend.orders.reviews.store', $order) }}" class="space-y-3">
						@csrf
						<div>
							<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Rating</label>
							<select name="rating"
								class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
								required>
								@for ($i = 5; $i >= 1; $i--)
									<option value="{{ $i }}">{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
								@endfor
							</select>
						</div>
						<div>
							<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Title</label>
							<input type="text" name="title"
								class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
								placeholder="Short review title">
						</div>
						<div>
							<label class="mb-1 block text-xs font-semibold text-gray-600 dark:text-gray-300">Comment</label>
							<textarea name="comment" rows="4"
							 class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100"
							 placeholder="Share your experience with the brand"></textarea>
						</div>
						<button type="submit"
							class="w-full rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-500">Submit
							Review</button>
					</form>
				@elseif (($hasSubmittedReview ?? false) === true)
					<p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700">You already
						reviewed this brand for this order.</p>
				@else
					<p
						class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
						You can leave a review after the brand completes the order.</p>
				@endif
				<a href="{{ route('frontend.orders.index') }}"
					class="block rounded-lg bg-gray-100 px-4 py-2 text-center font-semibold text-gray-900 hover:bg-gray-200 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700">Back
					to Orders</a>
			</div>
		</div>
	@endif
</div>
