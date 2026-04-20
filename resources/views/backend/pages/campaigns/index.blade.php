@extends('backend.layouts.app')

@section('title', 'Campaigns')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Campaigns" />

	<div class="space-y-6" id="campaigns-dashboard"
		data-filter-results-route="{{ route('dashboard.campaigns.standard.table') }}">
		@include('backend.pages.campaigns._alerts')

		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['published'] }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">{{ $stats['draft'] }}</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">{{ $stats['active'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Campaign Management</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage campaign targeting, schedules, and status across the
						platform.</p>
				</div>
				<div class="flex flex-wrap items-center gap-2">
					<a href="{{ route('dashboard.campaigns.standard.create') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						New Campaign
					</a>
				</div>
			</div>

			<div class="p-5">
				<form id="campaign-filters-form" method="GET" action="{{ route('dashboard.campaigns.standard') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-12">
					<div class="md:col-span-3">
						<label for="q"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Search</label>
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by title, type, status, or description"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div class="md:col-span-2">
						<label for="status"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</label>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($statusOptions as $option)
								<option value="{{ $option['value'] }}" {{ $status === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="md:col-span-2">
						<label for="type"
							class="mb-1 block text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</label>
						<select id="type" name="type"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@foreach ($typeOptions as $option)
								<option value="{{ $option['value'] }}" {{ $type === $option['value'] ? 'selected' : '' }}>
									{{ $option['label'] }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end gap-2 md:col-span-2">
						<a href="{{ route('dashboard.campaigns.standard') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.campaigns._results', ['campaigns' => $campaigns])
			</div>
		</div>
	</div>
@endsection
