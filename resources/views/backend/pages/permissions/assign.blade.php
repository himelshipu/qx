@extends('backend.layouts.app')

@section('title', 'Assign Permissions')

@section('content')
	@php
		$permissionIdBySlug = $permissions
			->flatten()
			->mapWithKeys(fn($permission) => [$permission->slug => $permission->id])
			->toArray();

		$allPermissionIds = $permissions
			->flatten()
			->pluck('id')
			->unique()
			->values()
			->all();
	@endphp

	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
		x-data="permissionManager(@js($permissionIdBySlug), @js($allPermissionIds))">
		<!-- Header -->
		<div class="p-6 border-b border-gray-200 dark:border-gray-700">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div>
					<h2 class="text-xl font-semibold text-gray-900 dark:text-white">Assign Permissions</h2>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Grant or revoke module access for roles</p>
				</div>
				<a href="{{ route('dashboard.roles.index') }}"
					class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Roles
				</a>
			</div>
		</div>

		<form action="{{ route('dashboard.permissions.assign.store') }}" method="POST" x-ref="permissionsForm">
			@csrf
			<input type="hidden" name="role_id" :value="roleId">
			<template x-for="permissionId in selectedPermissionIds" :key="`permission-${permissionId}`">
				<input type="hidden" name="permissions[]" :value="permissionId">
			</template>
			<p class="px-6 pt-4 text-xs text-gray-500 dark:text-gray-400">
				The grid shows common actions only. `Check All` still selects all active permissions in the system for this role.
			</p>

			<!-- Table Wrapper -->
			<div class="overflow-x-auto custom-scrollbar">
				<table class="w-full text-left border-collapse min-w-[1000px]">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
							<th class="px-6 py-4">Modules</th>
							<th class="px-4 py-4">
								<div class="flex items-center gap-2">
									<select x-model="roleId" @change="loadRolePermissions()"
										class="w-48 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-purple-500 dark:bg-gray-900 dark:text-white"
										required>
										<option value="">Select Role *</option>
										@foreach ($roles as $role)
											<option value="{{ $role->id }}">{{ $role->name }}</option>
										@endforeach
									</select>
									<button type="button" @click="allChecked ? uncheckAllPermissions() : checkAllPermissions()"
										class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-2">
										<svg x-show="!allChecked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
										</svg>
										<svg x-show="allChecked" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
										</svg>
										<span x-text="allChecked ? 'Uncheck All' : 'Check All'"></span>
									</button>
									<button type="submit"
										class="px-4 py-2 bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
										Save Permissions
									</button>
								</div>
							</th>
							@php $standardActions = ['View', 'Create', 'Edit', 'Delete', 'Show', 'Status', 'Manage']; @endphp
							@foreach ($standardActions as $action)
								<th class="px-4 py-4 text-center">{{ $action }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@foreach ($permissions->keys() as $moduleName)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
								<td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white capitalize">
									{{ $moduleName }}
								</td>
								<td class="px-4 py-4"></td>
								@foreach ($standardActions as $action)
									@php
										$modulePermissions = $permissions->get($moduleName, collect());
										$moduleSlug = strtolower($moduleName);
										$actionCandidates = match (strtolower($action)) {
											'view' => ['index', 'view'],
											'create' => ['create', 'store'],
											'edit' => ['edit', 'update'],
											'delete' => ['destroy', 'delete'],
											'show' => ['show'],
											'status' => ['toggle-status', 'update-status', 'status'],
											'manage' => ['assign', 'reorder', 'purchase', 'bulk-update', 'bulk-mark', 'refund', 'retry', 'pdf'],
											default => [strtolower($action)],
										};

										$specialModuleActionSlugs = [
											'content' => [
												'view' => 'static-pages.index',
												'create' => 'static-pages.create',
												'edit' => 'static-pages.edit',
												'delete' => 'static-pages.destroy',
												'show' => 'static-pages.show',
												'status' => 'static-pages.toggle-status',
												'manage' => 'settings.index',
											],
											'permissions' => [
												'view' => 'permissions.assign',
												'create' => 'permissions.assign.store',
												'manage' => 'permissions.assign',
											],
											'support' => [
												'view' => 'support-tickets.index',
												'edit' => 'support-tickets.update',
												'delete' => 'support-tickets.destroy',
												'show' => 'support-tickets.show',
												'manage' => 'support-tickets.bulk-update',
											],
											'roles' => [
												'create' => 'roles.store',
												'edit' => 'roles.update',
												'show' => 'roles.permissions',
												'status' => 'roles.toggle-status',
											],
										];

										$specialSlug = $specialModuleActionSlugs[$moduleSlug][strtolower($action)] ?? null;

										if ($specialSlug) {
											$matchedPermission = $modulePermissions->first(fn($permission) => $permission->slug === $specialSlug);
										} else {
											$matchedPermission = $modulePermissions->first(function ($permission) use ($actionCandidates) {
												$slug = $permission->slug;
												foreach ($actionCandidates as $candidate) {
													if ($slug === $candidate || str_ends_with($slug, '.' . $candidate)) {
														return true;
													}
												}

												return false;
											});
										}

										$permissionId = $matchedPermission?->id;
										$permissionSlug = $matchedPermission?->slug;
									@endphp
									<td class="px-4 py-4 text-center">
										<div class="flex justify-center">
											<label class="relative inline-flex items-center cursor-pointer">
												<input type="checkbox" class="sr-only peer"
													@disabled(!$permissionId)
													:checked="selectedPermissionIds.includes({{ $permissionId ?? 'null' }})"
													@change="togglePermission($event, {{ $permissionId ?? 'null' }}, '{{ $permissionSlug }}')">
												<div
													class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-disabled:opacity-40 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600">
												</div>
											</label>
										</div>
									</td>
								@endforeach
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function permissionManager(permissionIdBySlug = {}, allPermissionIds = []) {
				return {
					roleId: '',
					permissionIdBySlug,
					allPermissionIds,
					activePermissions: [],
					selectedPermissionIds: [],
					get allChecked() {
						return this.selectedPermissionIds.length === this.allPermissionIds.length && this.allPermissionIds.length > 0;
					},
					async loadRolePermissions() {
						if (!this.roleId) {
							this.activePermissions = [];
							this.selectedPermissionIds = [];
							return;
						}
						try {
							const response = await fetch(`/dashboard/roles/${this.roleId}/permissions/names`, {
								headers: {
									'Accept': 'application/json'
								}
							});
							const data = await response.json();
							this.activePermissions = data.permissions || [];
							this.selectedPermissionIds = this.activePermissions
								.map((slug) => this.permissionIdBySlug[slug])
								.filter((id) => Number.isInteger(id));
						} catch (error) {
							console.error("Error loading permissions:", error);
						}
					},
					checkAllPermissions() {
						this.activePermissions = Object.keys(this.permissionIdBySlug);
						this.selectedPermissionIds = [...this.allPermissionIds];
					},
					uncheckAllPermissions() {
						this.activePermissions = [];
						this.selectedPermissionIds = [];
					},
					togglePermission(event, permissionId, permissionSlug) {
						if (!Number.isInteger(permissionId)) {
							return;
						}

						if (event.target.checked) {
							if (!this.selectedPermissionIds.includes(permissionId)) {
								this.selectedPermissionIds.push(permissionId);
							}
							if (permissionSlug && !this.activePermissions.includes(permissionSlug)) {
								this.activePermissions.push(permissionSlug);
							}
							return;
						}

						this.selectedPermissionIds = this.selectedPermissionIds.filter((id) => id !== permissionId);
						this.activePermissions = this.activePermissions.filter((slug) => slug !== permissionSlug);
					}
				}
			}
		</script>
	@endpush
@endsection
