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

		$preferredActionOrder = [
			'index',
			'view',
			'create',
			'store',
			'edit',
			'update',
			'destroy',
			'show',
			'toggle-status',
			'update-status',
			'assign',
			'assign.store',
			'reorder',
			'purchase',
			'purchase.store',
			'bulk-update',
			'bulk-mark',
			'refund',
			'retry',
			'pdf',
		];

		$actionLabels = [
			'index' => 'View List',
			'view' => 'View',
			'create' => 'Create',
			'store' => 'Store',
			'edit' => 'Edit',
			'update' => 'Update',
			'destroy' => 'Delete',
			'show' => 'Show',
			'toggle-status' => 'Status',
			'update-status' => 'Update Status',
			'assign' => 'Assign',
			'assign.store' => 'Assign Save',
			'reorder' => 'Reorder',
			'purchase' => 'Purchase',
			'purchase.store' => 'Purchase Save',
			'bulk-update' => 'Bulk Update',
			'bulk-mark' => 'Bulk Mark',
			'refund' => 'Refund',
			'retry' => 'Retry',
			'pdf' => 'PDF',
		];

		$moduleActionPermissionIds = [];
		$detectedActions = collect();

		foreach ($permissions as $moduleName => $modulePermissions) {
			$moduleActionPermissionIds[$moduleName] = [];

			foreach ($modulePermissions as $permission) {
				$slug = (string) $permission->slug;
				$action = str_contains($slug, '.') ? explode('.', $slug, 2)[1] : $slug;

				$moduleActionPermissionIds[$moduleName][$action] ??= [];
				$moduleActionPermissionIds[$moduleName][$action][] = (int) $permission->id;
				$detectedActions->push($action);
			}
		}

		$detectedActions = $detectedActions->unique()->values();

		$orderedActions = collect($preferredActionOrder)
			->filter(fn($action) => $detectedActions->contains($action));

		$extraActions = $detectedActions
			->reject(fn($action) => in_array($action, $preferredActionOrder, true))
			->sort()
			->values();

		$tableActions = $orderedActions->concat($extraActions)->values();
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
				Columns are generated from active permission slugs. Each toggle controls all permissions in that module matching the action.
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
							@foreach ($tableActions as $action)
								<th class="px-4 py-4 text-center whitespace-nowrap">{{ $actionLabels[$action] ?? ucfirst(str_replace(['-', '.'], ' ', $action)) }}</th>
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
								@foreach ($tableActions as $action)
									@php
										$actionPermissionIds = $moduleActionPermissionIds[$moduleName][$action] ?? [];
									@endphp
									<td class="px-4 py-4 text-center">
										<div class="flex justify-center">
											<label class="relative inline-flex items-center cursor-pointer">
												<input type="checkbox" class="sr-only peer"
													@disabled(empty($actionPermissionIds))
													:checked="isActionChecked(@js($actionPermissionIds))"
													@change="toggleActionPermissions($event, @js($actionPermissionIds))">
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
					isActionChecked(permissionIds = []) {
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return false;
						}

						return permissionIds.every((id) => this.selectedPermissionIds.includes(id));
					},
					toggleActionPermissions(event, permissionIds = []) {
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return;
						}

						if (event.target.checked) {
							permissionIds.forEach((id) => {
								if (!this.selectedPermissionIds.includes(id)) {
									this.selectedPermissionIds.push(id);
								}
							});

							return;
						}

						this.selectedPermissionIds = this.selectedPermissionIds.filter((id) => !permissionIds.includes(id));
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
