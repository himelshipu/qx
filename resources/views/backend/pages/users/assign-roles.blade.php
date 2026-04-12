@extends('backend.layouts.app')

@section('title', 'Assign Roles to Users')

@section('content')
	<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700"
		x-data="userRoleManager()" @toast:error="handleToastError">
		<!-- Header -->
		<div class="p-6 border-b border-gray-200 dark:border-gray-700">
			<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
				<div>
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white">Assign Roles to Users</h2>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage roles and permissions for admin and moderator users
					</p>
				</div>
				<a href="{{ route('dashboard.users.index') }}"
					class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
					Back to Users
				</a>
			</div>
		</div>

		<form @submit.prevent="assignRoles()" class="p-6 space-y-6">
			@csrf

			<!-- User Selection Card -->
			<div class="space-y-3">
				<label for="user_id" class="block text-sm font-semibold text-gray-900 dark:text-white">
					Select User <span class="text-red-500">*</span>
				</label>
				<div class="relative">
					<select id="user_id" x-model="userId" @change="loadUserRoles()"
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent dark:bg-gray-900 dark:text-white text-sm font-medium transition-all duration-200 appearance-none cursor-pointer bg-white dark:bg-gray-800"
						required>
						<option value="">-- Select a user --</option>
						@foreach ($users as $user)
							<option value="{{ $user->id }}">
								{{ $user->name }}
								<span class="text-gray-500">({{ $user->email }})</span>
								- <span class="badge badge-{{ strtolower($user->user_type) }}">{{ ucfirst($user->user_type) }}</span>
							</option>
						@endforeach
					</select>
					<svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
					</svg>
				</div>
				<p class="text-xs text-gray-500 dark:text-gray-400">Choose a moderator or admin user to manage their roles</p>
			</div>

			<!-- Roles Selection Card -->
			<div class="space-y-4">
				<div class="flex items-center justify-between">
					<label class="block text-sm font-semibold text-gray-900 dark:text-white">
						Available Roles
					</label>
					<span
						class="px-3 py-1 text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 rounded-full"
						x-text="'Selected: ' + selectedRoles.length">
						Selected: 0
					</span>
				</div>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
					@foreach ($roles as $role)
						<label
							class="group relative flex items-center p-3 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-purple-400 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-all duration-200"
							:class="{ 'border-purple-500 dark:border-purple-600 bg-purple-50 dark:bg-purple-900/20': selectedRoles.includes(
							        '{{ $role->id }}') }">
							<input type="checkbox" name="roles[]" value="{{ $role->id }}"
								:checked="selectedRoles.includes('{{ $role->id }}')" @change="updateSelectedRoles()"
								class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 cursor-pointer">
							<span class="ml-3 flex-1">
								<span
									class="block text-sm font-medium text-gray-900 dark:text-white group-hover:text-purple-700 dark:group-hover:text-purple-300">
									{{ $role->name }}
								</span>
								@if ($role->description)
									<span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
										{{ $role->description }}
									</span>
								@endif
							</span>
						</label>
					@endforeach
				</div>
			</div>

			<!-- Current Roles Display -->
			<div v-if="userId && currentUserRoles.length > 0"
				class="p-4 bg-gradient-to-r from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg">
				<p class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-3">
					<svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
						<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
						<path fill-rule="evenodd"
							d="M4 5a2 2 0 012-2 1 1 0 00-1-1H3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1h-1a1 1 0 00-1 1 2 2 0 01-2-2H6a2 2 0 01-2 2zm0 5a1 1 0 100 2h6a1 1 0 100-2H4z"
							clip-rule="evenodd" />
					</svg>
					Currently Assigned Roles
				</p>
				<div class="flex flex-wrap gap-2">
					<template x-for="roleName in currentUserRoles" :key="roleName">
						<span
							class="px-3 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-300 bg-white dark:bg-blue-900/50 rounded-full border border-blue-200 dark:border-blue-700 shadow-sm">
							<span x-text="roleName"></span>
						</span>
					</template>
				</div>
			</div>

			<!-- Empty State -->
			<div v-if="userId && currentUserRoles.length === 0"
				class="p-4 bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg text-center">
				<svg class="w-8 h-8 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
				</svg>
				<p class="text-sm text-gray-500 dark:text-gray-400">No roles currently assigned to this user</p>
			</div>

			<!-- Action Buttons -->
			<div class="flex gap-3 pt-2">
				<button type="submit" :disabled="!userId || loading"
					:class="{ 'opacity-50 cursor-not-allowed': !userId || loading }"
					class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
					<svg v-if="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					<svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
					</svg>
					<span x-text="loading ? 'Assigning...' : 'Assign Roles'"></span>
				</button>
				<button type="button" @click="clearSelection()"
					class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-900 dark:text-white text-sm font-semibold rounded-lg transition-all duration-200">
					Clear Selection
				</button>
			</div>
		</form>
	</div>

	@push('scripts')
		<script>
			function userRoleManager() {
				return {
					userId: '',
					selectedRoles: [],
					currentUserRoles: [],
					loading: false,

					async loadUserRoles() {
						if (!this.userId) {
							this.selectedRoles = [];
							this.currentUserRoles = [];
							return;
						}

						this.loading = true;

						try {
							const response = await fetch(`/dashboard/users/${this.userId}/roles`, {
								headers: {
									'Accept': 'application/json'
								}
							});
							const data = await response.json();

							if (data.success) {
								// Convert role IDs to strings for comparison
								this.selectedRoles = data.roleIds.map(id => id.toString());

								// Get role names for display
								this.currentUserRoles = [];
								document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
									if (this.selectedRoles.includes(checkbox.value)) {
										const label = document.querySelector(
											`label[for="role_${checkbox.value}"] span:first-child`);
										if (label) {
											const roleName = label.textContent.trim();
											if (roleName && !this.currentUserRoles.includes(roleName)) {
												this.currentUserRoles.push(roleName);
											}
										}
									}
								});
							}
						} catch (error) {
							window.toast.error('Error loading user roles');
							console.error('Error:', error);
						} finally {
							this.loading = false;
						}
					},

					updateSelectedRoles() {
						const checkboxes = document.querySelectorAll('input[name="roles[]"]:checked');
						this.selectedRoles = Array.from(checkboxes).map(cb => cb.value);
					},

					async assignRoles() {
						if (!this.userId) {
							window.toast.error('Please select a user');
							return;
						}

						this.loading = true;

						try {
							const formData = new FormData();
							formData.append('user_id', this.userId);
							formData.append('_token', document.querySelector('input[name="_token"]').value);

							this.selectedRoles.forEach(roleId => {
								formData.append('roles[]', roleId);
							});

							const response = await fetch('/dashboard/users/roles/assign', {
								method: 'POST',
								headers: {
									'X-Requested-With': 'XMLHttpRequest',
									'Accept': 'application/json'
								},
								body: formData
							});

							const data = await response.json();

							if (data.success) {
								window.toast.success(data.message);

								// Update current user roles display
								if (data.user) {
									this.currentUserRoles = data.user.roles;
								}
							} else {
								window.toast.error(data.message || 'Failed to assign roles');
								if (data.errors) {
									console.error('Validation errors:', data.errors);
								}
							}
						} catch (error) {
							window.toast.error('Error assigning roles');
							console.error('Error:', error);
						} finally {
							this.loading = false;
						}
					},

					clearSelection() {
						this.selectedRoles = [];
						document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
							checkbox.checked = false;
						});
					}
				}
			}
		</script>
	@endpush

@endsection
