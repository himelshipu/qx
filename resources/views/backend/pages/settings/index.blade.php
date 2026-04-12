@extends('backend.layouts.app')

@section('title', 'Settings - Footer Pages')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Settings" />

	<div class="space-y-6">
		<!-- Header Card -->
		<div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-blue-50 shadow-sm dark:border-gray-800 dark:from-indigo-900/20 dark:to-blue-900/20 p-6 md:p-8">
			<div class="flex items-start justify-between">
				<div>
					<h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
						<i class="fas fa-cog text-indigo-600 dark:text-indigo-400"></i>
						Footer Pages Management
					</h2>
					<p class="mt-2 text-gray-600 dark:text-gray-400">Organize and select which static pages appear in your website footer</p>
				</div>
				<div class="hidden sm:block">
					<div class="inline-flex rounded-lg bg-indigo-100 dark:bg-indigo-900/40 px-4 py-2">
						<span class="text-sm font-medium text-indigo-900 dark:text-indigo-300">
							<i class="fas fa-list mr-2"></i>{{ $pages->count() }} Pages
						</span>
					</div>
				</div>
			</div>
		</div>

		<!-- Main Settings Card -->
		<div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<!-- Tab Style Header -->
			<div class="border-b border-gray-200 px-6 py-6 dark:border-gray-800">
				<div class="flex items-center gap-3">
					<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
						<i class="fas fa-list-check text-indigo-600 dark:text-indigo-400"></i>
					</div>
					<div>
						<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select & Order Pages</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400">Drag to reorder, check to display in footer</p>
					</div>
				</div>
			</div>

			<form action="{{ route('dashboard.settings.update') }}" method="POST" class="p-6">
				@csrf

				@if($pages->count() > 0)
					<!-- Info Box -->
					<div class="mb-6 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800/30 dark:bg-amber-900/20">
						<i class="fas fa-lightbulb mt-0.5 text-amber-600 dark:text-amber-400"></i>
						<div>
							<p class="text-sm font-medium text-amber-900 dark:text-amber-300">
								Drag pages to reorder them in the footer. Only checked pages will be displayed.
							</p>
						</div>
					</div>

					<!-- Sortable List -->
					<div id="sortable-list" class="space-y-2 mb-6">
						@foreach($pages as $page)
							<div class="sortable-item group rounded-lg border border-gray-200 bg-gray-50 p-4 transition hover:border-indigo-300 hover:bg-indigo-50/50 dark:border-gray-700 dark:bg-gray-800/50 dark:hover:border-indigo-600/50 dark:hover:bg-indigo-900/20 cursor-grab active:cursor-grabbing"
								data-id="{{ $page->id }}">
								<div class="flex items-center gap-4">
									<!-- Drag Handle -->
									<div class="flex h-8 w-8 items-center justify-center rounded text-gray-400 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition">
										<i class="fas fa-grip-vertical text-sm"></i>
									</div>

									<!-- Checkbox -->
									<input
										type="checkbox"
										id="page_{{ $page->id }}"
										name="footer_pages[]"
										value="{{ $page->id }}"
										{{ in_array($page->id, $footerPages ?? []) ? 'checked' : '' }}
										class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 cursor-pointer"
									>

									<!-- Content -->
									<div class="flex-1 min-w-0">
										<label for="page_{{ $page->id }}" class="block cursor-pointer">
											<span class="font-semibold text-gray-900 dark:text-white">{{ $page->title }}</span>
											<p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
												{{ $page->content_summary ?? 'No description' }}
											</p>
										</label>
									</div>

									<!-- Status Badge -->
									<div class="flex items-center gap-2 flex-shrink-0">
										@if($page->is_active)
											<span class="inline-flex items-center gap-1 rounded-full bg-green-100/80 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-300">
												<i class="fas fa-check-circle text-xs"></i>
												Published
											</span>
										@else
											<span class="inline-flex items-center gap-1 rounded-full bg-gray-200/80 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700/50 dark:text-gray-400">
												<i class="fas fa-circle text-xs"></i>
												Draft
											</span>
										@endif
									</div>

									<!-- Edit Link -->
									<a href="{{ route('dashboard.static-pages.edit', $page->id) }}"
										class="ml-2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-200 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
										<i class="fas fa-pencil text-sm"></i>
									</a>
								</div>
							</div>
						@endforeach
					</div>

					<!-- Hidden input for order -->
					<input type="hidden" id="footer_pages_order" name="footer_pages_order" value="">

				@else
					<!-- Empty State -->
					<div class="text-center py-12">
						<div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800 mb-4">
							<i class="fas fa-file-alt text-2xl text-gray-400"></i>
						</div>
						<p class="text-gray-600 dark:text-gray-400 text-base font-medium mb-2">No static pages found</p>
						<p class="text-gray-500 dark:text-gray-500 text-sm mb-6">Create your first static page to get started</p>
						<a href="{{ route('dashboard.static-pages.create') }}" 
							class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
							<i class="fas fa-plus"></i>
							Create Page
						</a>
					</div>
				@endif

				<!-- Actions -->
				@if($pages->count() > 0)
					<div class="border-t border-gray-200 pt-6 dark:border-gray-800 flex justify-end gap-3">
						<a href="{{ route('dashboard.index') }}" 
							class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
							<i class="fas fa-arrow-left"></i>
							Cancel
						</a>
						<button type="submit" 
							class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-2 text-sm font-medium text-white transition hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
							<i class="fas fa-check-circle"></i>
							Save Settings
						</button>
					</div>
				@endif
			</form>
		</div>
	</div>

	<!-- Sortable.js Library -->
	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const sortableList = document.getElementById('sortable-list');
			
			if (sortableList) {
				Sortable.create(sortableList, {
					animation: 150,
					ghostClass: 'opacity-50 bg-indigo-100 dark:bg-indigo-900/30',
					dragClass: 'dragging',
					handle: '.fa-grip-vertical',
					forceFallback: false,
					onEnd: function(evt) {
						// Optional: Auto-save order on drag
						updateOrder();
					}
				});
			}

			// Update hidden input with current order
			function updateOrder() {
				const items = document.querySelectorAll('.sortable-item');
				const order = Array.from(items).map(item => item.dataset.id);
				document.getElementById('footer_pages_order').value = JSON.stringify(order);
			}

			// Update order before form submission
			document.querySelector('form').addEventListener('submit', function() {
				updateOrder();
			});
		});
	</script>
@endsection
