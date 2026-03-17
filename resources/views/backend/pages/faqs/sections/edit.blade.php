@extends('backend.layouts.app')

@section('title', 'Edit FAQ Section')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'FAQ Sections', 'url' => route('dashboard.faqs.sections.index')]]" pageTitle="Edit FAQ Section: {{ $section->section_title }}" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<form action="{{ route('dashboard.faqs.sections.update', $section) }}" method="POST" class="p-6">
			@csrf
			@method('PUT')

			<div class="grid gap-6">
				<!-- Section Code -->
				<div>
					<label for="section_code" class="block text-sm font-medium text-gray-900 dark:text-white">Section Code <span
							class="text-red-500">*</span></label>
					<input type="text" id="section_code" name="section_code" value="{{ old('section_code', $section->section_code) }}"
						required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('section_code')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Section Title -->
				<div>
					<label for="section_title" class="block text-sm font-medium text-gray-900 dark:text-white">Section Title <span
							class="text-red-500">*</span></label>
					<input type="text" id="section_title" name="section_title"
						value="{{ old('section_title', $section->section_title) }}" required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('section_title')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Audience Type -->
				<div>
					<label for="audience_type" class="block text-sm font-medium text-gray-900 dark:text-white">Audience Type <span
							class="text-red-500">*</span></label>
					<select id="audience_type" name="audience_type" required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<option value="all" {{ old('audience_type', $section->audience_type) == 'all' ? 'selected' : '' }}>All (visible
							to everyone)</option>
						<option value="brand" {{ old('audience_type', $section->audience_type) == 'brand' ? 'selected' : '' }}>Brands only
						</option>
						<option value="creator" {{ old('audience_type', $section->audience_type) == 'creator' ? 'selected' : '' }}>
							Creators/Influencers only</option>
					</select>
					@error('audience_type')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Sort Order -->
				<div>
					<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
					<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $section->sort_order) }}"
						min="0"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('sort_order')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>
			</div>

			<div class="mt-8 flex gap-3">
				<button type="submit"
					class="rounded-lg bg-gray-900 px-6 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Update Section
				</button>
				<a href="{{ route('dashboard.faqs.sections.index') }}"
					class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-50 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
					Cancel
				</a>
			</div>
		</form>
	</div>
@endsection
