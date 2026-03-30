@extends('backend.layouts.app')

@section('content')
	<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-6">
		<div class=" mx-auto">
			<!-- Header -->
			<div class="mb-8">
				<a href="{{ route('dashboard.featured-collaborations.index') }}"
					class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-2 mb-4">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
					</svg>
					Back to Collaborations
				</a>
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Add Featured Collaboration</h1>
			</div>

			<!-- Form Card -->
			<div
				class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
				<form action="{{ route('dashboard.featured-collaborations.store') }}" method="POST" enctype="multipart/form-data"
					class="p-8">
					@csrf

					@error('media_upload')
						<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
							{{ $message }}
						</div>
					@enderror

					<!-- Brand Name -->
					<div class="mb-6">
						<label for="brand_name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Brand Name <span class="text-red-500">*</span>
						</label>
						<input type="text" id="brand_name" name="brand_name" value="{{ old('brand_name') }}"
							class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
							placeholder="Enter brand name" required>
						@error('brand_name')
							<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
						@enderror
					</div>

					<!-- Asset Type -->
					<div class="mb-6">
						<label for="asset_type" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Asset Type <span class="text-red-500">*</span>
						</label>
						<select id="asset_type" name="asset_type"
							class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
							required onchange="toggleAssetType()">
							<option value="">Select asset type</option>
							<option value="image" {{ old('asset_type') === 'image' ? 'selected' : '' }}>Image</option>
							<option value="video" {{ old('asset_type') === 'video' ? 'selected' : '' }}>Video</option>
						</select>
						@error('asset_type')
							<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
						@enderror
					</div>

					<!-- Image Upload (hidden by default) -->
					<div id="imageSection" class="mb-6 hidden">
						<label for="image_path" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Upload Image <span class="text-red-500">*</span>
						</label>
						<div class="mt-2">
							<div
								class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl px-6 py-8 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors duration-200"
								x-data="{ dragover: false }" @dragover="dragover = true" @dragleave="dragover = false"
								@drop="dragover = false; $refs.imageInput.click()" :class="dragover && 'bg-indigo-50 dark:bg-indigo-900/10'">
								<svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
									</path>
								</svg>
								<p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
									Drag and drop or <button type="button"
										onclick="this.closest('div').parentElement.querySelector('input[type=file]').click()"
										class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">click to select</button>
								</p>
								<p class="text-gray-500 dark:text-gray-500 text-xs">JPEG, PNG, WebP up to 5MB</p>
								<input type="file" id="image_path" name="image_path" class="hidden" accept="image/*" x-ref="imageInput"
									@change="
                                if($el.files.length > 0) {
                                    $el.closest('div').parentElement.querySelector('p').textContent = $el.files[0].name;
                                }
                            ">
							</div>
						</div>
						@error('image_path')
							<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
						@enderror
					</div>

					<!-- Video Upload (hidden by default) -->
					<div id="videoSection" class="mb-6 hidden">
						<label for="video_path" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Upload Video <span class="text-red-500">*</span>
						</label>
						<div class="mt-2">
							<div
								class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl px-6 py-8 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors duration-200"
								x-data="{ dragover: false }" @dragover="dragover = true" @dragleave="dragover = false"
								@drop="dragover = false; $refs.videoInput.click()" :class="dragover && 'bg-indigo-50 dark:bg-indigo-900/10'">
								<svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
									</path>
								</svg>
								<p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
									Drag and drop or <button type="button"
										onclick="this.closest('div').parentElement.querySelector('input[type=file]').click()"
										class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">click to select</button>
								</p>
								<p class="text-gray-500 dark:text-gray-500 text-xs">MP4, WebM, MOV up to 100MB</p>
								<input type="file" id="video_path" name="video_path" class="hidden" accept="video/*" x-ref="videoInput"
									@change="
                                if($el.files.length > 0) {
                                    $el.closest('div').parentElement.querySelector('p').textContent = $el.files[0].name;
                                }
                            ">
							</div>
						</div>
						@error('video_path')
							<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
						@enderror

						<!-- Thumbnail Upload - only shown with videos -->
						<div class="mt-6 pt-6 border-t border-gray-300 dark:border-gray-600">
							<label for="thumbnail_path" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Thumbnail (Optional)
							</label>
							<div class="mt-2">
								<div
									class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl px-6 py-8 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors duration-200"
									x-data="{ dragover: false }" @dragover="dragover = true" @dragleave="dragover = false"
									@drop="dragover = false; $refs.thumbnailInput.click()"
									:class="dragover && 'bg-indigo-50 dark:bg-indigo-900/10'">
									<svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
										</path>
									</svg>
									<p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
										Drag and drop or <button type="button"
											onclick="this.closest('div').parentElement.querySelector('input[type=file]').click()"
											class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">click to select</button>
									</p>
									<p class="text-gray-500 dark:text-gray-500 text-xs">JPEG, PNG, WebP up to 2MB</p>
									<input type="file" id="thumbnail_path" name="thumbnail_path" class="hidden" accept="image/*"
										x-ref="thumbnailInput"
										@change="
                                    if($el.files.length > 0) {
                                        $el.closest('div').parentElement.querySelector('p').textContent = $el.files[0].name;
                                    }
                                ">
								</div>
							</div>
							@error('thumbnail_path')
								<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Sort Order -->
					<div class="mb-6">
						<label for="sort_order" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Display Order
						</label>
						<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
							class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all duration-200"
							placeholder="0">
						@error('sort_order')
							<p class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</p>
						@enderror
					</div>

					<!-- Publish Status -->
					<div class="mb-8">
						<label class="flex items-center gap-3 cursor-pointer group">
							<input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
								class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-2 focus:ring-indigo-500">
							<span
								class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-200">Publish
								this collaboration</span>
						</label>
					</div>

					<!-- Submit Button -->
					<div class="flex gap-4">
						<button type="submit"
							class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
							Create Collaboration
						</button>
						<a href="{{ route('dashboard.featured-collaborations.index') }}"
							class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-semibold rounded-xl transition-all duration-200">
							Cancel
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script>
		function toggleAssetType() {
			const assetType = document.getElementById('asset_type').value;
			document.getElementById('imageSection').classList.toggle('hidden', assetType !== 'image');
			document.getElementById('videoSection').classList.toggle('hidden', assetType !== 'video');
		}

		// Initialize on page load
		document.addEventListener('DOMContentLoaded', function() {
			toggleAssetType();
		});
	</script>
@endsection
