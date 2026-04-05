@extends('backend.layouts.app')

@section('title', 'Conversations')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Conversations" />

	<div class="space-y-6">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Messages & Conversations</h3>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
						@if (auth()->user()->user_type === 'brand')
							Chat with creators about packages and campaigns
						@elseif(in_array(auth()->user()->user_type, ['moderator', 'admin']))
							Manage conversations and chat on behalf of creators
						@else
							Conversations are not available for your role
						@endif
					</p>
				</div>
			</div>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								@if (auth()->user()->user_type === 'brand')
									Creator
								@else
									Brand
								@endif
							</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Type</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Last Message</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Updated</th>
							<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Actions</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-800">
						@forelse ($conversations as $conversation)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
								<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
									@if (auth()->user()->user_type === 'brand')
										{{ $conversation->creator->display_name ?? $conversation->creator->user->name }}
									@else
										{{ $conversation->brandUser->name }}
									@endif
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									<span
										class="inline-flex rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
										{{ ucfirst(str_replace('_', ' ', $conversation->conversation_type)) }}
									</span>
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									@if ($conversation->messages->count() > 0)
										{{ Str::limit($conversation->messages->last()->message, 50) }}
									@else
										<span class="text-gray-400 dark:text-gray-500">No messages yet</span>
									@endif
								</td>
								<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
									{{ $conversation->updated_at->format('M d, Y H:i') }}
								</td>
								<td class="px-4 py-3 text-sm">
									<a href="{{ route('dashboard.conversations.show', $conversation) }}"
										class="inline-flex items-center gap-1 rounded bg-blue-600 px-3 py-1 text-xs font-medium text-white transition hover:bg-blue-700">
										<svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
											</path>
										</svg>
										View
									</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
									No conversations yet.
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>

			@if ($conversations->count() > 0)
				<div class="border-t border-gray-200 px-4 py-3 dark:border-gray-800">
					{{ $conversations->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
