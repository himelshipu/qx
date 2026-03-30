@extends('backend.layouts.app')

@section('content')
	<div class="min-h-screen  p-6">
		<div class="max-w-7xl mx-auto">
			<!-- Header -->
			<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
				<div>
					<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Featured Collaborations</h1>
					<p class="text-gray-600 dark:text-gray-400">Manage brand collaborations displayed on your homepage</p>
				</div>
				<a href="{{ route('dashboard.featured-collaborations.create') }}"
					class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
					</svg>
					Add Collaboration
				</a>
			</div>

		

			<!-- Table -->
			<div
				class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
				@if ($collaborations->count() > 0)
					<div class="overflow-x-auto">
						<table class="w-full">
							<thead>
								<tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">#</th>
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Brand Name</th>
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Type</th>
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Preview</th>
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Order</th>
									<th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">Status</th>
									<th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">Actions</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@foreach ($collaborations as $collaboration)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
										<td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
											{{ ($collaborations->firstItem() ?? 1) + $loop->index }}
										</td>
										<td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $collaboration->brand_name }}</td>
										<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
											<span
												class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-medium capitalize">
												{{ $collaboration->asset_type }}
											</span>
										</td>
										<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
											@if ($collaboration->asset_type === 'image')
												<img src="{{ image_url($collaboration->image_path) }}" alt="{{ $collaboration->brand_name }}"
													class="h-12 w-12 object-cover rounded-lg">
											@elseif($collaboration->asset_type === 'video' && $collaboration->video_path)
												@if ($collaboration->thumbnail_path)
													<img src="{{ image_url($collaboration->thumbnail_path) }}" alt="{{ $collaboration->brand_name }} thumbnail"
														class="h-12 w-12 object-cover rounded-lg">
												@else
													<div class="relative h-12 w-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
														<svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
															<path
																d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13.118V6.882a1 1 0 00-1.447-.894l-2 1z">
															</path>
														</svg>
													</div>
												@endif
											@else
												<span class="text-gray-400">-</span>
											@endif
										</td>
										<td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $collaboration->sort_order }}
										</td>
										<td class="px-6 py-4 text-sm">
											<form action="{{ route('dashboard.featured-collaborations.toggle-publish', $collaboration) }}" method="POST"
												class="inline">
												@csrf
												@method('PATCH')
												<div class="flex items-center">
													<label class="relative inline-flex cursor-pointer items-center">
														<input type="checkbox" {{ $collaboration->is_published ? 'checked' : '' }} onchange="this.form.submit()"
															class="peer sr-only" />
														<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
													</label>
												</div>
											</form>
										</td>
										<td class="px-6 py-4 text-center">
											<div class="flex items-center justify-center gap-2">
												<a href="{{ route('dashboard.featured-collaborations.edit', $collaboration) }}"
													class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-lg transition-colors duration-150"
													title="Edit">
													<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
														</path>
													</svg>
												</a>
												<form action="{{ route('dashboard.featured-collaborations.destroy', $collaboration) }}" method="POST"
													class="inline" onsubmit="return confirm('Are you sure you want to delete this collaboration?');">
													@csrf
													@method('DELETE')
													<button type="submit"
														class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-150"
														title="Delete">
														<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
															</path>
														</svg>
													</button>
												</form>
											</div>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-900">
						{{ $collaborations->links() }}
					</div>
				@else
					<div class="text-center py-12">
						<svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"></path>
						</svg>
						<p class="text-gray-600 dark:text-gray-400 mb-4">No featured collaborations yet</p>
						<a href="{{ route('dashboard.featured-collaborations.create') }}"
							class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
							Create the first one
						</a>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
