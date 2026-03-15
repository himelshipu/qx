@extends('backend.layouts.app')

@section('title', 'Moderator Details')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Moderator Details" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div class="flex items-center gap-4">
					<div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
						<span
							class="text-xl font-semibold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($moderator->name, 0, 1)) }}</span>
					</div>
					<div>
						<h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $moderator->name }}</h2>
						<p class="text-sm text-gray-500 dark:text-gray-400">Moderator account</p>
					</div>
				</div>
				<div class="flex items-center gap-2">
					<a href="{{ route('dashboard.moderators.edit', $moderator) }}"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.edit class="h-4 w-4" />
						Edit Moderator
					</a>
					<a href="{{ route('dashboard.moderators.index') }}"
						class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						Back to List
					</a>
				</div>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Account Details</h3>
			<dl class="mt-4 grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Name</dt>
					<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $moderator->name }}</dd>
				</div>
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</dt>
					<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $moderator->email }}</dd>
				</div>
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Phone</dt>
					<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $moderator->phone ?: 'N/A' }}</dd>
				</div>
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</dt>
					<dd
						class="mt-1 font-medium {{ $moderator->is_active ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
						{{ $moderator->is_active ? 'Active' : 'Inactive' }}
					</dd>
				</div>
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Created At</dt>
					<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $moderator->created_at?->format('M d, Y h:i A') }}
					</dd>
				</div>
				<div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
					<dt class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Updated At</dt>
					<dd class="mt-1 font-medium text-gray-900 dark:text-white">{{ $moderator->updated_at?->format('M d, Y h:i A') }}
					</dd>
				</div>
			</dl>
		</div>
	</div>
@endsection
