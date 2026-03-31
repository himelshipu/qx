@extends('backend.layouts.app')

@section('title', 'Assign Campaign to Creators')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Assign Campaign to Creators" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Campaign to Creators</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
					Select an active campaign and assign one or more creators (influencers) to participate in it.
				</p>
			</div>
			<a href="{{ route('dashboard.campaigns.index') }}"
				class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
				Back to Campaigns
			</a>
		</div>

		<form action="{{ route('dashboard.campaigns.assign.store') }}" method="POST" class="space-y-6 p-5"
			x-data="campaignAssignForm()">
			@csrf

			<!-- Campaign Selection Section -->
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="lg:col-span-2 space-y-5">
					<!-- Select Campaign -->
					<div>
						<label for="campaign_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Select Campaign <span class="text-red-500">*</span>
						</label>
						<select id="campaign_id" name="campaign_id" x-model="selectedCampaignId"
							@change="updateCampaignDetails()"
							required
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="">-- Choose an Active Campaign --</option>
							@foreach ($campaigns as $campaign)
								<option value="{{ $campaign->id }}"
									data-title="{{ $campaign->title }}"
									data-description="{{ $campaign->description ?? '' }}"
									data-type="{{ $campaign->campaign_type ?? 'Standard' }}"
									data-status="{{ $campaign->status ?? 'Active' }}"
									data-start="{{ $campaign->start_date?->format('M d, Y') ?? 'N/A' }}"
									data-end="{{ $campaign->end_date?->format('M d, Y') ?? 'N/A' }}"
									data-budget-min="{{ $campaign->budget_min ?? '0' }}"
									data-budget-max="{{ $campaign->budget_max ?? '0' }}"
									data-currency="{{ $campaign->currency ?? 'USD' }}">
									{{ $campaign->title }}
								</option>
							@endforeach
						</select>
						@error('campaign_id')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Campaign Details Display -->
					<div x-show="selectedCampaignId" x-cloak
						class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Campaign Details</h4>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
							<div>
								<p class="text-gray-600 dark:text-gray-400">Type</p>
								<p class="font-medium text-gray-900 dark:text-white" x-text="campaignDetails.type"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Status</p>
								<p class="font-medium text-gray-900 dark:text-white" x-text="campaignDetails.status"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Duration</p>
								<p class="font-medium text-gray-900 dark:text-white"
									x-text="`${campaignDetails.startDate} to ${campaignDetails.endDate}`"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Budget Range</p>
								<p class="font-medium text-gray-900 dark:text-white"
									x-text="`${campaignDetails.currency} ${campaignDetails.budgetMin} - ${campaignDetails.budgetMax}`"></p>
							</div>
							<div class="md:col-span-2">
								<p class="text-gray-600 dark:text-gray-400">Description</p>
								<p class="font-medium text-gray-900 dark:text-white line-clamp-2"
									x-text="campaignDetails.description || 'No description provided'"></p>
							</div>
							<div class="md:col-span-2">
								<p class="text-xs text-amber-600 dark:text-amber-400">Already Assigned: <span
										x-text="campaignDetails.assignedCount || '0'"></span> creator(s)</p>
							</div>
						</div>
					</div>

					<!-- Select Creators with Chips -->
					<div>
						<label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Select Creators <span class="text-red-500">*</span>
						</label>

						<div x-data="creatorChipsSelector()" class="space-y-3">
							<!-- Search and Filter -->
							<input type="search" @input="filterCreators($event)" placeholder="Search creators by name or email..."
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />

							<!-- Creator Chips Selection -->
							<div class="flex flex-wrap gap-2 p-3 min-h-[3rem] rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
								<template x-for="creator in filteredCreators" :key="creator.id">
									<button type="button" @click.prevent="toggleCreator(creator)"
										@keydown.enter.prevent="toggleCreator(creator)"
										:class="isCreatorSelected(creator.id) ?
											'bg-indigo-600 text-white dark:bg-indigo-500' :
											'bg-white text-gray-700 border border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'"
										class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
										<span x-text="creator.display_name"></span>
										<svg v-if="isCreatorSelected(creator.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
												clip-rule="evenodd" />
										</svg>
									</button>
								</template>

								<template x-if="filteredCreators.length === 0">
									<p class="text-sm text-gray-500 dark:text-gray-400">No creators match your search.</p>
								</template>
							</div>

							<!-- Selected Creators Summary -->
							<div x-show="selectedCreatorIds.length > 0" x-cloak
								class="text-sm text-gray-600 dark:text-gray-400">
								Selected: <span class="font-medium text-gray-900 dark:text-white"
									x-text="`${selectedCreatorIds.length} creator(s)`"></span>
							</div>

							@error('creator_ids')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Hidden input for selected creator IDs -->
						<template x-for="creatorId in selectedCreatorIds" :key="`hidden-creator-${creatorId}`">
							<input type="hidden" name="creator_ids[]" :value="creatorId" />
						</template>
					</div>

					<!-- Form Actions -->
					<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
						<a href="{{ route('dashboard.campaigns.index') }}"
							class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Cancel
						</a>
						<button type="submit"
							class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
							Assign Creators to Campaign
						</button>
					</div>
				</div>

				<!-- Sidebar - Info Cards -->
				<div class="space-y-4">
					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Active Creators</h4>
						<p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $activeCreatorsCount }}</p>
						<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Ready to assign</p>
					</div>

					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Active Campaigns</h4>
						<p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeCampaignsCount }}</p>
						<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Available for assignment</p>
					</div>
				</div>
			</div>
		</form>
	</div>

	<!-- Latest Active Campaigns Section -->
	<div class="mt-6 space-y-4">
		<div>
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Campaigns Overview</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A quick look at currently running campaigns</p>
		</div>

		<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
			@forelse ($latestCampaigns as $campaign)
				<div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 p-4 hover:shadow-md transition">
					<div class="flex items-start justify-between mb-3">
						<h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $campaign->title }}</h4>
						<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
							:class="'{{ $campaign->is_active ? 'Active' : 'Inactive' }}' === 'Active' ?
								'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
								'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'">
							{{ $campaign->is_active ? 'Active' : 'Inactive' }}
						</span>
					</div>

					<p class="text-xs text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
						{{ $campaign->description ?? 'No description' }}
					</p>

					<div class="space-y-2 text-sm">
						<div class="flex justify-between">
							<span class="text-gray-600 dark:text-gray-400">Duration:</span>
							<span class="font-medium text-gray-900 dark:text-white">
								{{ $campaign->start_date?->format('M d') ?? 'N/A' }} - {{ $campaign->end_date?->format('M d, Y') ?? 'N/A' }}
							</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600 dark:text-gray-400">Applicants:</span>
							<span class="font-medium text-indigo-600 dark:text-indigo-400">
								{{ count($campaign->applications) }} creator(s)
							</span>
						</div>
						<div class="flex justify-between">
							<span class="text-gray-600 dark:text-gray-400">Status:</span>
							<span class="font-medium text-gray-900 dark:text-white">{{ $campaign->status ?? 'Active' }}</span>
						</div>
					</div>

					<a href="{{ route('dashboard.campaigns.view', $campaign->id) }}"
						class="mt-3 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
						View Details →
					</a>
				</div>
			@empty
				<div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 p-8 col-span-full">
					<p class="text-center text-gray-500 dark:text-gray-400">No active campaigns available at the moment.</p>
				</div>
			@endforelse
		</div>
	</div>

	<script>
		function campaignAssignForm() {
			return {
				selectedCampaignId: '',
				campaignDetails: {
					type: '',
					status: '',
					startDate: '',
					endDate: '',
					budgetMin: '0',
					budgetMax: '0',
					currency: 'USD',
					description: '',
					assignedCount: 0
				},
				updateCampaignDetails() {
					const select = document.getElementById('campaign_id');
					const selected = select.options[select.selectedIndex];

					if (selected.value) {
						this.campaignDetails = {
							type: selected.dataset.type,
							status: selected.dataset.status,
							startDate: selected.dataset.start,
							endDate: selected.dataset.end,
							budgetMin: selected.dataset.budgetMin,
							budgetMax: selected.dataset.budgetMax,
							currency: selected.dataset.currency,
							description: selected.dataset.description
						};
						this.updateAssignedCount();
					} else {
						this.campaignDetails = {
							type: '', status: '', startDate: '', endDate: '',
							budgetMin: '0', budgetMax: '0', currency: 'USD',
							description: '', assignedCount: 0
						};
					}
				},
				updateAssignedCount() {
					if (!this.selectedCampaignId) return;
					fetch(`/dashboard/campaigns/${this.selectedCampaignId}/assigned-creators`)
						.then(r => r.json())
						.then(data => {
							this.campaignDetails.assignedCount = data.length || 0;
						})
						.catch(() => {
							this.campaignDetails.assignedCount = 0;
						});
				}
			};
		}

		function creatorChipsSelector() {
			return {
				selectedCreatorIds: [],
				filteredCreators: @js($creators->map(fn($c) => [
					'id' => $c->id,
					'display_name' => $c->display_name,
					'email' => $c->user?->email,
					'phone' => $c->user?->phone
				])),
				allCreators: @js($creators->map(fn($c) => [
					'id' => $c->id,
					'display_name' => $c->display_name,
					'email' => $c->user?->email,
					'phone' => $c->user?->phone
				])),
				filterCreators(event) {
					const searchTerm = event.target.value.toLowerCase();
					this.filteredCreators = this.allCreators.filter(creator =>
						creator.display_name.toLowerCase().includes(searchTerm) ||
						creator.email.toLowerCase().includes(searchTerm)
					);
				},
				toggleCreator(creator) {
					const index = this.selectedCreatorIds.indexOf(creator.id);
					if (index > -1) {
						this.selectedCreatorIds.splice(index, 1);
					} else {
						this.selectedCreatorIds.push(creator.id);
					}
				},
				isCreatorSelected(creatorId) {
					return this.selectedCreatorIds.includes(creatorId);
				}
			};
		}
	</script>
@endsection
