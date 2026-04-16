@extends('backend.layouts.app')

@section('title', 'Case Studies')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Case Studies" />

	<div class="space-y-6" id="case-studies-dashboard"
		data-csrf-token="{{ csrf_token() }}"
		data-reorder-route="{{ route('dashboard.case-studies.reorder') }}"
		data-status-toggle-template="{{ route('dashboard.case-studies.toggle-status', ['caseStudy' => '__ID__']) }}">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">
					{{ $stats['published'] }}
				</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">
					{{ $stats['draft'] }}
				</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Case Studies</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage case studies displayed on your marketing pages.</p>
				</div>
				<div class="flex gap-2">
					<a href="{{ route('case-studies') }}" target="_blank" rel="noopener noreferrer"
						class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
						<x-icons.eye class="h-4 w-4" />
						View Public
					</a>
					<a href="{{ route('dashboard.case-studies.create') }}"
						class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
						<x-icons.plus class="h-4 w-4" />
						New Case Study
					</a>
				</div>
			</div>

			<div class="overflow-x-auto">
				<form id="case-studies-filters-form" method="GET" action="{{ route('dashboard.case-studies.index') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 p-4 md:grid-cols-12 dark:border-gray-800">
					<div class="md:col-span-8">
						<input id="q" name="q" type="text" value="{{ $search }}"
							placeholder="Search by title, slug, or summary"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
					</div>
					<div class="md:col-span-2">
						<select id="status" name="status"
							class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
							<option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
							<option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
						</select>
					</div>
					<div class="md:col-span-2">
						<a href="{{ route('dashboard.case-studies.index') }}"
							class="block h-10 w-full rounded-lg bg-gray-900 px-3 text-center text-sm font-medium leading-10 text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
							Reset
						</a>
					</div>
				</form>

				<div id="case-studies-results">
				<table class="w-full">
					<thead class="border-b border-gray-200 dark:border-gray-800">
						<tr class="bg-gray-50 dark:bg-gray-800/50">
							<th class="w-12 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Drag</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Title</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Status</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Order</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Published</th>
							<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody id="case-studies-sortable" class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse($caseStudies as $case)
							<tr class="cursor-move" data-case-study-id="{{ $case->id }}">
								<td class="px-4 py-3 text-gray-400">
									<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
										<path d="M8 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM8 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM14 19a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
									</svg>
								</td>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $case->title }}</td>
								<td class="px-4 py-3 text-sm">
									<div class="flex items-center">
										<label class="relative inline-flex cursor-pointer items-center">
											<input type="checkbox" {{ $case->is_published ? 'checked' : '' }}
												class="peer sr-only js-case-study-status-toggle" data-case-study-id="{{ $case->id }}" />
											<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
										</label>
									</div>
								</td>
								<td class="js-sort-order-value px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $case->sort_order }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
									{{ $case->published_at ? $case->published_at->format('M d, Y') : '-' }}
								</td>
								<td class="px-4 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<a href="{{ route('dashboard.case-studies.show', $case) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-purple-600 transition hover:bg-purple-50 dark:hover:bg-purple-900/20" title="View Details">
											<x-icons.eye class="h-4 w-4" />
										</a>
										@if ($case->is_published)
											<a href="{{ route('case-studies.show', $case) }}" target="_blank" rel="noopener noreferrer"
												class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-green-600 transition hover:bg-green-50 dark:hover:bg-green-900/20" title="View Public Page">
												<x-icons.share class="h-4 w-4" />
											</a>
										@else
											<span class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-gray-400 cursor-not-allowed" title="Not Published">
												<x-icons.share class="h-4 w-4" />
											</span>
										@endif
										<a href="{{ route('dashboard.case-studies.edit', $case) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
											<x-icons.edit class="h-4 w-4" />
										</a>
										<form action="{{ route('dashboard.case-studies.destroy', $case) }}" method="POST" class="inline js-confirmable"
											data-confirm-title="Delete Case Study"
											data-confirm-message="Are you sure you want to delete this case study?"
											data-confirm-button="Delete"
											data-confirm-variant="danger">
											@csrf
											@method('DELETE')
											<button type="submit"
												class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:hover:bg-red-900/20">
												<x-icons.trash class="h-4 w-4" />
											</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
									No case studies found. <a href="{{ route('dashboard.case-studies.create') }}"
										class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
				</div>
			</div>

			@if ($caseStudies->hasPages())
				<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
					{{ $caseStudies->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
