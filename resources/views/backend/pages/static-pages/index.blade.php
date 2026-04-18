@extends('backend.layouts.app')

@section('title', 'Static Pages')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Static Pages" />

	<div class="space-y-6" id="static-pages-dashboard"
		data-csrf-token="{{ csrf_token() }}"
		data-filter-results-route="{{ route('dashboard.static-pages.table') }}"
		data-status-toggle-template="{{ route('dashboard.static-pages.toggle-status', ['staticPage' => '__ID__']) }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
			</div>
			<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/40 dark:bg-yellow-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-yellow-700 dark:text-yellow-200">{{ $stats['draft'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Page Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create and manage static pages for your platform.</p>
				</div>
				<a href="{{ route('dashboard.static-pages.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Page
				</a>
			</div>

			<div class="overflow-x-auto">
				<form id="static-pages-filters-form" method="GET" action="{{ route('dashboard.static-pages.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 p-4 md:grid-cols-12 dark:border-gray-800">
					<div class="md:col-span-8">
						<input id="q" name="q" type="text" value="{{ $search }}" placeholder="Search by title, slug, or content" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div class="md:col-span-2">
						<select id="status" name="status" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
							<option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
							<option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<a href="{{ route('dashboard.static-pages.index') }}" class="block h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.static-pages._results', ['pages' => $pages])
			</div>
		</div>
	</div>
@endsection
