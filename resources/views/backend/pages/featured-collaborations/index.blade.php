@extends('backend.layouts.app')

@section('title', 'Featured Collaborations')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Featured Collaborations" />

	<div class="space-y-6" id="featured-collaborations-dashboard"
		data-csrf-token="{{ csrf_token() }}"
		data-filter-results-route="{{ route('dashboard.featured-collaborations.table') }}"
		data-reorder-route="{{ route('dashboard.featured-collaborations.reorder') }}"
		data-status-toggle-template="{{ route('dashboard.featured-collaborations.toggle-publish', ['featuredCollaboration' => '__ID__']) }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Unpublished</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">{{ $stats['unpublished'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Featured Collaborations</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage collaboration cards shown in homepage spotlight section.</p>
				</div>
				<a href="{{ route('dashboard.featured-collaborations.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Collaboration
				</a>
			</div>

			<div class="overflow-x-auto">
				<form id="featured-collaborations-filters-form" method="GET" action="{{ route('dashboard.featured-collaborations.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 p-4 md:grid-cols-12 dark:border-gray-800">
					<div class="md:col-span-6">
						<input id="q" name="q" type="text" value="{{ $search }}" placeholder="Search by brand name" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div class="md:col-span-2">
						<select id="status" name="status" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
							<option value="unpublished" {{ $status === 'unpublished' ? 'selected' : '' }}>Unpublished</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<select id="asset_type" name="asset_type" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $assetType === 'all' ? 'selected' : '' }}>All Types</option>
							<option value="image" {{ $assetType === 'image' ? 'selected' : '' }}>Image</option>
							<option value="video" {{ $assetType === 'video' ? 'selected' : '' }}>Video</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<a href="{{ route('dashboard.featured-collaborations.index') }}" class="block h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.featured-collaborations._results', ['collaborations' => $collaborations])
			</div>
		</div>
	</div>
@endsection
