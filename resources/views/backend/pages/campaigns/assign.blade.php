@extends('backend.layouts.app')

@section('title', 'Assign Campaign to Influencers')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Assign Campaign to Influencers" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div
			class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assign Campaign to Influencers</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
					Select an active campaign and assign one or more influencers to participate in it.
				</p>
			</div>
			<a href="{{ route('dashboard.campaigns.standard') }}"
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
						<select id="campaign_id" name="campaign_id" x-model="selectedCampaignId" @change="updateCampaignDetails()" required
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="">-- Choose an Active Campaign --</option>
							@foreach ($campaigns as $campaign)
								<option value="{{ $campaign->id }}" data-title="{{ $campaign->title }}"
									data-description="{{ $campaign->description ?? '' }}" data-type="{{ $campaign->campaign_type ?? 'Standard' }}"
									data-status="{{ $campaign->status ?? 'Active' }}"
									data-start="{{ $campaign->start_date?->format('M d, Y') ?? 'N/A' }}"
									data-end="{{ $campaign->end_date?->format('M d, Y') ?? 'N/A' }}"
									data-budget-min="{{ $campaign->budget_min ?? '0' }}" data-budget-max="{{ $campaign->budget_max ?? '0' }}"
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
										x-text="campaignDetails.assignedCount || '0'"></span> influencer(s)</p>
							</div>
						</div>
					</div>

					<!-- Select Influencers with Chips -->
					<div>
						<label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Select Influencers <span class="text-red-500">*</span>
						</label>

						<div class="space-y-3">
							<!-- Search and Filter -->
							<input type="search" @input="filterInfluencers($event)" placeholder="Search influencers by name or email..."
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />

							<!-- Influencer Chips Selection -->
							<div
								class="flex flex-wrap gap-2 p-3 min-h-[3rem] rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
								<template x-for="influencer in filteredInfluencers" :key="influencer.id">
									<button type="button" @click.prevent="toggleInfluencer(influencer)"
										@keydown.enter.prevent="toggleInfluencer(influencer)"
										:class="isInfluencerSelected(influencer.id) ?
										    'bg-indigo-600 text-white dark:bg-indigo-500' :
										    'bg-white text-gray-700 border border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'"
										class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
										<span x-text="influencer.display_name"></span>
										<svg v-if="isInfluencerSelected(influencer.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
												clip-rule="evenodd" />
										</svg>
									</button>
								</template>

								<template x-if="filteredInfluencers.length === 0">
									<p class="text-sm text-gray-500 dark:text-gray-400">No influencers match your search.</p>
								</template>
							</div>

							<!-- Selected Influencers Summary -->
							<div x-show="selectedInfluencerIds.length > 0" x-cloak class="text-sm text-gray-600 dark:text-gray-400">
								Selected: <span class="font-medium text-gray-900 dark:text-white"
									x-text="`${selectedInfluencerIds.length} influencer(s)`"></span>
							</div>

							@error('influencer_ids')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>

						<!-- Hidden input for selected influencer IDs -->
						<template x-for="influencerId in selectedInfluencerIds" :key="`hidden-influencer-${influencerId}`">
							<input type="hidden" name="influencer_ids[]" :value="influencerId" />
						</template>
					</div>

					<!-- Form Actions -->
					<div
						class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
						<a href="{{ route('dashboard.campaigns.standard') }}"
							class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Cancel
						</a>
						<button type="submit" :disabled="selectedInfluencerIds.length === 0"
							:class="selectedInfluencerIds.length === 0 ? 'opacity-50 cursor-not-allowed bg-gray-400' :
							    'hover:bg-indigo-700 bg-indigo-600'"
							class="inline-flex items-center justify-center rounded-lg px-6 py-2 text-sm font-semibold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition">
							Assign &nbsp; <span x-text="selectedInfluencerIds.length || '0'"></span> &nbsp; Influencer<span
								x-text="selectedInfluencerIds.length === 1 ? '' : 's'"></span> &nbsp; to Campaign
						</button>
					</div>
				</div>

				<!-- Sidebar - Info Cards -->
				<div class="space-y-4">
					<!-- Already Assigned Influencers Card -->
					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800" x-show="selectedCampaignId" x-cloak>
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Already Assigned Influencers</h4>
						<div x-show="assignedInfluencers.length > 0" x-cloak class="space-y-2">
							<p class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="assignedInfluencers.length"></p>
							<div class="mt-3 space-y-2 max-h-48 overflow-y-auto">
								<template x-for="influencer in assignedInfluencers" :key="influencer.id">
									<div class="p-2 rounded bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
									<div class="flex items-start justify-between gap-2">
										<div class="flex-1">
											<p class="text-xs font-medium text-gray-900 dark:text-white" x-text="influencer.display_name"></p>
											<p class="text-xs text-gray-600 dark:text-gray-400" x-text="influencer.email"></p>
										</div>
										<span :class="influencer.status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : influencer.status === 'declined' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200'" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium whitespace-nowrap" x-text="influencer.status?.charAt(0).toUpperCase() + influencer.status?.slice(1)"></span>
									</div>
									</div>
								</template>
							</div>
						</div>
						<div x-show="assignedInfluencers.length === 0" x-cloak>
							<p class="text-sm text-gray-600 dark:text-gray-400">No influencers assigned yet</p>
						</div>
					</div>

					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Active Influencers</h4>
						<p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $activeInfluencersCount }}</p>
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
				<div
					class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 p-4 hover:shadow-md transition">
					<div class="flex items-start justify-between mb-3">
						<h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">{{ $campaign->title }}</h4>
						<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
							:class="'{{ $campaign->is_active ? 'Active' : 'Inactive' }}'
							=== 'Active' ?
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
								{{ count($campaign->applications) }} influencer(s)
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
				<div
					class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 p-8 col-span-full">
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
				assignedInfluencers: [],
				assignedInfluencerIds: [],
				selectedInfluencerIds: [],
                filteredInfluencers: [],
                allInfluencers: @js(
    $influencers->map(
        fn($c) => [
            'id' => $c->id,
            'display_name' => $c->display_name,
            'email' => $c->user?->email,
            'phone' => $c->user?->phone,
        ],
    ),
),
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
							type: '',
							status: '',
							startDate: '',
							endDate: '',
							budgetMin: '0',
							budgetMax: '0',
							currency: 'USD',
							description: '',
							assignedCount: 0
						};
						this.filteredInfluencers = [];
						this.assignedInfluencers = [];
						this.assignedInfluencerIds = [];
					}
				},
				updateAssignedCount() {
					if (!this.selectedCampaignId) return;
					fetch(`/dashboard/campaigns/${this.selectedCampaignId}/assigned-influencers`)
						.then(r => r.json())
						.then(data => {
							this.assignedInfluencers = data || [];
							this.assignedInfluencerIds = (data || []).map(influencer => influencer.id);
							this.campaignDetails.assignedCount = data.length || 0;
							// Update available influencers after fetching assigned ones
							this.updateAvailableInfluencers();
						})
						.catch(() => {
							this.assignedInfluencers = [];
							this.assignedInfluencerIds = [];
							this.campaignDetails.assignedCount = 0;
							this.updateAvailableInfluencers();
						});
				},
				updateAvailableInfluencers() {
					// Filter out already assigned influencers from the available list
					this.filteredInfluencers = this.allInfluencers.filter(influencer => 
						!this.assignedInfluencerIds.includes(influencer.id)
					);
				},
				filterInfluencers(event) {
					const searchTerm = event.target.value.toLowerCase();
					this.filteredInfluencers = this.allInfluencers.filter(influencer => {
						// Exclude already assigned influencers
						if (this.assignedInfluencerIds.includes(influencer.id)) {
							return false;
						}
						// Match search term
						return influencer.display_name.toLowerCase().includes(searchTerm) ||
							influencer.email.toLowerCase().includes(searchTerm);
					});
				},
				toggleInfluencer(influencer) {
					const index = this.selectedInfluencerIds.indexOf(influencer.id);
					if (index > -1) {
						this.selectedInfluencerIds.splice(index, 1);
					} else {
						this.selectedInfluencerIds.push(influencer.id);
					}
				},
				isInfluencerSelected(influencerId) {
					return this.selectedInfluencerIds.includes(influencerId);
				}
			};
		}
	</script>
@endsection
