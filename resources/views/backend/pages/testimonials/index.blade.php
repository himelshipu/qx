@extends('backend.layouts.app')

@section('title', 'Testimonials')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Testimonials" />

	<div class="space-y-6">
		<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
				<p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
				<p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $testimonials->total() }}</p>
			</div>
			<div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-green-700 dark:text-green-300">Published</p>
				<p class="mt-2 text-2xl font-semibold text-green-700 dark:text-green-200">
					{{ $testimonials->filter(fn($t) => $t->is_published)->count() }}
				</p>
			</div>
			<div class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/40 dark:bg-red-900/20">
				<p class="text-xs font-semibold uppercase tracking-wider text-red-700 dark:text-red-300">Unpublished</p>
				<p class="mt-2 text-2xl font-semibold text-red-700 dark:text-red-200">
					{{ $testimonials->filter(fn($t) => !$t->is_published)->count() }}
				</p>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Testimonials</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage customer testimonials displayed on your website.</p>
				</div>
				<a href="{{ route('dashboard.testimonials.create') }}"
					class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					<x-icons.plus class="h-4 w-4" />
					New Testimonial
				</a>
			</div>

			<div class="overflow-x-auto">
				<table class="w-full">
					<thead class="border-b border-gray-200 dark:border-gray-800">
						<tr class="bg-gray-50 dark:bg-gray-800/50">
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Author</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Company</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Rating</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Status</th>
							<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
						@forelse($testimonials as $testimonial)
							<tr>
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $testimonial->author_name }}</td>
								<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $testimonial->company_name ?? '-' }}</td>
								<td class="px-4 py-3 text-sm">
									@if ($testimonial->rating)
										<div class="flex items-center gap-1">
											@for ($i = 0; $i < $testimonial->rating; $i++)
												<span class="text-yellow-400">★</span>
											@endfor
											@for ($i = $testimonial->rating; $i < 5; $i++)
												<span class="text-gray-300">★</span>
											@endfor
										</div>
									@else
										<span class="text-gray-500">-</span>
									@endif
								</td>
								<td class="px-4 py-3 text-sm">
									<span
										class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-medium {{ $testimonial->is_published ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
										<span
											class="h-1.5 w-1.5 rounded-full {{ $testimonial->is_published ? 'bg-green-500' : 'bg-gray-500' }}"></span>
										{{ $testimonial->is_published ? 'Published' : 'Unpublished' }}
									</span>
								</td>
								<td class="px-4 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<form action="{{ route('dashboard.testimonials.toggle-status', $testimonial) }}" method="POST" class="inline">
											@csrf
											<button type="submit"
												class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium transition hover:bg-gray-100 dark:hover:bg-gray-800">
												@if ($testimonial->is_published)
													<x-icons.eye class="h-4 w-4 text-green-600" />
												@else
													<x-icons.eye-off class="h-4 w-4 text-gray-600" />
												@endif
											</button>
										</form>
										<a href="{{ route('dashboard.testimonials.edit', $testimonial) }}"
											class="flex items-center gap-1 rounded px-2 py-1 text-xs font-medium text-blue-600 transition hover:bg-blue-50 dark:hover:bg-blue-900/20">
											<x-icons.pencil class="h-4 w-4" />
										</a>
										<form action="{{ route('dashboard.testimonials.destroy', $testimonial) }}" method="POST" class="inline"
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
									No testimonials found. <a href="{{ route('dashboard.testimonials.create') }}"
										class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($testimonials->hasPages())
				<div class="border-t border-gray-200 px-4 py-4 dark:border-gray-800">
					{{ $testimonials->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
