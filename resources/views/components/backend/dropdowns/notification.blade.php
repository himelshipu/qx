<div class="relative" x-data="notificationDropdown()" @click.away="open = false">
	<button @click="open = !open; loadNotifications()"
		class="relative flex items-center justify-center w-11 h-11 text-gray-500 bg-white border border-gray-200 rounded-full hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white">
		<span x-show="unreadCount > 0" class="absolute top-0.5 right-0 w-2 h-2 bg-orange-400 rounded-full">
			<span class="absolute w-full h-full bg-orange-400 rounded-full opacity-75 animate-ping"></span>
		</span>
		<x-icons.bell class="w-5 h-5" />
	</button>

	<div x-show="open" x-transition
		class="absolute right-0 mt-2 w-[350px] bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-3 z-50 sm:w-[361px]"
		style="display: none;">
		<div class="flex items-center justify-between pb-3 mb-3 border-b border-gray-200 dark:border-gray-700">
			<h5 class="text-lg font-semibold text-gray-800 dark:text-white">Notifications</h5>
			<button @click="open = false" class="text-gray-500 dark:text-gray-400">
				<x-icons.close class="w-5 h-5" />
			</button>
		</div>

		<div x-show="loading" class="flex items-center justify-center py-8">
			<div class="animate-spin">
				<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
					</path>
				</svg>
			</div>
		</div>

		<ul class="overflow-y-auto max-h-[400px]" x-show="!loading">
			<template x-for="notification in notifications" :key="notification.id">
				<li>
					<div
						class="flex gap-3 p-3 border-b border-gray-200 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-white/5 transition-colors cursor-pointer"
						@click="markAsReadAndRedirect(notification)">
						<!-- Icon -->
						<div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
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
						<div class="flex-1 min-w-0">
							<div class="flex items-start justify-between gap-2 mb-1">
								<p class="text-sm font-semibold text-gray-800 dark:text-white" x-text="notification.title"></p>
								<span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full"
									:class="getBadgeClass(notification.type)"
									x-text="notification.type.charAt(0).toUpperCase() + notification.type.slice(1)"></span>
							</div>
							<p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2" x-text="notification.body"></p>
							<p class="text-xs text-gray-500 dark:text-gray-500 mt-1" x-text="getTimeAgo(notification.created_at)"></p>
						</div>

						<!-- Read Indicator -->
						<div class="flex-shrink-0" x-show="!notification.is_read">
							<div class="w-2.5 h-2.5 rounded-full bg-blue-600 dark:bg-blue-400"></div>
						</div>
					</div>
				</li>
			</template>

			<li x-show="notifications.length === 0" class="text-center py-8">
				<p class="text-sm text-gray-600 dark:text-gray-400">No notifications</p>
			</li>
		</ul>

		<a href="{{ route('dashboard.notifications.index') }}"
			class="flex justify-center p-3 mt-3 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors"
			@click="open = false">
			View All Notifications
		</a>
	</div>
</div>

<script>
	function notificationDropdown() {
		return {
			open: false,
			loading: false,
			notifications: [],
			unreadCount: 0,

			loadNotifications() {
				this.loading = true;
				fetch('{{ route('dashboard.notifications.api.unread') }}')
					.then(response => response.json())
					.then(data => {
						this.notifications = data.notifications;
						this.unreadCount = data.unreadCount;
						this.loading = false;
					})
					.catch(error => {
						console.error('Error loading notifications:', error);
						this.loading = false;
					});
			},

			getIconClass(colorClass) {
				const colorMap = {
					'blue': 'bg-blue-100 dark:bg-blue-900/30',
					'green': 'bg-green-100 dark:bg-green-900/30',
					'red': 'bg-red-100 dark:bg-red-900/30',
					'yellow': 'bg-yellow-100 dark:bg-yellow-900/30',
					'purple': 'bg-purple-100 dark:bg-purple-900/30',
					'indigo': 'bg-indigo-100 dark:bg-indigo-900/30',
					'emerald': 'bg-emerald-100 dark:bg-emerald-900/30',
				};
				return colorMap[colorClass] || 'bg-gray-100 dark:bg-gray-700';
			},

			getBadgeClass(type) {
				const badgeMap = {
					'order': 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
					'payment': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
					'campaign': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
					'message': 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
					'review': 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
					'payout': 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
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
