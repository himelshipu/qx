<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		@php
			$siteName = \App\Models\Setting::get('branding.site_name', config('app.name', 'QX Marketplace'));
			$explicitTitle = trim((string) $__env->yieldContent('title'));
			if ($explicitTitle === '' && isset($title)) {
				$explicitTitle = trim((string) $title);
			}

			$humanize = static function (string $value): string {
				return ucwords(str_replace(['-', '_'], ' ', $value));
			};

			$deriveFromRoute = static function (string $routeName) use ($humanize): string {
				if ($routeName === '') {
					return '';
				}

				$parts = array_values(array_filter(explode('.', $routeName), fn ($part) => !in_array($part, ['frontend', 'dashboard', 'api'], true)));
				if (empty($parts)) {
					return '';
				}

				$action = end($parts);
				$resource = count($parts) >= 2 ? $parts[count($parts) - 2] : $parts[0];
				$actionMap = [
					'index' => '',
					'show' => '',
					'create' => 'Create ',
					'store' => 'Create ',
					'edit' => 'Edit ',
					'update' => 'Update ',
					'destroy' => 'Delete ',
				];

				if (array_key_exists($action, $actionMap)) {
					return trim($actionMap[$action] . $humanize($resource));
				}

				return implode(' - ', array_map($humanize, $parts));
			};

			$routeName = (string) (\Illuminate\Support\Facades\Route::currentRouteName() ?? '');
			$pageTitle = $explicitTitle !== '' ? $explicitTitle : $deriveFromRoute($routeName);
			if ($pageTitle === '') {
				$pageTitle = 'Home';
			}
		@endphp

		<title>{{ $pageTitle }} | {{ $siteName }}</title>

		<!-- Apply theme before CSS loads -->
		<script>
			(function() {
				let savedTheme = null;
				try {
					savedTheme = localStorage.getItem('theme');
				} catch (e) {}

				const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
				const theme = savedTheme || systemTheme;
				const isDark = theme === 'dark';

				document.documentElement.classList.toggle('dark', isDark);
				document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
			})();
		</script>

		<style>
			[x-cloak] {
				display: none !important;
			}
		</style>

		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>

	<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
		<div class="min-h-screen flex flex-col">
			<!-- Header/Navigation -->
			@auth
				<x-frontend.navigation.auth-header />
			@else
				<x-frontend.navigation.header />
			@endauth

			<!-- Main Content -->
			<main class="flex-1 w-full">
				@yield('content')
			</main>

			<!-- Footer -->
			<x-frontend.navigation.footer />
		</div>

		<!-- Toast Container -->
		<div x-data="window.Alpine.store('toast')" class="fixed top-4 right-4 z-50 flex flex-col gap-2">
			<template x-for="t in toasts" :key="t.id">
				<div class="px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 min-w-75 max-w-md animate-slide-in"
					:class="{
					    'success': 'bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-100',
					    'error': 'bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-100',
					    'info': 'bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-100',
					    'warning': 'bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-100',
					} [t.type]">
					<div class="shrink-0">
						<template x-if="t.type === 'success'">
							<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'error'">
							<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'info'">
							<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
						</template>
						<template x-if="t.type === 'warning'">
							<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4v2m0 0v2m0-6v-2m0 6v2m0-6V9m0 0V7m0 0V5m0 6v-2m0 0V5m0 0V3m0 0h2m0 0h2m-2 0h-2" />
							</svg>
						</template>
					</div>
					<div>
						<p x-text="t.message"></p>
					</div>
				</div>
			</template>
		</div>
	</body>

</html>
