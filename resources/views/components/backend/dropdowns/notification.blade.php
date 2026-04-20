<div class="relative" x-data="notificationDropdown({ unreadCount: @js($dashboardUnreadNotifications ?? 0) })" @click.away="open = false">
	<button @click="open = !open; loadNotifications()"
		class="relative flex items-center justify-center w-11 h-11 text-gray-500 bg-white border border-gray-200 rounded-full hover:bg-gray-50 hover:text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white transition-colors duration-200">
		<span x-show="unreadCount > 0" class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900">
			<span class="absolute w-full h-full bg-red-500 rounded-full opacity-75 animate-pulse"></span>
		</span>
		<x-icons.bell class="w-5 h-5" />
	</button>

	<div x-show="open" x-transition
		class="absolute right-0 mt-2 w-90 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
		style="display: none;">
		<!-- Header -->
		<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-750">
			<div>
				<h5 class="text-base font-semibold text-gray-900 dark:text-white">Notifications</h5>
				<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="`${unreadCount} new`"></p>
			</div>
			<button @click="open = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
				<x-icons.close class="w-5 h-5" />
			</button>
		</div>

		<!-- Loading State -->
		<div x-show="loading" class="flex flex-col items-center justify-center py-12">
			<div class="relative w-10 h-10 mb-2">
				<div class="absolute inset-0 rounded-full animate-spin" style="border: 2px solid #f0f0f0; border-top-color: #3b82f6;"></div>
			</div>
			<p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...</p>
		</div>

		<!-- Notifications List -->
		<ul class="overflow-y-auto max-h-105 divide-y divide-gray-100 dark:divide-gray-700" x-show="!loading">
			<template x-for="notification in notifications" :key="notification.id">
				<li>
					<div
						class="flex gap-3.5 px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 cursor-pointer group"
						@click="markAsReadAndRedirect(notification)"
						:class="!notification.is_read ? 'bg-blue-50 dark:bg-blue-900/10' : ''"
						>
						<!-- Icon Container -->
						<div class="shrink-0 w-11 h-11 rounded-lg flex items-center justify-center ring-1 ring-transparent group-hover:ring-gray-200 dark:group-hover:ring-gray-600 transition-all"
							:class="getIconClassForType(notification.type)">
							<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
								<template x-if="notification.type === 'order'">
									<path
										d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.45-9h8.95c.6 0 1.08-.38 1.3-.92l3.02-7.05c.12-.3.12-.62 0-.92-.12-.3-.39-.48-.72-.48H6.21l-.94-2H1v2h2l3.6 7.59-1.35 2.45c-.13.23-.2.49-.2.75 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l2.96-6.59c.13-.22.8-.04 0-.09H6.21l-.94-2z" />
								</template>
								<template x-if="notification.type === 'payment'">
									<path
										d="M20 8h-3V4H3c-1.1 0-1.99.9-1.99 2L1 18c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8h3l-3-4zm-1 13H4V4h10v4h5v13z" />
								</template>
								<template x-if="notification.type === 'campaign'">
									<path
										d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14zm-5.04-6.71l-2.75 3.54h2.79c.77 0 1.49.31 2 .82.51-.51 1.23-.82 2-.82h2.79l-2.75-3.54 2.75-3.54h-2.79c-.77 0-1.49-.31-2-.82-.51.51-1.23.82-2 .82H8.21l2.75 3.54z" />
								</template>
								<template x-if="notification.type === 'message'">
									<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 8h-8v-2h8v2zm0 4h-8v-2h8v2z" />
								</template>
								<template x-if="notification.type === 'review'">
									<path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2l-2.81 6.63L2 9.24l5.46 4.73L5.82 21z" />
								</template>
								<template x-if="notification.type === 'payout'">
									<path
										d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5zm-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11zm3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
								</template>
							</svg>
						</div>

						<!-- Content -->
						<div class="flex-1 min-w-0 flex flex-col justify-between">
							<div class="flex items-start justify-between gap-2 mb-2">
								<p class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1" x-text="notification.title"></p>
								<span class="inline-block px-2.5 py-1 text-xs font-medium rounded-md shrink-0 whitespace-nowrap"
									:class="getBadgeClass(notification.type)"
									x-text="notification.type.charAt(0).toUpperCase() + notification.type.slice(1)"></span>
							</div>
							<p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 mb-2" x-text="notification.body"></p>
							<p class="text-xs text-gray-500 dark:text-gray-500 font-medium" x-text="getTimeAgo(notification.created_at)"></p>
						</div>

						<!-- Unread Indicator -->
						<div class="flex flex-col items-center justify-center" x-show="!notification.is_read">
							<div class="w-2.5 h-2.5 rounded-full bg-blue-500 dark:bg-blue-400 shrink-0"></div>
						</div>
					</div>
				</li>
			</template>

			<li x-show="notifications.length === 0" class="text-center py-12">
				<div class="flex flex-col items-center justify-center">
					<svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
					</svg>
					<p class="text-sm font-medium text-gray-600 dark:text-gray-400">All caught up!</p>
					<p class="text-xs text-gray-500 dark:text-gray-500 mt-1">No new notifications</p>
				</div>
			</li>
		</ul>

		<!-- Footer Action -->
		<div class="border-t border-gray-100 dark:border-gray-700 bg-linear-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-750 p-3">
			<a href="{{ route('dashboard.notifications.index') }}"
				class="flex items-center justify-center gap-2 w-full py-2.5 px-4 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg transition-colors"
				@click="open = false">
				<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
				</svg>
				View All Notifications
			</a>
		</div>
	</div>
