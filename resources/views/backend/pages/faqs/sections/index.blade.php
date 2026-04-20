@extends('backend.layouts.app')

@section('title', 'FAQ Sections')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="FAQ Sections" />

	<div class="space-y-6" id="faq-sections-dashboard"
		data-csrf-token="{{ csrf_token() }}"
		data-filter-results-route="{{ route('dashboard.faqs.sections.table') }}"
		data-status-toggle-template="{{ route('dashboard.faqs.sections.toggle-status', ['section' => '__ID__']) }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Sections</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['active'] }}</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Inactive</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">{{ $stats['inactive'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">FAQ Sections</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage FAQ sections and their items.</p>
				</div>
				<a href="{{ route('dashboard.faqs.sections.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Section
				</a>
			</div>

			<div class="overflow-x-auto">
				<form id="faq-sections-filters-form" method="GET" action="{{ route('dashboard.faqs.sections.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 p-4 md:grid-cols-12 dark:border-gray-800">
					<div class="md:col-span-6">
						<input id="q" name="q" type="text" value="{{ $search }}" placeholder="Search by title or code" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div class="md:col-span-2">
						<select id="status" name="status" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<select id="audience" name="audience" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $audience === 'all' ? 'selected' : '' }}>All Audience</option>
							<option value="brand" {{ $audience === 'brand' ? 'selected' : '' }}>Brand</option>
							<option value="influencer" {{ $audience === 'influencer' ? 'selected' : '' }}>Influencer</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<a href="{{ route('dashboard.faqs.sections.index') }}" class="block h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.faqs.sections._results', ['sections' => $sections])
			</div>
		</div>
	</div>
@endsection
