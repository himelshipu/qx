@extends('backend.layouts.app')

@section('title', 'Case Studies')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Case Studies" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $caseStudies->total() }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">
					{{ $caseStudies->filter(fn($c) => $c->is_published)->count() }}
				</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Draft</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">
					{{ $caseStudies->filter(fn($c) => !$c->is_published)->count() }}
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
				<a href="{{ route('dashboard.case-studies.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Case Study
				</a>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="border-b border-gray-200 dark:border-gray-800">
						<tr class="bg-gray-50 dark:bg-gray-800/50">
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
					<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse($caseStudies as $case)
							<tr>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $case->title }}</td>
								<td class="px-4 py-3 text-sm">
									<span
										class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-medium {{ $case->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
										<span class="h-1.5 w-1.5 rounded-full {{ $case->is_published ? 'bg-green-500' : 'bg-gray-500' }}"></span>
										{{ $case->is_published ? 'Published' : 'Draft' }}
									</span>
								</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $case->sort_order }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
									{{ $case->published_at ? $case->published_at->format('M d, Y') : '-' }}
								</td>
								<td class="px-4 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<form action="{{ route('dashboard.case-studies.toggle-status', $case) }}" method="POST" class="inline">
											@csrf
											<button type="submit"
												class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium transition hover:bg-gray-100 dark:hover:bg-gray-800">
												@if ($case->is_published)
													<x-icons.eye class="h-4 w-4 text-green-600" />
												@else
													<x-icons.eye-off class="h-4 w-4 text-gray-600" />
												@endif
											</button>
										</form>
										<a href="{{ route('dashboard.case-studies.edit', $case) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
											<x-icons.pencil class="h-4 w-4" />
										</a>
										<form action="{{ route('dashboard.case-studies.destroy', $case) }}" method="POST" class="inline"
											onsubmit="return confirm('Are you sure?')">
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
								<td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
									No case studies found. <a href="{{ route('dashboard.case-studies.create') }}"
										class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($caseStudies->hasPages())
				<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
					{{ $caseStudies->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
