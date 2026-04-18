@extends('backend.layouts.app')

@section('title', 'Brand Details')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Brand Details" />

	<div class="space-y-6">
		<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="relative h-44 w-full bg-gray-100 dark:bg-gray-800">
				@if ($coverUrl)
					<img src="{{ $coverUrl }}" alt="{{ $brand->brand_name }} cover" class="h-full w-full object-cover">
				@else
					<div class="flex h-full w-full items-center justify-center text-sm text-gray-500 dark:text-gray-400">
						No cover image
					</div>
				@endif
			</div>

			<div class="p-5">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div class="flex items-center gap-4">
						<div class="-mt-14 flex h-20 w-20 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-gray-100 shadow-sm dark:border-gray-900 dark:bg-gray-800">
							@if ($profileUrl)
								<img src="{{ $profileUrl }}" alt="{{ $brand->brand_name }} profile" class="h-20 w-20 object-cover">
							@else
								<span class="text-xl font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($brand->brand_name ?? 'B', 0, 1)) }}</span>
							@endif
						</div>
						<div>
							<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $brand->brand_name ?: 'Unnamed Brand' }}</h2>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $brand->industry ?: 'No industry selected' }}</p>
							<div class="mt-2 flex flex-wrap items-center gap-2">
								<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $brand->is_verified ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
									{{ $brand->is_verified ? 'Verified' : 'Not Verified' }}
								</span>
								<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $brand->user?->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' }}">
									{{ $brand->user?->is_active ? 'Active Account' : 'Inactive Account' }}
								</span>
								@if ($brand->user?->email_verified_at)
									<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
										Email Verified
									</span>
								@endif
							</div>
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

				<p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">
					{{ $brand->user?->bio ?: 'No bio provided.' }}
				</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Campaigns</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $brand->campaigns_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Orders</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $brand->orders_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reviews</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $brand->reviews_count }}</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand & Owner Profile</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Brand Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->brand_name ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Industry</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->industry ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Website</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							@if ($brand->website)
								<a href="{{ $brand->website }}" target="_blank" rel="noopener" class="text-blue-600 underline dark:text-blue-400">{{ $brand->website }}</a>
							@else
								N/A
							@endif
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Owner Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Owner Email</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->email ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Phone</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->phone ?: 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account Metadata</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">User ID</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->id ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">User Slug</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->slug ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">User Type</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->user_type ? ucfirst($brand->user->user_type) : 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Email Verified At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->email_verified_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Last Login At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->last_login_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Stripe Customer ID</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->stripe_customer_id ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Brand Created</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Brand Updated</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->updated_at?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
					</div>
				</dl>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Location</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Address</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->address_line ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">City</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->city ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Country</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->country ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Postal Code</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->postal_code ?: 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Social Links</h3>
				<dl class="mt-4 space-y-3 text-sm">
					@foreach ($socialRows as $row)
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">{{ $row['label'] }}</dt>
							<dd class="font-medium text-gray-900 dark:text-white">
								@if (!empty($row['url']))
									<a href="{{ $row['url'] }}" target="_blank" rel="noopener"
										class="underline {{ $row['class'] }}">Open</a>
								@else
									N/A
								@endif
							</dd>
						</div>
					@endforeach
				</dl>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Billing Profile</h3>
				@if ($billingProfile)
					<dl class="mt-4 space-y-3 text-sm">
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Legal Company Name</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->legal_company_name ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">VAT ID</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->vat_id ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Billing Address</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->billing_address ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">City</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->billing_city ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Country</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->billing_country ?: 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Postal Code</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $billingProfile->billing_postal_code ?: 'N/A' }}</dd>
						</div>
					</dl>
				@else
					<p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No billing profile available.</p>
				@endif
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Onboarding Profile</h3>
				@if (!empty($onboardingData['has_any_data']))
					<dl class="mt-4 space-y-3 text-sm">
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Objective</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $onboardingData['objective'] ?? 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Budget Range</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $onboardingData['budget_range'] ?? 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Business Type</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $onboardingData['business_type'] ?? 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Company Size</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $onboardingData['company_size'] ?? 'N/A' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Completed</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ !empty($onboardingData['is_completed']) ? 'Yes' : 'No' }}</dd>
						</div>
						<div class="flex justify-between gap-4">
							<dt class="text-gray-500 dark:text-gray-400">Completed At</dt>
							<dd class="font-medium text-gray-900 dark:text-white">{{ $onboardingData['completed_at']?->format('M d, Y h:i A') ?? 'N/A' }}</dd>
						</div>
					</dl>

					<h4 class="mt-6 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Selected Industries</h4>
					<div class="mt-3 flex flex-wrap gap-2">
						@forelse (($onboardingData['industries'] ?? []) as $industry)
							<span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $industry }}</span>
						@empty
							<span class="text-sm text-gray-500 dark:text-gray-400">No onboarding industries selected.</span>
						@endforelse
					</div>

					@if (!empty($onboardingData['source']))
						<p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
							Source: {{ $onboardingData['source'] === 'onboarding_profile' ? 'Onboarding profile table' : 'Brand setup data' }}
						</p>
					@endif
				@else
					<p class="mt-4 text-sm text-gray-500 dark:text-gray-400">No onboarding profile available.</p>
				@endif
			</div>
		</div>
	</div>
@endsection
