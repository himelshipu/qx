@php
	/** @var \App\Models\Brand|null $brand */
	$brand = $brand ?? null;
	$user = $brand?->user;
	$isEditMode = $brand !== null;

	$resolvePreviewUrl = static function (?string $path): ?string {
	    if (!$path) {
	        return null;
	    }

	    $isExternal = str_starts_with($path, 'http://') || str_starts_with($path, 'https://');

	    return $isExternal ? $path : asset($path);
	};

	$initialProfilePreview = $resolvePreviewUrl($user?->profile_image_path);
	$initialCoverPreview = $resolvePreviewUrl($user?->cover_image_path);
@endphp

<div x-data="brandUploader({
    profilePreview: @js($initialProfilePreview),
    coverPreview: @js($initialCoverPreview)
})" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="contact_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Contact Name <span class="text-red-500">*</span>
				</label>
				<input id="contact_name" name="contact_name" type="text" value="{{ old('contact_name', $user?->name) }}" required
					placeholder="e.g., John Doe"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('contact_name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="brand_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Brand Name <span class="text-red-500">*</span>
				</label>
				<input id="brand_name" name="brand_name" type="text" value="{{ old('brand_name', $brand?->brand_name) }}"
					required placeholder="e.g., Nova Labs"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('brand_name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Email <span class="text-red-500">*</span>
				</label>
				<input id="email" name="email" type="email" value="{{ old('email', $user?->email ?? $brand?->email) }}"
					required placeholder="brand@example.com"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('email')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
				<input id="phone" name="phone" type="text" value="{{ old('phone', $brand?->phone ?? $user?->phone) }}"
					placeholder="+1 202 555 0100"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('phone')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="password" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					{{ $isEditMode ? 'New Password' : 'Password' }}
					@if (!$isEditMode)
						<span class="text-red-500">*</span>
					@endif
				</label>
				<input id="password" name="password" type="password" {{ $isEditMode ? '' : 'required' }}
					placeholder="{{ $isEditMode ? 'Leave blank to keep current password' : 'Enter secure password' }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('password')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="password_confirmation" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Confirm Password
					@if (!$isEditMode)
						<span class="text-red-500">*</span>
					@endif
				</label>
				<input id="password_confirmation" name="password_confirmation" type="password" {{ $isEditMode ? '' : 'required' }}
					placeholder="Confirm password"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			</div>

			<div>
				<label for="industry" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Industry</label>
				<input id="industry" name="industry" type="text" value="{{ old('industry', $brand?->industry) }}"
					placeholder="e.g., Fashion"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('industry')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="website" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
				<input id="website" name="website" type="url" value="{{ old('website', $brand?->website) }}"
					placeholder="https://example.com"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('website')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div>
			<label for="description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
			<textarea id="description" name="description" rows="4"
			 placeholder="Briefly describe the brand and its positioning"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description', $brand?->description) }}</textarea>
			@error('description')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="location" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Address Line</label>
				<input id="location" name="location" type="text" value="{{ old('location', $user?->address_line) }}"
					placeholder="Street address"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('location')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="city" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
				<input id="city" name="city" type="text" value="{{ old('city', $user?->city) }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('city')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="country" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
				<input id="country" name="country" type="text" value="{{ old('country', $user?->country) }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('country')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="postal_code" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Postal
					Code</label>
				<input id="postal_code" name="postal_code" type="text"
					value="{{ old('postal_code', $user?->postal_code) }}"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('postal_code')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
				<div class="mb-3 flex items-start justify-between gap-3">
					<div>
						<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Profile Image</p>
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
							<x-icons.image class="h-6 w-6 text-gray-400" />
						</div>
					</template>
					<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Profile Image</p>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 5MB</p>
					<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="profileFileName" x-text="profileFileName"></p>
				</label>

				<input id="profile_image_file" x-ref="profileInput" name="profile_image_file" type="file"
					accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden"
					@change="onProfileSelected($event)">

				<p x-show="profileClientError" x-text="profileClientError" class="mt-2 text-sm text-red-600 dark:text-red-400">
				</p>
				@error('profile_image_file')
					<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
				<div class="mb-3 flex items-start justify-between gap-3">
					<div>
						<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Cover Image</p>
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
						<img :src="coverPreview" alt="Cover preview" class="mb-3 h-20 w-full max-w-[220px] rounded object-cover">
					</template>
					<template x-if="!coverPreview">
						<div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
							<x-icons.photo class="h-6 w-6 text-gray-400" />
						</div>
					</template>
					<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Cover Image</p>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 6MB</p>
					<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="coverFileName" x-text="coverFileName"></p>
				</label>

				<input id="cover_image_file" x-ref="coverInput" name="cover_image_file" type="file"
					accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden" @change="onCoverSelected($event)">

				<p x-show="coverClientError" x-text="coverClientError" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
				@error('cover_image_file')
					<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>
	</div>

	<div class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
		<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Account Flags</h4>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_verified" value="0">
			<label for="is_verified" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Verified Brand</span>
				<input id="is_verified" name="is_verified" type="checkbox" value="1"
					{{ old('is_verified', $brand?->is_verified ?? false) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_verified')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<input id="is_active" name="is_active" type="checkbox" value="1"
					{{ old('is_active', $brand?->is_active ?? true) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>
</div>

@once
	@push('scripts')
		<script>
			function brandUploader(config) {
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
						this.profilePreview = config.profilePreview || null;
						if (this.$refs.profileInput) {
							this.$refs.profileInput.value = '';
						}
					},

					clearCover() {
						this.coverClientError = '';
						this.coverFileName = '';
						this.coverPreview = config.coverPreview || null;
						if (this.$refs.coverInput) {
							this.$refs.coverInput.value = '';
						}
					}
				};
			}
		</script>
	@endpush
@endonce
