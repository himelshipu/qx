@extends('backend.layouts.app')

@section('title', 'Creator Details')

@section('content')
	@php
		$previewPath = $creator->user?->profile_image_path ?: $creator->user?->cover_image_path;
		$previewUrl = null;

		if (!empty($previewPath)) {
		    $isExternal = str_starts_with($previewPath, 'http://') || str_starts_with($previewPath, 'https://');
		    $previewUrl = $isExternal ? $previewPath : asset($previewPath);
		}
	@endphp

	<x-backend.shell.breadcrumb pageTitle="Creator Details" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div class="flex items-center gap-4">
					<div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
						@if ($previewUrl)
							<img src="{{ $previewUrl }}" alt="{{ $creator->display_name ?: $creator->user?->name }}"
								class="h-16 w-16 object-cover">
						@else
							<span
								class="text-xl font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($creator->display_name ?: $creator->user?->name ?? 'C', 0, 1)) }}</span>
						@endif
					</div>
					<div>
						<h2 class="text-xl font-semibold text-gray-900 dark:text-white">
							{{ $creator->display_name ?: $creator->user?->name ?? 'Unnamed' }}</h2>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ $creator->title_name ?: 'No title set' }}</p>
					</div>
				</div>
				<div class="flex items-center gap-2"> <a href="{{ route('dashboard.creators.portfolio.index', $creator) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition hover:bg-blue-100 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-300 dark:hover:bg-blue-900/30">
						<x-icons.camera class="h-4 w-4" />
						Manage Portfolio
					</a> <a href="{{ route('dashboard.creators.edit', $creator) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.edit class="h-4 w-4" />
						Edit Creator
					</a>
					<a href="{{ route('dashboard.creators.index') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back to List
					</a>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Applications</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $creator->campaign_applications_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Items</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $creator->order_items_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cart Items</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $creator->cart_items_count }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
				<p
					class="mt-2 text-sm font-semibold {{ $creator->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
					{{ $creator->is_active ? 'Active' : 'Inactive' }}
				</p>
			</div>
		</div>

		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Profile</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Full Name</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->user?->name ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Email</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->user?->email ?? 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Phone</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->user?->phone ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Gender</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ ucfirst($creator->gender ?? 'n/a') }}</dd>
					</div>
				</dl>
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Location</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Address</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->location ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">City</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->city ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Country</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->country ?: 'N/A' }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Postal Code</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $creator->postal_code ?: 'N/A' }}</dd>
					</div>
				</dl>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Categories</h3>
			<div class="mt-3 flex flex-wrap gap-2">
				@forelse ($creator->categories as $category)
					<span
						class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $category->name }}</span>
				@empty
					<span class="text-sm text-gray-500 dark:text-gray-400">No categories assigned.</span>
				@endforelse
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Bio</h3>
			<p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $creator->description ?: 'No bio provided.' }}
			</p>

			<h3 class="mt-6 text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Audience</h3>
			<p class="mt-3 text-sm leading-6 text-gray-700 dark:text-gray-300">
				{{ $creator->audience ?: 'No audience summary provided.' }}</p>
		</div>
	</div>
@endsection
