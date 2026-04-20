@extends('backend.layouts.app')

@section('title', "Add Portfolio - {$influencer->display_name}")

@section('content')
	<x-backend.shell.breadcrumb :items="[
	    ['label' => 'Influencers', 'route' => 'dashboard.influencers.index'],
	    ['label' => $influencer->display_name, 'route' => 'dashboard.influencers.view', 'params' => $influencer->id],
	    ['label' => 'Portfolio', 'route' => 'dashboard.influencers.portfolio.index', 'params' => $influencer->id],
	    ['label' => 'Add Item'],
	]" />

	<div class="max-w-2xl">
		<div class="space-y-6">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Portfolio Item</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add a new photo or video to {{ $influencer->display_name }}'s
					portfolio</p>
			</div>

			<form action="{{ route('dashboard.influencers.portfolio.store', $influencer) }}" method="POST" enctype="multipart/form-data"
				class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
				@csrf

				<div class="space-y-6 p-5">
					<!-- Media Type -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Media Type</label>
						<div class="space-y-3">
							<label class="flex items-center">
								<input type="radio" name="media_type" value="image" checked @error('media_type') :error @enderror
									class="h-4 w-4 text-purple-600 dark:text-purple-500">
								<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Image (JPG, PNG, WebP)</span>
							</label>
							<label class="flex items-center">
								<input type="radio" name="media_type" value="video" @error('media_type') :error @enderror
									class="h-4 w-4 text-purple-600 dark:text-purple-500">
								<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Video (MP4, WebM, MOV)</span>
							</label>
						</div>
						@error('media_type')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- File Upload -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Upload File</label>
						<div
							class="relative rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800/50">
							<input type="file" name="file" accept="image/*,video/*" required
								class="absolute inset-0 h-full w-full cursor-pointer opacity-0" id="file-input">
							<div class="text-center pointer-events-none">
								<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								<p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Click to upload or drag and drop</p>
								<p class="text-xs text-gray-500 dark:text-gray-500 mt-1">PNG, JPG, WebP, MP4, WebM or MOV</p>
							</div>
						</div>
						@error('file')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- File Preview -->
					<div id="preview-container" class="hidden">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</label>
						<div class="rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 max-w-xs">
							<img id="image-preview" class="hidden w-full h-auto" />
							<video id="video-preview" class="hidden w-full h-auto" controls></video>
						</div>
					</div>

					<!-- Title -->
					<div>
						<label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title
							(Optional)</label>
						<input type="text" id="title" name="title" value="{{ old('title') }}"
							placeholder="Give this item a title" maxlength="255"
							class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400">
						@error('title')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Description -->
					<div>
						<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description
							(Optional)</label>
						<textarea id="description" name="description" rows="4" maxlength="1000" placeholder="Describe this media item..."
						 class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 resize-none">{{ old('description') }}</textarea>
						@error('description')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Sort Order -->
					<div>
						<label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Display
							Order</label>
						<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
							class="block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder-gray-500 focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
						@error('sort_order')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Active Status -->
					<div>
						<label class="flex items-center">
							<input type="checkbox" name="is_active" value="1" checked
								class="h-4 w-4 rounded border-gray-300 text-purple-600 shadow-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800">
							<span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Show this item in portfolio</span>
						</label>
					</div>
				</div>

				<div class="flex gap-3 border-t border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-800/50">
					<a href="{{ route('dashboard.influencers.portfolio.index', $influencer) }}"
						class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
						Cancel
					</a>
					<button type="submit"
						class="flex-1 rounded-lg bg-purple-600 px-4 py-2 text-center font-medium text-white transition hover:bg-purple-700 dark:bg-purple-700 dark:hover:bg-purple-600">
						Add Item
					</button>
				</div>
			</form>
		</div>
	</div>

	@push('scripts')
		<script>
			const fileInput = document.getElementById('file-input');
			const previewContainer = document.getElementById('preview-container');
			const imagePreview = document.getElementById('image-preview');
			const videoPreview = document.getElementById('video-preview');

			fileInput.addEventListener('change', (e) => {
				const file = e.target.files[0];
				if (!file) return;

				const reader = new FileReader();
				reader.onload = (event) => {
					previewContainer.classList.remove('hidden');

					if (file.type.startsWith('image/')) {
						imagePreview.classList.remove('hidden');
						videoPreview.classList.add('hidden');
						imagePreview.src = event.target.result;
					} else if (file.type.startsWith('video/')) {
						videoPreview.classList.remove('hidden');
						imagePreview.classList.add('hidden');
						videoPreview.src = event.target.result;
					}
				};
				reader.readAsDataURL(file);
			});

			// Drag and drop
			const dropZone = fileInput.parentElement;
			['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
				dropZone.addEventListener(eventName, preventDefaults, false);
			});

			function preventDefaults(e) {
				e.preventDefault();
				e.stopPropagation();
			}

			['dragenter', 'dragover'].forEach(eventName => {
				dropZone.addEventListener(eventName, () => {
					dropZone.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
				});
			});

			['dragleave', 'drop'].forEach(eventName => {
				dropZone.addEventListener(eventName, () => {
					dropZone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
				});
			});

			dropZone.addEventListener('drop', (e) => {
				const dt = e.dataTransfer;
				const files = dt.files;
				fileInput.files = files;
				fileInput.dispatchEvent(new Event('change'));
			});
		</script>
	@endpush
@endsection
