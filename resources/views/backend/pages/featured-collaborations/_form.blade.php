<div class="grid gap-6">
	<div>
		<label for="brand_name" class="block text-sm font-medium text-gray-900 dark:text-white">Brand Name <span class="text-red-500">*</span></label>
		<input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name', $featuredCollaboration?->brand_name ?? '') }}" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
		@error('brand_name')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div>
		<label for="asset_type" class="block text-sm font-medium text-gray-900 dark:text-white">Asset Type <span class="text-red-500">*</span></label>
		<select id="asset_type" name="asset_type" required
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			<option value="">Select asset type</option>
			<option value="image" {{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'image' ? 'selected' : '' }}>Image</option>
			<option value="video" {{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'video' ? 'selected' : '' }}>Video</option>
		</select>
		@error('asset_type')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div id="imageSection" class="{{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'image' ? '' : 'hidden' }}">
		<label for="image_path" class="block text-sm font-medium text-gray-900 dark:text-white">Image {{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'image' ? '*' : '' }}</label>
		@if (!empty($featuredCollaboration?->image_path))
			<div class="mt-2 mb-2">
				<img src="{{ image_url($featuredCollaboration?->image_path) }}" alt="Current image" class="h-16 w-16 rounded-lg object-cover">
			</div>
		@endif
		<input type="file" id="image_path" name="image_path" accept="image/jpeg,image/png,image/webp"
			class="mt-2 block w-full text-sm text-gray-900 dark:text-gray-300" />
		<p id="image-file-label" class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPEG, PNG, WebP</p>
		@error('image_path')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror

		@if (!empty($featuredCollaboration?->image_path))
			<label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
				<input type="checkbox" name="delete_image" value="1" class="h-4 w-4 rounded border-gray-300">
				Remove current image
			</label>
		@endif
	</div>

	<div id="videoSection" class="{{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'video' ? '' : 'hidden' }}">
		<label for="video_path" class="block text-sm font-medium text-gray-900 dark:text-white">Video {{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'video' ? '*' : '' }}</label>
		@if (!empty($featuredCollaboration?->video_path))
			<p class="mt-2 text-xs text-gray-600 dark:text-gray-400">Current: {{ basename($featuredCollaboration?->video_path) }}</p>
		@endif
		<input type="file" id="video_path" name="video_path" accept="video/mp4,video/webm,video/quicktime"
			class="mt-2 block w-full text-sm text-gray-900 dark:text-gray-300" />
		<p id="video-file-label" class="mt-1 text-xs text-gray-500 dark:text-gray-400">MP4, WebM, MOV</p>
		@error('video_path')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror

		@if (!empty($featuredCollaboration?->video_path))
			<label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
				<input type="checkbox" name="delete_video" value="1" class="h-4 w-4 rounded border-gray-300">
				Remove current video
			</label>
		@endif
	</div>

	<div id="thumbnailSection" class="{{ old('asset_type', $featuredCollaboration?->asset_type ?? '') === 'video' ? '' : 'hidden' }}">
		<label for="thumbnail_path" class="block text-sm font-medium text-gray-900 dark:text-white">Thumbnail</label>
		@if (!empty($featuredCollaboration?->thumbnail_path))
			<div class="mt-2 mb-2">
				<img src="{{ image_url($featuredCollaboration?->thumbnail_path) }}" alt="Current thumbnail" class="h-16 w-16 rounded-lg object-cover">
			</div>
		@endif
		<input type="file" id="thumbnail_path" name="thumbnail_path" accept="image/jpeg,image/png,image/webp"
			class="mt-2 block w-full text-sm text-gray-900 dark:text-gray-300" />
		<p id="thumbnail-file-label" class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPEG, PNG, WebP</p>
		@error('thumbnail_path')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror

		@if (!empty($featuredCollaboration?->thumbnail_path))
			<label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
				<input type="checkbox" name="delete_thumbnail" value="1" class="h-4 w-4 rounded border-gray-300">
				Remove current thumbnail
			</label>
		@endif
	</div>

	<div>
		<label for="sort_order" class="block text-sm font-medium text-gray-900 dark:text-white">Sort Order</label>
		<input type="number" id="sort_order" name="sort_order" min="0" value="{{ old('sort_order', $featuredCollaboration?->sort_order ?? ($nextSortOrder ?? 0)) }}"
			class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
		@error('sort_order')
			<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
		@enderror
	</div>

	<div class="flex items-center">
		<input type="checkbox" id="is_published" name="is_published" value="1" {{ old('is_published', $featuredCollaboration?->is_published ?? false) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
		<label for="is_published" class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Published</label>
	</div>
</div>
