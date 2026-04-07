@extends('backend.layouts.app')

@section('title', "Portfolio - {$influencer->display_name}")

@section('content')
	<x-backend.shell.breadcrumb :items="[
	    ['label' => 'Influencers', 'route' => 'dashboard.influencers.index'],
	    ['label' => $influencer->display_name, 'route' => 'dashboard.influencers.view', 'params' => $influencer->id],
	    ['label' => 'Portfolio'],
	]" />

	<div class="space-y-6">
		<div class="flex items-center justify-between">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Portfolio Management</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage photos and videos for {{ $influencer->display_name }}</p>
			</div>
			<a href="{{ route('dashboard.influencers.portfolio.create', $influencer) }}"
				class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600">
				<x-icons.plus class="h-4 w-4" />
				Add Portfolio Item
			</a>
		</div>

		<!-- Statistics -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Items</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $portfolios->count() }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">
					{{ $portfolios->where('is_active', true)->count() }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Images</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ $portfolios->where('media_type', 'image')->count() }}</p>
			</div>
		</div>

		<!-- Portfolio Items Grid -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-5 dark:border-gray-800">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Portfolio Items</h3>
			</div>

			@if ($portfolios->isNotEmpty())
				<div class="grid grid-cols-1 gap-4 p-5">
					@foreach ($portfolios as $portfolio)
						<div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-800">
							<div class="flex items-center gap-4">
								<!-- Thumbnail -->
								<div class="relative w-16 h-16 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800">
									@if ($portfolio->media_type === 'image')
										<img src="{{ \App\Helpers\ImageHelper::url($portfolio->file_path) }}" alt="{{ $portfolio->title }}"
											class="w-full h-full object-cover">
									@else
										<div class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-700">
											<svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
												<path d="M4 6h16v10H4z" />
												<path d="M4 18h16v2H4z" />
											</svg>
										</div>
									@endif
								</div>

								<!-- Info -->
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<h4 class="font-semibold text-gray-900 dark:text-white">
											{{ $portfolio->title ?: 'Untitled' }}
										</h4>
										<span
											class="inline-flex items-center rounded-full {{ $portfolio->media_type === 'image' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }} dark:{{ $portfolio->media_type === 'image' ? 'bg-blue-900/30 dark:text-blue-300' : 'bg-purple-900/30 dark:text-purple-300' }} px-2.5 py-0.5 text-xs font-medium">
											{{ ucfirst($portfolio->media_type) }}
										</span>
										@if (!$portfolio->is_active)
											<span
												class="inline-flex items-center rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 px-2.5 py-0.5 text-xs font-medium">
												Hidden
											</span>
										@endif
									</div>
									@if ($portfolio->description)
										<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($portfolio->description, 100) }}</p>
									@endif
									<p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Uploaded {{ $portfolio->created_at->diffForHumans() }}
									</p>
								</div>
							</div>

							<!-- Actions -->
							<div class="flex items-center gap-2">
								<!-- Visibility Toggle -->
								<button data-portfolio-id="{{ $portfolio->id }}"
									data-toggle-url="{{ route('dashboard.influencers.portfolio.toggle', [$influencer, $portfolio]) }}"
									class="toggle-visibility inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
									title="{{ $portfolio->is_active ? 'Hide' : 'Show' }}">
									@if ($portfolio->is_active)
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
										</svg>
									@else
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
										</svg>
									@endif
								</button>

								<!-- Edit Button -->
								<a href="{{ route('dashboard.influencers.portfolio.edit', [$influencer, $portfolio]) }}"
									class="inline-flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
									<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
									</svg>
								</a>

								<!-- Delete Button -->
								<form action="{{ route('dashboard.influencers.portfolio.destroy', [$influencer, $portfolio]) }}" method="POST"
									class="inline js-confirmable"
									data-confirm-title="Delete Portfolio Item"
									data-confirm-message="Are you sure you want to delete this portfolio item?"
									data-confirm-button="Delete"
									data-confirm-variant="danger">
									@csrf
									@method('DELETE')
									<button type="submit"
										class="inline-flex items-center justify-center rounded-lg p-2 text-red-500 transition hover:bg-red-100 dark:hover:bg-red-900/20">
										<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
										</svg>
									</button>
								</form>
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="flex flex-col items-center justify-center py-12 px-4">
					<div class="text-center">
						<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>
						<h3 class="mt-4 text-sm font-medium text-gray-900 dark:text-white">No portfolio items yet</h3>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding your first photo or video.</p>
						<div class="mt-6">
							<a href="{{ route('dashboard.influencers.portfolio.create', $influencer) }}"
								class="inline-flex items-center gap-2 rounded-lg bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-700">
								<x-icons.plus class="h-4 w-4" />
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
