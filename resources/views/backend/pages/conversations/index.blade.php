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
							Chat with influencers about packages and campaigns
						@elseif(in_array(auth()->user()->user_type, ['moderator', 'admin']))
							Manage conversations and chat on behalf of influencers
						@else
							Conversations are not available for your role
						@endif
					</p>
				</div>
			</div>
		</div>

		<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="overflow-x-auto">
				<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
					<thead class="bg-gray-50 dark:bg-gray-800/50">
						<tr>
							<th scope="col"
								class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Conversation
							</th>
							<th scope="col"
								class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Participants
							</th>
							<th scope="col"
								class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Assigned Moderator
							</th>
							<th scope="col"
								class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
								Last Update
							</th>
							<th scope="col" class="relative px-6 py-3">
								<span class="sr-only">Actions</span>
							</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
						@forelse ($conversations as $conversation)
							<tr>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="flex items-center">
										<div class="ml-4">
											<div class="text-sm font-medium text-gray-900 dark:text-white">
												{{ $conversation->title ?? 'Conversation #' . $conversation->id }}
											</div>
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ ucfirst(str_replace('_', ' ', $conversation->conversation_type)) }}
											</div>
										</div>
									</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									<div class="text-sm text-gray-900 dark:text-white">
										Brand: {{ $conversation->brandUser->name ?? 'N/A' }}
									</div>
									<div class="text-sm text-gray-500 dark:text-gray-400">
										Influencer: {{ $conversation->influencer->user->name ?? 'N/A' }}
									</div>
								</td>
								<td class="px-6 py-4 whitespace-nowrap">
									@if ($conversation->moderatorAssignment && $conversation->moderatorAssignment->moderator)
										<span
											class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
											{{ $conversation->moderatorAssignment->moderator->name }}
										</span>
									@else
										<span
											class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
											Unassigned
										</span>
									@endif
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
									{{ $conversation->updated_at->diffForHumans() }}
								</td>
								<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
									<a href="{{ route('dashboard.conversations.show', $conversation) }}"
										class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">View</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="5" class="px-6 py-12 text-center">
									<div class="text-center">
										<svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
											aria-hidden="true">
											<path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
										</svg>
										<h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No conversations</h3>
										<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new conversation.</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
			@if ($conversations->hasPages())
				<div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
					{{ $conversations->links() }}
				</div>
			@endif
		</div>
	</div>
@endsection
