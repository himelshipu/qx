@extends('frontend.layouts.app')

@section('title', isset($isEditMode) && $isEditMode ? 'Edit Package' : 'Create Package')

@section('content')
	<x-backend.shell.breadcrumb :pageTitle="isset($isEditMode) && $isEditMode ? 'Edit Package' : 'Create Package'" />

	<div class="max-w-6xl px-2 py-2 transition-colors duration-300"
		x-data="packageDesignedForm({
			platformOptions: @js($platformOptions),
			isEditMode: @js(isset($isEditMode) && $isEditMode),
			packageData: @js(isset($package) ? [
				'name' => $package->name,
				'description' => $package->description,
				'platform' => $package->platform,
				'base_price' => $package->base_price,
				'currency' => $package->currency,
				'delivery_days' => $package->delivery_days,
				'revisions_included' => $package->revisions_included,
				'is_active' => $package->is_active,
			] : []),
		})">

		<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
			<div>
				<h1 class="text-2xl font-medium text-gray-800 dark:text-white">
					@if(isset($isEditMode) && $isEditMode)
						Edit Your Package
					@else
						Create a New Package
					@endif
				</h1>
				<p class="mt-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
					@if(isset($isEditMode) && $isEditMode)
						Update package details and pricing
					@else
						Set up a new package to offer your services
					@endif
				</p>
			</div>

			<div class="flex flex-wrap items-center gap-2">
				<a href="{{ route('frontend.packages.index') }}"
					class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
					</svg>
					Back
				</a>
			</div>
		</div>

		<form action="{{ isset($isEditMode) && $isEditMode ? route('frontend.packages.update', $package->id) : route('frontend.packages.store') }}" method="POST" novalidate>
			@csrf
			@if(isset($isEditMode) && $isEditMode)
				@method('PUT')
			@endif

			<div class="grid grid-cols-1 items-start gap-8 md:grid-cols-[1fr_360px]">
				<!-- Main Form -->
				<div class="space-y-6">
					<!-- Basic Information Section -->
					<div class="rounded-xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h2 class="mb-6 text-lg font-semibold text-gray-800 dark:text-gray-200">Package Information</h2>

						<div class="space-y-6">
							<!-- Platform Selection -->
							<div>
								<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
									Platform <span class="text-red-500">*</span>
								</label>
								<select name="platform" x-model="packageData.platform"
									class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									<option value="">Select a platform...</option>
									@foreach ($platformOptions as $option)
										<option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
									@endforeach
								</select>
								@error('platform')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<!-- Package Name -->
							<div>
								<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
									Package Name <span class="text-red-500">*</span>
								</label>
								<input type="text" name="name" x-model="packageData.name"
									placeholder="e.g., 1 Instagram Feed Post"
									class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
								@error('name')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>

							<!-- Description -->
							<div>
								<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
									Description <span class="text-red-500">*</span>
								</label>
								<textarea name="description" x-model="packageData.description" rows="4"
									placeholder="Describe what's included in this package..."
									class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
								@error('description')
									<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
								@enderror
							</div>
						</div>
					</div>

					<!-- Pricing & Delivery Section -->
					<div class="rounded-xl border border-gray-100 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
						<h2 class="mb-6 text-lg font-semibold text-gray-800 dark:text-gray-200">Pricing & Delivery</h2>

						<div class="space-y-6">
							<!-- Price Section -->
							<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
										Price <span class="text-red-500">*</span>
									</label>
									<input type="number" name="base_price" x-model="packageData.base_price" step="0.01" min="0"
										placeholder="e.g., 250.00"
										class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									@error('base_price')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>

								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
										Currency <span class="text-red-500">*</span>
									</label>
									<input type="text" name="currency" x-model="packageData.currency" maxlength="3"
										placeholder="USD"
										class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 uppercase text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									@error('currency')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>
							</div>

							<!-- Delivery & Revisions -->
							<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
										Delivery Time (Days)
									</label>
									<input type="number" name="delivery_days" x-model="packageData.delivery_days" min="1"
										placeholder="e.g., 7"
										class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									@error('delivery_days')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>

								<div>
									<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
										Free Revisions
									</label>
									<input type="number" name="revisions_included" x-model="packageData.revisions_included" min="0"
										placeholder="e.g., 1"
										class="shadow-theme-xs focus:ring-gray-500/10 dark:focus:border-gray-800 h-12 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-1 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
									@error('revisions_included')
										<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
									@enderror
								</div>
							</div>
						</div>
					</div>

					<!-- Status Section -->
					<div class="rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
						<label class="flex cursor-pointer items-center justify-between gap-3">
							<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Publish Package</span>
							<input type="checkbox" name="is_active" value="1" x-model="packageData.is_active"
								class="h-4 w-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-800">
						</label>
						<p class="mt-1 text-xs text-gray-600 dark:text-gray-500">Make this package visible to brands on the marketplace</p>
					</div>

					<!-- Submit Button -->
					<div class="flex flex-col gap-3 pt-6">
						<button type="submit" class="w-full rounded-lg bg-purple-600 py-3 text-lg font-bold uppercase tracking-widest text-white shadow-sm transition hover:bg-purple-700 active:scale-95 dark:bg-purple-500 dark:hover:bg-purple-600">
							@if(isset($isEditMode) && $isEditMode)
								Save Changes
							@else
								Create Package
							@endif
						</button>
						<a href="{{ route('frontend.packages.index') }}" class="text-center text-sm font-medium text-gray-500 transition hover:text-gray-800 dark:hover:text-gray-200">
							Cancel
						</a>
					</div>
				</div>

				<!-- Sidebar Preview -->
				<div class="space-y-6 md:sticky md:top-24">
					<div class="rounded-lg border border-gray-100 bg-gray-50 p-6 shadow-sm dark:border-gray-900/30 dark:bg-gray-950/20">
						<h3 class="mb-6 text-lg font-medium text-gray-800 dark:text-gray-300">Package Preview</h3>

						<div class="space-y-4">
							<div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-850">
								<div class="mb-2 flex items-center justify-between">
									<span class="text-xs font-bold uppercase text-gray-400">Platform</span>
									<span class="inline-flex items-center rounded px-2 py-1 text-xs font-semibold text-white"
										:style="{ backgroundColor: getPlatformColor(packageData.platform) }"
										x-text="getPlatformName(packageData.platform)"></span>
								</div>
								<p class="text-sm font-bold text-gray-800 dark:text-white" x-text="packageData.name || 'Package name'"></p>
								<p class="mt-1 text-xs text-gray-600 dark:text-gray-400 line-clamp-2" x-text="packageData.description || 'Package description will appear here'"></p>
							</div>

							<div class="grid grid-cols-2 gap-3">
								<div class="rounded-lg bg-white p-3 dark:bg-gray-850">
									<p class="text-xs font-bold text-gray-400 uppercase">Price</p>
									<p class="text-lg font-bold text-purple-600 dark:text-purple-400">
										<span x-text="packageData.currency || 'USD'"></span>
										<span x-text="parseInt(packageData.base_price) || '0'"></span>
									</p>
								</div>

								<div class="rounded-lg bg-white p-3 dark:bg-gray-850">
									<p class="text-xs font-bold text-gray-400 uppercase">Delivery</p>
									<p class="text-lg font-bold text-gray-800 dark:text-white">
										<span x-text="packageData.delivery_days || '—'"></span>
										<span class="text-xs font-normal text-gray-600 dark:text-gray-400">days</span>
									</p>
								</div>

								<div class="rounded-lg bg-white p-3 dark:bg-gray-850">
									<p class="text-xs font-bold text-gray-400 uppercase">Revisions</p>
									<p class="text-lg font-bold text-gray-800 dark:text-white">
										<span x-text="packageData.revisions_included || '0'"></span>
									</p>
								</div>

								<div class="rounded-lg bg-white p-3 dark:bg-gray-850">
									<p class="text-xs font-bold text-gray-400 uppercase">Status</p>
									<p class="text-sm font-bold" :class="packageData.is_active ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-400'"
										x-text="packageData.is_active ? 'Active' : 'Inactive'"></p>
								</div>
							</div>
						</div>

						<p class="mt-6 px-3 text-center text-xs leading-relaxed text-gray-500 dark:text-gray-500">
							Preview updates as you edit your package details
						</p>
					</div>
				</div>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function packageDesignedForm(config) {
				return {
					platformOptions: Array.isArray(config.platformOptions) ? config.platformOptions : [],
					isEditMode: Boolean(config.isEditMode),
					packageData: {
						name: config.packageData?.name || '',
						description: config.packageData?.description || '',
						platform: config.packageData?.platform || '',
						base_price: config.packageData?.base_price || '',
						currency: config.packageData?.currency || 'USD',
						delivery_days: config.packageData?.delivery_days || 7,
						revisions_included: config.packageData?.revisions_included || 1,
						is_active: Boolean(config.packageData?.is_active ?? true),
					},

					getPlatformColor(platform) {
						const colors = {
							'facebook': '#1877F2',
							'instagram': '#E1306C',
							'tiktok': '#000000',
							'linkedin': '#0A66C2',
							'x': '#000000',
							'youtube': '#FF0000',
							'ugc': '#A855F7',
							'other': '#6B7280'
						};
						return colors[platform] || '#6B7280';
					},

					getPlatformName(platform) {
						const names = {
							'facebook': 'Facebook',
							'instagram': 'Instagram',
							'tiktok': 'TikTok',
							'linkedin': 'LinkedIn',
							'x': 'X',
							'youtube': 'YouTube',
							'ugc': 'UGC',
							'other': 'Other'
						};
						return names[platform] || 'Select Platform';
					}
				}
			}
		</script>
	@endpush

	<style>
		[x-cloak] {
			display: none !important;
		}
	</style>
@endsection
