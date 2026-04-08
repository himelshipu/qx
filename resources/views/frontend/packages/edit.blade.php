@extends('frontend.layouts.app')

@section('content')
	<section class="py-12">
		<div class="max-w-2xl mx-auto px-4">
			<!-- Breadcrumb and Back Button -->
			<div class="mb-8">
				<a href="{{ route('frontend.packages.index') }}"
					class="inline-flex items-center text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-medium transition-colors">
					<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
					Back to Packages
				</a>
			</div>

			<!-- Main Card -->
			<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
				<!-- Header Section -->
				<div
					class="p-8 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-purple-50 to-purple-100 dark:from-gray-800 dark:to-gray-700">
					<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Package</h1>
					<p class="text-gray-600 dark:text-gray-400">Update your service package information</p>
				</div>

				<!-- Form Section -->
				<div class="p-8">
					@if ($errors->any())
						<div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
							<div class="flex items-start gap-3">
								<svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
									<path fill-rule="evenodd"
										d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
										clip-rule="evenodd" />
								</svg>
								<div>
									<h3 class="font-semibold text-red-800 dark:text-red-200 mb-2">Please fix the following errors:</h3>
									<ul class="list-disc list-inside space-y-1">
										@foreach ($errors->all() as $error)
											<li class="text-red-700 dark:text-red-300 text-sm">{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>
					@endif

					<form action="{{ route('frontend.packages.update', $package) }}" method="POST" class="space-y-6">
						@csrf
						@method('PUT')

						<!-- Package Name Field -->
						<div>
							<label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Package Name <span class="text-red-600">*</span>
							</label>
							<input type="text" id="name" name="name"
								class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
								value="{{ old('name', $package->name) }}" placeholder="e.g., Instagram Reel Creation" required>
							@error('name')
								<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Description Field -->
						<div>
							<label for="description" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Description <span class="text-red-600">*</span>
							</label>
							<textarea id="description" name="description" rows="5"
							 class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition resize-none @error('description') border-red-500 @enderror"
							 placeholder="Describe what's included in this package, deliverables, and any special requirements..." required>{{ old('description', $package->description) }}</textarea>
							<p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Provide clear details about what brands will receive</p>
							@error('description')
								<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Form Grid -->
						<div class="grid grid-cols-2 md:grid-cols-4 gap-6">
							<!-- Platform Field -->
							<div>
								<label for="platform" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
									Platform <span class="text-red-600">*</span>
								</label>
								<select id="platform" name="platform"
									class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition @error('platform') border-red-500 @enderror"
									required>
									<option value="">Select Platform</option>
									<option value="facebook" {{ old('platform', $package->platform) === 'facebook' ? 'selected' : '' }}>Facebook
									</option>
									<option value="instagram" {{ old('platform', $package->platform) === 'instagram' ? 'selected' : '' }}>Instagram
									</option>
									<option value="tiktok" {{ old('platform', $package->platform) === 'tiktok' ? 'selected' : '' }}>TikTok</option>
									<option value="linkedin" {{ old('platform', $package->platform) === 'linkedin' ? 'selected' : '' }}>LinkedIn
									</option>
									<option value="x" {{ old('platform', $package->platform) === 'x' ? 'selected' : '' }}>X (Twitter)</option>
									<option value="youtube" {{ old('platform', $package->platform) === 'youtube' ? 'selected' : '' }}>YouTube
									</option>
									<option value="ugc" {{ old('platform', $package->platform) === 'ugc' ? 'selected' : '' }}>UGC</option>
									<option value="other" {{ old('platform', $package->platform) === 'other' ? 'selected' : '' }}>Other</option>
								</select>
								@error('platform')
									<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<!-- Base Price Field -->
							<div>
								<label for="base_price" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
									Base Price <span class="text-red-600">*</span>
								</label>
								<div class="relative">
									<span class="absolute left-4 top-3 text-gray-600 dark:text-gray-400 font-semibold">$</span>
									<input type="number" id="base_price" name="base_price" step="0.01" min="0"
										class="w-full pl-8 pr-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition @error('base_price') border-red-500 @enderror"
										value="{{ old('base_price', $package->base_price) }}" required>
								</div>
								@error('base_price')
									<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<!-- Currency Field -->
							<div>
								<label for="currency" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
									Currency <span class="text-red-600">*</span>
								</label>
								<select id="currency" name="currency"
									class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition @error('currency') border-red-500 @enderror"
									required>
									<option value="USD" {{ old('currency', $package->currency ?? 'USD') === 'USD' ? 'selected' : '' }}>USD
									</option>
									<option value="EUR" {{ old('currency', $package->currency ?? 'USD') === 'EUR' ? 'selected' : '' }}>EUR
									</option>
									<option value="GBP" {{ old('currency', $package->currency ?? 'USD') === 'GBP' ? 'selected' : '' }}>GBP
									</option>
									<option value="CAD" {{ old('currency', $package->currency ?? 'USD') === 'CAD' ? 'selected' : '' }}>CAD
									</option>
								</select>
								@error('currency')
									<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<!-- Delivery Days Field -->
							<div>
								<label for="delivery_days" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
									Delivery Days <span class="text-red-600">*</span>
								</label>
								<input type="number" id="delivery_days" name="delivery_days" min="1" max="365"
									class="w-full px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition @error('delivery_days') border-red-500 @enderror"
									value="{{ old('delivery_days', $package->delivery_days) }}" required>
								@error('delivery_days')
									<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>
						</div>
						<!-- Status Checkbox -->
						<div>
							<label class="flex items-center gap-3 cursor-pointer">
								<input type="checkbox" id="is_active" name="is_active" value="1"
									class="w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-600"
									{{ old('is_active', $package->is_active) ? 'checked' : '' }} />
								<span class="text-sm font-medium text-gray-900 dark:text-white">Active Package</span>
								<span class="text-xs text-gray-600 dark:text-gray-400">(Visible to brands when active)</span>
							</label>
						</div>
						<!-- Form Actions -->
						<div class="flex gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
							<button type="submit"
								class="flex-1 inline-flex items-center justify-center px-6 py-3 text-base font-semibold text-white dark:text-gray-900 bg-purple-600 dark:bg-purple-500 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-400 transition-colors active:scale-95">
								<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
								</svg>
								Save Changes
							</button>
							<a href="{{ route('frontend.packages.show', $package) }}"
								class="flex-1 inline-flex items-center justify-center px-6 py-3 text-base font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
								Cancel
							</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
@endsection
