@extends('backend.layouts.app')

@section('title', 'Create Campaign')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Create Campaign" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div
			class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">New Campaign</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Create campaign targeting, budget, and scheduling details for
					influencer applications.</p>
			</div>
			<div class="flex flex-wrap items-center gap-2">
				<a href="{{ route('dashboard.campaigns.standard') }}"
					class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Back to Campaigns
				</a>
			</div>
		</div>

		<form action="{{ route('dashboard.campaigns.store') }}" method="POST" novalidate class="space-y-6 p-5">
			@csrf

			@include('backend.pages.campaigns._alerts')

			@include('backend.pages.campaigns._form')

			<div
				class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
				<a href="{{ route('dashboard.campaigns.standard') }}"
					class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Cancel
				</a>
				<button type="submit"
					class="inline-flex items-center justify-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
					Create Campaign
				</button>
			</div>
		</form>
	</div>
@endsection
