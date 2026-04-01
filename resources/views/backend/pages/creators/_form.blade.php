@php
	/** @var \App\Models\Creator|null $creator */
	$creator = $creator ?? null;
	$user = $creator?->user;
	$isEditMode = $creator !== null;

	$selectedCategories = old('categories', $creator?->categories?->pluck('id')->all() ?? []);
	$selectedCategoryIds = array_values(array_unique(array_map('intval', (array) $selectedCategories)));

	$initialProfilePreview = $user?->profile_image_path ? \App\Helpers\ImageHelper::url($user->profile_image_path) : null;
	$initialCoverPreview = $user?->cover_image_path ? \App\Helpers\ImageHelper::url($user->cover_image_path) : null;
@endphp

<div x-data="creatorUploader({
    profilePreview: @js($initialProfilePreview),
    coverPreview: @js($initialCoverPreview),
    categoryOptions: @js($categoryOptions),
    selectedCategoryIds: @js($selectedCategoryIds)
})" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="full_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Full Name <span class="text-red-500">*</span>
				</label>
				<input id="full_name" name="full_name" type="text" value="{{ old('full_name', $user?->name) }}" required
					placeholder="e.g., Sarah Khan"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('full_name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="display_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Display
					Name</label>
				<input id="display_name" name="display_name" type="text"
					value="{{ old('display_name', $creator?->display_name) }}" placeholder="Public profile name"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('display_name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="email" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Email <span class="text-red-500">*</span>
				</label>
				<input id="email" name="email" type="email" value="{{ old('email', $user?->email) }}" required
					placeholder="creator@example.com"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('email')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="phone" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
				<input id="phone" name="phone" type="text" value="{{ old('phone', $user?->phone) }}"
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
				<label for="title_name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
				<input id="title_name" name="title_name" type="text" value="{{ old('title_name', $creator?->title_name) }}"
					placeholder="e.g., Fashion UGC Creator"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('title_name')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="gender" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
				<select id="gender" name="gender"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					<option value="">Select gender</option>
					<option value="male" {{ old('gender', $user?->gender) === 'male' ? 'selected' : '' }}>Male
					</option>
					<option value="female" {{ old('gender', $user?->gender) === 'female' ? 'selected' : '' }}>
						Female</option>
					<option value="other" {{ old('gender', $user?->gender) === 'other' ? 'selected' : '' }}>
						Other</option>
				</select>
				@error('gender')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div class="relative" @click.away="categoryDropdownOpen = false">
			<label class="mb-1.5 block text-base font-medium text-gray-800 dark:text-gray-400">
				What categories do you want to assign? <span class="text-gray-400 font-normal text-sm">(optional)</span>
			</label>

			<div
				class="flex min-h-[46px] w-full cursor-pointer flex-wrap gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2 text-sm text-gray-800 shadow-theme-xs transition focus-within:border-pink-50 focus-within:ring-1 focus-within:ring-gray-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-within:border-gray-800"
				@click="categoryDropdownOpen = !categoryDropdownOpen">
				<template x-if="selectedCategoryIds.length === 0">
					<span class="py-1 text-sm text-gray-400">Select categories...</span>
				</template>

				<template x-for="categoryId in selectedCategoryIds" :key="`selected-category-${categoryId}`">
					<span
						class="flex items-center gap-3 rounded-md bg-purple-400 px-3 py-1 text-sm font-normal text-white dark:text-gray-800">
						<span x-text="getCategoryName(categoryId)"></span>
						<svg class="h-3 w-3 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24"
							@click.stop="removeCategory(categoryId)">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</span>
				</template>

				<div class="ml-auto flex items-center text-gray-500 dark:text-gray-400">
					<svg class="h-4 w-4 transition-transform" :class="categoryDropdownOpen ? 'rotate-180' : ''" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
					</svg>
				</div>
			</div>

			<div x-show="categoryDropdownOpen" x-cloak
				class="custom-scrollbar absolute left-0 top-full z-50 mt-2 max-h-40 w-full overflow-y-auto rounded-lg border border-purple-100 bg-white shadow-md dark:border-gray-800 dark:bg-gray-900">
				<div class="flex flex-wrap gap-3 p-4">
					<template x-for="option in categoryOptions" :key="`category-option-${option.id}`">
						<button type="button" class="rounded-md px-3 py-2 text-sm font-medium leading-tight transition-all"
							@click="toggleCategory(option.id)"
							:class="isCategorySelected(option.id) ?
							    'bg-black text-white dark:bg-purple-400 dark:text-gray-800' :
							    'bg-gray-50 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700'">
							<span x-text="option.name"></span>
						</button>
					</template>

					<template x-if="categoryOptions.length === 0">
						<p class="text-sm text-gray-500 dark:text-gray-400">No active categories available.</p>
					</template>
				</div>
			</div>

			<template x-for="categoryId in selectedCategoryIds" :key="`hidden-category-${categoryId}`">
				<input type="hidden" name="categories[]" :value="categoryId">
			</template>

			<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pick one or multiple categories with chips.</p>
			@error('categories')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
			@error('categories.*')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="bio" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
			<textarea id="bio" name="bio" rows="4" placeholder="Short creator biography"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('bio', $user?->bio) }}</textarea>
			@error('bio')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="audience" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Audience</label>
			<textarea id="audience" name="audience" rows="3" placeholder="Audience profile summary"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('audience', $creator?->audience) }}</textarea>
			@error('audience')
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
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<input id="is_active" name="is_active" type="checkbox" value="1"
					{{ old('is_active', $creator?->is_active ?? true) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-3 dark:border-amber-800/60 dark:bg-amber-900/20"
			x-data="{ featured: {{ old('is_featured', $creator?->is_featured ?? false) ? 'true' : 'false' }} }">
			<input type="hidden" name="is_featured" value="0">
			<label for="is_featured" class="flex cursor-pointer items-center justify-between gap-3">
				<div>
					<span class="text-sm font-medium text-amber-800 dark:text-amber-200">Featured Creator</span>
					<p class="text-xs text-amber-600 dark:text-amber-400 mt-0.5">Show this creator in the homepage Featured section
						and on /influencer/featured</p>
				</div>
				<input id="is_featured" name="is_featured" type="checkbox" value="1"
					{{ old('is_featured', $creator?->is_featured ?? false) ? 'checked' : '' }} x-model="featured"
					class="h-4 w-4 rounded border-amber-300 text-amber-600 focus:ring-amber-500 dark:border-amber-600 dark:bg-gray-800">
			</label>

			<div x-show="featured" x-cloak class="mt-3 border-t border-amber-200 dark:border-amber-800/60 pt-3">
				<label for="featured_priority" class="mb-1 block text-xs font-medium text-amber-800 dark:text-amber-300">Featured
					Priority <span class="font-normal opacity-70">(lower = shown first)</span></label>
				<input id="featured_priority" name="featured_priority" type="number" min="1" max="999"
					value="{{ old('featured_priority', $creator?->featured_priority) }}" placeholder="e.g. 1, 2, 3 …"
					class="h-10 w-40 rounded-lg border border-amber-300 bg-transparent px-3 text-sm text-gray-900 focus:border-amber-500 focus:outline-none dark:border-amber-700 dark:bg-gray-800 dark:text-white">
				@error('featured_priority')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			@error('is_featured')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>
</div>

@once
	@push('scripts')
		<script>
			function creatorUploader(config) {
				return {
					profilePreview: config.profilePreview || null,
					coverPreview: config.coverPreview || null,
					categoryDropdownOpen: false,
					categoryOptions: Array.isArray(config.categoryOptions) ? config.categoryOptions : [],
					selectedCategoryIds: Array.isArray(config.selectedCategoryIds) ?
						config.selectedCategoryIds
						.map((id) => Number(id))
						.filter((id) => Number.isInteger(id) && id > 0) : [],
					profileFileName: '',
					coverFileName: '',
					profileClientError: '',
					coverClientError: '',

					isCategorySelected(categoryId) {
						const normalizedId = Number(categoryId);
						return this.selectedCategoryIds.includes(normalizedId);
					},

					toggleCategory(categoryId) {
						const normalizedId = Number(categoryId);
						if (!Number.isInteger(normalizedId) || normalizedId <= 0) {
							return;
						}

						if (this.isCategorySelected(normalizedId)) {
							this.selectedCategoryIds = this.selectedCategoryIds.filter((id) => id !== normalizedId);
							return;
						}

						this.selectedCategoryIds = [...this.selectedCategoryIds, normalizedId];
					},

					removeCategory(categoryId) {
						const normalizedId = Number(categoryId);
						this.selectedCategoryIds = this.selectedCategoryIds.filter((id) => id !== normalizedId);
					},

					getCategoryName(categoryId) {
						const normalizedId = Number(categoryId);
						const option = this.categoryOptions.find((item) => Number(item.id) === normalizedId);
						return option ? option.name : `Category ${normalizedId}`;
					},

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
