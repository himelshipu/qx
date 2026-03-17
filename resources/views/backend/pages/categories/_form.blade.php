@php
	/** @var \App\Models\Category|null $category */
	$category = $category ?? null;
	$resolvedSortOrder = old('sort_order', $category?->sort_order ?? ($nextSortOrder ?? 1));

	$resolvePreviewUrl = static function (?string $path): ?string {
	    if (!$path) {
	        return null;
	    }

	    $isExternal = str_starts_with($path, 'http://') || str_starts_with($path, 'https://');

	    return $isExternal ? $path : asset($path);
	};

	$initialIconPreview = $resolvePreviewUrl($category?->icon_path);
	$initialImagePreview = $resolvePreviewUrl($category?->image_path);
@endphp

<div x-data="categoryUploader({
    iconPreview: @js($initialIconPreview),
    imagePreview: @js($initialImagePreview)
})" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div>
			<label for="name" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Category Name <span class="text-red-500">*</span>
			</label>
			<input id="name" name="name" type="text" value="{{ old('name', $category?->name) }}"
				placeholder="e.g., Beauty, Tech, Lifestyle" required
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('name')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="slug" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
			<input id="slug" name="slug" type="text" value="{{ old('slug', $category?->slug) }}"
				placeholder="auto-generated-if-empty"
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If left empty, it will be generated from the name.</p>
			@error('slug')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="description" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
			<textarea id="description" name="description" rows="4" placeholder="Short description for this category"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description', $category?->description) }}</textarea>
			@error('description')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
				<div class="mb-3 flex items-start justify-between gap-3">
					<div>
						<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Icon Upload</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">SVG only</p>
					</div>
					<button type="button" @click="clearIcon()"
						class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
						Clear
					</button>
				</div>

				<label for="icon_file"
					class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
					<template x-if="iconPreview">
						<img :src="iconPreview" alt="Icon preview" class="mb-3 h-16 w-16 rounded bg-gray-100 p-2 dark:bg-gray-800">
					</template>
					<template x-if="!iconPreview">
						<div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
							<x-icons.image class="h-6 w-6 text-gray-400" />
						</div>
					</template>
					<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose SVG Icon</p>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">.svg up to 1MB</p>
					<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="iconFileName" x-text="iconFileName"></p>
				</label>

				<input id="icon_file" x-ref="iconInput" name="icon_file" type="file" accept=".svg,image/svg+xml" class="hidden"
					@change="onIconSelected($event)">

				<p x-show="iconClientError" x-text="iconClientError" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
				@error('icon_file')
					<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
				<div class="mb-3 flex items-start justify-between gap-3">
					<div>
						<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Category Image</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF</p>
					</div>
					<button type="button" @click="clearImage()"
						class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
						Clear
					</button>
				</div>

				<label for="image_file"
					class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
					<template x-if="imagePreview">
						<img :src="imagePreview" alt="Image preview" class="mb-3 h-20 w-full max-w-[180px] rounded object-cover">
					</template>
					<template x-if="!imagePreview">
						<div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
							<x-icons.photo class="h-6 w-6 text-gray-400" />
						</div>
					</template>
					<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Category Image</p>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 5MB</p>
					<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="imageFileName" x-text="imageFileName"></p>
				</label>

				<input id="image_file" x-ref="imageInput" name="image_file" type="file"
					accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden" @change="onImageSelected($event)">

				<p x-show="imageClientError" x-text="imageClientError" class="mt-2 text-sm text-red-600 dark:text-red-400"></p>
				@error('image_file')
					<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>
	</div>

	<div class="space-y-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
		<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Display and Ordering</h4>

		<div>
			<label for="sort_order" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
			<input id="sort_order" name="sort_order" type="number" min="0" value="{{ $resolvedSortOrder }}"
				class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
			@error('sort_order')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
			<input type="hidden" name="is_active" value="0">
			<label for="is_active" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Status</span>
				<input id="is_active" name="is_active" type="checkbox" value="1"
					{{ old('is_active', $category?->is_active ?? true) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_active')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900" x-data="{ isFeatured: {{ old('is_featured', $category?->is_featured ?? false) ? 'true' : 'false' }} }">
			<input type="hidden" name="is_featured" value="0">
			<label for="is_featured" class="flex cursor-pointer items-center justify-between gap-3">
				<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured Category</span>
				<input id="is_featured" name="is_featured" type="checkbox" value="1"
					x-model="isFeatured"
					{{ old('is_featured', $category?->is_featured) ? 'checked' : '' }}
					class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
			</label>
			@error('is_featured')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror

			<div x-show="isFeatured" class="mt-3 border-t border-gray-200 pt-3 dark:border-gray-700">
				<label for="featured_order" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Featured Order <span class="text-red-500">*</span></label>
				<input id="featured_order" name="featured_order" type="number" min="1" max="4" 
					value="{{ old('featured_order', $category?->featured_order) }}"
					placeholder="1-4"
					class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white">
				<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Position 1-4 in featured section (1 = first)</p>
				@error('featured_order')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>
	</div>
</div>

@once
	@push('scripts')
		<script>
			function categoryUploader(config) {
				return {
					iconPreview: config.iconPreview || null,
					imagePreview: config.imagePreview || null,
					iconFileName: '',
					imageFileName: '',
					iconClientError: '',
					imageClientError: '',

					onIconSelected(event) {
						this.iconClientError = '';
						const file = event.target.files[0];

						if (!file) {
							return;
						}

						const extension = (file.name.split('.').pop() || '').toLowerCase();
						const isSvg = extension === 'svg' || file.type === 'image/svg+xml';

						if (!isSvg) {
							this.iconClientError = 'Icon must be an SVG file.';
							this.clearIcon();
							return;
						}

						this.iconFileName = file.name;
						this.iconPreview = URL.createObjectURL(file);
					},

					onImageSelected(event) {
						this.imageClientError = '';
						const file = event.target.files[0];

						if (!file) {
							return;
						}

						const extension = (file.name.split('.').pop() || '').toLowerCase();
						const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'];
						const isSvg = extension === 'svg' || file.type === 'image/svg+xml';

						if (isSvg || !allowedExtensions.includes(extension)) {
							this.imageClientError = 'Image must be JPG, PNG, WEBP, AVIF, or GIF.';
							this.clearImage();
							return;
						}

						this.imageFileName = file.name;
						this.imagePreview = URL.createObjectURL(file);
					},

					clearIcon() {
						this.iconClientError = '';
						this.iconFileName = '';
						this.iconPreview = config.iconPreview || null;
						if (this.$refs.iconInput) {
							this.$refs.iconInput.value = '';
						}
					},

					clearImage() {
						this.imageClientError = '';
						this.imageFileName = '';
						this.imagePreview = config.imagePreview || null;
						if (this.$refs.imageInput) {
							this.$refs.imageInput.value = '';
						}
					}
				};
			}
		</script>
	@endpush
@endonce
