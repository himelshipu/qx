@extends('backend.layouts.app')

@section('title', 'Assign Roles to Users')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Users', 'url' => route('dashboard.users.index')]]" pageTitle="Assign User Roles" />

	<div id="user-role-assignment"
		data-fetch-user-roles-template="{{ route('dashboard.users.roles.get', ['user' => '__ID__']) }}"
		data-assign-route="{{ route('dashboard.users.roles.assign.store') }}"
		data-csrf-token="{{ csrf_token() }}"
		class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
			<div class="border-b border-gray-200 p-6 dark:border-gray-700">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Roles to Users</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage role access for active dashboard users.</p>
					</div>
					<a href="{{ route('dashboard.users.index') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
						<x-icons.chevron-left class="h-4 w-4" />
						Back to Users
					</a>
				</div>
			</div>

			<form id="user-role-assignment-form" class="space-y-6 p-6">
				<div class="space-y-3">
					<label for="user_id" class="block text-sm font-semibold text-gray-900 dark:text-white">
						Select User <span class="text-red-500">*</span>
	<x-backend.shell.breadcrumb :links="[['label' => 'Users', 'url' => route('dashboard.users.index')]]" pageTitle="Assign User Roles" />

	<div id="user-role-assignment"
		data-fetch-user-roles-template="{{ route('dashboard.users.roles.get', ['user' => '__ID__']) }}"
		data-assign-route="{{ route('dashboard.users.roles.assign.store') }}"
		data-csrf-token="{{ csrf_token() }}"
		class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
			<div class="border-b border-gray-200 p-6 dark:border-gray-700">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Roles to Users</h2>
						<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage role access for active dashboard users.</p>
					</div>
					<a href="{{ route('dashboard.users.index') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors duration-200 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
						<x-icons.chevron-left class="h-4 w-4" />
						Back to Users
					</a>
				</div>
			</div>

			<form id="user-role-assignment-form" class="space-y-6 p-6">
				<div class="space-y-3">
					<label for="user_id" class="block text-sm font-semibold text-gray-900 dark:text-white">
						Select User <span class="text-red-500">*</span>
					</label>
					<select id="user_id" name="user_id"
						class="h-11 w-full rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-900 transition-all duration-200 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
						required>
						<option value="">-- Select a user --</option>
						@foreach ($users as $user)
							<option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }}) - {{ ucfirst($user->user_type) }}</option>
						@endforeach
					</select>
				</div>

				<div class="space-y-4">
					<div class="flex items-center justify-between">
						<label class="block text-sm font-semibold text-gray-900 dark:text-white">Available Roles</label>
						<span id="selected-role-count"
							class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">Selected: 0</span>
					</div>

					<div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
						@foreach ($roles as $role)
							<label for="role_{{ $role->id }}"
								class="group flex items-center rounded-lg border-2 border-gray-200 p-4 transition-all duration-200 hover:border-purple-400 hover:bg-purple-50 dark:border-gray-700 dark:hover:border-purple-600 dark:hover:bg-purple-900/10">
								<input id="role_{{ $role->id }}" type="checkbox" name="roles[]" value="{{ $role->id }}"
									class="h-5 w-5 cursor-pointer rounded text-purple-600 focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700">
								<div class="ml-3 flex-1">
									<div class="mb-1 flex items-center justify-between">
										<span class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 dark:text-white dark:group-hover:text-purple-300">{{ $role->name }}</span>
										<span class="rounded bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/50 dark:text-purple-300">{{ $role->permissions_count ?? 0 }} perms</span>
									</div>
									@if ($role->description)
										<span class="mt-1 block text-xs text-gray-600 dark:text-gray-400">{{ $role->description }}</span>
									@endif
								</div>
							</label>
						@endforeach
					</div>
				</div>

				<div id="current-role-box"
					class="hidden rounded-lg border border-blue-200 bg-linear-to-r from-blue-50 to-blue-100 p-4 dark:border-blue-800 dark:from-blue-900/20 dark:to-blue-900/10">
					<p class="mb-3 text-sm font-semibold text-blue-900 dark:text-blue-200">Currently Assigned Roles</p>
					<div id="current-role-chips" class="flex flex-wrap gap-2"></div>
				</div>

				<div class="flex gap-3 pt-2">
					<button id="assign-roles-submit" type="submit"
						class="inline-flex items-center gap-2 rounded-lg bg-purple-700 px-6 py-3 text-sm font-semibold text-white transition-all duration-200 hover:bg-purple-800 disabled:cursor-not-allowed disabled:opacity-50">
						Assign Roles
					</button>
					<button id="clear-role-selection" type="button"
						class="rounded-lg bg-gray-200 px-6 py-3 text-sm font-semibold text-gray-900 transition-all duration-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
						Clear Selection
					</button>
				</div>
			</form>
		</div>
	</div>
