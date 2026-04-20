@extends('backend.layouts.app')

@section('title', 'Users')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Users" />

	<div class="space-y-6" id="users-dashboard"
		data-filter-results-route="{{ route('dashboard.users.table') }}"
		data-status-toggle-template="{{ route('dashboard.users.toggle-status', ['user' => '__ID__']) }}"
		data-csrf-token="{{ csrf_token() }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Brands + Influencers</p>
				<p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">{{ $stats['brands'] + $stats['influencers'] }}
				</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Moderators + Admins
				</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">
					{{ $stats['moderators'] + $stats['admins'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">{{ $stats['active'] }}</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Directory</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All brands, influencers, moderators, and admins in one
						place.
					</p>
				</div>
				<a href="{{ route('dashboard.users.create') }}"
					class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
					</svg>
					Add User
				</a>
			</div>

			<div class="p-5">
				<form id="users-filters-form" method="GET" action="{{ route('dashboard.users.index') }}"
					class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-6">
					<div class="md:col-span-2">
						<div class="relative">
							<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
								<x-icons.search class="h-4 w-4" />
							</span>
							<input id="q" name="q" type="text" value="{{ $search }}"
								placeholder="Search by name, email, phone, city, country or type"
								class="h-10 w-full rounded-lg border border-gray-200 bg-transparent pl-10 pr-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						</div>
					</div>
					<div>
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
							<option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
							<option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>
					<div>
						<select id="role" name="role"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="" {{ empty($role) ? 'selected' : '' }}>All Roles</option>
							@foreach ($roles as $roleItem)
								<option value="{{ $roleItem->id }}" {{ $role === (string) $roleItem->id ? 'selected' : '' }}>
									{{ $roleItem->name }}
								</option>
							@endforeach
						</select>
					</div>
					<div class="flex items-end gap-2">
						<a href="{{ route('dashboard.users.index') }}"
							class="h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				@include('backend.pages.users._results', ['users' => $users])
			</div>
		</div>
	</div>
@endsection
