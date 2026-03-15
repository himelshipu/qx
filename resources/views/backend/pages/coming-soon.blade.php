@extends('backend.layouts.app')

@section('title', ($module ?? 'Module') . ' - Coming Soon')

@section('content')
	<x-backend.shell.breadcrumb :pageTitle="($module ?? 'Module') . ' (Coming Soon)'" />

	<div class="rounded-xl border border-gray-200 bg-white p-8 text-center shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
			<x-icons.clock class="h-7 w-7 text-gray-500" />
		</div>

		<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $module ?? 'This module' }} is coming soon</h2>
		<p class="mx-auto mt-2 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
			Navigation and route structure is ready. This page is a placeholder so your sidebar can stay complete while
			implementation is in progress.
		</p>

		<a href="{{ route('dashboard.index') }}"
			class="mt-6 inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
			Back to Dashboard
		</a>
	</div>
@endsection
