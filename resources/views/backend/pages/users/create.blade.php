@extends('backend.layouts.app')

@section('title', 'Create User')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Create User" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div
			class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New User</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new user to the system with complete profile
					information.</p>
			</div>
			<a href="{{ route('dashboard.users.index') }}"
				class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
				Back to Users
			</a>
		</div>

		<form action="{{ route('dashboard.users.store') }}" method="POST" enctype="multipart/form-data" novalidate
			class="space-y-6 p-5">
			@csrf

			<div x-data="userUploader({})" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
				<div class="space-y-5 lg:col-span-2">
					<!-- Basic Information Row -->
					<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
						<div>
							<label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
								Full Name <span class="text-red-500">*</span>
							</label>
							<input id="name" name="name" type="text" value="{{ old('name') }}" required
								placeholder="e.g., John Doe"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('name')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
								Email Address <span class="text-red-500">*</span>
							</label>
							<input id="email" name="email" type="email" value="{{ old('email') }}" required
								placeholder="user@example.com"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('email')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Password Row -->
					<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
						<div>
							<label for="password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
								Password <span class="text-red-500">*</span>
							</label>
							<input id="password" name="password" type="password" required placeholder="Enter secure password"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('password')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
								Confirm Password <span class="text-red-500">*</span>
							</label>
							<input id="password_confirmation" name="password_confirmation" type="password" required
								placeholder="Confirm password"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('password_confirmation')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Contact Information Row -->
					<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
						<div>
							<label for="phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
							<input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('phone')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="address_line" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
							<input id="address_line" name="address_line" type="text" value="{{ old('address_line') }}"
								placeholder="Street address"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('address_line')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Profile and Cover Images Row -->
					<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
						<!-- Profile Image -->
						<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
							<div class="mb-3 flex items-start justify-between gap-3">
								<div>
									<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Profile Picture</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF</p>
								</div>
								<button type="button" @click="clearProfile()"
									class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
									Clear
								</button>
							</div>

							<label for="profile_image_file"
								class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
								<template x-if="profilePreview">
									<img :src="profilePreview" alt="Profile preview" class="mb-3 h-20 w-20 rounded-full object-cover">
								</template>
								<template x-if="!profilePreview">
									<div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
										<svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
										</svg>
									</div>
								</template>
								<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Profile Image</p>
								<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 5MB</p>
								<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="profileFileName" x-text="profileFileName"></p>
							</label>

							<input id="profile_image_file" x-ref="profileInput" name="profile_image_path" type="file"
								accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden"
								@change="onProfileSelected($event)">

							<p x-show="profileClientError" x-text="profileClientError" class="mt-2 text-sm text-red-600 dark:text-red-400">
							</p>
							@error('profile_image_path')
								<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Cover Image -->
						<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
							<div class="mb-3 flex items-start justify-between gap-3">
								<div>
									<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Cover Photo</p>
									<p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF</p>
								</div>
								<button type="button" @click="clearCover()"
									class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
									Clear
								</button>
							</div>

							<label for="cover_image_file"
								class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
								<template x-if="coverPreview">
									<img :src="coverPreview" alt="Cover preview" class="mb-3 h-20 max-w-[220px] rounded object-cover">
								</template>
								<template x-if="!coverPreview">
									<div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
										<svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
										</svg>
									</div>
								</template>
								<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Cover Image</p>
								<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 6MB</p>
								<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="coverFileName" x-text="coverFileName"></p>
							</label>

							<input id="cover_image_file" x-ref="coverInput" name="cover_image_path" type="file"
								accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden"
								@change="onCoverSelected($event)">

							<p x-show="coverClientError" x-text="coverClientError" class="mt-2 text-sm text-red-600 dark:text-red-400">
							</p>
							@error('cover_image_path')
								<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Role Selection -->
					<div>
						<label for="role_id" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Assign Role <span class="text-red-500">*</span>
						</label>
						<select id="role_id" name="role_id" required
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="">-- Select a role --</option>
							@foreach ($roles as $role)
								<option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
									{{ $role->name }}
								</option>
							@endforeach
						</select>
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The user will be created with this role and user_type</p>
						@error('role_id')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<!-- Sidebar -->
				<div
					class="space-y-5 rounded-xl border border-purple-200 bg-gradient-to-br from-purple-50 to-indigo-50 p-4 dark:border-purple-900/30 dark:from-purple-900/20 dark:to-indigo-900/20">
					<h4 class="text-sm font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-300">📋 Information</h4>

					<div class="space-y-3 text-sm">
						<div class="rounded-lg bg-white/50 p-3 dark:bg-gray-800/50">
							<p class="font-semibold text-blue-700 dark:text-blue-300">🔐 Password</p>
							<p class="mt-1 text-gray-700 dark:text-gray-300">Must be at least 8 characters and confirmed.</p>
						</div>
						<div class="rounded-lg bg-white/50 p-3 dark:bg-gray-800/50">
							<p class="font-semibold text-indigo-700 dark:text-indigo-300">👤 Role</p>
							<p class="mt-1 text-gray-700 dark:text-gray-300">The user will be assigned the selected role and their user_type
								will be set accordingly.</p>
						</div>
						<div class="rounded-lg bg-white/50 p-3 dark:bg-gray-800/50">
							<p class="font-semibold text-pink-700 dark:text-pink-300">🖼️ Profile Media</p>
							<p class="mt-1 text-gray-700 dark:text-gray-300">Upload images for profile picture and cover photo (optional).
							</p>
						</div>
						<div class="rounded-lg bg-white/50 p-3 dark:bg-gray-800/50">
							<p class="font-semibold text-green-700 dark:text-green-300">📱 Contact</p>
							<p class="mt-1 text-gray-700 dark:text-gray-300">Phone number and address are optional fields.</p>
						</div>
					</div>
				</div>
			</div>

			<!-- Form Actions -->
			<div
				class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
				<a href="{{ route('dashboard.users.index') }}"
					class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Cancel
				</a>
				<button type="submit"
					class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Create User
				</button>
			</div>
		</form>
	</div>

	@once
		@push('scripts')
			<script>
				function userUploader(config) {
					return {
						profilePreview: config.profilePreview || null,
						coverPreview: config.coverPreview || null,
						profileFileName: '',
						coverFileName: '',
						profileClientError: '',
						coverClientError: '',

						onProfileSelected(event) {
							this.profileClientError = '';
							const file = event.target.files[0];

							if (!file) {
								return;
							}

							const extension = (file.name.split('.').pop() || '').toLowerCase();
							const allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

							if (!allowed.includes(extension)) {
								this.profileClientError = 'Profile image must be JPG, PNG, WEBP, AVIF, or GIF.';
								this.clearProfile();
								return;
							}

							this.profileFileName = file.name;
							this.profilePreview = URL.createObjectURL(file);
						},

						onCoverSelected(event) {
							this.coverClientError = '';
							const file = event.target.files[0];

							if (!file) {
								return;
							}

							const extension = (file.name.split('.').pop() || '').toLowerCase();
							const allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];

							if (!allowed.includes(extension)) {
								this.coverClientError = 'Cover image must be JPG, PNG, WEBP, AVIF, or GIF.';
								this.clearCover();
								return;
							}

							this.coverFileName = file.name;
							this.coverPreview = URL.createObjectURL(file);
						},

						clearProfile() {
							this.profileClientError = '';
							this.profileFileName = '';
							this.profilePreview = null;
							if (this.$refs.profileInput) {
								this.$refs.profileInput.value = '';
							}
						},

						clearCover() {
							this.coverClientError = '';
							this.coverFileName = '';
							this.coverPreview = null;
							if (this.$refs.coverInput) {
								this.$refs.coverInput.value = '';
							}
						}
					};
				}
			</script>
		@endpush
	@endonce
@endsection
