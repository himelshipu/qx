@extends('backend.layouts.app')

@section('content')
	<div data-support-tickets-dashboard data-table-url="{{ route('dashboard.support-tickets.table') }}" class="space-y-6">
		<div class="mb-6">
			<div class="flex items-center justify-between">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Support Tickets</h1>
					<p class="mt-1 text-gray-600 dark:text-gray-400">Manage customer support requests</p>
				</div>
			</div>
		</div>

		<div class="grid gap-4 md:grid-cols-4">
			<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
				<p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] ?? 0 }}</p>
			</div>
			<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
				<p class="text-sm text-gray-500 dark:text-gray-400">New</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['new'] ?? 0 }}</p>
			</div>
			<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
				<p class="text-sm text-gray-500 dark:text-gray-400">Resolved</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['resolved'] ?? 0 }}</p>
			</div>
			<div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
				<p class="text-sm text-gray-500 dark:text-gray-400">Closed</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['closed'] ?? 0 }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<form data-support-tickets-filter-form method="GET" action="{{ route('dashboard.support-tickets.index') }}" class="flex flex-wrap items-end gap-3">
				<div class="min-w-60 flex-1">
					<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
					<input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ticket #, subject or description..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				</div>
				<div class="min-w-44">
					<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<option value="">All Statuses</option>
						@foreach ($statuses as $status)
							<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
						@endforeach
					</select>
				</div>
				<div class="min-w-44">
					<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
					<select name="category" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<option value="">All Categories</option>
						@foreach ($categories as $category)
							<option value="{{ $category->id }}" @selected((string) ($filters['category'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
						@endforeach
					</select>
				</div>
				<div class="min-w-44">
					<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
					<select name="priority" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<option value="">All Priorities</option>
						@foreach ($priorities as $priority)
							<option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>{{ ucfirst($priority) }}</option>
						@endforeach
					</select>
				</div>
				<div class="flex items-center gap-2">
					<button type="button" data-support-tickets-reset class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">Reset</button>
				</div>
			</form>
		</div>

		<div data-support-tickets-results>
			@include('backend.pages.support-tickets._results', ['tickets' => $tickets])
		</div>
	</div>
@endsection
