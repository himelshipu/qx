@extends('backend.layouts.app')

@section('title', 'Notifications')

@section('content')
	<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
		<!-- Statistics Cards -->
		<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Total Notifications</p>
					<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
				</div>
				<div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
					<x-icons.bell class="w-6 h-6 text-blue-600 dark:text-blue-400" />
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Unread</p>
					<p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['unread'] }}</p>
				</div>
				<div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
					<svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 24 24">
						<path
							d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-6-8h4v4h-4z" />
					</svg>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
			<div class="flex items-center justify-between">
				<div>
					<p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Read</p>
					<p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $stats['read'] }}</p>
				</div>
				<div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
					<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
						<path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
					</svg>
				</div>
			</div>
		</div>

		<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
			<div class="flex items-center gap-3">
				@if ($stats['unread'] > 0)
					<form action="{{ route('dashboard.notifications.mark-all-as-read') }}" method="POST" class="flex-1">
						@csrf
						<button type="submit"
							class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
							Mark All as Read
						</button>
					</form>
				@endif
			</div>
		</div>
	</div>

	<!-- Notifications Table -->
	<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
		<div class="flex flex-col sm:flex-row items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
			<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">All Notifications</h2>

			<div class="flex items-center gap-3 w-full sm:w-auto">
				<form action="{{ route('dashboard.notifications.index') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
					<select name="filter" onchange="this.form.submit()"
						class="px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg dark:text-white">
						<option value="">All Notifications</option>
						<option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>Unread</option>
						<option value="read" {{ request('filter') === 'read' ? 'selected' : '' }}>Read</option>
					</select>
				</form>

				@if ($stats['total'] > 0)
					<form action="{{ route('dashboard.notifications.clear-all') }}" method="POST"
						onsubmit="return confirm('Are you sure? This action cannot be undone.');">
						@csrf
						@method('DELETE')
						<button type="submit"
							class="px-3 py-2 text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
							Clear All
						</button>
					</form>
				@endif
			</div>
		</div>

		@if ($notifications->count() > 0)
			<div class="divide-y divide-gray-200 dark:divide-gray-700">
				@foreach ($notifications as $notification)
					<div
						class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">
						<div class="flex gap-4">
							<!-- Icon -->
							<div class="flex-shrink-0">
								<div
									class="w-12 h-12 rounded-lg flex items-center justify-center {{ match ($notification->getColor()) {
									    'blue' => 'bg-blue-100 dark:bg-blue-900/30',
									    'green' => 'bg-green-100 dark:bg-green-900/30',
									    'red' => 'bg-red-100 dark:bg-red-900/30',
									    'yellow' => 'bg-yellow-100 dark:bg-yellow-900/30',
									    'purple' => 'bg-purple-100 dark:bg-purple-900/30',
									    'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30',
									    'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30',
									    default => 'bg-gray-100 dark:bg-gray-700',
									} }}">
									@switch($notification->type)
										@case('order')
											<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
												<path
													d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-. 9-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.45-9h8.95c.6 0 1.08-.38 1.3-.92l3.02-7.05c.12-.3.12-.62 0-.92-.12-.3-.39-.48-.72-.48H6.21l-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.13.23-.2.49-.2.75 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l2.96-6.59c.13-.22.8-.04 0-.09H6.21l-.94-2z" />
											</svg>
										@break

										@case('payment')
											<svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
												<path
													d="M20 8h-3V4H3c-1.1 0-1.99.9-1.99 2L1 18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8h3l-3-4zm-1 13H4V4h10v4h5v13z" />
											</svg>
										@break

										@case('campaign')
											<svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
												<path
													d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h2.79c.77 0 1.49.31 2 .82.51-.51 1.23-.82 2-.82h2.79l-2.75-3.54 2.75-3.54h-2.79c-.77 0-1.49-.31-2-.82-.51.51-1.23.82-2 .82H8.21l2.75 3.54z" />
											</svg>
										@break

										@case('message')
											<svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
												<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 8h-8v-2h8v2zm0 4h-8v-2h8v2z" />
											</svg>
										@break

										@case('review')
											<svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
												<path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2l-2.81 6.63L2 9.24l5.46 4.73L5.82 21z" />
											</svg>
										@break

										@case('payout')
											<svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
												<path
													d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
											</svg>
										@break

										@default
											<x-icons.bell class="w-6 h-6 text-gray-600 dark:text-gray-400" />
									@endswitch
								</div>
							</div>

							<!-- Content -->
							<div class="flex-1 min-w-0">
								<div class="flex items-start justify-between gap-2 mb-1">
									<h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</h3>
									<span
										class="inline-block px-2 py-1 text-xs font-medium rounded-full {{ match ($notification->getColor()) {
										    'blue' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
										    'green' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
										    'red' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
										    'yellow' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
										    'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
										    'indigo' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
										    'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
										    default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
										} }}">
										{{ ucfirst($notification->type) }}
									</span>
								</div>

								<p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $notification->message }}</p>

								<div class="flex items-center justify-between gap-2">
									<span class="text-xs text-gray-500 dark:text-gray-400">
										{{ $notification->created_at->diffForHumans() }}
									</span>

									<div class="flex items-center gap-2">
										@if (!$notification->is_read)
											<form action="{{ route('dashboard.notifications.mark-as-read', $notification) }}" method="POST"
												class="inline">
												@csrf
												<button type="submit"
													class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
													Mark as Read
												</button>
											</form>
										@else
											<form action="{{ route('dashboard.notifications.mark-as-unread', $notification) }}" method="POST"
												class="inline">
												@csrf
												<button type="submit"
													class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
													Mark as Unread
												</button>
											</form>
										@endif

										@if ($notification->action_url)
											<a href="{{ route('dashboard.notifications.show', $notification) }}"
												class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
												View
											</a>
										@endif

										<form action="{{ route('dashboard.notifications.destroy', $notification) }}" method="POST" class="inline"
											onsubmit="return confirm('Delete this notification?');">
											@csrf
											@method('DELETE')
											<button type="submit"
												class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
												Delete
											</button>
										</form>
									</div>
								</div>
							</div>

							<!-- Read Indicator -->
							@if (!$notification->is_read)
								<div class="flex-shrink-0">
									<div class="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
								</div>
							@endif
						</div>
					</div>
				@endforeach
			</div>

			<!-- Pagination -->
			<div class="p-6 border-t border-gray-200 dark:border-gray-700">
				{{ $notifications->links('pagination::tailwind') }}
			</div>
		@else
			<div class="text-center py-12">
				<x-icons.bell class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" />
				<p class="text-gray-600 dark:text-gray-400 text-lg">No notifications yet</p>
				<p class="text-gray-500 dark:text-gray-500 text-sm mt-2">You're all caught up!</p>
			</div>
		@endif
	</div>

@endsection
