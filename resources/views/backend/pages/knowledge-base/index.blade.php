@extends('backend.layouts.app')

@section('title', 'Knowledge Base')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Knowledge Base" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Articles</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $articles->total() }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">
					{{ $articles->filter(fn($a) => $a->is_published)->count() }}
				</p>
			</div>
			<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/40 dark:bg-yellow-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-yellow-700 dark:text-yellow-300">Drafts</p>
				<p class="mt-2 text-2xl font-semibold text-yellow-700 dark:text-yellow-200">
					{{ $articles->filter(fn($a) => !$a->is_published)->count() }}
				</p>
			</div>
			<div class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900/40 dark:bg-indigo-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-indigo-700 dark:text-indigo-300">Featured</p>
				<p class="mt-2 text-2xl font-semibold text-indigo-700 dark:text-indigo-200">
					{{ $articles->filter(fn($a) => $a->is_featured)->count() }}
				</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Knowledge Base Articles</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage support guides shown to users.</p>
				</div>
				<a href="{{ route('dashboard.knowledge-base.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Article
				</a>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="border-b border-gray-200 dark:border-gray-800">
						<tr class="bg-gray-50 dark:bg-gray-800/50">
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Title</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Badge</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Read Time</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Status</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Published</th>
							<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse($articles as $article)
							<tr>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
									{{ $article->title }}
									@if ($article->is_featured)
										<span class="ml-2 rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">Featured</span>
									@endif
								</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $article->badge ?? '-' }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $article->read_time_minutes }} min</td>
								<td class="px-4 py-3 text-sm">
									<form action="{{ route('dashboard.knowledge-base.toggle-status', $article) }}" method="POST" class="inline">
										@csrf
										<div class="flex items-center">
											<label class="relative inline-flex cursor-pointer items-center">
												<input type="checkbox" {{ $article->is_published ? 'checked' : '' }} onchange="this.form.submit()" class="peer sr-only" />
												<div class="h-6 w-11 rounded-full bg-gray-200 transition-colors duration-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-400 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-green-300 dark:bg-gray-700 dark:peer-focus:ring-green-800"></div>
											</label>
										</div>
									</form>
								</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $article->published_at?->format('M d, Y') ?? '-' }}</td>
								<td class="px-4 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<a href="{{ route('dashboard.knowledge-base.edit', $article) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
											<x-icons.edit class="h-4 w-4" />
										</a>
										<form action="{{ route('dashboard.knowledge-base.destroy', $article) }}" method="POST" class="inline js-confirmable"
											data-confirm-title="Delete Article"
											data-confirm-message="Are you sure you want to delete this article?"
											data-confirm-button="Delete"
											data-confirm-variant="danger">
											@csrf
											@method('DELETE')
											<button type="submit" class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-red-600 transition hover:bg-red-50 dark:hover:bg-red-900/20">
												<x-icons.trash class="h-4 w-4" />
											</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
									No articles found. <a href="{{ route('dashboard.knowledge-base.create') }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($articles->hasPages())
				<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
					{{ $articles->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
