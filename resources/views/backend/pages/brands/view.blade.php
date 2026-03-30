@extends('backend.layouts.app')

@section('title', 'Brand Details')

@section('content')
	@php
		$previewPath = $brand->profile_image_path ?: $brand->cover_image_path;
		$previewUrl = null;

		if (!empty($previewPath)) {
		    $isExternal = str_starts_with($previewPath, 'http://') || str_starts_with($previewPath, 'https://');
		    $previewUrl = $isExternal ? $previewPath : asset($previewPath);
		}
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Brand Details" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div class="flex items-center gap-4">
					<div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
						@if ($previewUrl)
							<img src="{{ $previewUrl }}" alt="{{ $brand->brand_name }}" class="h-16 w-16 object-cover">
						@else
							<span
								class="text-xl font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($brand->brand_name, 0, 1)) }}</span>
						@endif
					</div>
					<div>
						<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $brand->brand_name }}</h2>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ $brand->industry ?: 'No industry set' }}</p>
					</div>


				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('dashboard.brands.edit', $brand) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.edit class="h-4 w-4" />
						Edit Brand
					</a>
					<a href="{{ route('dashboard.brands.index') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back to List
					</a>
				</div>
			</div>

			<div>
				<p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
					{{ $brand->description ?: 'No description provided.' }}</p>

			</div>
		</div>


		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account & Brand Info</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Contact Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Email</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->email ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Phone</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->phone ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Company Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->company_name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Job Title</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->job_title ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Website</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->website ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Industry</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->industry ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Created At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->created_at?->format('M d, Y') ?? 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Location & Social</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">City</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->city ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Country</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->country ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Postal Code</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->postal_code ?? 'N/A' }}</dd>
					</div>
					@if ($brand->socialLinks)
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Facebook</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if ($brand->socialLinks->facebook_url)
									<a href="{{ $brand->socialLinks->facebook_url }}" target="_blank" class="text-blue-600 underline">Facebook</a>
								@else
									N/A
								@endif
							</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Instagram</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if ($brand->socialLinks->instagram_url)
									<a href="{{ $brand->socialLinks->instagram_url }}" target="_blank" class="text-pink-600 underline">Instagram</a>
								@else
									N/A
								@endif
							</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">TikTok</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if ($brand->socialLinks->tiktok_url)
									<a href="{{ $brand->socialLinks->tiktok_url }}" target="_blank" class="text-black underline">TikTok</a>
								@else
									N/A
								@endif
							</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">LinkedIn</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if ($brand->socialLinks->linkedin_url)
									<a href="{{ $brand->socialLinks->linkedin_url }}" target="_blank" class="text-blue-700 underline">LinkedIn</a>
								@else
									N/A
								@endif
							</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">YouTube</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if ($brand->socialLinks->youtube_url)
									<a href="{{ $brand->socialLinks->youtube_url }}" target="_blank" class="text-red-600 underline">YouTube</a>
								@else
									N/A
								@endif
							</dd>
						</div>
					@endif
				</dl>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mt-6">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Billing Profile</h3>
				@if ($brand->billingProfile)
					<dl class="mt-4 space-y-3 text-sm">
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Legal Company Name</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->legal_company_name ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">VAT ID</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->vat_id ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Billing Address</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->billing_address ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Billing City</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->billing_city ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Billing Country</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->billing_country ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Billing Postal Code</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->billingProfile->billing_postal_code ?: 'N/A' }}</dd>
						</div>
					</dl>
				@else
					<p class="text-gray-500 dark:text-gray-400">No billing profile available.</p>
				@endif
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Onboarding Profile</h3>
				@if ($brand->onboardingProfile)
					<dl class="mt-4 space-y-3 text-sm">
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Objective</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->objective ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Budget Range</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->budget_range ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Business Type</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->business_type ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Company Size</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->company_size ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Completed</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->is_completed ? 'Yes' : 'No' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Completed At</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->onboardingProfile->completed_at?->format('M d, Y') ?? 'N/A' }}</dd>
						</div>
					</dl>
				@else
					<p class="text-gray-500 dark:text-gray-400">No onboarding profile available.</p>
				@endif
			</div>
		</div>
					</div>
				</dl>
			</div>

		</div>

	</div>
@endsection
