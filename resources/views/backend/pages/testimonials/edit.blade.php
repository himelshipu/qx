@extends('backend.layouts.app')

@section('title', 'Edit Testimonial')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Testimonials', 'url' => route('dashboard.testimonials.index')]]" pageTitle="Edit Testimonial" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<form action="{{ route('dashboard.testimonials.update', $testimonial) }}" method="POST" class="p-6">
			@csrf
			@method('PUT')

			<div class="grid gap-6">
				<!-- Author Name -->
				<div>
					<label for="author_name" class="block text-sm font-medium text-gray-900 dark:text-white">Author Name <span
							class="text-red-500">*</span></label>
					<input type="text" id="author_name" name="author_name" value="{{ old('author_name', $testimonial->author_name) }}"
						required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('author_name')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Author Role -->
				<div>
					<label for="author_role" class="block text-sm font-medium text-gray-900 dark:text-white">Author Role</label>
					<input type="text" id="author_role" name="author_role"
						value="{{ old('author_role', $testimonial->author_role) }}"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('author_role')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Company Name -->
				<div>
					<label for="company_name" class="block text-sm font-medium text-gray-900 dark:text-white">Company Name</label>
					<input type="text" id="company_name" name="company_name"
						value="{{ old('company_name', $testimonial->company_name) }}"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('company_name')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Quote -->
				<div>
					<label for="quote" class="block text-sm font-medium text-gray-900 dark:text-white">Testimonial Quote <span
							class="text-red-500">*</span></label>
					<textarea id="quote" name="quote" rows="5" required
					 class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('quote', $testimonial->quote) }}</textarea>
					@error('quote')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Rating -->
				<div>
					<label for="rating" class="block text-sm font-medium text-gray-900 dark:text-white">Rating</label>
					<select id="rating" name="rating"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<option value="">Select a rating</option>
						<option value="5" {{ old('rating', $testimonial->rating) == 5 ? 'selected' : '' }}>5 Stars ★★★★★</option>
						<option value="4" {{ old('rating', $testimonial->rating) == 4 ? 'selected' : '' }}>4 Stars ★★★★</option>
						<option value="3" {{ old('rating', $testimonial->rating) == 3 ? 'selected' : '' }}>3 Stars ★★★</option>
						<option value="2" {{ old('rating', $testimonial->rating) == 2 ? 'selected' : '' }}>2 Stars ★★</option>
						<option value="1" {{ old('rating', $testimonial->rating) == 1 ? 'selected' : '' }}>1 Star ★</option>
					</select>
					@error('rating')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Sort Order -->
				<div>
					<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
					<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}"
						min="0"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('sort_order')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Published -->
				<div class="flex items-center">
					<input type="checkbox" id="is_published" name="is_published" value="1"
						{{ old('is_published', $testimonial->is_published) ? 'checked' : '' }}
						class="h-4 w-4 rounded border-gray-300 bg-white text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-blue-600">
					<label for="is_published" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Published</label>
				</div>
			</div>

			<div class="mt-8 flex gap-3">
				<button type="submit"
					class="rounded-lg bg-gray-900 px-6 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Update Testimonial
				</button>
				<a href="{{ route('dashboard.testimonials.index') }}"
					class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-50 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
					Cancel
				</a>
			</div>
		</form>
	</div>
@endsection
