@extends('backend.layouts.app')

@section('content')
	<div class="mb-6">
		<div class="flex items-center justify-between">
			<div>
				<a href="{{ route('dashboard.support-tickets.index') }}"
					class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 text-sm font-medium mb-2 inline-block">
					← Back to Tickets
				</a>
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $ticket->subject }}</h1>
				<p class="mt-1 text-gray-600 dark:text-gray-400">Ticket {{ $ticket->ticket_number }} • Created
					{{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</p>
			</div>
			<div class="flex gap-2">
				<form action="{{ route('dashboard.support-tickets.destroy', $ticket) }}" method="POST" class="inline-block">
					@csrf
					@method('DELETE')
					<button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium text-sm"
						onclick="return confirm('Are you sure you want to delete this ticket?')">
						Delete
					</button>
				</form>
			</div>
		</div>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Ticket Details -->
		<div class="lg:col-span-2">
			<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
				<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Description</h2>
				<p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $ticket->description }}</p>
			</div>

			<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
				<h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ticket Information</h2>
				<dl class="space-y-4">
					<div>
						<dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</dt>
						<dd class="mt-1 flex items-center gap-2">
							<span
								class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-sm font-medium">
								{{ $ticket->category->name }}
							</span>
						</dd>
					</div>
					<div>
						<dt class="text-sm font-medium text-gray-700 dark:text-gray-300">From</dt>
						<dd class="mt-1 text-gray-600 dark:text-gray-400">
							<a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline">
								{{ $ticket->requester->name }} ({{ $ticket->requester->email }})
							</a>
						</dd>
					</div>
					<div>
						<dt class="text-sm font-medium text-gray-700 dark:text-gray-300">Source</dt>
						<dd class="mt-1 text-gray-600 dark:text-gray-400">
							{{ ucfirst($ticket->source) }}
						</dd>
					</div>
				</dl>
			</div>
		</div>

		<!-- Sidebar: Update Form -->
		<div class="lg:col-span-1">
			<form action="{{ route('dashboard.support-tickets.update', $ticket) }}" method="POST" class="space-y-6">
				@csrf
				@method('PUT')

				<!-- Status -->
				<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
					<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Status</label>
					<div class="space-y-2">
						@foreach ($statuses as $statusOption)
							@php
								$statusBg = match ($statusOption) {
								    'open' => 'border-yellow-300 dark:border-yellow-600',
								    'in_progress' => 'border-blue-300 dark:border-blue-600',
								    'waiting_user' => 'border-purple-300 dark:border-purple-600',
								    'resolved' => 'border-green-300 dark:border-green-600',
								    'closed' => 'border-gray-300 dark:border-gray-600',
								    default => 'border-gray-300',
								};
							@endphp
							<label
								class="flex items-center p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $ticket->status === $statusOption ? 'border-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-600' : 'border-gray-200' }}">
								<input type="radio" name="status" value="{{ $statusOption }}"
									{{ $ticket->status === $statusOption ? 'checked' : '' }} class="w-4 h-4">
								<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
									{{ ucfirst(str_replace('_', ' ', $statusOption)) }}
								</span>
							</label>
						@endforeach
					</div>
				</div>

				<!-- Priority -->
				<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
					<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Priority</label>
					<div class="space-y-2">
						@foreach ($priorities as $priorityOption)
							<label
								class="flex items-center p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition {{ $ticket->priority === $priorityOption ? 'border-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-600' : 'border-gray-200' }}">
								<input type="radio" name="priority" value="{{ $priorityOption }}"
									{{ $ticket->priority === $priorityOption ? 'checked' : '' }} class="w-4 h-4">
								<span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
									{{ ucfirst($priorityOption) }}
								</span>
							</label>
						@endforeach
					</div>
				</div>

				<!-- Assigned To -->
				<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
					<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Assign To</label>
					<select name="assigned_to_user_id"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
						<option value="">Unassigned</option>
						@foreach ($admins as $admin)
							<option value="{{ $admin->id }}" {{ $ticket->assigned_to_user_id === $admin->id ? 'selected' : '' }}>
								{{ $admin->name }}
							</option>
						@endforeach
					</select>
				</div>

				<!-- Save Button -->
				<button type="submit"
					class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold transition">
					Update Ticket
				</button>
			</form>
		</div>
	</div>
@endsection