</div>

<script>
	function notificationDropdown(initialState = {}) {
		return {
			open: false,
			loading: false,
			notifications: [],
			unreadCount: initialState.unreadCount ?? 0,

			loadNotifications() {
			// Check if user is authenticated by verifying CSRF token presence
			const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
			if (!csrfToken) {
				return; // Not authenticated, skip loading
			}
			
			this.loading = true;
			fetch('{{ route('dashboard.notifications.api.unread') }}')
				.then(response => {
					// Silently ignore auth errors
					if (response.status === 401 || response.status === 403) {
						this.loading = false;
						return null;
					}
					if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
					return response.json();
				})
				.then(data => {
					if (data) {
						this.notifications = data.notifications;
						this.unreadCount = data.unreadCount;
					}
					})
					.catch(error => {
						console.error('Error loading notifications:', error);
						this.loading = false;
					});
			},

			getIconClass(colorClass) {
				const colorMap = {
					'blue': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
					'green': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
					'red': 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
					'yellow': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
					'purple': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
					'indigo': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
					'emerald': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
				};
				return colorMap[colorClass] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
			},

			getBadgeClass(type) {
				const badgeMap = {
					'order': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
					'payment': 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
					'campaign': 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
					'message': 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
					'review': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
					'payout': 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
				};
				return badgeMap[type] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
			},

			getIconClassForType(type) {
				const colorMap = {
					'order': 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
					'payment': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
					'campaign': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
					'message': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
					'review': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
					'payout': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
				};
				return colorMap[type] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400';
			},

			getTimeAgo(createdAt) {
				const date = new Date(createdAt);
				const now = new Date();
				const seconds = Math.floor((now - date) / 1000);

				if (seconds < 60) return 'just now';
				if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
				if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
				if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';

				return date.toLocaleDateString();
			},

			markAsReadAndRedirect(notification) {
				if (!notification.is_read) {
					fetch('{{ route('dashboard.notifications.mark-as-read', ['notification' => 'NOTIFICATION_ID']) }}'
							.replace('NOTIFICATION_ID', notification.id), {
								method: 'POST',
								headers: {
									'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
									'Content-Type': 'application/json'
								}
							})
						.then(() => {
							window.location.href =
								'{{ route('dashboard.notifications.show', ['notification' => 'NOTIFICATION_ID']) }}'
								.replace('NOTIFICATION_ID', notification.id);
						});
				} else {
					window.location.href =
						'{{ route('dashboard.notifications.show', ['notification' => 'NOTIFICATION_ID']) }}'.replace(
							'NOTIFICATION_ID', notification.id);
				}
			}
		}
	}
</script>
