@extends('backend.layouts.app')

@section('title', 'Site Settings')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Site Settings" />

	<div class="space-y-6" x-data="settingsTabs(@js($activeTab))">
		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settings Center</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage branding, email setup, platform charges, footer pages, and recovery from one place.</p>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div class="border-b border-gray-200 p-4 dark:border-gray-800">
				<div class="flex flex-wrap gap-2">
					<button type="button" @click="setTab('branding')" :class="tab === 'branding' ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="rounded-lg px-4 py-2 text-sm font-medium transition">Branding</button>
					<button type="button" @click="setTab('email')" :class="tab === 'email' ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="rounded-lg px-4 py-2 text-sm font-medium transition">Email Setup</button>
					<button type="button" @click="setTab('platform')" :class="tab === 'platform' ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="rounded-lg px-4 py-2 text-sm font-medium transition">Platform Charge</button>
					<button type="button" @click="setTab('footer')" :class="tab === 'footer' ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="rounded-lg px-4 py-2 text-sm font-medium transition">Footer Pages</button>
					<button type="button" @click="setTab('recovery')" :class="tab === 'recovery' ? 'bg-gray-900 text-white dark:bg-gray-100 dark:text-gray-900' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'" class="rounded-lg px-4 py-2 text-sm font-medium transition">Recovery</button>
				</div>
			</div>

			<div class="p-5">
				<div x-show="tab === 'branding'" x-cloak>
					@php
						$initialLogoLight = $brandingSettings['logo_light'] ?? null;
						$initialLogoDark = $brandingSettings['logo_dark'] ?? null;
						$initialFavicon = $brandingSettings['favicon'] ?? null;
					@endphp
					<form action="{{ route('dashboard.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="settingsBrandingUploader({ logoLight: @js($initialLogoLight), logoDark: @js($initialLogoDark), favicon: @js($initialFavicon) })">
						@csrf
						<input type="hidden" name="section" value="branding">

						<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name *</label>
								<input name="site_name" type="text" value="{{ old('site_name', $brandingSettings['site_name']) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
								@error('site_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Tagline</label>
								<input name="tagline" type="text" value="{{ old('tagline', $brandingSettings['tagline']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
								@error('tagline')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
							</div>
						</div>

						<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
							<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
								<div class="mb-3 flex items-start justify-between gap-3">
									<div>
										<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Light Logo</p>
										<p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF, SVG</p>
									</div>
									<button type="button" @click="clearLight()" class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Clear</button>
								</div>
								<label for="logo_light" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
									<template x-if="logoLightPreview"><img :src="logoLightPreview" alt="Light logo" class="mb-3 h-16 w-full max-w-55 rounded object-contain"></template>
									<template x-if="!logoLightPreview"><div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800"><x-icons.image class="h-6 w-6 text-gray-400" /></div></template>
									<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Light Logo</p>
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 6MB</p>
									<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="logoLightName" x-text="logoLightName"></p>
								</label>
								<input id="logo_light" x-ref="lightInput" name="logo_light" type="file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml" class="hidden" @change="onLightSelected($event)">
								@error('logo_light')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
							</div>

							<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
								<div class="mb-3 flex items-start justify-between gap-3">
									<div>
										<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Dark Logo</p>
										<p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, WEBP, AVIF, GIF, SVG</p>
									</div>
									<button type="button" @click="clearDark()" class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Clear</button>
								</div>
								<label for="logo_dark" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
									<template x-if="logoDarkPreview"><img :src="logoDarkPreview" alt="Dark logo" class="mb-3 h-16 w-full max-w-55 rounded object-contain"></template>
									<template x-if="!logoDarkPreview"><div class="mb-3 flex h-16 w-16 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800"><x-icons.image class="h-6 w-6 text-gray-400" /></div></template>
									<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Dark Logo</p>
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 6MB</p>
									<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="logoDarkName" x-text="logoDarkName"></p>
								</label>
								<input id="logo_dark" x-ref="darkInput" name="logo_dark" type="file" accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml" class="hidden" @change="onDarkSelected($event)">
								@error('logo_dark')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
							</div>

							<div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
								<div class="mb-3 flex items-start justify-between gap-3">
									<div>
										<p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Favicon</p>
										<p class="text-xs text-gray-500 dark:text-gray-400">PNG, ICO, SVG</p>
									</div>
									<button type="button" @click="clearFavicon()" class="text-xs font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Clear</button>
								</div>
								<label for="favicon" class="flex cursor-pointer flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center transition hover:border-gray-400 dark:border-gray-600 dark:hover:border-gray-500">
									<template x-if="faviconPreview"><img :src="faviconPreview" alt="Favicon" class="mb-3 h-12 w-12 rounded object-cover"></template>
									<template x-if="!faviconPreview"><div class="mb-3 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800"><x-icons.image class="h-5 w-5 text-gray-400" /></div></template>
									<p class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Favicon</p>
									<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">up to 2MB</p>
									<p class="mt-2 text-xs text-gray-400 dark:text-gray-500" x-show="faviconName" x-text="faviconName"></p>
								</label>
								<input id="favicon" x-ref="faviconInput" name="favicon" type="file" accept="image/png,image/x-icon,image/svg+xml" class="hidden" @change="onFaviconSelected($event)">
								@error('favicon')<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
							</div>
						</div>

						<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
							<a href="{{ route('dashboard.settings.index', ['tab' => 'branding']) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
							<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Save Branding</button>
						</div>
					</form>
				</div>

				<div x-show="tab === 'email'" x-cloak>
					<form action="{{ route('dashboard.settings.update') }}" method="POST" class="space-y-6">
						@csrf
						<input type="hidden" name="section" value="email">
						<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Mailer</label>
								<input name="mailer" type="text" value="{{ old('mailer', $emailSettings['mailer']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Host</label>
								<input name="host" type="text" value="{{ old('host', $emailSettings['host']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">SMTP Port</label>
								<input name="port" type="number" value="{{ old('port', $emailSettings['port']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Encryption</label>
								<input name="encryption" type="text" value="{{ old('encryption', $emailSettings['encryption']) }}" placeholder="tls or ssl" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
								<input name="username" type="text" value="{{ old('username', $emailSettings['username']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
								<input name="password" type="password" value="{{ old('password', $emailSettings['password']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">From Name</label>
								<input name="from_name" type="text" value="{{ old('from_name', $emailSettings['from_name']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">From Address</label>
								<input name="from_address" type="email" value="{{ old('from_address', $emailSettings['from_address']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
						</div>
						<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
							<a href="{{ route('dashboard.settings.index', ['tab' => 'email']) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
							<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Save Email Settings</button>
						</div>
					</form>
				</div>

				<div x-show="tab === 'platform'" x-cloak>
					<form action="{{ route('dashboard.settings.update') }}" method="POST" class="space-y-6">
						@csrf
						<input type="hidden" name="section" value="platform">
						<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Charge Type</label>
								<select name="charge_type" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
									<option value="percentage" @selected(old('charge_type', $platformSettings['charge_type']) === 'percentage')>Percentage</option>
									<option value="fixed" @selected(old('charge_type', $platformSettings['charge_type']) === 'fixed')>Fixed Amount</option>
								</select>
							</div>
							<div>
								<label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Charge Value</label>
								<input name="charge_value" type="number" step="0.01" min="0" value="{{ old('charge_value', $platformSettings['charge_value']) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							</div>
						</div>
						<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
							<a href="{{ route('dashboard.settings.index', ['tab' => 'platform']) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
							<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Save Platform Charge</button>
						</div>
					</form>
				</div>

				<div x-show="tab === 'footer'" x-cloak>
					<form action="{{ route('dashboard.settings.update') }}" method="POST" class="space-y-6">
						@csrf
						<input type="hidden" name="section" value="footer">
						@if($pages->count() > 0)
							<div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-800/30 dark:bg-amber-900/20">
								<p class="text-sm font-medium text-amber-900 dark:text-amber-300">Drag pages to set footer order. Only checked pages will be shown.</p>
							</div>
							<div id="sortable-list" class="space-y-2">
								@foreach($pages as $page)
									<div class="sortable-item rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50" data-id="{{ $page->id }}">
										<div class="flex items-center gap-4">
											<div class="flex h-8 w-8 items-center justify-center rounded text-gray-400"><i class="fas fa-grip-vertical text-sm"></i></div>
											<input type="checkbox" id="page_{{ $page->id }}" name="footer_pages[]" value="{{ $page->id }}" {{ in_array($page->id, $footerPages ?? []) ? 'checked' : '' }} class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700">
											<div class="min-w-0 flex-1">
												<label for="page_{{ $page->id }}" class="cursor-pointer">
													<span class="font-semibold text-gray-900 dark:text-white">{{ $page->title }}</span>
													<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $page->content_summary ?? 'No description' }}</p>
												</label>
											</div>
										</div>
									</div>
								@endforeach
							</div>
							<input type="hidden" id="footer_pages_order" name="footer_pages_order" value="">
						@else
							<p class="text-sm text-gray-500 dark:text-gray-400">No static pages available.</p>
						@endif
						<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
							<a href="{{ route('dashboard.settings.index', ['tab' => 'footer']) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
							<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Save Footer Pages</button>
						</div>
					</form>
				</div>

				<div x-show="tab === 'recovery'" x-cloak>
					<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
						<div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
							<h4 class="text-base font-semibold text-gray-900 dark:text-white">Deleted Records Recovery</h4>
							<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">All recoverable deleted records across the platform are listed here.</p>
						</div>
						<div class="overflow-x-auto">
							<table class="min-w-full">
								<thead class="bg-gray-50 dark:bg-gray-800/50">
									<tr>
										<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Type</th>
										<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Title</th>
										<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Identifier</th>
										<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Deleted At</th>
										<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-400">Action</th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-800">
									@forelse ($recoveryItems as $item)
										<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
											<td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item['type_label'] }}</td>
											<td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $item['title'] }}</td>
											<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item['identifier'] }}</td>
											<td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item['deleted_at']?->format('M d, Y h:i A') ?? '-' }}</td>
											<td class="px-4 py-3 text-right">
												<form action="{{ route('dashboard.settings.recovery.restore', ['type' => $item['type'], 'id' => $item['id']]) }}" method="POST" class="inline-block">
													@csrf
													<button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Restore</button>
												</form>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No deleted records available for recovery.</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
	@push('scripts')
		<script>
			function settingsTabs(initialTab) {
				return {
					tab: initialTab,
					setTab(nextTab) {
						this.tab = nextTab;
						const url = new URL(window.location.href);
						url.searchParams.set('tab', nextTab);
						window.history.replaceState({}, '', url.toString());
					}
				};
			}

			function settingsBrandingUploader(config) {
				return {
					logoLightPreview: config.logoLight || null,
					logoDarkPreview: config.logoDark || null,
					faviconPreview: config.favicon || null,
					logoLightName: '',
					logoDarkName: '',
					faviconName: '',
					onLightSelected(event) {
						const file = event.target.files[0];
						if (!file) return;
						this.logoLightName = file.name;
						this.logoLightPreview = URL.createObjectURL(file);
					},
					onDarkSelected(event) {
						const file = event.target.files[0];
						if (!file) return;
						this.logoDarkName = file.name;
						this.logoDarkPreview = URL.createObjectURL(file);
					},
					onFaviconSelected(event) {
						const file = event.target.files[0];
						if (!file) return;
						this.faviconName = file.name;
						this.faviconPreview = URL.createObjectURL(file);
					},
					clearLight() {
						this.logoLightName = '';
						this.logoLightPreview = config.logoLight || null;
						if (this.$refs.lightInput) this.$refs.lightInput.value = '';
					},
					clearDark() {
						this.logoDarkName = '';
						this.logoDarkPreview = config.logoDark || null;
						if (this.$refs.darkInput) this.$refs.darkInput.value = '';
					},
					clearFavicon() {
						this.faviconName = '';
						this.faviconPreview = config.favicon || null;
						if (this.$refs.faviconInput) this.$refs.faviconInput.value = '';
					}
				};
			}

			document.addEventListener('DOMContentLoaded', function() {
				const sortableList = document.getElementById('sortable-list');
				if (!sortableList) return;

				Sortable.create(sortableList, {
					animation: 150,
					ghostClass: 'opacity-50 bg-indigo-100 dark:bg-indigo-900/30',
					handle: '.fa-grip-vertical',
					onEnd: function() {
						const items = document.querySelectorAll('.sortable-item');
						const order = Array.from(items).map(item => item.dataset.id);
						const orderInput = document.getElementById('footer_pages_order');
						if (orderInput) {
							orderInput.value = JSON.stringify(order);
						}
					}
				});
			});
		</script>
	@endpush
@endsection
