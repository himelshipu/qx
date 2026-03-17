@extends('backend.layouts.app')

@section('content')
	<div class="mb-6">
		<div class="flex items-center justify-between">
			<div>
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Support Tickets</h1>
				<p class="mt-1 text-gray-600 dark:text-gray-400">Manage customer support requests</p>
			</div>
		</div>
	</div>

	<!-- Filters -->
	<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
		<form action="{{ route('dashboard.support-tickets.index') }}" method="GET" class="space-y-4">
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
				<!-- Search -->
				<div>
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
					<input type="text" name="search" value="{{ request('search') }}"
						placeholder="Ticket #, Subject or description..."
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
				</div>

				<!-- Status Filter -->
				<div>
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
					<select name="status"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
						<option value="">All Statuses</option>
						@foreach ($statuses as $status)
							<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
								{{ ucfirst(str_replace('_', ' ', $status)) }}
							</option>
						@endforeach
					</select>
				</div>

				<!-- Category Filter -->
				<div>
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
					<select name="category"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
						<option value="">All Categories</option>
						@foreach ($categories as $cat)
							<option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
								{{ $cat->name }}
							</option>
						@endforeach
					</select>
				</div>

				<!-- Priority Filter -->
				<div>
					<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
					<select name="priority"
						class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
						<option value="">All Priorities</option>
						@foreach ($priorities as $priority)
							<option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>
								{{ ucfirst($priority) }}
							</option>
						@endforeach
					</select>
				</div>
			</div>

			<div class="flex gap-2">
				<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
					Filter
				</button>
				<a href="{{ route('dashboard.support-tickets.index') }}"
					class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 font-medium">
					Reset
				</a>
			</div>
		</form>
	</div>

	<!-- Tickets Table -->
	<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
		<div class="overflow-x-auto">
			<table class="w-full">
				<thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
					<tr>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Ticket #</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Subject</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">From</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Category</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Status</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Priority</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Assigned To</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Created</th>
						<th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Action</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
					@forelse($tickets as $ticket)
						<tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
							<td class="px-6 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400">
								{{ $ticket->ticket_number }}
							</td>
							<td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">
								{{ Str::limit($ticket->subject, 40) }}
							</td>
							<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
								{{ $ticket->requester->name }}
							</td>
							<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
								<span
									class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-xs font-medium">
									{{ $ticket->category->name }}
								</span>
							</td>
							<td class="px-6 py-3 text-sm">
								@php
									$statusBg = match ($ticket->status) {
									    'open' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
									    'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
									    'waiting_user' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
									    'resolved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
									    'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
									    default => 'bg-gray-100 text-gray-700',
									};
								@endphp
								<span class="px-2 py-1 {{ $statusBg }} rounded text-xs font-medium">
									{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
								</span>
							</td>
							<td class="px-6 py-3 text-sm">
								@php
									$priorityBg = match ($ticket->priority) {
									    'low' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
									    'medium' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
									    'high' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
									    'urgent' => 'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-200',
									    default => 'bg-gray-100 text-gray-700',
									};
								@endphp
								<span class="px-2 py-1 {{ $priorityBg }} rounded text-xs font-medium">
									{{ ucfirst($ticket->priority) }}
								</span>
							</td>
							<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
								{{ $ticket->assignedTo?->name ?? 'Unassigned' }}
							</td>
							<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
								{{ $ticket->created_at->format('M d, Y') }}
							</td>
							<td class="px-6 py-3 text-sm">
								<a href="{{ route('dashboard.support-tickets.show', $ticket) }}"
									class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">
									View
								</a>
							</td>
						</tr>
					@empty
						<tr>
							<td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
								No support tickets found
							</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>

	<!-- Pagination -->
	<div class="mt-6">
		{{ $tickets->links() }}
	</div>
@endsection
