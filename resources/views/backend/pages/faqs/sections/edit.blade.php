@extends('backend.layouts.app')

@section('title', 'Edit FAQ Section')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'FAQ Sections', 'url' => route('dashboard.faqs.sections.index')]]" pageTitle="Edit FAQ Section: {{ $section->section_title }}" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<form action="{{ route('dashboard.faqs.sections.update', $section) }}" method="POST" class="p-6">
			@csrf
			@method('PUT')

			@include('backend.pages.faqs.sections._form', ['section' => $section])

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
