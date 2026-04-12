@extends('backend.layouts.app')

@section('title', 'Roles Management')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Roles Management" />

	<div class="space-y-6">
		<!-- Stats Grid -->
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Roles</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white" id="totalRoles">{{ $roles->total() }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200" id="activeRoles">
					{{ $roles->where('is_active', true)->count() }}</p>
			</div>
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Permissions</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">
					{{ \App\Models\Permission::where('is_active', true)->count() }}</p>
			</div>
		</div>

		<!-- Main Card -->
		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<!-- Header -->
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Roles</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage system roles and assign permissions to users.
					</p>
				</div>
				<button type="button" @click="window.Alpine.store('roleFormModal').openCreateModal()"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Role
				</button>
			</div>

			<!-- Table -->
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr>
							<th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Role Name</th>
							<th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Description</th>
							<th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Permissions</th>
							<th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Users</th>
							<th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Status</th>
							<th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-800" id="rolesTableBody">
						@forelse($roles as $role)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors" data-role-id="{{ $role->id }}">
								<td class="whitespace-nowrap px-6 py-4">
									<div class="flex items-center gap-3">
										<div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
											<svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
												viewBox="0 0 24 24" stroke-width="2">
												<path stroke-linecap="round" stroke-linejoin="round"
													d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
												</path>
											</svg>
										</div>
										<div>
											<p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $role->name }}</p>
											<p class="text-xs text-gray-500 dark:text-gray-400">{{ $role->slug }}</p>
										</div>
									</div>
								</td>
								<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
									{{ $role->description ? Str::limit($role->description, 50) : '—' }}
								</td>
								<td class="px-6 py-4">
									<div class="flex flex-wrap gap-1">
										@foreach ($role->permissions->take(2) as $permission)
											<span
												class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
												{{ $permission->name }}
											</span>
										@endforeach
										@if ($role->permissions->count() > 2)
											<span
												class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
												+{{ $role->permissions->count() - 2 }}
											</span>
										@endif
										@if ($role->permissions->count() === 0)
											<span class="text-xs text-gray-400">No permissions</span>
										@endif
									</div>
								</td>
								<td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
									<span
										class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">
										{{ $role->users()->count() }}
									</span>
								</td>
								<td class="whitespace-nowrap px-6 py-4">
									<button type="button"
										@click="toggleRoleStatus({{ $role->id }}, {{ $role->is_active ? 'false' : 'true' }})"
										class="relative inline-flex h-6 w-11 cursor-pointer rounded-full transition-colors {{ $role->is_active ? 'bg-green-500' : 'bg-gray-300' }} hover:opacity-80 dark:{{ $role->is_active ? 'bg-green-600' : 'bg-gray-600' }}"
										role="switch" :aria-checked="true">
										<span
											class="inline-block h-5 w-5 transform rounded-full bg-white transition {{ $role->is_active ? 'translate-x-5' : 'translate-x-0' }}">
										</span>
									</button>
								</td>
								<td class="whitespace-nowrap px-6 py-4 text-right">
									<div class="flex items-center justify-end gap-2">
										<button type="button" @click="window.Alpine.store('roleFormModal').openEditModal({{ $role->id }})"
											class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-indigo-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-indigo-400"
											title="Edit">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
												<path stroke-linecap="round" stroke-linejoin="round"
													d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
												</path>
											</svg>
										</button>
										<button type="button"
											@click="$dispatch('openDeleteModal', {roleId: {{ $role->id }}, roleName: '{{ $role->name }}'})"
											class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-red-600 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-red-400"
											title="Delete">
											<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
												<path stroke-linecap="round" stroke-linejoin="round"
													d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
												</path>
											</svg>
										</button>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-6 py-12 text-center">
									<div class="flex flex-col items-center justify-center">
										<svg class="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor"
											viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
											</path>
										</svg>
										<p class="text-gray-500 dark:text-gray-400">No roles found</p>
										<button type="button" @click="$dispatch('openCreateModal')"
											class="mt-2 text-indigo-600 transition hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
											Create your first role
										</button>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			<!-- Pagination -->
			@if ($roles->hasPages())
				<div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
					{{ $roles->links() }}
				</div>
			@endif
		</div>
	</div>

	<!-- Create/Edit Modal -->
	<div x-show="$store.roleFormModal.isOpen" x-cloak
		class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
		@click.outside="$store.roleFormModal.closeModal()">
		<div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl dark:bg-gray-900" @click.stop>
			<!-- Header -->
			<div class="border-b border-gray-200 p-6 dark:border-gray-700">
				<div class="flex items-center justify-between">
					<h3 class="text-xl font-semibold text-gray-900 dark:text-white"
						x-text="$store.roleFormModal.editingId ? 'Edit Role' : 'Create New Role'"></h3>
					<button type="button" @click="$store.roleFormModal.closeModal()"
						class="text-gray-400 transition hover:text-gray-600 dark:hover:text-white">
						<svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>
			</div>

			<!-- Content -->
			<form @submit.prevent="$store.roleFormModal.submitForm()" class="space-y-6 p-6">
				<!-- Name -->
				<div>
					<label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role Name *</label>
					<input type="text" id="name" x-model="$store.roleFormModal.formData.name"
						placeholder="e.g., Super Admin"
						class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
						required />
					<template x-if="$store.roleFormModal.errors.name">
						<p class="mt-1 text-sm text-red-600 dark:text-red-400" x-text="$store.roleFormModal.errors.name[0]"></p>
					</template>
				</div>

				<!-- Status -->
				<div class="flex items-center gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
					<label class="flex items-center gap-2 cursor-pointer flex-1">
						<input type="checkbox" x-model="$store.roleFormModal.formData.is_active"
							class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800" />
						<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
					</label>
				</div>

				<!-- Actions -->
				<div class="flex gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
					<button type="button" @click="$store.roleFormModal.closeModal()"
						class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
						Cancel
					</button>
					<button type="submit" :disabled="$store.roleFormModal.loading"
						class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 disabled:opacity-50"
						:class="{ 'opacity-50 cursor-not-allowed': $store.roleFormModal.loading }">
						<span x-show="!$store.roleFormModal.loading"
							x-text="$store.roleFormModal.editingId ? 'Update Role' : 'Create Role'"></span>
						<span x-show="$store.roleFormModal.loading" class="inline-flex items-center gap-2">
							<svg class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
								</path>
							</svg>
							Saving...
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Delete Confirmation Modal -->
	<x-confirmation-modal title="Delete Role"
		message="Are you sure you want to delete this role? This action cannot be undone." confirmText="Delete"
		variant="danger" />

	@push('scripts')
		<script>
			async function deleteRole(roleId) {
				try {
					const response = await fetch(`/dashboard/roles/${roleId}`, {
						method: 'DELETE',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
							'Content-Type': 'application/json'
						}
					});

					const data = await response.json();

					if (data.success) {
						window.toast.success(data.message);
						setTimeout(() => window.location.reload(), 500);
					} else {
						window.toast.error(data.message);
					}
				} catch (error) {
					console.error('Error:', error);
					window.toast.error('An error occurred while deleting the role');
				}
			}

			async function toggleRoleStatus(roleId, isActive) {
				try {
					const response = await fetch(`/dashboard/roles/${roleId}/toggle-status`, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
							'Content-Type': 'application/json'
						},
						body: JSON.stringify({
							is_active: isActive
						})
					});

					const data = await response.json();

					if (data.success) {
						window.toast.success(data.message);
						setTimeout(() => window.location.reload(), 500);
					} else {
						window.toast.error(data.message);
					}
				} catch (error) {
					console.error('Error:', error);
					window.toast.error('Failed to update role status');
				}
			}

			// Initialize delete modal listener
			document.addEventListener('openDeleteModal', function(e) {
				const {
					roleId,
					roleName
				} = e.detail;
				window.confirmationModal.open({
					title: 'Delete Role',
					message: `Are you sure you want to delete the role "${roleName}"? If this role is assigned to users, you won't be able to delete it.`,
					confirmText: 'Delete',
					variant: 'danger',
					onConfirm: () => deleteRole(roleId)
				});
			});
		</script>
	@endpush
@endsection
