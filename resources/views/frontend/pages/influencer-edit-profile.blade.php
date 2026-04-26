@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen">
		<div class="max-w-4xl mx-auto px-4 py-8">
			<div class="mb-6">
				<a href="{{ route('influencer.profile', ['slug' => $slug]) }}"
					class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-full text-sm font-medium text-gray-800 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
					</svg>
					Back
				</a>
			</div>

			<h1 class="text-3xl md:text-4xl font-semibold text-[#222] dark:text-white mb-6">Edit Profile</h1>

			<div class="flex gap-10 border-b border-gray-100 dark:border-gray-800 mb-8">
				<button
					class="tab-btn pb-4 text-base font-medium transition-all border-b-2 border-black dark:border-white text-black dark:text-white"
					data-tab="details">Details</button>
				<button class="tab-btn pb-4 text-base font-medium transition-all text-gray-400 hover:text-gray-600"
					data-tab="social">Social Media</button>
				<button class="tab-btn pb-4 text-base font-medium transition-all text-gray-400 hover:text-gray-600"
					data-tab="images">Images</button>
			</div>

			<div class="tab-content" id="tab-details">
				<form id="detailsForm" action="{{ route('influencer.profile.update', ['slug' => $slug]) }}" method="POST"
					class="space-y-6">
					@csrf
					<input type="hidden" name="active_tab" value="details">

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Display Name</label>
						<input type="text" name="display_name" value="{{ old('display_name', $influencer->display_name ?? '') }}"
							placeholder="Enter your display name"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('display_name')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Title Name</label>
						<input type="text" name="title_name" value="{{ old('title_name', $influencer->title_name ?? '') }}"
							placeholder="e.g. Beauty Influencer, Fitness Coach"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('title_name')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Bio</label>
						<textarea name="bio" rows="5" placeholder="Tell us about yourself, your content, and what you do..."
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('bio', $user->bio ?? '') }}</textarea>
						@error('bio')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Who is your audience?</label>
						<textarea name="audience" rows="5" placeholder="Describe your target audience"
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-none">{{ old('audience', $influencer->audience ?? '') }}</textarea>
						@error('audience')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">What brands have you worked
							with?</label>
						<input type="text" name="brands_worked_with"
							value="{{ old('brands_worked_with', $influencer->brands_worked_with ?? '') }}"
							placeholder="Enter brands you've worked with"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('brands_worked_with')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Gender</label>
							<select name="gender"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								<option value="">Select Gender</option>
								<option value="male" {{ old('gender', $user->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
								<option value="female" {{ old('gender', $user->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
								<option value="other" {{ old('gender', $user->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('gender')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Date of Birth</label>
							<input type="date" name="date_of_birth"
								value="{{ old('date_of_birth', optional($user->date_of_birth)->format('Y-m-d') ?? '') }}"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
							@error('date_of_birth')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Phone</label>
							<input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
								placeholder="Enter your phone number"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
							@error('phone')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Country</label>
							<input type="text" name="country" value="{{ old('country', $user->country ?? '') }}"
								placeholder="Enter your country"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
							@error('country')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Address Line</label>
						<input type="text" name="address_line" value="{{ old('address_line', $user->address_line ?? '') }}"
							placeholder="Enter your street address"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('address_line')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">City</label>
							<input type="text" name="city" value="{{ old('city', $user->city ?? '') }}"
								placeholder="Enter your city"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
							@error('city')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-1.5">Postal Code</label>
							<input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}"
								placeholder="Enter your postal code"
								class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
							@error('postal_code')
								<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="flex justify-end pt-4">
						<button type="submit"
							class="bg-[#1A1A1A] hover:bg-purple-400 px-8 py-3 rounded-xl text-sm font-medium text-white transition shadow-lg active:scale-95">
							Save Details
						</button>
					</div>
				</form>
			</div>

			<div class="tab-content hidden" id="tab-social">
				<form id="socialForm" action="{{ route('influencer.profile.update', ['slug' => $slug]) }}" method="POST"
					class="space-y-6">
					@csrf
					<input type="hidden" name="active_tab" value="social">

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Instagram</label>
						<input type="url" name="instagram_url"
							value="{{ old('instagram_url', $influencer->socialLinks?->instagram_url ?? '') }}"
							placeholder="https://instagram.com/yourprofile"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('instagram_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">TikTok</label>
						<input type="url" name="tiktok_url"
							value="{{ old('tiktok_url', $influencer->socialLinks?->tiktok_url ?? '') }}"
							placeholder="https://tiktok.com/@yourprofile"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('tiktok_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Facebook</label>
						<input type="url" name="facebook_url"
							value="{{ old('facebook_url', $influencer->socialLinks?->facebook_url ?? '') }}"
							placeholder="https://facebook.com/yourprofile"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('facebook_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">X (Twitter)</label>
						<input type="url" name="x_url" value="{{ old('x_url', $influencer->socialLinks?->x_url ?? '') }}"
							placeholder="https://x.com/yourprofile"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('x_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">YouTube</label>
						<input type="url" name="youtube_url"
							value="{{ old('youtube_url', $influencer->socialLinks?->youtube_url ?? '') }}"
							placeholder="https://youtube.com/c/yourchannel"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('youtube_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">LinkedIn</label>
						<input type="url" name="linkedin_url"
							value="{{ old('linkedin_url', $influencer->socialLinks?->linkedin_url ?? '') }}"
							placeholder="https://linkedin.com/in/yourprofile"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('linkedin_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label class="block text-sm font-medium text-gray-800 dark:text-gray-400 mb-2">Other Website</label>
						<input type="url" name="other_url" value="{{ old('other_url', $influencer->socialLinks?->other_url ?? '') }}"
							placeholder="https://yourwebsite.com"
							class="h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-1 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
						@error('other_url')
							<p class="mt-1 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="flex justify-end pt-4">
						<button type="submit"
							class="bg-[#1A1A1A] hover:bg-purple-400 px-8 py-3 rounded-xl text-sm font-medium text-white transition shadow-lg active:scale-95">
							Save Social Links
						</button>
					</div>
				</form>
			</div>

			<div class="tab-content hidden" id="tab-images">
				<form id="imagesForm" action="{{ route('influencer.profile.update', ['slug' => $slug]) }}" method="POST"
					enctype="multipart/form-data" class="space-y-8">
					@csrf
					<input type="hidden" name="active_tab" value="images">

					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profile & Cover</h3>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
							<div class="flex flex-col items-center">
								<div
									class="relative w-40 h-40 rounded-full bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:border-purple-400 transition"
									id="profileUploadArea" data-saved-image="{{ \App\Helpers\ImageHelper::url($user->profile_image_path) }}">
									<input type="file" name="profile_image" id="profileInput" class="hidden" accept="image/*">
									<div id="profilePreview" class="w-full h-full flex items-center justify-center">
										@if ($user->profile_image_path)
											<img src="{{ \App\Helpers\ImageHelper::url($user->profile_image_path) }}" alt="Profile"
												class="w-full h-full object-cover" onerror="this.src='{{ asset('default.webp') }}'">
										@else
											<div class="flex flex-col items-center gap-2">
												<svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
													<path
														d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
												</svg>
												<span class="text-xs text-gray-400">Click to upload</span>
											</div>
										@endif
									</div>
								</div>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Profile (1:1 ratio)</p>
							</div>

							<div class="flex flex-col">
								<div
									class="relative h-40 rounded-lg bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:border-purple-400 transition"
									id="coverUploadArea" data-saved-image="{{ \App\Helpers\ImageHelper::url($user->cover_image_path) }}">
									<input type="file" name="cover_image" id="coverInput" class="hidden" accept="image/*">
									<div id="coverPreview" class="w-full h-full flex items-center justify-center">
										@if ($user->cover_image_path)
											<img src="{{ \App\Helpers\ImageHelper::url($user->cover_image_path) }}" alt="Cover"
												class="w-full h-full object-cover">
										@else
											<div class="flex flex-col items-center gap-2">
												<svg class="w-8 h-8 text-gray-300" fill="currentColor" viewBox="0 0 24 24">
													<path
														d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
												</svg>
												<span class="text-xs text-gray-400">Click to upload</span>
											</div>
										@endif
									</div>
								</div>
								<p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Cover (3:1 ratio)</p>
							</div>
						</div>

						<div class="flex justify-end pt-4 pb-4">
							<button type="submit" name="image_type" value="profile-cover"
								class="bg-[#1A1A1A] hover:bg-purple-400 px-8 py-3 rounded-xl text-sm font-medium text-white transition shadow-lg active:scale-95">
								Save Profile & Cover
							</button>
						</div>
					</div>

					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Portfolio Images</h3>
						<p class="mb-3 text-sm text-gray-500 dark:text-gray-400">Upload multiple images and videos together. Images and videos will be saved in the order selected.</p>
						<div id="portfolioGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
							@foreach ($portfolioPreviewMedia as $portfolio)
								<div class="portfolio-item relative aspect-3/4 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm bg-gray-50 dark:bg-gray-900"
									data-id="{{ $portfolio['id'] }}">
									@if ($portfolio['media_type'] === 'image')
										<img src="{{ $portfolio['url'] }}" alt="{{ $portfolio['title'] !== '' ? $portfolio['title'] : 'Portfolio' }}"
											class="w-full h-full object-cover">
									@else
										<div class="relative h-full w-full bg-black">
											<video src="{{ $portfolio['url'] }}" poster="{{ $portfolio['poster_url'] ?? '' }}" muted playsinline preload="metadata"
												class="h-full w-full object-cover"></video>
											<button type="button" data-portfolio-video-toggle
												class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20">
												<div class="flex h-12 w-12 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm">
													<svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
														<path d="M8 5v14l11-7z" />
													</svg>
												</div>
											</button>
										</div>
									@endif
									<div class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm border border-gray-100 px-2 py-0.5 rounded text-[9px] font-bold text-gray-600 uppercase">
										{{ $portfolio['title'] !== '' ? $portfolio['title'] : 'Portfolio' }}
									</div>
									<button type="button"
										class="delete-portfolio-btn absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full transition-colors"
										data-id="{{ $portfolio['id'] }}">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
											<path d="M6 18L18 6M6 6l12 12" />
										</svg>
									</button>
								</div>
							@endforeach

							@if ($portfolioOverflowCount > 0)
								<button type="button" id="openPortfolioGalleryBtn"
									class="relative aspect-3/4 rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-center px-4 transition hover:bg-gray-200 dark:hover:bg-gray-800">
									<span class="text-lg font-bold text-gray-700 dark:text-gray-200">+{{ $portfolioOverflowCount }} more</span>
								</button>
							@endif
						</div>

						<input type="file" id="portfolioInput" class="hidden" multiple accept="image/*,video/*"
							name="portfolio_images[]">
						<button type="button" id="addPortfolioBtn"
							class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium text-white transition">
							Add Portfolio Media
						</button>
						@error('portfolio_images')
							<p class="mt-2 text-xs text-red-500">{{ $message }}</p>
						@enderror
						@error('portfolio_images.*')
							<p class="mt-2 text-xs text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="flex justify-end pt-4">
						<button type="submit" name="image_type" value="portfolio"
							class="bg-[#1A1A1A] hover:bg-purple-400 px-8 py-3 rounded-xl text-sm font-medium text-white transition shadow-lg active:scale-95">
							Save Portfolio Media
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div id="portfolio-gallery-modal" class="fixed inset-0 z-100 hidden items-center justify-center bg-black/90 p-4 sm:p-6" role="dialog" aria-modal="true">
		<div class="mx-auto flex h-full w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-gray-900">
			<div class="flex justify-end p-4 sm:p-6 pb-0">
				<button type="button" id="close-portfolio-gallery" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white" aria-label="Close gallery">
					<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>

			<div class="flex-1 overflow-y-auto p-4 sm:p-6">
				<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
					@foreach ($portfolioMediaItems as $portfolio)
						<div class="group overflow-hidden rounded-2xl bg-black">
							@if ($portfolio['media_type'] === 'image')
								<img src="{{ $portfolio['url'] }}" alt="{{ $portfolio['title'] !== '' ? $portfolio['title'] : 'Portfolio media' }}" class="h-60 w-full object-cover">
							@else
								<div class="relative">
									<video src="{{ $portfolio['url'] }}" poster="{{ $portfolio['poster_url'] ?? '' }}" muted playsinline preload="metadata" class="h-60 w-full object-cover"></video>
									<button type="button" data-portfolio-video-toggle class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20">
										<div class="flex h-14 w-14 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm">
											<svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
												<path d="M8 5v14l11-7z" />
											</svg>
										</div>
									</button>
								</div>
							@endif
						</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>

	<!-- Confirmation Modal -->
	<div id="confirmation-modal" class="fixed inset-0 z-100 hidden items-center justify-center p-4">
		<!-- Backdrop -->
		<div id="modal-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

		<!-- Modal Content -->
		<div
			class="relative w-auto max-w-md bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-95 opacity-0 duration-300"
			id="modal-container">

			<!-- Header -->
			<div class="relative p-6 text-center border-b border-gray-100 dark:border-gray-800">
				<h3 class="text-lg font-bold text-gray-900 dark:text-white">Confirm Delete</h3>
				<button id="close-modal" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
					<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>

			<!-- Body -->
			<div class="p-6">
				<p class="text-gray-700 dark:text-gray-300 text-center mb-6">Are you sure you want to delete this portfolio image? This action cannot be undone.</p>
				
				<!-- Footer Actions -->
				<div class="flex gap-3 justify-center">
					<button id="cancel-btn" class="px-6 py-2.5 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
						Cancel
					</button>
					<button id="confirm-btn" class="px-6 py-2.5 rounded-lg bg-red-500 text-white font-medium hover:bg-red-600 transition-colors">
						Delete
					</button>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				// ===== Modal Setup =====
				const confirmationModal = document.getElementById('confirmation-modal');
				const modalContainer = document.getElementById('modal-container');
				const closeBtn = document.getElementById('close-modal');
				const backdrop = document.getElementById('modal-backdrop');
				const cancelBtn = document.getElementById('cancel-btn');
				const confirmBtn = document.getElementById('confirm-btn');
				let portfolioIdToDelete = null;

				function openModal() {
					confirmationModal.classList.remove('hidden');
					confirmationModal.classList.add('flex');
					setTimeout(() => {
						modalContainer.classList.remove('scale-95', 'opacity-0');
						modalContainer.classList.add('scale-100', 'opacity-100');
					}, 10);
				}

				function closeModal() {
					modalContainer.classList.remove('scale-100', 'opacity-100');
					modalContainer.classList.add('scale-95', 'opacity-0');
					setTimeout(() => {
						confirmationModal.classList.add('hidden');
						confirmationModal.classList.remove('flex');
						portfolioIdToDelete = null;
					}, 300);
				}

				closeBtn.addEventListener('click', closeModal);
				backdrop.addEventListener('click', closeModal);
				cancelBtn.addEventListener('click', closeModal);

				// Close on escape key
				document.addEventListener('keydown', (e) => {
					if (e.key === 'Escape' && !confirmationModal.classList.contains('hidden')) {
						closeModal();
					}
				});

				// ===== Tab Navigation =====
				const tabBtns = document.querySelectorAll('.tab-btn');
				const tabContents = document.querySelectorAll('.tab-content');

				tabBtns.forEach(btn => {
					btn.addEventListener('click', () => {
						const tabId = btn.dataset.tab;
						tabContents.forEach(content => content.classList.add('hidden'));
						document.getElementById(`tab-${tabId}`).classList.remove('hidden');

						tabBtns.forEach(b => {
							b.classList.remove('border-b-2', 'border-black', 'dark:border-white',
								'text-black', 'dark:text-white');
							b.classList.add('text-gray-400', 'hover:text-gray-600');
						});
						btn.classList.remove('text-gray-400', 'hover:text-gray-600');
						btn.classList.add('border-b-2', 'border-black', 'dark:border-white', 'text-black',
							'dark:text-white');
					});
				});

				// Check sessionStorage first (for post-reload navigation), then fall back to session
				let initialTab = sessionStorage.getItem('activeProfileTab');
				if (initialTab) {
					sessionStorage.removeItem('activeProfileTab'); // Clear immediately after retrieval
				} else {
					initialTab = "{{ old('active_tab', session('active_tab', 'details')) }}" || 'details';
				}

				if (initialTab) {
					const initialButton = document.querySelector(`.tab-btn[data-tab="${initialTab}"]`);
					if (initialButton) {
						initialButton.click();
					}
				}

				const profileUploadArea = document.getElementById('profileUploadArea');
				const profileInput = document.getElementById('profileInput');
				const profilePreview = document.getElementById('profilePreview');
				const savedProfileImage = profileUploadArea.dataset.savedImage;

				profileUploadArea.addEventListener('click', () => profileInput.click());
				profileInput.addEventListener('change', function(e) {
					const file = e.target.files[0];
					if (file) {
						const reader = new FileReader();
						reader.onload = (event) => {
							if (file.type.startsWith('image/')) {
								profilePreview.innerHTML =
									`<img src="${event.target.result}" class="w-full h-full object-cover">`;
							} else if (file.type.startsWith('video/')) {
								profilePreview.innerHTML =
									`<video controls class="w-full h-full object-cover"><source src="${event.target.result}" type="${file.type}" /></video>`;
							} else {
								profilePreview.innerHTML =
									`<div class="w-full h-full flex items-center justify-center bg-gray-200 text-sm text-gray-600">Preview not available</div>`;
							}
						};
						reader.readAsDataURL(file);
					}
				});

				const coverUploadArea = document.getElementById('coverUploadArea');
				const coverInput = document.getElementById('coverInput');
				const coverPreview = document.getElementById('coverPreview');
				const savedCoverImage = coverUploadArea.dataset.savedImage;

				coverUploadArea.addEventListener('click', () => coverInput.click());
				coverInput.addEventListener('change', function(e) {
					const file = e.target.files[0];
					if (file) {
						const reader = new FileReader();
						reader.onload = (event) => {
							coverPreview.innerHTML =
								`<img src="${event.target.result}" class="w-full h-full object-cover">`;
						};
						reader.readAsDataURL(file);
					}
				});

				const portfolioGrid = document.getElementById('portfolioGrid');
				const portfolioInput = document.getElementById('portfolioInput');
				const addPortfolioBtn = document.getElementById('addPortfolioBtn');
				const openPortfolioGalleryBtn = document.getElementById('openPortfolioGalleryBtn');
				const portfolioGalleryModal = document.getElementById('portfolio-gallery-modal');
				const closePortfolioGalleryBtn = document.getElementById('close-portfolio-gallery');
				let activePortfolioVideo = null;

				addPortfolioBtn.addEventListener('click', () => portfolioInput.click());

				function pausePortfolioVideos() {
					[...portfolioGrid.querySelectorAll('video'), ...portfolioGalleryModal.querySelectorAll('video')].forEach((video) => {
						video.pause();
					});
					activePortfolioVideo = null;
				}

				function togglePortfolioVideo(trigger) {
					const wrapper = trigger.closest('.group, .portfolio-item');
					const video = wrapper ? wrapper.querySelector('video') : null;

					if (!video) {
						return;
					}

					if (video.paused) {
						pausePortfolioVideos();
						activePortfolioVideo = video;
						const playPromise = video.play();
						if (playPromise && typeof playPromise.catch === 'function') {
							playPromise.catch(() => {
								if (activePortfolioVideo === video) {
									activePortfolioVideo = null;
								}
							});
						}
					} else {
						video.pause();
						activePortfolioVideo = null;
					}
				}

				function openPortfolioGallery() {
					portfolioGalleryModal.classList.remove('hidden');
					portfolioGalleryModal.classList.add('flex');
					document.body.style.overflow = 'hidden';
				}

				function closePortfolioGallery() {
					pausePortfolioVideos();
					portfolioGalleryModal.classList.add('hidden');
					portfolioGalleryModal.classList.remove('flex');
					document.body.style.overflow = 'auto';
				}

				if (openPortfolioGalleryBtn) {
					openPortfolioGalleryBtn.addEventListener('click', openPortfolioGallery);
				}

				closePortfolioGalleryBtn.addEventListener('click', closePortfolioGallery);
				portfolioGalleryModal.addEventListener('click', (event) => {
					if (event.target === portfolioGalleryModal) {
						closePortfolioGallery();
					}
				});

				document.addEventListener('keydown', (event) => {
					if (event.key === 'Escape' && !portfolioGalleryModal.classList.contains('hidden')) {
						closePortfolioGallery();
					}
				});

				portfolioGrid.addEventListener('click', (event) => {
					const trigger = event.target.closest('[data-portfolio-video-toggle]');
					if (trigger) {
						togglePortfolioVideo(trigger);
					}
				});

				portfolioGalleryModal.addEventListener('click', (event) => {
					const trigger = event.target.closest('[data-portfolio-video-toggle]');
					if (trigger) {
						togglePortfolioVideo(trigger);
					}
				});

				portfolioInput.addEventListener('change', function(e) {
					const files = Array.from(e.target.files);

					files.forEach(file => {
						const reader = new FileReader();
						reader.onload = (event) => {
							const div = document.createElement('div');
							div.className =
								'portfolio-item relative aspect-[3/4] rounded-lg overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm bg-gray-50 dark:bg-gray-900';

							let previewHtml = '';
							if (file.type.startsWith('image/')) {
								previewHtml =
									`<img src="${event.target.result}" alt="New" class="w-full h-full object-cover">`;
							} else if (file.type.startsWith('video/')) {
								previewHtml =
									`<div class="relative h-full w-full bg-black"><video muted playsinline preload="metadata" class="w-full h-full object-cover"><source src="${event.target.result}" type="${file.type}" /></video><button type="button" data-portfolio-video-toggle class="absolute inset-0 flex items-center justify-center bg-black/10 transition hover:bg-black/20"><div class="flex h-12 w-12 items-center justify-center rounded-full bg-black/55 text-white backdrop-blur-sm"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z" /></svg></div></button></div>`;
							} else {
								previewHtml =
									`<div class="w-full h-full flex items-center justify-center bg-gray-200 text-sm text-gray-600">Preview not supported</div>`;
							}

							div.innerHTML = `
                    ${previewHtml}
                    <div class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm border border-gray-100 px-2 py-0.5 rounded text-[9px] font-bold text-gray-600 uppercase">New</div>
                    <button type="button" class="remove-new-portfolio absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white p-1.5 rounded-full transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
							portfolioGrid.appendChild(div);

							div.querySelector('.remove-new-portfolio').addEventListener('click', () =>
								div.remove());
						};
						reader.readAsDataURL(file);
					});
				});

				document.querySelectorAll('.delete-portfolio-btn').forEach(btn => {
					btn.addEventListener('click', function() {
						portfolioIdToDelete = this.dataset.id;
						openModal();
					});
				});

				confirmBtn.addEventListener('click', function() {
					if (portfolioIdToDelete) {
						const form = document.createElement('form');
						form.method = 'POST';
						form.action =
							`{{ route('influencer.profile.edit', ['slug' => $slug]) }}`
							.replace('/edit', `/portfolio/${portfolioIdToDelete}/delete`);
						form.innerHTML =
							`<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
						document.body.appendChild(form);
						form.submit();
					}
					closeModal();
				});

				['detailsForm', 'socialForm', 'imagesForm'].forEach(formId => {
					const form = document.getElementById(formId);
					const submitBtn = form.querySelector('button[type="submit"]');

					form.addEventListener('submit', async function(e) {
						if (formId === 'imagesForm') {
							// Use native multipart form submission for image uploads.
							return;
						}

						e.preventDefault();
						const formData = new FormData(this);
						const activeTab = this.querySelector('input[name="active_tab"]').value;

						// Disable submit button and show loading state
						if (submitBtn) {
							submitBtn.disabled = true;
							submitBtn.style.opacity = '0.6';
							submitBtn.style.pointerEvents = 'none';
						}

						try {
							const response = await fetch(this.action, {
								method: 'POST',
								credentials: 'same-origin',
								headers: {
									'X-Requested-With': 'XMLHttpRequest',
									'Accept': 'application/json'
								},
								body: formData
							});

							const contentType = response.headers.get('content-type') || '';
							const isJson = contentType.includes('application/json');

							if (response.status === 422) {
								const data = isJson ? await response.json() : {
									errors: {
										form: ['Validation failed. Please check your inputs.']
									}
								};
								const errors = Object.values(data.errors).flat().join('\n');
								if (window.toast) {
									window.toast.error(errors);
								}
								// Restore to previously saved images on validation failure
								if (formId === 'imagesForm') {
									// Reset file inputs
									profileInput.value = '';
									coverInput.value = '';

									// Restore saved images to preview
									if (savedProfileImage && savedProfileImage !==
										'http://qx.local/default.webp') {
										profilePreview.innerHTML =
											`<img src="${savedProfileImage}" alt="Profile" class="w-full h-full object-cover" onerror="this.src='{{ asset('default.webp') }}'">`;
									}

									if (savedCoverImage && savedCoverImage !==
										'http://qx.local/default.webp') {
										coverPreview.innerHTML =
											`<img src="${savedCoverImage}" alt="Cover" class="w-full h-full object-cover" onerror="this.src='{{ asset('default.webp') }}'">`;
									}
								}
								return;
							} else if (response.ok) {
								const data = isJson ? await response.json() : {
									message: 'Profile updated successfully!'
								};
								if (window.toast) {
									window.toast.success(data.message ||
										'Profile updated successfully!');
								}

								// For images tab, reload page to show updated images from server with correct paths
								if (formId === 'imagesForm') {
									// Store the active tab in sessionStorage so page reloads to correct tab
									sessionStorage.setItem('activeProfileTab', 'images');
									// Reload page to fetch fresh data from server
									setTimeout(() => {
										window.location.reload(
										true); // true = hard reload, bypass cache
									}, 1000);
								} else {
									// For other tabs, stay on same page and scroll to top
									form.scrollIntoView({
										behavior: 'smooth',
										block: 'start'
									});
								}
								return;
							} else {
								throw new Error('Failed to update profile');
							}
						} catch (error) {
							console.error('Error:', error);
							if (window.toast) {
								window.toast.error(error.message ||
									'Error updating profile. Please try again.');
							}
						} finally {
							// Re-enable submit button
							if (submitBtn) {
								submitBtn.disabled = false;
								submitBtn.style.opacity = '1';
								submitBtn.style.pointerEvents = 'auto';
							}
						}
					});
				});
			});
		</script>
	@endpush
@endsection
