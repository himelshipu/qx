@extends('backend.layouts.app')

@section('title', 'FAQ Sections')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="FAQ Sections" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Sections</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $sections->total() }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Active</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">
					{{ $sections->filter(fn($s) => $s->is_active)->count() }}
				</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Inactive</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">
					{{ $sections->filter(fn($s) => !$s->is_active)->count() }}
				</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">FAQ Sections</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage FAQ sections and their items.</p>
				</div>
				<a href="{{ route('dashboard.faqs.sections.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Section
				</a>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="border-b border-gray-200 dark:border-gray-800">
						<tr class="bg-gray-50 dark:bg-gray-800/50">
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Title</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Code</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Audience</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Items</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Status</th>
							<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse($sections as $section)
							<tr>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $section->section_title }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $section->section_code }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $section->audience_type }}</td>
								<td class="px-4 py-3 text-sm">
									<a href="{{ route('dashboard.faqs.items.index', $section) }}"
										class="text-blue-600 hover:text-blue-700 font-medium">
										{{ $section->items()->count() }} Items
									</a>
								</td>
								<td class="px-4 py-3 text-sm">
									<form action="{{ route('dashboard.faqs.sections.toggle-status', $section) }}" method="POST" class="inline">
										@csrf
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $section->is_active ? 'checked' : '' }} onchange="this.form.submit()"
													class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
											</label>
										</div>
									</form>
								</td>
								<td class="px-4 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<a href="{{ route('dashboard.faqs.items.create', $section) }}"
											class="inline-flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-emerald-600 transition hover:bg-emerald-50 dark:hover:bg-emerald-900/20"
											title="Add FAQ item">
											<x-icons.plus class="h-4 w-4" />
											<span>Add FAQ</span>
										</a>
										<a href="{{ route('dashboard.faqs.sections.edit', $section) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
											<x-icons.edit class="h-4 w-4" />
										</a>
										<form action="{{ route('dashboard.faqs.sections.destroy', $section) }}" method="POST" class="inline"
											onsubmit="return confirm('This will delete all FAQ items in this section. Are you sure?')">
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
									No sections found. <a href="{{ route('dashboard.faqs.sections.create') }}"
										class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($sections->hasPages())
				<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
					{{ $sections->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
