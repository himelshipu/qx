@extends('frontend.layouts.app')

@section('title', 'Notifications')

@section('content')
	<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8 sm:py-12">
		<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
			<!-- Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Notifications</h1>
				<p class="text-gray-600 dark:text-gray-400">Manage and view all your notifications</p>
			</div>

			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
				<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
					<div class="flex items-center justify-between">
						<div>
							<p class="text-gray-600 dark:text-gray-400 text-sm mb-2">Total</p>
							<p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
						</div>
						<div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
							<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
								<path
									d="M17.391 3.634l-2.321.02c-1.013.01-2.02.225-2.996.646L8.1 5.532c-1.405.625-2.236 2.126-2.082 3.646l.556 6.174c.227 2.519 2.225 4.488 4.74 4.614l5.676.312c1.406.078 2.886-.523 3.734-1.6.849-1.076 1.098-2.603.661-3.956l-1.994-6.498c-.33-1.076-1.276-1.94-2.416-2.15l-2.864-.506a2.61 2.61 0 01-.566-.146l3.825-2.896zm-8.54 1.97L8.1 5.532" />
							</svg>
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
									d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm0-13c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5z" />
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
								<path
									d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
							</svg>
						</div>
					</div>
				</div>
			</div>

			<!-- Filter and Actions -->
			<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
				<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
					<!-- Filter Dropdown -->
					<div class="w-full sm:w-auto">
						<select onchange="filterNotifications(this.value)"
							class="w-full sm:w-auto px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
							<option value="">All Notifications</option>
							<option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
							<option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
						</select>
					</div>

					<!-- Action Buttons -->
					<div class="flex gap-3 w-full sm:w-auto">
						@if ($stats['unread'] > 0)
							<button onclick="markAllAsRead()"
								class="flex-1 sm:flex-none px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
								Mark All as Read
							</button>
						@endif
						@if ($stats['total'] > 0)
							<button onclick="clearAll()"
								class="flex-1 sm:flex-none px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
								Clear All
							</button>
						@endif
					</div>
				</div>
			</div>

			<!-- Notifications List -->
			<div class="space-y-4">
				@forelse ($notifications as $notification)
					<div
						class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
						<div class="flex items-start justify-between gap-4">
							<div class="flex items-start gap-4 flex-1">
								<!-- Icon -->
								<div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 text-lg"
									style="background-color: var(--badge-bg); color: var(--badge-color);"
									@php
$colors = ['order' => ['bg' => '#dbeafe', 'text' => '#1e40af'], 'payment' => ['bg' => '#dcfce7', 'text' => '#166534'], 'campaign' => ['bg' => '#dbeafe', 'text' => '#1e40af'], 'message' => ['bg' => '#e0e7ff', 'text' => '#312e81'], 'review' => ['bg' => '#fef3c7', 'text' => '#b45309'], 'payout' => ['bg' => '#e9d5ff', 'text' => '#6b21a8']];
									$color = $colors[$notification->type] ?? $colors['message']; @endphp
									:style="`--badge-bg: ${@json($color['bg'])}; --badge-color: ${@json($color['text'])};`">
									{!! $notification->getIconClass() !!}
								</div>

								<!-- Content -->
								<div class="flex-1">
									<div class="flex items-start justify-between mb-2">
										<h3 class="font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</h3>
										@if (!$notification->is_read)
											<span class="w-2 h-2 bg-blue-600 rounded-full ml-2 mt-2 flex-shrink-0"></span>
										@endif
									</div>
									<p class="text-gray-600 dark:text-gray-400 text-sm mb-2">{{ $notification->body }}</p>
									<p class="text-xs text-gray-500 dark:text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
								</div>
							</div>

							<!-- Actions -->
							<div class="flex items-center gap-2 flex-shrink-0">
								@if (!$notification->is_read)
									<button onclick="markAsRead({{ $notification->id }})"
										class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Mark as read">
										<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
											<path
												d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
										</svg>
									</button>
								@else
									<button onclick="markAsUnread({{ $notification->id }})"
										class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Mark as unread">
										<svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
											<path
												d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
										</svg>
									</button>
								@endif

								<button onclick="deleteNotification({{ $notification->id }})"
									class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Delete">
									<svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 24 24">
										<path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-9l-1 1H5v2h14V4z" />
									</svg>
								</button>

								@if ($actionUrl = $notification->getActionUrl())
									<a href="{{ $actionUrl }}"
										class="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View">
										<svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
											<path
												d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
										</svg>
									</a>
								@endif
							</div>
						</div>
					</div>
				@empty
					<div
						class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
						<svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
							viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
						</svg>
						<p class="text-gray-600 dark:text-gray-400 mb-2">No notifications yet</p>
						<p class="text-sm text-gray-500 dark:text-gray-500">You'll see your notifications here when something important
							happens</p>
					</div>
				@endforelse
			</div>

			<!-- Pagination -->
			@if ($notifications->hasPages())
				<div class="mt-8">
					{{ $notifications->links('pagination::tailwind') }}
				</div>
			@endif
		</div>
	</div>

	<script>
		function filterNotifications(status) {
			const params = new URLSearchParams();
			if (status) params.append('status', status);
			window.location.href = `{{ route('frontend.notifications.index') }}?${params.toString()}`;
		}

		function markAsRead(notificationId) {
			fetch(`/notifications/${notificationId}/mark-as-read`, {
					method: 'POST',
					headers: {
						'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json',
					},
				})
				.then(response => response.json())
				.then(data => location.reload())
				.catch(error => console.error('Error:', error));
		}

		function markAsUnread(notificationId) {
			fetch(`/notifications/${notificationId}/mark-as-unread`, {
					method: 'POST',
					headers: {
						'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json',
					},
				})
				.then(response => response.json())
				.then(data => location.reload())
				.catch(error => console.error('Error:', error));
		}

		function markAllAsRead() {
			if (!confirm('Mark all notifications as read?')) return;

			fetch('/notifications/mark-all-as-read', {
					method: 'POST',
					headers: {
						'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json',
					},
				})
				.then(response => response.json())
				.then(data => location.reload())
				.catch(error => console.error('Error:', error));
		}

		function deleteNotification(notificationId) {
			if (!confirm('Delete this notification?')) return;

			fetch(`/notifications/${notificationId}`, {
					method: 'DELETE',
					headers: {
						'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json',
					},
				})
				.then(response => response.json())
				.then(data => location.reload())
				.catch(error => console.error('Error:', error));
		}

		function clearAll() {
			if (!confirm('This will delete all notifications. Are you sure?')) return;

			fetch('{{ route('frontend.notifications.clear-all') }}', {
					method: 'POST',
					headers: {
						'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
						'Content-Type': 'application/json',
					},
				})
				.then(response => response.json())
				.then(data => location.reload())
				.catch(error => console.error('Error:', error));
		}
	</script>
@endsection
