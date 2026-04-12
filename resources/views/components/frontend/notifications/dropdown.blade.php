<!-- Frontend Notification Dropdown Component -->
<div x-data='frontendNotificationDropdown()' x-init="init()" class="relative">
	<!-- Bell Icon Button -->
	<button @click="isOpen = !isOpen"
		class="relative p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors"
		:title="unreadCount > 0 ? `${unreadCount} unread notification${unreadCount !== 1 ? 's' : ''}` : 'No new notifications'">
		<!-- Bell Icon -->
		<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
		</svg>

		<!-- Unread Badge -->
		<span x-show="unreadCount > 0" x-cloak
			class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
			x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
	</button>

	<!-- Dropdown Panel -->
	<div x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
		@click.away="isOpen = false"
		class="absolute right-0 top-full mt-2 w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

		<!-- Header -->
		<div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4">
			<div class="flex items-center justify-between">
				<div>
					<h3 class="font-semibold">Notifications</h3>
					<p class="text-sm text-blue-100" x-text="`${unreadCount} unread`"></p>
				</div>
				<a href="{{ route('frontend.notifications.index') }}"
					class="text-sm text-blue-100 hover:text-white transition-colors underline">
					View All
				</a>
			</div>
		</div>

		<!-- Loading State -->
		<div x-show="isLoading" class="px-6 py-8 text-center">
			<div class="animate-spin inline-block w-6 h-6 border-3 border-blue-600 border-t-transparent rounded-full"></div>
		</div>

		<!-- Notifications List -->
		<div x-show="!isLoading" class="max-h-96 overflow-y-auto">
			<template x-for="notification in notifications" :key="notification.id">
				<div @click="handleNotificationClick(notification)"
					class="border-b border-gray-100 dark:border-gray-700 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors">
					<div class="flex items-start gap-3">
						<!-- Icon -->
						<div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-sm"
							:style="`background-color: ${getColorBg(notification.color)}; color: ${getColorText(notification.color)};`">
							<span x-html="notification.icon"></span>
						</div>

						<!-- Content -->
						<div class="flex-1 min-w-0">
							<p class="font-medium text-gray-900 dark:text-white text-sm" x-text="notification.title"></p>
							<p class="text-gray-600 dark:text-gray-400 text-sm mt-1 line-clamp-2" x-text="notification.body"></p>
							<p class="text-xs text-gray-500 dark:text-gray-500 mt-2" x-text="notification.created_at"></p>
						</div>

						<!-- Unread Indicator -->
						<div x-show="notification.is_read === false" class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-1.5"></div>
					</div>
				</div>
			</template>

			<!-- Empty State -->
			<div x-show="notifications.length === 0 && !isLoading" class="p-8 text-center">
				<svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor"
					viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
				</svg>
				<p class="text-gray-600 dark:text-gray-400 text-sm">No notifications</p>
			</div>
		</div>

		<!-- Footer Actions -->
		<div x-show="unreadCount > 0 && !isLoading"
			class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 px-6 py-3 flex gap-2">
			<button @click="markAllAsRead()"
				class="flex-1 px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-colors">
				Mark All as Read
			</button>
		</div>
	</div>
</div>

<script>
	function frontendNotificationDropdown() {
		return {
			isOpen: false,
			isLoading: false,
			notifications: [],
			unreadCount: 0,

			async init() {
		// Only load if user is authenticated by checking for CSRF token
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
		if (!csrfToken) {
			return; // User not authenticated, don't load notifications
		}
		await this.loadNotifications();
		// Refresh every minute
		setInterval(() => this.loadNotifications(), 60000);
	},

	async loadNotifications() {
		const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
		if (!csrfToken) {
			return; // User not authenticated, skip loading
		}
		
		this.isLoading = true;
		try {
			const response = await fetch('{{ route('frontend.notifications.api.unread') }}', {
				credentials: 'same-origin',
				headers: {
					'X-CSRF-Token': csrfToken,
					'X-Requested-With': 'XMLHttpRequest',
					'Accept': 'application/json',
				}
			});
			
			// Middleware may redirect to HTML pages during auth/session transitions.
			if (response.redirected || response.status === 401 || response.status === 403 || response.status === 419) {
				this.notifications = [];
				this.unreadCount = 0;
				this.isLoading = false;
				return;
			}
			
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			const contentType = response.headers.get('content-type') || '';
			if (!contentType.includes('application/json')) {
				this.notifications = [];
				this.unreadCount = 0;
				return;
			}
			
			const data = await response.json();
			this.notifications = data.notifications;
			this.unreadCount = data.unreadCount;
		} catch (error) {
			console.error('Failed to load notifications:', error);
		} finally {
			this.isLoading = false;
		}
	},
			getColorBg(colorClass) {
				const colors = {
					'emerald': '#ecfdf5',
					'green': '#dcfce7',
					'blue': '#dbeafe',
					'indigo': '#e0e7ff',
					'yellow': '#fef3c7',
					'purple': '#f3e8ff',
				};
				return colors[colorClass] || '#e0e7ff';
			},

			getColorText(colorClass) {
				const colors = {
					'emerald': '#065f46',
					'green': '#166534',
					'blue': '#1e40af',
					'indigo': '#312e81',
					'yellow': '#b45309',
					'purple': '#6b21a8',
				};
				return colors[colorClass] || '#1e40af';
			},

			async handleNotificationClick(notification) {
				if (notification.action_url) {
					window.location.href = notification.action_url;
				}
			},

			async markAllAsRead() {
				try {
					const response = await fetch('{{ route('frontend.notifications.mark-all-as-read') }}', {
						method: 'POST',
						headers: {
							'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute(
								'content'),
							'Content-Type': 'application/json',
						}
					});
					if (response.ok) {
						await this.loadNotifications();
					}
				} catch (error) {
					console.error('Error marking as read:', error);
				}
			}
		};
	}
</script>
