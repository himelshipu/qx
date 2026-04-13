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

		$bundleDefinitions = [
			'access' => [
				'label' => 'Access',
				'actions' => ['index', 'view', 'show'],
			],
			'manage' => [
				'label' => 'Manage',
				'actions' => ['create', 'store', 'edit', 'update', 'destroy', 'toggle-status', 'update-status'],
			],
			'workflow' => [
				'label' => 'Workflow',
				'actions' => ['assign', 'assign.store', 'reorder', 'bulk-update', 'bulk-mark', 'purchase', 'purchase.store'],
			],
			'financial' => [
				'label' => 'Financial',
				'actions' => ['refund', 'retry', 'pdf', 'mark-paid', 'undo-item', 'undo-suborder'],
			],
		];

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
		$moduleBundlePermissionIds = [];
		$moduleAllPermissionIds = [];
		$detectedActions = collect();

		foreach ($permissions as $moduleName => $modulePermissions) {
			$moduleActionPermissionIds[$moduleName] = [];
			$moduleBundlePermissionIds[$moduleName] = [];
			$moduleAllPermissionIds[$moduleName] = [];

			foreach ($modulePermissions as $permission) {
				$slug = (string) $permission->slug;
				$action = str_contains($slug, '.') ? explode('.', $slug, 2)[1] : $slug;

				$moduleActionPermissionIds[$moduleName][$action] ??= [];
				$moduleActionPermissionIds[$moduleName][$action][] = (int) $permission->id;
				$moduleAllPermissionIds[$moduleName][] = (int) $permission->id;
				$detectedActions->push($action);

				foreach ($bundleDefinitions as $bundleKey => $bundleDefinition) {
					if (in_array($action, $bundleDefinition['actions'], true)) {
						$moduleBundlePermissionIds[$moduleName][$bundleKey] ??= [];
						$moduleBundlePermissionIds[$moduleName][$bundleKey][] = (int) $permission->id;
					}
				}
			}

			foreach ($moduleBundlePermissionIds[$moduleName] as $bundleKey => $ids) {
				$moduleBundlePermissionIds[$moduleName][$bundleKey] = array_values(array_unique($ids));
			}

			$moduleAllPermissionIds[$moduleName] = array_values(array_unique($moduleAllPermissionIds[$moduleName]));
		}

		$detectedActions = $detectedActions->unique()->values();

		$orderedActions = collect($preferredActionOrder)
			->filter(fn($action) => $detectedActions->contains($action));

		$extraActions = $detectedActions
			->reject(fn($action) => in_array($action, $preferredActionOrder, true))
			->sort()
			->values();

		$tableActions = $orderedActions->concat($extraActions)->values();

		$moduleOrderedActions = [];
		foreach ($permissions as $moduleName => $modulePermissions) {
			$moduleDetectedActions = collect(array_keys($moduleActionPermissionIds[$moduleName] ?? []));

			$moduleOrdered = collect($preferredActionOrder)
				->filter(fn($action) => $moduleDetectedActions->contains($action));

			$moduleExtra = $moduleDetectedActions
				->reject(fn($action) => in_array($action, $preferredActionOrder, true))
				->sort()
				->values();

			$moduleOrderedActions[$moduleName] = $moduleOrdered->concat($moduleExtra)->values()->all();
		}

		$advancedModuleNames = collect(array_keys($moduleOrderedActions))
			->sortByDesc(fn($moduleName) => count($moduleOrderedActions[$moduleName] ?? []))
			->values()
			->all();
	@endphp

	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
		x-data="permissionManager(@js($permissionIdBySlug), @js($allPermissionIds), @js($bundleDefinitions), @js($moduleBundlePermissionIds), @js($moduleAllPermissionIds))">
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

			<div class="px-6 pt-4">
				<div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
					<div class="flex items-center gap-2">
						<select x-model="roleId" @change="loadRolePermissions()"
							class="w-52 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-1 focus:ring-purple-500 dark:bg-gray-900 dark:text-white"
							required>
							<option value="">Select Role *</option>
							@foreach ($roles as $role)
								<option value="{{ $role->id }}">{{ $role->name }}</option>
							@endforeach
						</select>

						<div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
							<button type="button" @click="mode = 'basic'"
								:class="mode === 'basic' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 dark:bg-gray-900 dark:text-gray-300'"
								class="px-3 py-2 text-sm font-medium transition-colors">
								Basic
							</button>
							<button type="button" @click="mode = 'advanced'"
								:class="mode === 'advanced' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 dark:bg-gray-900 dark:text-gray-300'"
								class="px-3 py-2 text-sm font-medium transition-colors border-l border-gray-300 dark:border-gray-600">
								Advanced
							</button>
						</div>
					</div>

					<div class="flex items-center gap-2">
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
				</div>

				<p class="mt-3 text-xs text-gray-500 dark:text-gray-400" x-show="mode === 'basic'">
					Basic mode uses capability bundles (Access, Manage, Workflow, Financial) for faster role setup.
				</p>
				<div class="mt-2 flex flex-wrap items-center gap-2" x-show="mode === 'basic'" x-cloak>
					<span class="text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">Access: view/list</span>
					<span class="text-[11px] px-2 py-0.5 rounded-full bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-300">Manage: create/edit/delete/status</span>
					<span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Workflow: assign/reorder/bulk</span>
					<span class="text-[11px] px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">Financial: refund/retry/pdf/paid</span>
				</div>
				<p class="mt-3 text-xs text-gray-500 dark:text-gray-400" x-show="mode === 'advanced'">
					Advanced mode shows exact action columns generated from active permission slugs.
				</p>
			</div>

			<!-- Table Wrapper -->
			<div class="overflow-x-auto custom-scrollbar mt-4" x-show="mode === 'basic'" x-cloak>
				<table class="w-full text-left border-collapse min-w-[880px]">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
							<th class="px-6 py-4">Modules</th>
							@foreach ($bundleDefinitions as $bundleDefinition)
								<th class="px-4 py-4 text-center whitespace-nowrap">{{ $bundleDefinition['label'] }}</th>
							@endforeach
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
						@foreach ($permissions->keys() as $moduleName)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
								<td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $moduleName }}</td>
								@foreach ($bundleDefinitions as $bundleKey => $bundleDefinition)
									@php
										$bundlePermissionIds = $moduleBundlePermissionIds[$moduleName][$bundleKey] ?? [];
									@endphp
									<td class="px-4 py-4 text-center">
										@if (empty($bundlePermissionIds))
											<span class="text-[11px] font-medium text-amber-600 dark:text-amber-400">Not Applicable</span>
										@else
											<div class="flex justify-center">
												<label class="relative inline-flex items-center cursor-pointer">
													<input type="checkbox" class="sr-only peer"
														:checked="isBundleChecked('{{ $moduleName }}', '{{ $bundleKey }}')"
														@change="toggleBundlePermissions($event, '{{ $moduleName }}', '{{ $bundleKey }}')">
													<div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
												</label>
											</div>
										@endif
									</td>
								@endforeach
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="mt-4" x-show="mode === 'advanced'" x-cloak>
				<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
				@foreach ($advancedModuleNames as $moduleName)
					@php
						$actionsForModule = $moduleOrderedActions[$moduleName] ?? [];
						$modulePermissionIds = $moduleAllPermissionIds[$moduleName] ?? [];
					@endphp
					<div class="rounded-xl border border-gray-200/90 dark:border-gray-700 bg-white/90 dark:bg-gray-900 p-3 shadow-sm h-full flex flex-col">
						<div class="flex items-center justify-between mb-2.5">
							<div class="flex items-center gap-2">
								<h3 class="text-sm font-semibold text-gray-900 dark:text-white capitalize tracking-wide">{{ $moduleName }}</h3>
								<span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ count($actionsForModule) }}</span>
								<span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"
									x-text="moduleSelectionLabel('{{ $moduleName }}')"></span>
							</div>
							<div class="flex items-center gap-1.5">
								<button type="button"
									class="text-[11px] font-medium px-2 py-1 rounded-md border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
									@click="toggleModulePermissions('{{ $moduleName }}', true)"
									@disabled(empty($modulePermissionIds))>
									All On
								</button>
								<button type="button"
									class="text-[11px] font-medium px-2 py-1 rounded-md border border-gray-200 text-gray-600 hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
									@click="toggleModulePermissions('{{ $moduleName }}', false)"
									@disabled(empty($modulePermissionIds))>
									All Off
								</button>
							</div>
						</div>

						@if (empty($actionsForModule))
							<p class="text-xs text-gray-500 dark:text-gray-400">No active actions.</p>
						@else
							<div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 mt-auto">
								@foreach ($actionsForModule as $action)
									@php
										$actionPermissionIds = $moduleActionPermissionIds[$moduleName][$action] ?? [];
										$actionLabel = $actionLabels[$action] ?? ucfirst(str_replace(['-', '.'], ' ', $action));
									@endphp
									<div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 bg-gray-50/70 dark:bg-gray-800/30">
										<p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate pr-2" title="{{ $action }}">{{ $actionLabel }}</p>
										<label class="relative inline-flex items-center cursor-pointer shrink-0">
											<input type="checkbox" class="sr-only peer"
												@disabled(empty($actionPermissionIds))
												:checked="isActionChecked(@js($actionPermissionIds))"
												@change="toggleActionPermissions($event, @js($actionPermissionIds))">
											<div class="w-9 h-5 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-disabled:opacity-40 peer-checked:after:translate-x-full peer-checked:bg-indigo-600 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
										</label>
									</div>
								@endforeach
							</div>
						@endif
					</div>
				@endforeach
				</div>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function permissionManager(permissionIdBySlug = {}, allPermissionIds = [], bundleDefinitions = {}, moduleBundlePermissionIds = {}, moduleAllPermissionIds = {}) {
				return {
					roleId: '',
					mode: 'basic',
					permissionIdBySlug,
					allPermissionIds,
					bundleDefinitions,
					moduleBundlePermissionIds,
					moduleAllPermissionIds,
					activePermissions: [],
					selectedPermissionIds: [],
					get allChecked() {
						const selectedSet = new Set(this.selectedPermissionIds);
						return selectedSet.size === this.allPermissionIds.length && this.allPermissionIds.length > 0;
					},
					normalizeSelection() {
						this.selectedPermissionIds = [...new Set(this.selectedPermissionIds)].filter((id) => Number.isInteger(id));
					},
					addPermissionIds(permissionIds = []) {
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return;
						}

						permissionIds.forEach((id) => {
							if (Number.isInteger(id) && !this.selectedPermissionIds.includes(id)) {
								this.selectedPermissionIds.push(id);
							}
						});

						this.normalizeSelection();
					},
					removePermissionIds(permissionIds = []) {
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return;
						}

						this.selectedPermissionIds = this.selectedPermissionIds.filter((id) => !permissionIds.includes(id));
						this.normalizeSelection();
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
							this.normalizeSelection();
						} catch (error) {
							console.error("Error loading permissions:", error);
						}
					},
					checkAllPermissions() {
						this.activePermissions = Object.keys(this.permissionIdBySlug);
						this.selectedPermissionIds = [...this.allPermissionIds.filter((id) => Number.isInteger(id))];
						this.normalizeSelection();
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
					getBundlePermissionIds(moduleName, bundleKey) {
						return this.moduleBundlePermissionIds?.[moduleName]?.[bundleKey] || [];
					},
					moduleSelectionLabel(moduleName) {
						const permissionIds = this.moduleAllPermissionIds?.[moduleName] || [];
						const total = Array.isArray(permissionIds) ? permissionIds.length : 0;
						if (total === 0) {
							return '0/0';
						}

						const selected = permissionIds.filter((id) => this.selectedPermissionIds.includes(id)).length;
						return `${selected}/${total}`;
					},
					isBundleChecked(moduleName, bundleKey) {
						const permissionIds = this.getBundlePermissionIds(moduleName, bundleKey);
						if (permissionIds.length === 0) {
							return false;
						}

						return permissionIds.every((id) => this.selectedPermissionIds.includes(id));
					},
					toggleBundlePermissions(event, moduleName, bundleKey) {
						const permissionIds = this.getBundlePermissionIds(moduleName, bundleKey);
						if (permissionIds.length === 0) {
							return;
						}

						if (event.target.checked) {
							this.addPermissionIds(permissionIds);
							return;
						}

						this.removePermissionIds(permissionIds);
					},
					toggleModulePermissions(moduleName, shouldEnable) {
						const permissionIds = this.moduleAllPermissionIds?.[moduleName] || [];
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return;
						}

						if (shouldEnable) {
							this.addPermissionIds(permissionIds);
							return;
						}

						this.removePermissionIds(permissionIds);
					},
					toggleActionPermissions(event, permissionIds = []) {
						if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
							return;
						}

						if (event.target.checked) {
							this.addPermissionIds(permissionIds);
							return;
						}

						this.removePermissionIds(permissionIds);
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
