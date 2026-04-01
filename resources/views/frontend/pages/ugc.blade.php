@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen transition-colors duration-200">
		<main>
			<!-- Header Section with Filter -->
			<section class="w-full flex flex-col gap-8">
				<x-frontend.partials.filter />
			</section>

			<!-- UGC Categories Grid -->
			<section class="w-full pb-8">
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
					<div>
						<h2 class="text-3xl font-semibold text-[#222] dark:text-white">
							User Generated Content (UGC)
						</h2>
						<p class="text-sm text-gray-400 font-normal dark:text-gray-400">
							Find UGC creators by category
						</p>
					</div>
				</div>

				@if ($categories->isEmpty())
					<div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
						<p class="text-sm text-gray-500 dark:text-gray-400">
							No UGC categories available at the moment.
						</p>
					</div>
				@else
					<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
						@foreach ($categories as $category)
							<a href="{{ route('influencers.category', ['categorySlug' => $category->slug]) }}"
								class="group block overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 hover:shadow-lg dark:hover:shadow-lg dark:hover:shadow-purple-500/20">
								<!-- Category Image or Icon -->
								<div
									class="relative overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 h-48 flex items-center justify-center">
									@if ($category->image_path)
										<img src="{{ \App\Helpers\ImageHelper::url($category->image_path) }}" alt="{{ $category->name }}"
											class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
									@elseif ($category->icon_path)
										<img src="{{ \App\Helpers\ImageHelper::url($category->icon_path) }}" alt="{{ $category->name }}"
											class="w-16 h-16 object-contain group-hover:scale-125 transition-transform duration-300">
									@else
										<div class="text-gray-400 dark:text-gray-500">
											<svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
													d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
											</svg>
										</div>
									@endif
								</div>

								<!-- Category Info -->
								<div class="p-4">
									<h3
										class="font-semibold text-gray-900 dark:text-white text-lg mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
										{{ $category->name }}
									</h3>
									@if ($category->description)
										<p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
											{{ $category->description }}
										</p>
									@endif

									<!-- View Creators Button -->
									<div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
										<span
											class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 group-hover:gap-3 transition-all duration-200">
											View Creators
											<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
											</svg>
										</span>
									</div>
								</div>
							</a>
						@endforeach
					</div>
				@endif
			</section>
		</main>
	</div>
@endsection
