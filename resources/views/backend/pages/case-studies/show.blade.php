@extends('backend.layouts.app')

@section('title', 'Case Study Details - ' . $caseStudy->title)

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Case Studies', 'url' => route('dashboard.case-studies.index')]]" 
		pageTitle="Case Study Details: {{ $caseStudy->title }}" />

	<div class="space-y-6">
		<!-- Cover Image Section -->
		<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="relative h-64 w-full bg-gray-100 dark:bg-gray-800">
				@if ($coverUrl)
					<img src="{{ $coverUrl }}" alt="{{ $caseStudy->title }} cover" class="h-full w-full object-cover">
				@else
					<div class="flex h-full w-full items-center justify-center">
						<div class="text-center">
							<x-icons.image class="mx-auto h-16 w-16 text-gray-400" />
							<p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No cover image</p>
						</div>
					</div>
				@endif
			</div>

			<div class="p-6">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div class="flex-1">
						<div class="flex items-center gap-3">
							<h2 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $caseStudy->title }}</h2>
							<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $caseStudy->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
								{{ $caseStudy->is_published ? 'Published' : 'Draft' }}
							</span>
						</div>
						<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Created {{ $caseStudy->created_at->format('M d, Y') }}</p>
					</div>

					<div class="flex items-center gap-2">
						@if ($caseStudy->is_published && $caseStudy->external_url)
							<a href="{{ $caseStudy->external_url }}" target="_blank" rel="noopener noreferrer"
								class="inline-flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-sm font-medium text-green-700 transition hover:bg-green-100 dark:border-green-900/40 dark:bg-green-900/20 dark:text-green-300 dark:hover:bg-green-900/30">
								<x-icons.external-link class="h-4 w-4" />
								View External
							</a>
						@endif
						<a href="{{ route('dashboard.case-studies.edit', $caseStudy) }}"
							class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							<x-icons.edit class="h-4 w-4" />
							Edit
						</a>
						<a href="{{ route('dashboard.case-studies.index') }}"
							class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Back to List
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Key Information Grid -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
				<p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
					{{ $caseStudy->is_published ? 'Published' : 'Draft' }}
				</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Sort Order</p>
				<p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $caseStudy->sort_order }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Published Date</p>
				<p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">
					{{ $caseStudy->published_at ? $caseStudy->published_at->format('M d, Y') : 'Not published' }}
				</p>
			</div>
		</div>

		<!-- Case Study Details -->
		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<!-- Summary Section -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Summary</h3>
				<p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">
					{{ $caseStudy->summary ?: 'No summary provided.' }}
				</p>
			</div>

			<!-- Metadata Section -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Details</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Case Study ID</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $caseStudy->id }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Slug</dt>
						<dd class="font-medium text-gray-900 dark:text-white break-all">{{ $caseStudy->slug }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Status</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							<span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $caseStudy->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' }}">
								{{ $caseStudy->is_published ? 'Published' : 'Draft' }}
							</span>
						</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Created At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $caseStudy->created_at->format('M d, Y h:i A') }}</dd>
					</div>
					<div class="flex justify-between gap-4">
						<dt class="text-gray-500 dark:text-gray-400">Updated At</dt>
						<dd class="font-medium text-gray-900 dark:text-white">{{ $caseStudy->updated_at->format('M d, Y h:i A') }}</dd>
					</div>
				</dl>
			</div>
		</div>

		<!-- External URL and Image Section -->
		<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
			<!-- External Link -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">External Link</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex flex-col gap-2">
						<dt class="text-gray-500 dark:text-gray-400">External URL</dt>
						<dd class="font-medium text-gray-900 dark:text-white">
							@if ($caseStudy->external_url)
								<a href="{{ $caseStudy->external_url }}" target="_blank" rel="noopener noreferrer" 
									class="text-blue-600 hover:text-blue-700 underline break-all dark:text-blue-400 dark:hover:text-blue-300">
									{{ $caseStudy->external_url }}
								</a>
							@else
								<span class="text-gray-400 dark:text-gray-500">Not provided</span>
							@endif
						</dd>
					</div>
				</dl>
			</div>

			<!-- Cover Image Info -->
			<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cover Image</h3>
				<dl class="mt-4 space-y-3 text-sm">
					<div class="flex flex-col gap-2">
						<dt class="text-gray-500 dark:text-gray-400">Image Path</dt>
						<dd class="font-medium text-gray-900 dark:text-white break-all">
							{{ $caseStudy->cover_image_path ?: 'No image uploaded' }}
						</dd>
					</div>
					@if ($coverUrl)
						<div class="flex flex-col gap-2">
							<dt class="text-gray-500 dark:text-gray-400">Preview</dt>
							<dd>
								<img src="{{ $coverUrl }}" alt="{{ $caseStudy->title }}" class="h-32 w-full object-cover rounded-lg">
							</dd>
						</div>
					@endif
				</dl>
			</div>
		</div>

		<!-- Delete Section -->
		<div class="rounded-xl border border-red-200 bg-red-50 p-5 dark:border-red-900/40 dark:bg-red-900/20">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Danger Zone</h3>
			<p class="mt-2 text-sm text-red-600 dark:text-red-400">
				Deleting this case study will permanently remove it and cannot be undone.
			</p>
			<form action="{{ route('dashboard.case-studies.destroy', $caseStudy) }}" method="POST" class="mt-4 inline js-confirmable"
				data-confirm-title="Delete Case Study"
				data-confirm-message="Are you sure you want to permanently delete '{{ $caseStudy->title }}'? This action cannot be undone."
				data-confirm-button="Delete Case Study"
				data-confirm-variant="danger">
				@csrf
				@method('DELETE')
				<button type="submit"
					class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800">
					<x-icons.trash class="h-4 w-4" />
					Delete Case Study
				</button>
			</form>
		</div>
	</div>
@endsection
