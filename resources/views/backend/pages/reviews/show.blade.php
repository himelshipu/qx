@extends('backend.layouts.app')

@section('title', 'Review Details')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Reviews', 'url' => route('dashboard.reviews.index')]]" pageTitle="Review #{{ $review->id }}" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->brand?->brand_name ?? 'N/A' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Influencer</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->influencer?->user?->name ?? $review->influencer?->display_name ?? 'N/A' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Rating</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->rating }}/5</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Visibility</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->is_public ? 'Public' : 'Private' }}</p>
				</div>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Feedback</h3>
			<p class="mt-3 text-sm font-medium text-gray-900 dark:text-white">{{ $review->title ?: 'Untitled review' }}</p>
			<p class="mt-2 text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $review->comment ?: 'No comment provided.' }}</p>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Item Context</h3>
			<div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Number</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->orderItem?->order?->order_number ?? 'N/A' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Status</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $review->orderItem?->order?->status ?? 'n/a')) }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Item</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $review->orderItem?->title ?? 'N/A' }}</p>
				</div>
				<div>
					<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Line Total</p>
					<p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
						@if ($review->orderItem?->order)
							{{ strtoupper($review->orderItem->order->currency) }} {{ number_format((float) $review->orderItem->line_total, 2) }}
						@else
							N/A
						@endif
					</p>
				</div>
			</div>
		</div>
	</div>
@endsection

