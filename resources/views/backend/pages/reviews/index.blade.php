@extends('backend.layouts.app')

@section('title', 'Reviews')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Reviews" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Reviews</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Public</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['public'] }}</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Private</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">{{ $stats['private'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Avg Rating</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ number_format($stats['avg_rating'], 1) }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Review Management</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review real feedback records from the platform.</p>
			</div>

			<div class="p-5">
				<form method="GET" action="{{ route('dashboard.reviews.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5">
					<div class="md:col-span-2">
						<label for="q" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<input id="q" name="q" type="text" value="{{ $search }}" placeholder="Title, comment, brand, creator"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div>
						<label for="visibility" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Visibility</label>
						<select id="visibility" name="visibility"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $visibility === 'all' ? 'selected' : '' }}>All</option>
							<option value="public" {{ $visibility === 'public' ? 'selected' : '' }}>Public</option>
							<option value="private" {{ $visibility === 'private' ? 'selected' : '' }}>Private</option>
						</select>
					</div>
					<div>
						<label for="rating" class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rating</label>
						<select id="rating" name="rating"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $rating === 'all' ? 'selected' : '' }}>All</option>
							@for ($i = 5; $i >= 1; $i--)
								<option value="{{ $i }}" {{ $rating === (string) $i ? 'selected' : '' }}>{{ $i }} star</option>
							@endfor
						</select>
					</div>
					<div class="flex items-end gap-2 md:col-span-5 lg:col-span-1">
						<button type="submit" class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Apply</button>
						<a href="{{ route('dashboard.reviews.index') }}" class="h-10 w-full rounded-lg border border-gray-200 px-3 text-center text-sm font-medium leading-10 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Reset</a>
					</div>
				</form>

				<div class="overflow-x-auto">
					<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
						<thead class="bg-gray-50 dark:bg-gray-800/50">
							<tr>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Creator</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Review</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Rating</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Visibility</th>
								<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
								<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
							@forelse ($reviews as $review)
								<tr class="transition hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
									<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $review->brand?->brand_name ?? 'N/A' }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $review->creator?->user?->name ?? 'N/A' }}</td>
									<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
										<p class="font-medium text-gray-900 dark:text-white">{{ $review->title ?: 'Untitled review' }}</p>
										<p class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($review->comment ?: '-', 90) }}</p>
									</td>
									<td class="px-4 py-3 text-sm">
										<div class="flex items-center gap-1">
											@for ($i = 1; $i <= 5; $i++)
												<span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}">★</span>
											@endfor
											<span class="ml-1 text-xs text-gray-500 dark:text-gray-400">{{ $review->rating }}/5</span>
										</div>
									</td>
									<td class="px-4 py-3 text-sm">
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $review->is_public ? 'checked' : '' }} onchange="toggleReviewVisibility({{ $review->id }}, this)"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
											</label>
										</div>
									</td>
									<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $review->created_at?->format('M d, Y') }}</td>
									<td class="px-4 py-3 text-right">
										<a href="{{ route('dashboard.reviews.show', $review) }}"
											class="inline-flex items-center rounded-lg border border-gray-200 p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
											title="View review">
											<x-icons.eye class="h-4 w-4" />
										</a>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">No reviews found for the current filters.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if ($reviews->hasPages())
					<div class="mt-5 border-t border-gray-200 pt-4 dark:border-gray-800">
						{{ $reviews->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function toggleReviewVisibility(reviewId, checkbox) {
				if (checkbox?.disabled) {
					return;
				}

				if (checkbox) {
					checkbox.disabled = true;
				}

				const urlTemplate = @json(route('dashboard.reviews.toggle-visibility', ['review' => '__ID__']));
				const url = urlTemplate.replace('__ID__', String(reviewId));

				fetch(url, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': @json(csrf_token()),
							'Accept': 'application/json',
							'Content-Type': 'application/json'
						}
					})
					.then((response) => {
						if (!response.ok) {
							throw new Error('Failed to update visibility');
						}

						return response.json();
					})
					.then((data) => {
						if (data.success) {
							if (checkbox && typeof data.is_public !== 'undefined') {
								checkbox.checked = Boolean(data.is_public);
							}

							if (window.toast) {
								window.toast.success(data.message || 'Review visibility updated successfully.');
							}
							return;
						}

						throw new Error(data.message || 'Failed to update review visibility.');
					})
					.catch((error) => {
						console.error(error);
						if (window.toast) {
							window.toast.error(error?.message || 'Unable to update review visibility right now.');
						}

						if (checkbox) {
							checkbox.checked = !checkbox.checked;
						}
					})
					.finally(() => {
						if (checkbox) {
							checkbox.disabled = false;
						}
					});
			}
		</script>
	@endpush
@endsection

