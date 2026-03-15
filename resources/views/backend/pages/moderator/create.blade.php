@extends('backend.layouts.app')

@section('title', 'Create Moderator')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Create Moderator" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div
			class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Moderator</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create moderator credentials and set initial account status.
				</p>
			</div>
			<a href="{{ route('dashboard.moderators.index') }}"
				class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
				Back to Moderators
			</a>
		</div>

		<form action="{{ route('dashboard.moderators.store') }}" method="POST" class="space-y-6 p-5">
			@csrf

			@if ($errors->any())
				<div
					class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
					Please fix the highlighted fields and try again.
				</div>
			@endif

			@include('backend.pages.moderator._form')

			<div
				class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
				<a href="{{ route('dashboard.moderators.index') }}"
					class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Cancel
				</a>
				<button type="submit"
					class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Create Moderator
				</button>
			</div>
		</form>
	</div>
@endsection
