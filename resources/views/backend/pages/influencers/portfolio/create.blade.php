@extends('backend.layouts.app')

@section('title', "Add Portfolio - {$influencer->display_name}")

@section('content')
	<x-backend.shell.breadcrumb :items="[
	    ['label' => 'Influencers', 'route' => 'dashboard.influencers.index'],
	    ['label' => $influencer->display_name, 'route' => 'dashboard.influencers.view', 'params' => $influencer->id],
	    ['label' => 'Portfolio', 'route' => 'dashboard.influencers.portfolio.index', 'params' => $influencer->id],
	    ['label' => 'Add Item'],
	]" />

	<div class="w-full">
		<div class="space-y-6">
			<div>
				<h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add Portfolio Item</h1>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add photo or video to {{ $influencer->display_name }}'s portfolio</p>
			</div>

			<!-- Upload Mode Tabs -->
			<div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
				<button type="button" id="single-mode-btn" 
					class="single-mode-tab px-4 py-2 text-sm font-medium border-b-2 transition {{ session('active_tab') == 'bulk' ? 'border-transparent text-gray-600 dark:text-gray-400' : 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' }}">
					Single Upload
				</button>
				<button type="button" id="bulk-mode-btn"
					class="bulk-mode-tab px-4 py-2 text-sm font-medium border-b-2 transition {{ session('active_tab') == 'bulk' ? 'border-purple-600 text-purple-600 dark:text-purple-400 dark:border-purple-400' : 'border-transparent text-gray-600 dark:text-gray-400' }}">
					Bulk Upload
				</button>
			</div>

			<!-- Single Upload Form -->
			<form id="single-form" action="{{ route('dashboard.influencers.portfolio.store', $influencer) }}" method="POST" enctype="multipart/form-data"
				class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 {{ session('active_tab') == 'bulk' ? 'hidden' : '' }}">
				@csrf
				<input type="hidden" name="upload_mode" value="single">

				<div class="p-5 space-y-4">
					<!-- File Upload -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload File *</label>
						<div id="single-dropzone"
							class="relative rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50 transition @error('file') border-red-500 @enderror">
							<input type="file" name="file" accept="image/*,video/*" class="absolute inset-0 h-full w-full cursor-pointer opacity-0" id="single-file-input">
							<div class="text-center">
								<svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								<p class="text-sm text-gray-600 dark:text-gray-400">Click or drag to upload</p>
								<p class="text-xs text-gray-500">JPG, PNG, WebP, MP4, WebM, MOV (Max 10MB)</p>
							</div>
						</div>
						@error('file')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<!-- File Preview -->
					<div id="single-preview-container" class="hidden">
						<div class="relative rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 max-w-[200px]">
							<div id="single-preview-content"></div>
							<button type="button" id="single-remove-file" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow">
								<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>
					</div>

					<div class="grid grid-cols-2 gap-4">
						<!-- Title -->
						<div>
							<label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (Optional)</label>
							<input type="text" id="title" name="title" value="{{ old('title') }}" maxlength="255"
								class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('title')
								<p class="mt-1 text-xs text-red-600">{{ $message }}</p>
							@enderror
						</div>

						<!-- Sort Order -->
						<div>
							<label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Display Order</label>
							<input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
								class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							@error('sort_order')
								<p class="mt-1 text-xs text-red-600">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Description -->
					<div>
						<label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
						<textarea id="description" name="description" rows="3" maxlength="1000" placeholder="Describe this media item..."
							class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-purple-500 focus:ring-purple-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white resize-none">{{ old('description') }}</textarea>
						@error('description')
							<p class="mt-1 text-xs text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<!-- Active Status -->
					<div>
						<label class="flex items-center gap-2">
							<input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
								class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Show this item in portfolio</span>
						</label>
					</div>
				</div>

				<div class="flex gap-3 border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
					<a href="{{ route('dashboard.influencers.portfolio.index', $influencer) }}"
						class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
						Cancel
					</a>
					<button type="submit" id="single-submit-btn"
						class="flex-1 rounded-lg bg-purple-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-purple-700 dark:hover:bg-purple-600">
						Add Item
					</button>
				</div>
			</form>

			<!-- Bulk Upload Form -->
			<form id="bulk-form" action="{{ route('dashboard.influencers.portfolio.store', $influencer) }}" method="POST" enctype="multipart/form-data"
				class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 {{ session('active_tab') == 'bulk' ? '' : 'hidden' }}">
				@csrf
				<input type="hidden" name="upload_mode" value="bulk">

				<div class="p-5 space-y-4">
					<!-- File Upload -->
					<div>
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Files *</label>
						<div id="bulk-dropzone"
							class="relative rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50 transition @error('portfolio_files') border-red-500 @enderror">
							<input type="file" name="portfolio_files[]" accept="image/*,video/*" multiple class="absolute inset-0 h-full w-full cursor-pointer opacity-0" id="bulk-file-input">
							<div class="text-center">
								<svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
								</svg>
								<p class="text-sm text-gray-600 dark:text-gray-400">Click or drag to upload</p>
								<p class="text-xs text-gray-500">JPG, PNG, WebP, MP4, WebM, MOV (Max 10 files, 10MB each)</p>
							</div>
						</div>
						@error('portfolio_files')
							<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
						@enderror
					</div>

					<!-- Preview Grid -->
					<div id="bulk-preview-grid" class="hidden">
						<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selected Files</label>
						<div id="bulk-preview-list" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3"></div>
					</div>

					<!-- Active Status -->
					<div>
						<label class="flex items-center gap-2">
							<input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
								class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
							<span class="text-sm text-gray-700 dark:text-gray-300">Show these items in portfolio</span>
						</label>
					</div>
				</div>

				<div class="flex gap-3 border-t border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/50">
					<a href="{{ route('dashboard.influencers.portfolio.index', $influencer) }}"
						class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
						Cancel
					</a>
					<button type="submit" id="bulk-submit-btn"
						class="flex-1 rounded-lg bg-purple-600 px-4 py-2 text-center text-sm font-medium text-white hover:bg-purple-700 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-purple-700 dark:hover:bg-purple-600">
						Add Items
					</button>
				</div>
			</form>
		</div>
	</div>

	@push('scripts')
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Tab Management
			const singleModeBtn = document.getElementById('single-mode-btn');
			const bulkModeBtn = document.getElementById('bulk-mode-btn');
			const singleForm = document.getElementById('single-form');
			const bulkForm = document.getElementById('bulk-form');

			function updateTabStyles(activeTab) {
				if (activeTab === 'single') {
					singleModeBtn.classList.add('border-purple-600', 'text-purple-600', 'dark:text-purple-400', 'dark:border-purple-400');
					singleModeBtn.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
					bulkModeBtn.classList.remove('border-purple-600', 'text-purple-600', 'dark:text-purple-400', 'dark:border-purple-400');
					bulkModeBtn.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
				} else {
					bulkModeBtn.classList.add('border-purple-600', 'text-purple-600', 'dark:text-purple-400', 'dark:border-purple-400');
					bulkModeBtn.classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
					singleModeBtn.classList.remove('border-purple-600', 'text-purple-600', 'dark:text-purple-400', 'dark:border-purple-400');
					singleModeBtn.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
				}
			}

			singleModeBtn.addEventListener('click', () => {
				singleForm.classList.remove('hidden');
				bulkForm.classList.add('hidden');
				updateTabStyles('single');
			});

			bulkModeBtn.addEventListener('click', () => {
				bulkForm.classList.remove('hidden');
				singleForm.classList.add('hidden');
				updateTabStyles('bulk');
			});

			// ===== SINGLE UPLOAD =====
			const singleFileInput = document.getElementById('single-file-input');
			const singlePreviewContainer = document.getElementById('single-preview-container');
			const singlePreviewContent = document.getElementById('single-preview-content');
			const singleRemoveBtn = document.getElementById('single-remove-file');
			const singleSubmitBtn = document.getElementById('single-submit-btn');
			
			let singleHasError = false;

			function updateSingleSubmitBtn() {
				singleSubmitBtn.disabled = singleHasError || !singleFileInput.files.length;
			}

			singleFileInput.addEventListener('change', (e) => {
				const file = e.target.files[0];
				if (!file) {
					singlePreviewContainer.classList.add('hidden');
					singleHasError = false;
					updateSingleSubmitBtn();
					return;
				}
				
				const fileSizeMB = file.size / (1024 * 1024);
				
				if (fileSizeMB > 10) {
					singleHasError = true;
					showSingleError(`File size (${fileSizeMB.toFixed(1)}MB) exceeds 10MB`);
					singleFileInput.value = '';
					updateSingleSubmitBtn();
					return;
				}

				if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
					singleHasError = true;
					showSingleError('File must be an image or video');
					singleFileInput.value = '';
					updateSingleSubmitBtn();
					return;
				}

				singleHasError = false;
				const reader = new FileReader();
				reader.onload = (event) => {
					singlePreviewContainer.classList.remove('hidden');
					
					if (file.type.startsWith('image/')) {
						singlePreviewContent.innerHTML = `<img src="${event.target.result}" class="w-full h-auto">`;
					} else {
						singlePreviewContent.innerHTML = `<video controls class="w-full h-auto"><source src="${event.target.result}" type="${file.type}"></video>`;
					}
					updateSingleSubmitBtn();
				};
				reader.readAsDataURL(file);
			});

			singleRemoveBtn.addEventListener('click', () => {
				singleFileInput.value = '';
				singlePreviewContainer.classList.add('hidden');
				singlePreviewContent.innerHTML = '';
				singleHasError = false;
				updateSingleSubmitBtn();
			});

			function showSingleError(message) {
				singlePreviewContainer.classList.remove('hidden');
				singlePreviewContent.innerHTML = `
					<div class="p-3 text-center">
						<p class="text-sm text-red-600">${message}</p>
						<p class="text-xs text-gray-500 mt-1">Please select another file</p>
					</div>
				`;
			}

			// ===== BULK UPLOAD =====
			const bulkFileInput = document.getElementById('bulk-file-input');
			const bulkPreviewGrid = document.getElementById('bulk-preview-grid');
			const bulkPreviewList = document.getElementById('bulk-preview-list');
			const bulkSubmitBtn = document.getElementById('bulk-submit-btn');
			let currentBulkFiles = [];
			let bulkHasError = false;

			function updateBulkSubmitBtn() {
				const hasValidFiles = currentBulkFiles.length > 0 && !bulkHasError;
				bulkSubmitBtn.disabled = !hasValidFiles;
			}

			bulkFileInput.addEventListener('change', function(e) {
				const files = Array.from(e.target.files);
				
				if (files.length > 10) {
					showBulkNotification('Maximum 10 files allowed', 'error');
					this.value = '';
					currentBulkFiles = [];
					bulkPreviewList.innerHTML = '';
					bulkPreviewGrid.classList.add('hidden');
					bulkHasError = true;
					updateBulkSubmitBtn();
					return;
				}

				currentBulkFiles = files;
				bulkHasError = false;
				displayBulkPreviews(files);
				updateBulkSubmitBtn();
			});

			function displayBulkPreviews(files) {
				bulkPreviewList.innerHTML = '';
				let hasInvalid = false;
				
				files.forEach((file, index) => {
					const fileSizeMB = file.size / (1024 * 1024);
					const isValid = fileSizeMB <= 10 && (file.type.startsWith('image/') || file.type.startsWith('video/'));
					
					if (!isValid) hasInvalid = true;
					
					const reader = new FileReader();
					reader.onload = (event) => {
						const div = document.createElement('div');
						div.className = `relative aspect-square rounded-lg overflow-hidden border ${isValid ? 'border-gray-200 dark:border-gray-700' : 'border-red-500'} bg-gray-100 dark:bg-gray-800`;
						
						let content = '';
						if (file.type.startsWith('image/')) {
							content = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
						} else if (file.type.startsWith('video/')) {
							content = `<div class="relative w-full h-full bg-black flex items-center justify-center"><svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></div>`;
						} else {
							content = `<div class="w-full h-full flex items-center justify-center text-xs text-gray-500">Invalid</div>`;
						}
						
						div.innerHTML = `
							${content}
							${!isValid ? `<div class="absolute inset-0 bg-red-500/80 flex items-center justify-center"><p class="text-white text-xs font-bold text-center px-1">${fileSizeMB > 10 ? '>10MB' : 'Invalid type'}</p></div>` : ''}
							<div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-[10px] truncate px-1 py-0.5">${file.name.substring(0, 15)}</div>
							<button type="button" class="remove-bulk-item absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 shadow" data-index="${index}">
								<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
							</button>
						`;
						bulkPreviewList.appendChild(div);
						
						div.querySelector('.remove-bulk-item').addEventListener('click', () => removeBulkFile(index));
					};
					reader.readAsDataURL(file);
				});
				
				bulkHasError = hasInvalid;
				bulkPreviewGrid.classList.remove('hidden');
				updateBulkSubmitBtn();
			}

			function removeBulkFile(indexToRemove) {
				currentBulkFiles = currentBulkFiles.filter((_, index) => index !== indexToRemove);
				
				const dataTransfer = new DataTransfer();
				currentBulkFiles.forEach(file => dataTransfer.items.add(file));
				bulkFileInput.files = dataTransfer.files;
				
				if (currentBulkFiles.length === 0) {
					bulkPreviewGrid.classList.add('hidden');
					bulkPreviewList.innerHTML = '';
					bulkHasError = false;
				} else {
					displayBulkPreviews(currentBulkFiles);
				}
				updateBulkSubmitBtn();
			}

			function showBulkNotification(message, type) {
				// You can integrate with your toast notification system here
				alert(message);
			}

			// Drag and drop handlers
			function setupDragAndDrop(dropzoneId, fileInputId) {
				const dropzone = document.getElementById(dropzoneId);
				const fileInput = document.getElementById(fileInputId);
				
				['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
					dropzone.addEventListener(eventName, (e) => {
						e.preventDefault();
						e.stopPropagation();
					});
				});

				['dragenter', 'dragover'].forEach(eventName => {
					dropzone.addEventListener(eventName, () => {
						dropzone.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
					});
				});

				['dragleave', 'drop'].forEach(eventName => {
					dropzone.addEventListener(eventName, () => {
						dropzone.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-900/20');
					});
				});

				dropzone.addEventListener('drop', (e) => {
					const files = e.dataTransfer.files;
					fileInput.files = files;
					fileInput.dispatchEvent(new Event('change'));
				});
			}

			setupDragAndDrop('single-dropzone', 'single-file-input');
			setupDragAndDrop('bulk-dropzone', 'bulk-file-input');
			
			// Initial button states
			updateSingleSubmitBtn();
			updateBulkSubmitBtn();
		});
	</script>
	@endpush
@endsection