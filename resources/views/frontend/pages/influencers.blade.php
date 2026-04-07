@extends('frontend.layouts.app')

@section('content')
	@php
		$currentPlatformLabel = $selectedPlatform['label'] ?? 'All Platforms';
	@endphp

	<div class="min-h-screen transition-colors duration-200">
		<main>
			<div class="w-7xl mx-auto">
				<x-frontend.partials.filter />
			</div>

			<section class="w-full pb-8">
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3">
					<div>
						<h2 class="text-3xl font-semibold text-[#222] dark:text-white">
							{{ $selectedPlatform ? $selectedPlatform['label'] . ' Influencers' : 'Influencers' }}
						</h2>
						<p class="text-sm text-gray-400 font-normal dark:text-gray-400">
							{{ $selectedPlatform ? 'Hire top ' . $selectedPlatform['label'] . ' influencers' : 'Hire top influencers across all platforms' }}
						</p>
					</div>
				</div>

				@if ($influencers->isEmpty())
					<div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-8 text-center">
						<p class="text-sm text-gray-500 dark:text-gray-400">
							No active influencers found for this platform yet.
						</p>
					</div>
				@else
					<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
						@foreach ($influencers as $influencer)
							@php
								$profileUrl = !empty($influencer['slug']) ? route('influencer.profile', ['slug' => $influencer['slug']]) : '#';
							@endphp

							<a href="{{ $profileUrl }}" class="group overflow-hidden font-sans cursor-pointer creator-card block"
								data-creator-id="{{ $influencer['id'] }}">
								<div class="relative overflow-hidden rounded-xl">
									<button type="button"
										class="wishlist-btn absolute top-3 right-3 z-30 p-1.5 transition-all duration-300 hover:scale-110 drop-shadow-md"
										onclick="event.preventDefault(); event.stopPropagation();">
										<x-icons.heart class="w-6 h-6 wishlist-heart-icon fill-none stroke-white stroke-[2px]" />
									</button>

									<img src="{{ image_url($influencer['image_url']) }}"
										class="w-full h-48 sm:h-64 object-cover transition-transform duration-500 ease-out group-hover:scale-110"
										alt="{{ $influencer['name'] }}">

									<div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
										<span
											class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
											<x-icons.heart-badge class="w-4 h-4 text-purple-400" />
											{{ $influencer['platform_label'] }} Creator
										</span>
										<span
											class="bg-black/80 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-1 rounded-md border border-white/20 flex items-center gap-1">
											<x-icons.checkmark class="w-4 h-4 text-green-500" />
											{{ $influencer['engagement_label'] }} ER
										</span>
									</div>

									<div class="absolute bottom-3 left-3 right-3">
										<div class="flex flex-row items-center gap-2">
											<div
												class="bg-white text-black text-[10px] font-medium px-2 py-0.5 rounded-md w-fit flex items-center gap-1 mb-1">
												@if ($influencer['platform'] === 'facebook')
													<x-icons.facebook class="w-4 h-4 text-blue-600" />
												@elseif ($influencer['platform'] === 'instagram')
													<x-icons.instagram class="w-4 h-4 text-pink-500" />
												@elseif ($influencer['platform'] === 'tiktok')
													<x-icons.tiktok class="w-4 h-4 text-black" />
												@elseif ($influencer['platform'] === 'x')
													<x-icons.x class="w-4 h-4 text-black" />
												@elseif ($influencer['platform'] === 'ugc')
													<x-icons.camera class="w-4 h-4 text-gray-700" />
												@else
													<x-icons.group class="w-4 h-4 text-gray-700" />
												@endif
												{{ $influencer['followers_label'] }}
											</div>
										</div>
										<div class="flex items-center gap-1 text-white drop-shadow-md">
											<span class="font-bold text-sm">{{ $influencer['name'] }}</span>
											<span class="flex items-center text-xs gap-0.5">
												<x-icons.star class="w-4 h-4 text-yellow-400" />
												{{ $influencer['rating_label'] }}
											</span>
										</div>
									</div>
								</div>

								<div class="pt-3 px-1">
									<div class="flex items-start justify-between gap-3">
										<h3 class="text-gray-800 dark:text-gray-200 text-[15px] leading-tight font-medium line-clamp-1">
											{{ $influencer['title'] }}
										</h3>
										<span class="text-[#222] dark:text-white font-medium text-sm leading-none">
											{{ $influencer['handle'] ?: (!empty($influencer['slug']) ? '@' . $influencer['slug'] : 'N/A') }}
										</span>
									</div>
									<p class="text-[13px] text-gray-400 font-normal mt-1">{{ $influencer['location'] ?: 'N/A' }}</p>
								</div>
							</a>
						@endforeach
					</div>

					<div class="mt-10">
						{{ $influencers->onEachSide(1)->links() }}
					</div>
				@endif
			</section>
		</main>
	</div>

	<!-- Modal Overlay -->
	<div id="wishlist-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
		<div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

		<div
			class="relative w-auto lg:w-[500px] max-w-md bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300"
			id="modal-container">
			<div class="relative p-6 text-center border-b border-gray-100 dark:border-gray-800">
				<h3 class="text-xl font-bold text-gray-900 dark:text-white">Add to List</h3>
				<button id="close-modal" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600" type="button">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>

			<div class="p-6 space-y-4">
				<button
					class="w-full flex items-center gap-4 p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors group"
					type="button">
					<div class="w-14 h-14 bg-black dark:bg-white flex items-center justify-center rounded-xl">
						<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white dark:text-black" fill="none"
							viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
						</svg>
					</div>
					<span class="text-lg font-bold text-gray-900 dark:text-white">Create new list</span>
				</button>

				<div id="existing-lists" class="space-y-2">
					<button
						class="list-item w-full flex items-center gap-4 p-4 rounded-2xl bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
						type="button">
						<div class="w-14 h-14 bg-gray-200 dark:bg-gray-700 rounded-xl flex items-center justify-center overflow-hidden">
							<img src="" class="list-preview-img hidden w-full h-full object-cover" alt="Selected influencer preview">
							<svg class="placeholder-icon w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path
									d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
								</path>
							</svg>
						</div>
						<div class="text-left">
							<p class="font-bold text-gray-900 dark:text-white text-lg">Brain Capita</p>
							<p class="text-sm text-gray-500">0 influencers</p>
						</div>
					</button>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const modal = document.getElementById('wishlist-modal');
			const modalContainer = document.getElementById('modal-container');
			const closeBtn = document.getElementById('close-modal');
			const backdrop = document.getElementById('modal-backdrop');
			const wishlistBtns = document.querySelectorAll('.wishlist-btn');

			if (!modal || !modalContainer || !closeBtn || !backdrop) {
				return;
			}

			const openModal = (card, btn) => {
				const heart = btn.querySelector('.wishlist-heart-icon');
				const imageElement = card ? card.querySelector('img') : null;
				const imageUrl = imageElement ? imageElement.src : '';

				if (heart) {
					heart.classList.remove('fill-none', 'stroke-white');
					heart.classList.add('fill-red-500', 'stroke-red-500');
				}

				const previewImg = modal.querySelector('.list-preview-img');
				const placeholder = modal.querySelector('.placeholder-icon');

				if (previewImg) {
					previewImg.src = imageUrl;
					previewImg.classList.remove('hidden');
				}

				if (placeholder) {
					placeholder.classList.add('hidden');
				}

				modal.classList.remove('hidden');
				modal.classList.add('flex');

				setTimeout(() => {
					modalContainer.classList.remove('scale-95', 'opacity-0');
					modalContainer.classList.add('scale-100', 'opacity-100');
				}, 10);
			};

			const closeModal = () => {
				modalContainer.classList.add('scale-95', 'opacity-0');
				modalContainer.classList.remove('scale-100', 'opacity-100');

				setTimeout(() => {
					modal.classList.add('hidden');
					modal.classList.remove('flex');
				}, 300);
			};

			wishlistBtns.forEach((btn) => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const card = this.closest('.creator-card');
					const heart = this.querySelector('.wishlist-heart-icon');

					if (heart && heart.classList.contains('fill-red-500')) {
						heart.classList.add('fill-none', 'stroke-white');
						heart.classList.remove('fill-red-500', 'stroke-red-500');
					} else {
						openModal(card, this);
					}
				});
			});

			closeBtn.addEventListener('click', closeModal);
			backdrop.addEventListener('click', closeModal);

			document.addEventListener('keydown', (e) => {
				if (e.key === 'Escape') {
					closeModal();
				}
			});
		});
	</script>
@endpush
