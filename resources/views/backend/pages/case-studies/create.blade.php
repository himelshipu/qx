@extends('backend.layouts.app')

@section('title', 'Create Case Study')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Case Studies', 'url' => route('dashboard.case-studies.index')]]" pageTitle="Create Case Study" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<form action="{{ route('dashboard.case-studies.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
			@csrf

			<div class="grid gap-6">
				<!-- Title -->
				<div>
					<label for="title" class="block text-sm font-medium text-gray-900 dark:text-white">Title <span
							class="text-red-500">*</span></label>
					<input type="text" id="title" name="title" value="{{ old('title') }}" required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('title')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Summary -->
				<div>
					<label for="summary" class="block text-sm font-medium text-gray-900 dark:text-white">Summary <span
							class="text-red-500">*</span></label>
					<textarea id="summary" name="summary" rows="4" required
					 class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('summary') }}</textarea>
					@error('summary')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Cover Image -->
				<div>
					<label for="cover_image" class="block text-sm font-medium text-gray-900 dark:text-white">Cover Image</label>
					<div class="mt-2">
						<input type="file" id="cover_image" name="cover_image" accept="image/*"
							class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-gray-800 dark:file:bg-gray-700 dark:hover:file:bg-gray-600">
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recommended: 600x400px, Max: 5MB</p>
					</div>
					@error('cover_image')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- External URL -->
				<div>
					<label for="external_url" class="block text-sm font-medium text-gray-900 dark:text-white">External URL</label>
					<input type="url" id="external_url" name="external_url" value="{{ old('external_url') }}"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('external_url')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Sort Order -->
				<div>
					<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
					<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('sort_order')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Published -->
				<div class="flex items-center">
					<input type="checkbox" id="is_published" name="is_published" value="1"
						{{ old('is_published') ? 'checked' : '' }}
						class="h-4 w-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-blue-600">
					<label for="is_published" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Publish immediately</label>
				</div>
			</div>

			<div class="mt-8 flex gap-3">
				<button type="submit"
					class="rounded-lg bg-gray-900 px-6 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Create Case Study
				</button>
				<a href="{{ route('dashboard.case-studies.index') }}"
					class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-50 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
					Cancel
				</a>
			</div>
		</form>
	</div>
@endsection
