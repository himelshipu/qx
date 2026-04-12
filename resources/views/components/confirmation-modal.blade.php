@props([
    'title' => 'Please confirm',
    'message' => 'Are you sure you want to continue?',
    'confirmText' => 'Confirm',
    'variant' => 'danger',
])

<div x-show="$store.confirmModal.isOpen" x-cloak x-on:keydown.escape.window="$store.confirmModal.close()"
	class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">

	<div
		class="w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
		@click.outside="$store.confirmModal.close()">

		<!-- Header with variant-specific styling -->
		<div
			:class="{
			    'bg-gradient-to-r from-amber-50 to-amber-100/50 dark:from-amber-900/20 dark:to-amber-800/20': (($store
			        .confirmModal?.variant ?? @js($variant)) === 'warning'),
			    'bg-gradient-to-r from-red-50 to-red-100/50 dark:from-red-900/20 dark:to-red-800/20': (($store.confirmModal
			        ?.variant ?? @js($variant)) === 'danger'),
			    'bg-gradient-to-r from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/20': (($store.confirmModal
			        ?.variant ?? @js($variant)) === 'info')
			}"
			class="flex items-start justify-between p-6 border-b border-gray-200 dark:border-gray-700">
			<div class="flex items-start gap-4 flex-1">
				<!-- Icon -->
				<div
					:class="{
					    'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400': (($store.confirmModal?.variant ??
					        @js($variant)) === 'warning'),
					    'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400': (($store.confirmModal?.variant ??
					        @js($variant)) === 'danger'),
					    'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400': (($store.confirmModal?.variant ??
					        @js($variant)) === 'info')
					}"
					class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center">
					<template x-if="($store.confirmModal?.variant ?? @js($variant)) === 'warning'">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4v2m0 0v2m-6-8a9 9 0 1118 0 9 9 0 01-18 0z" />
						</svg>
					</template>
					<template x-if="($store.confirmModal?.variant ?? @js($variant)) === 'danger'">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</template>
					<template x-if="($store.confirmModal?.variant ?? @js($variant)) === 'info'">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</template>
				</div>

				<div class="flex-1">
					<h3 class="text-lg font-bold text-gray-900 dark:text-white"
						x-text="$store.confirmModal?.title || @js($title)"></h3>
					<p class="mt-2 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line leading-relaxed"
						x-text="$store.confirmModal?.message || @js($message)"></p>
				</div>
			</div>

			<button type="button" @click="$store.confirmModal.close()"
				class="text-gray-400 hover:text-gray-600 dark:hover:text-white transition flex-shrink-0">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<!-- Footer -->
		<div class="p-6 flex justify-end gap-3 bg-gray-50/50 dark:bg-gray-800/50">
			<button type="button" @click="$store.confirmModal.close()"
				class="px-4 py-2 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
				Cancel
			</button>
			<button type="button" @click="$store.confirmModal.confirm()"
				class="px-6 py-2 rounded-lg text-sm font-semibold text-white transition shadow-lg hover:shadow-xl"
				:class="{
				    'bg-amber-500 hover:bg-amber-600': (($store.confirmModal?.variant ??
				        @js($variant)) === 'warning'),
				    'bg-red-600 hover:bg-red-700': (($store.confirmModal?.variant ?? @js($variant)) === 'danger'),
				    'bg-blue-600 hover:bg-blue-700': (($store.confirmModal?.variant ??
				        @js($variant)) === 'info')
				}">
				<span x-text="$store.confirmModal?.confirmText ?? @js($confirmText)"></span>
			</button>
		</div>
	</div>
</div>
