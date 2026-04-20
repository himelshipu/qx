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
						<td class="px-6 py-3 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $ticket->ticket_number }}</td>
						<td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 40) }}</td>
						<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $ticket->requester->name }}</td>
						<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">
							<span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-xs font-medium">{{ $ticket->category->name }}</span>
						</td>
						<td class="px-6 py-3 text-sm">
							<span class="px-2 py-1 {{ $ticket->statusBadgeClass() }} rounded text-xs font-medium">{{ $ticket->statusLabel() }}</span>
						</td>
						<td class="px-6 py-3 text-sm">
							<span class="px-2 py-1 {{ $ticket->priorityBadgeClass() }} rounded text-xs font-medium">{{ $ticket->priorityLabel() }}</span>
						</td>
						<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $ticket->assignedTo?->name ?? 'Unassigned' }}</td>
						<td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $ticket->created_at->format('M d, Y') }}</td>
						<td class="px-6 py-3 text-sm">
							<a href="{{ route('dashboard.support-tickets.show', $ticket) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-medium">View</a>
						</td>
					</tr>
				@empty
					<tr>
						<td colspan="9" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No support tickets found</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	@if ($tickets->hasPages())
		<div class="border-t border-gray-200 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900 sm:px-6">
			{{ $tickets->links() }}
		</div>
	@endif
</div>
