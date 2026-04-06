{{-- Frontend Campaigns Designed Edit --}}
@extends('frontend.layouts.app')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
		<div class="max-w-4xl mx-auto">
			<!-- Header -->
			<div class="mb-12">
				<h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-2">Edit Campaign</h1>
				<p class="text-gray-600 dark:text-gray-400 text-lg">Update your campaign details and settings</p>
			</div>

			<!-- Alerts -->
			@if ($errors->any())
				<div class="mb-8 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/30 dark:bg-red-900/10">
					<h3 class="font-semibold text-red-900 dark:text-red-300 mb-2">Validation Errors</h3>
					<ul class="space-y-1 text-sm text-red-700 dark:text-red-400">
						@foreach ($errors->all() as $error)
							<li>• {{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<!-- Form -->
			<form action="{{ route('frontend.campaigns.update', $campaign) }}" method="POST" class="space-y-8">
				@csrf
				@method('PUT')

				<!-- Basic Information Section -->
				<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Campaign Information</h2>

					<div class="space-y-5">
						<!-- Campaign Title -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Campaign Title <span
									class="text-red-500">*</span></label>
							<input type="text" name="title" placeholder="e.g., Summer 2026 Brand Launch"
								value="{{ old('title', $campaign->title) }}"
								class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
								required>
							@error('title')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Campaign Type -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Campaign Type <span
									class="text-red-500">*</span></label>
							<select name="campaign_type"
								class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
								required>
								<option value="">Select campaign type</option>
								<option value="product_launch"
									{{ old('campaign_type', $campaign->campaign_type) === 'product_launch' ? 'selected' : '' }}>Product Launch
								</option>
								<option value="brand_awareness"
									{{ old('campaign_type', $campaign->campaign_type) === 'brand_awareness' ? 'selected' : '' }}>Brand Awareness
								</option>
								<option value="ugc" {{ old('campaign_type', $campaign->campaign_type) === 'ugc' ? 'selected' : '' }}>UGC
									Campaign</option>
							</select>
							@error('campaign_type')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Description -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Campaign Description <span
									class="text-red-500">*</span></label>
							<textarea name="description" rows="4" placeholder="Describe your product and campaign goals..."
							 class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
							 required>{{ old('description', $campaign->description) }}</textarea>
							@error('description')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Instructions -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Content Instructions <span
									class="text-red-500">*</span></label>
							<textarea name="instructions" rows="5"
							 placeholder="What should creators do? Include key messages, deliverables, hashtags, etc..."
							 class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
							 required>{{ old('instructions', $campaign->instructions) }}</textarea>
							@error('instructions')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Budget & Dates Section -->
				<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Budget & Timeline</h2>

					<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
						<!-- Budget Min -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Minimum Budget <span
									class="text-red-500">*</span></label>
							<div class="relative">
								<span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">$</span>
								<input type="number" name="budget_min" min="0" step="0.01" placeholder="0.00"
									value="{{ old('budget_min', $campaign->budget_min) }}"
									class="w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
									required>
							</div>
							@error('budget_min')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Budget Max -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Maximum Budget <span
									class="text-red-500">*</span></label>
							<div class="relative">
								<span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">$</span>
								<input type="number" name="budget_max" min="0" step="0.01" placeholder="0.00"
									value="{{ old('budget_max', $campaign->budget_max) }}"
									class="w-full pl-8 pr-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
									required>
							</div>
							@error('budget_max')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Start Date -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date <span
									class="text-red-500">*</span></label>
							<input type="date" name="start_date" value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}"
								class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
								required>
							@error('start_date')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- End Date -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date <span
									class="text-red-500">*</span></label>
							<input type="date" name="end_date" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}"
								class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
								required>
							@error('end_date')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>

				<!-- Visibility Section -->
				<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 sm:p-8 shadow-sm">
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Campaign Status</h2>

					<div class="space-y-4">
						<!-- Status -->
						<div>
							<label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Campaign Status <span
									class="text-red-500">*</span></label>
							<select name="status"
								class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-purple-500 focus:ring-2 focus:ring-purple-100 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-purple-400 dark:focus:ring-purple-900/30 transition"
								required>
								<option value="draft" {{ old('status', $campaign->status) === 'draft' ? 'selected' : '' }}>Draft</option>
								<option value="published" {{ old('status', $campaign->status) === 'published' ? 'selected' : '' }}>Published
								</option>
								<option value="paused" {{ old('status', $campaign->status) === 'paused' ? 'selected' : '' }}>Paused</option>
								<option value="closed" {{ old('status', $campaign->status) === 'closed' ? 'selected' : '' }}>Closed</option>
								<option value="archived" {{ old('status', $campaign->status) === 'archived' ? 'selected' : '' }}>Archived
								</option>
							</select>
							@error('status')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Active Status -->
						<div
							class="flex items-center justify-between p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
							<div>
								<p class="font-semibold text-gray-700 dark:text-gray-300">Make Campaign Active</p>
								<p class="text-sm text-gray-600 dark:text-gray-400">Creators can apply to active campaigns</p>
							</div>
							<label class="relative inline-block">
								<input type="checkbox" name="is_active" value="1"
									{{ old('is_active', $campaign->is_active) ? 'checked' : '' }} class="sr-only peer">
								<div
									class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600">
								</div>
							</label>
						</div>
					</div>
				</div>

				<!-- Actions -->
				<div class="flex flex-col sm:flex-row gap-4">
					<button type="submit"
						class="flex-1 px-6 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 hover:shadow-lg text-white font-semibold transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
						<span class="flex items-center justify-center gap-2">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
							</svg>
							Save Changes
						</span>
					</button>

					<a href="{{ route('frontend.campaigns.show', $campaign) }}"
						class="flex-1 px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold transition text-center">
						Cancel
					</a>
				</div>
			</form>
		</div>
	</div>
@endsection
