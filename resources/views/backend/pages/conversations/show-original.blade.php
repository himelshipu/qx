@extends('backend.layouts.app')

@section('title', 'Conversation')

@section('content')
	<x-backend.shell.breadcrumb :links="[['label' => 'Conversations', 'url' => route('dashboard.conversations.index')]]" pageTitle="Chat" />

	<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
		<!-- Chat Area -->
		<div class="lg:col-span-2">
			<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 flex flex-col"
				style="height: 600px;">
				<!-- Header -->
				<div class="border-b border-gray-200 p-4 dark:border-gray-800">
					<div class="flex items-center justify-between">
						<div>
							<h3 class="font-semibold text-gray-900 dark:text-white">
								@if (auth()->user()->user_type === 'brand')
									{{ $conversation->creator->display_name ?? $conversation->creator->user->name }}
								@else
									{{ $conversation->brandUser->name }}
								@endif
							</h3>
							<p class="text-xs text-gray-500 dark:text-gray-400">
								@if (auth()->user()->user_type === 'brand')
									Package Order
								@else
									Moderating for {{ $conversation->creator->display_name ?? $conversation->creator->user->name }}
								@endif
							</p>
						</div>
						<a href="{{ route('dashboard.conversations.index') }}"
							class="inline-flex items-center gap-1 rounded border border-gray-200 px-3 py-1 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Back
						</a>
					</div>
				</div>

				<!-- Messages Container -->
				<div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
					@forelse ($messages as $message)
						@php
							$isOwn = $message->sender_user_id === auth()->id();
						@endphp
						<div class="flex @if ($isOwn) justify-end @else justify-start @endif">
							<div
								class="max-w-xs rounded-lg @if ($isOwn) bg-blue-600 text-white @else bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white @endif px-4 py-2">
								<p class="text-sm">{{ $message->message }}</p>
								<p
									class="mt-1 text-xs @if ($isOwn) text-blue-100 @else text-gray-500 dark:text-gray-400 @endif">
									{{ $message->created_at->format('H:i') }}
									@if (auth()->user()->user_type !== 'brand' && !$isOwn)
										<span class="ml-1">(as creator)</span>
									@endif
								</p>
							</div>
						</div>
					@empty
						<div class="flex h-full items-center justify-center">
							<p class="text-sm text-gray-500 dark:text-gray-400">No messages yet. Start the conversation!</p>
						</div>
					@endforelse
				</div>

				<!-- Message Input -->
				<div class="border-t border-gray-200 p-4 dark:border-gray-800">
					<form action="{{ route('dashboard.conversations.storeMessage', $conversation) }}" method="POST" class="flex gap-2">
						@csrf
						<input type="text" name="message" placeholder="Type your message..."
							class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white"
							required>
						<button type="submit"
							class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-blue-700">
							Send
						</button>
					</form>
				</div>
			</div>
		</div>

		<!-- Sidebar -->
		<div class="space-y-6">
			<!-- Conversation Info -->
			<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h4 class="font-semibold text-gray-900 dark:text-white">Conversation Details</h4>
				<div class="mt-4 space-y-3">
					<div>
						<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</p>
						<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
							{{ ucfirst(str_replace('_', ' ', $conversation->conversation_type)) }}
						</p>
					</div>
					<div>
						<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Started</p>
						<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
							{{ $conversation->created_at->format('M d, Y H:i') }}
						</p>
					</div>
					@if ($conversation->order_id)
						<div>
							<p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order</p>
							<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
								<a href="{{ route('dashboard.orders.show', $conversation->order_id) }}" class="text-blue-600 hover:underline">
									View Order
								</a>
							</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Participants -->
			<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
				<h4 class="font-semibold text-gray-900 dark:text-white">Participants</h4>
				<div class="mt-4 space-y-3">
					<div>
						<p class="text-xs text-gray-500 dark:text-gray-400">Creator</p>
						<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
							{{ $conversation->creator->display_name ?? $conversation->creator->user->name }}
						</p>
					</div>
					<div>
						<p class="text-xs text-gray-500 dark:text-gray-400">Brand</p>
						<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
							{{ $conversation->brandUser->name }}
						</p>
					</div>
					@if ($conversation->handled_by_user_id)
						<div>
							<p class="text-xs text-gray-500 dark:text-gray-400">Moderator</p>
							<p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
								{{ $conversation->handledBy->name }}
							</p>
						</div>
					@else
						<div class="mt-3">
							<p class="text-xs text-yellow-600 dark:text-yellow-400">No moderator assigned yet</p>
						</div>
					@endif
				</div>
			</div>

			<!-- Assign Moderator (Admin only) -->
			@if (auth()->user()->user_type === 'admin' && !$conversation->handled_by_user_id)
				<div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900/30 dark:bg-yellow-900/20">
					<h4 class="font-semibold text-yellow-900 dark:text-yellow-100">Assign Moderator</h4>
					<p class="mt-1 text-xs text-yellow-800 dark:text-yellow-300">This conversation needs a moderator to handle creator
						responses.</p>
					<form action="{{ route('dashboard.conversations.assign-moderator', $conversation) }}" method="POST"
						class="mt-3 space-y-2">
						@csrf
						<select name="moderator_user_id"
							class="w-full rounded-lg border border-yellow-300 bg-white px-3 py-2 text-sm dark:border-yellow-700 dark:bg-gray-800"
							required>
							<option value="">Select a moderator...</option>
							@foreach (\App\Models\User::where('user_type', 'moderator')->get() as $mod)
								<option value="{{ $mod->id }}">{{ $mod->name }}</option>
							@endforeach
						</select>
						<button type="submit"
							class="w-full rounded-lg bg-yellow-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-yellow-700">
							Assign
						</button>
					</form>
				</div>
			@endif
		</div>
	</div>

	<script>
		// Auto-scroll to bottom of messages
		const container = document.getElementById('messagesContainer');
		if (container) {
			container.scrollTop = container.scrollHeight;
		}
	</script>
@endsection
