@php
	/** @var \App\Models\CaseStudy|null $caseStudy */
	$caseStudy = $caseStudy ?? null;
	$isEditMode = $caseStudy !== null;

	$initialCoverPreview = $caseStudy?->cover_image_path ? \App\Helpers\ImageHelper::url($caseStudy->cover_image_path) : null;
@endphp

<div x-data="caseStudyUploader({
    coverPreview: @js($initialCoverPreview)
})" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
	<div class="space-y-5 lg:col-span-2">
		<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
			<div>
				<label for="title" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Title <span class="text-red-500">*</span>
				</label>
				<input id="title" name="title" type="text" value="{{ old('title', $caseStudy?->title) }}" required
					placeholder="e.g., Skincare UGC Scale Campaign"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('title')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>

			<div>
				<label for="sort_order" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
					Sort Order
				</label>
				<input id="sort_order" name="sort_order" type="number" value="{{ old('sort_order', $caseStudy?->sort_order ?? 0) }}" min="0"
					class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
				@error('sort_order')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div>
			<label for="summary" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				Summary <span class="text-red-500">*</span>
			</label>
			<textarea id="summary" name="summary" rows="4" required
			 placeholder="Brief description of the case study"
			 class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('summary', $caseStudy?->summary) }}</textarea>
			@error('summary')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div>
			<label for="external_url" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">
				External URL
			</label>
			<input id="external_url" name="external_url" type="url" value="{{ old('external_url', $caseStudy?->external_url) }}"
				placeholder="https://example.com/case-study"
				class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
			@error('external_url')
				<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>
	</div>

	<div class="space-y-5">
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

			<label for="cover_image"
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
				<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 5MB</p>
				<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="coverFileName" x-text="coverFileName"></p>
			</label>

			<input id="cover_image" x-ref="coverInput" name="cover_image" type="file"
				accept="image/jpeg,image/png,image/webp,image/avif,image/gif" class="hidden"
				@change="onCoverSelected($event)">

			<p x-show="coverClientError" x-text="coverClientError" class="mt-2 text-sm text-red-600 dark:text-red-400">
			</p>
			@error('cover_image')
				<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
			@enderror
		</div>

		<div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
			<h4 class="text-sm font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">Publishing</h4>

			<div class="mt-3 rounded-lg border border-gray-200 bg-white px-3 py-3 dark:border-gray-700 dark:bg-gray-900">
				<input type="hidden" name="is_published" value="0">
				<label for="is_published" class="flex cursor-pointer items-center justify-between gap-3">
					<span class="text-sm font-medium text-gray-700 dark:text-gray-300">Published</span>
					<input id="is_published" name="is_published" type="checkbox" value="1"
						{{ old('is_published', $caseStudy?->is_published ?? false) ? 'checked' : '' }}
						class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500 dark:border-gray-600 dark:bg-gray-800">
				</label>
				@error('is_published')
					<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>
	</div>
</div>

@once
	@push('scripts')
		<script>
			function caseStudyUploader(config) {
				return {
					coverPreview: config.coverPreview || null,
					coverFileName: '',
					coverClientError: '',

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