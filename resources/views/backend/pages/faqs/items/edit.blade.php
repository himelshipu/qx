@extends('backend.layouts.app')

@section('title', 'Edit FAQ Item')

@section('content')
	<x-backend.shell.breadcrumb :links="[
	    ['label' => 'FAQ Sections', 'url' => route('dashboard.faqs.sections.index')],
	    ['label' => $section->section_title, 'url' => route('dashboard.faqs.items.index', $section)],
	]" pageTitle="Edit FAQ Item" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<form action="{{ route('dashboard.faqs.items.update', [$section, $item]) }}" method="POST" class="p-6">
			@csrf
			@method('PUT')

			<div class="grid gap-6">
				<!-- Question -->
				<div>
					<label for="question" class="block text-sm font-medium text-gray-900 dark:text-white">Question <span
							class="text-red-500">*</span></label>
					<input type="text" id="question" name="question" value="{{ old('question', $item->question) }}" required
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
					@error('question')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Answer -->
				<div>
					<label for="answer" class="block text-sm font-medium text-gray-900 dark:text-white">Answer <span
							class="text-red-500">*</span></label>
					<textarea id="answer" name="answer" rows="8" required
					 class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">{{ old('answer', $item->answer) }}</textarea>
					@error('answer')
						<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
					@enderror
				</div>

				<!-- Sort Order -->
				<div>
					<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
					<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}"
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
					Update FAQ Item
				</button>
				<a href="{{ route('dashboard.faqs.items.index', $section) }}"
					class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-50 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
					Cancel
				</a>
			</div>
		</form>
	</div>
@endsection
