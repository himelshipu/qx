@extends('backend.layouts.app')

@section('title', 'Assign Influencers to Campaign')

@section('content')
	<x-backend.shell.breadcrumb :links="[
	    ['label' => 'Campaigns', 'url' => route('dashboard.campaigns.index')],
	    ['label' => $campaign->title, 'url' => route('dashboard.campaigns.influencers.index', $campaign)],
	]" pageTitle="Assign Influencers" />

	<form action="{{ route('dashboard.campaigns.influencers.store', $campaign) }}" method="POST" class="space-y-6">
		@csrf

		<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
			<div
				class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-5 dark:border-gray-800">
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Influencers</h3>
					<p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Select influencers to assign to "{{ $campaign->title }}"</p>
				</div>
				<a href="{{ route('dashboard.campaigns.influencers.index', $campaign) }}"
					class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
					Back
				</a>
			</div>

			<div class="mt-5">
				<label class="mb-3 block text-sm font-medium text-gray-900 dark:text-white">
					Select Influencers <span class="text-red-500">*</span>
				</label>
				<div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
					@forelse ($creators as $creator)
						<label
							class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 transition hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800/50 cursor-pointer"
							@if (in_array($creator->id, $assignedCreatorIds)) style="background-color: rgba(59, 130, 246, 0.05);" @endif>
							<input type="checkbox" name="creator_ids[]" value="{{ $creator->id }}"
								@if (in_array($creator->id, $assignedCreatorIds)) checked disabled @endif class="mt-1 rounded border-gray-300 text-blue-600">
							<div class="flex-1">
								<p class="font-medium text-gray-900 dark:text-white">{{ $creator->display_name }}</p>
								<p class="text-sm text-gray-600 dark:text-gray-400">{{ $creator->user->email }}</p>
								@if (in_array($creator->id, $assignedCreatorIds))
									<p class="mt-1 text-xs text-yellow-600 dark:text-yellow-400">Already assigned</p>
								@endif
							</div>
						</label>
					@empty
						<p class="text-sm text-gray-600 dark:text-gray-400 col-span-full">No active influencers available.</p>
					@endforelse
				</div>

				@error('creator_ids')
					<p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<div class="flex gap-3">
			<button type="submit"
				class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 font-medium text-white transition hover:bg-blue-700">
				<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
				</svg>
				Assign Influencers
			</button>
			<a href="{{ route('dashboard.campaigns.influencers.index', $campaign) }}"
				class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-6 py-2.5 font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
				Cancel
			</a>
		</div>
	</form>
@endsection
