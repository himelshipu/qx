@extends('backend.layouts.app')

@section('title', "Portfolio - {$influencer->display_name}")

@section('content')
	<x-backend.shell.breadcrumb :items="[
	    ['label' => 'Influencers', 'route' => 'dashboard.influencers.index'],
	    ['label' => $influencer->display_name, 'route' => 'dashboard.influencers.view', 'params' => $influencer->id],
	    ['label' => 'Portfolio'],
	]" />

	<div class="space-y-6">
		<!-- Influencer Profile Header Card -->
		<div class="rounded-xl border border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50 shadow-sm dark:border-gray-800 dark:from-gray-800 dark:to-gray-900 overflow-hidden">
			<div class="p-6">
				<div class="flex items-start justify-between gap-6">
					<!-- Left: Influencer Info -->
					<div class="flex items-start gap-4 flex-1">
						<!-- Profile Image -->
						<div class="relative flex-shrink-0">
							<div class="w-20 h-20 rounded-xl overflow-hidden border-2 border-white dark:border-gray-700 shadow-md bg-gray-100 dark:bg-gray-800">
								@if ($influencer->user->profile_image_path)
									<img src="{{ \App\Helpers\ImageHelper::url($influencer->user->profile_image_path) }}" 
										alt="{{ $influencer->display_name }}"
										class="w-full h-full object-cover">
								@else
									<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-400 to-blue-400">
										<span class="text-2xl font-bold text-white">{{ substr($influencer->display_name, 0, 1) }}</span>
									</div>
								@endif
							</div>
						</div>

						<!-- Name & Details -->
						<div class="flex-1">
							<div class="flex items-center gap-3 mb-2">
								<h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $influencer->display_name }}</h1>
								<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $influencer->user->is_verified ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
									@if ($influencer->user->is_verified)
										<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
										Verified
									@else
										Unverified
									@endif
								</span>
							</div>
							<p class="text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">{{ $influencer->title_name ?? 'Influencer' }}</p>
							@if ($influencer->user->bio)
								<p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $influencer->user->bio }}</p>
							@endif
						</div>
					</div>

					<!-- Right: Action Buttons -->
					<div class="flex flex-col gap-3">
						<a href="{{ route('dashboard.influencers.portfolio.create', $influencer) }}"
							class="inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 shadow-md hover:shadow-lg">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
							</svg>
							Add Portfolio Item
						</a>
						<a href="{{ route('dashboard.influencers.view', $influencer) }}"
							class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
							</svg>
							View Profile
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Header Section -->
		<div class="flex items-center justify-between">
			<div>
				<h2 class="text-xl font-bold text-gray-900 dark:text-white">Portfolio Items</h2>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage and organize portfolio media</p>
			</div>
		</div>

		<!-- Statistics -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Items</p>
						<p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $portfolios->count() }}</p>
					</div>
					<div class="rounded-lg bg-blue-100 dark:bg-blue-900/30 p-3">
						<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>
					</div>
				</div>
			</div>

			<div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100/50 p-4 dark:border-blue-900/40 dark:from-blue-900/20 dark:to-blue-900/10 shadow-sm">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Active Items</p>
						<p class="mt-2 text-3xl font-bold text-blue-700 dark:text-blue-200">{{ $portfolios->where('is_active', true)->count() }}</p>
					</div>
					<div class="rounded-lg bg-blue-200 dark:bg-blue-900/50 p-3">
						<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
							<path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
							<path fill-rule="evenodd" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" clip-rule="evenodd" />
						</svg>
					</div>
				</div>
			</div>

			<div class="rounded-xl border border-green-200 bg-gradient-to-br from-green-50 to-green-100/50 p-4 dark:border-green-900/40 dark:from-green-900/20 dark:to-green-900/10 shadow-sm">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Images</p>
						<p class="mt-2 text-3xl font-bold text-green-700 dark:text-green-200">{{ $portfolios->where('media_type', 'image')->count() }}</p>
					</div>
					<div class="rounded-lg bg-green-200 dark:bg-green-900/50 p-3">
						<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
							<path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>
					</div>
				</div>
			</div>

			<div class="rounded-xl border border-purple-200 bg-gradient-to-br from-purple-50 to-purple-100/50 p-4 dark:border-purple-900/40 dark:from-purple-900/20 dark:to-purple-900/10 shadow-sm">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">Videos</p>
						<p class="mt-2 text-3xl font-bold text-purple-700 dark:text-purple-200">{{ $portfolios->where('media_type', 'video')->count() }}</p>
					</div>
					<div class="rounded-lg bg-purple-200 dark:bg-purple-900/50 p-3">
						<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
							<path d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
							<path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zM0 12a12 12 0 1024 0A12 12 0 000 12z" clip-rule="evenodd" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Portfolio Items Grid -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Portfolio Items</h3>
			</div>

			@if ($portfolios->isNotEmpty())
				<div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
					@foreach ($portfolios as $portfolio)
						<div class="group relative rounded-lg border border-gray-200 overflow-hidden bg-white hover:border-purple-400 dark:border-gray-800 dark:hover:border-purple-500 transition-all shadow-sm hover:shadow-md dark:bg-gray-800">
						<!-- Thumbnail Container - Clickable to Edit -->
						<a href="{{ route('dashboard.influencers.portfolio.edit', [$influencer, $portfolio]) }}"
							class="relative overflow-hidden bg-gray-100 dark:bg-gray-700 block aspect-square">
							@if ($portfolio->media_type === 'image')
								<img src="{{ \App\Helpers\ImageHelper::url($portfolio->file_path) }}" alt="{{ $portfolio->title }}"
									class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
							@else
								<video src="{{ \App\Helpers\ImageHelper::url($portfolio->file_path) }}" 
									class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
									muted playsinline></video>
							@endif

							<!-- Status Badge -->
								<div class="absolute top-2 right-2 flex gap-2">
									@if (!$portfolio->is_active)
										<span class="inline-flex items-center rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 px-2.5 py-0.5 text-xs font-semibold shadow-sm">
											Hidden
										</span>
									@else
										<span class="inline-flex items-center rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 px-2.5 py-0.5 text-xs font-semibold shadow-sm">
											Active
										</span>
									@endif
								</div>

								<!-- Media Type Badge -->
								<div class="absolute bottom-2 left-2">
									<span class="inline-flex items-center rounded-full {{ $portfolio->media_type === 'image' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' }} px-2.5 py-0.5 text-xs font-semibold shadow-sm">
										{{ ucfirst($portfolio->media_type) }}
									</span>
								</div>

								<!-- Hover Overlay -->
								<div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-colors duration-300 flex items-center justify-center">
									<svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
									</svg>
								</div>
							</a>

							<!-- Info Section -->
							<div class="p-4">
								<a href="{{ route('dashboard.influencers.portfolio.edit', [$influencer, $portfolio]) }}"
									class="block hover:text-purple-600 dark:hover:text-purple-400 transition-colors mb-1">
									<h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2 text-sm">
										{{ $portfolio->title ?: 'Untitled Item' }}
									</h4>
								</a>

								@if ($portfolio->description)
									<p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-2">{{ $portfolio->description }}</p>
								@endif

								<p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
									Uploaded {{ $portfolio->created_at->diffForHumans() }}
								</p>

								<!-- Action Buttons -->
								<div class="flex items-center gap-2 justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
									<div class="flex items-center gap-2">
										<!-- Visibility Toggle -->
										<button data-portfolio-id="{{ $portfolio->id }}"
											data-toggle-url="{{ route('dashboard.influencers.portfolio.toggle', [$influencer, $portfolio]) }}"
											class="toggle-visibility inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 hover:text-gray-700"
											title="{{ $portfolio->is_active ? 'Hide from portfolio' : 'Show in portfolio' }}">
											@if ($portfolio->is_active)
												<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
												</svg>
											@else
												<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
												</svg>
											@endif
										</button>

										<!-- Edit Button -->
										<a href="{{ route('dashboard.influencers.portfolio.edit', [$influencer, $portfolio]) }}"
											class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 hover:text-gray-700"
											title="Edit item">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
											</svg>
										</a>
									</div>

									<!-- Delete Button -->
									<form action="{{ route('dashboard.influencers.portfolio.destroy', [$influencer, $portfolio]) }}" method="POST"
										class="inline js-confirmable"
										data-confirm-title="Delete Portfolio Item"
										data-confirm-message="Are you sure you want to delete this portfolio item? This action cannot be undone."
										data-confirm-button="Delete"
										data-confirm-variant="danger">
										@csrf
										@method('DELETE')
										<button type="submit"
											class="inline-flex items-center justify-center rounded-lg p-2 text-red-500 transition hover:bg-red-100 dark:hover:bg-red-900/20 hover:text-red-700"
											title="Delete item">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
											</svg>
										</button>
									</form>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="flex flex-col items-center justify-center py-16 px-4">
					<div class="text-center">
						<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
							<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
							</svg>
						</div>
						<h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">No portfolio items yet</h3>
						<p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm">Start building {{ $influencer->display_name }}'s portfolio by uploading their first photo or video.</p>
						<div class="mt-6">
							<a href="{{ route('dashboard.influencers.portfolio.create', $influencer) }}"
								class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600 shadow-md hover:shadow-lg">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								Add Your First Item
							</a>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>

	@push('scripts')
		<script>
			document.querySelectorAll('.toggle-visibility').forEach(button => {
				button.addEventListener('click', async (e) => {
					e.preventDefault();
					const url = button.dataset.toggleUrl;
					try {
						const response = await fetch(url, {
							method: 'POST',
							headers: {
								'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
									.content,
								'Content-Type': 'application/json'
							}
						});
						const data = await response.json();
						if (data.success) {
							location.reload();
						}
					} catch (error) {
						console.error('Error:', error);
					}
				});
			});
		</script>
	@endpush
@endsection
