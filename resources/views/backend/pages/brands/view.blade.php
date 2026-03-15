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
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Orders</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $brand->orders_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reviews</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $brand->reviews_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Verification</p>
				<p
					class="mt-2 text-sm font-semibold {{ $brand->is_verified ? 'text-green-700 dark:text-green-300' : 'text-gray-600 dark:text-gray-300' }}">
					{{ $brand->is_verified ? 'Verified' : 'Not Verified' }}
				</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
				<p
					class="mt-2 text-sm font-semibold {{ $brand->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
					{{ $brand->is_active ? 'Active' : 'Inactive' }}
				</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Contact Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Email</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->user?->email ?? ($brand->email ?? 'N/A') }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Phone</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->phone ?: ($brand->user?->phone ?: 'N/A') }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Website</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->website ?: 'N/A' }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Location</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Address</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->location ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">City</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->city ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Country</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->country ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Postal Code</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $brand->postal_code ?: 'N/A' }}</dd>
					</div>
				</dl>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Description</h3>
			<p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
				{{ $brand->description ?: 'No description provided.' }}</p>
		</div>
	</div>
@endsection
