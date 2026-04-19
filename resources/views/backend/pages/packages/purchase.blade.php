@extends('backend.layouts.app')

@section('title', 'Purchase Package for Brands')

@section('content')
	<x-backend.shell.breadcrumb pageTitle="Purchase Package for Brands" />

	<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
		<div class="flex flex-col gap-4 border-b border-gray-200 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-800">
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Package for Brands</h3>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
					Select an available package and purchase it for one or more brands.
				</p>
			</div>
			<a href="{{ route('dashboard.packages.index') }}"
				class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
				Back to Packages
			</a>
		</div>

		<form action="{{ route('dashboard.packages.purchase.store') }}" method="POST" class="space-y-6 p-5"
			x-data="packagePurchaseForm()">
			@csrf

			<!-- Package Selection Section -->
			<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
				<div class="lg:col-span-2 space-y-5">
					<!-- Select Package -->
					<div>
						<label for="package_id" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Select Package <span class="text-red-500">*</span>
						</label>
						<select id="package_id" name="package_id" x-model="selectedPackageId"
							@change="updatePackageDetails()"
							required
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
							<option value="">-- Choose an Active Package --</option>
							@foreach ($packages as $package)
								<option value="{{ $package->id }}"
									data-name="{{ $package->name }}"
									data-description="{{ $package->description ?? '' }}"
									data-price="{{ $package->base_price }}"
									data-currency="{{ $package->currency }}"
									data-platform="{{ $package->platform }}"
									data-delivery="{{ $package->delivery_days ?? 0 }}"
									data-revisions="{{ $package->revisions_included ?? 0 }}"
									data-influencer="{{ $package->influencer?->display_name ?? 'Unknown' }}"
									data-influencer-email="{{ $package->influencer?->user?->email ?? 'N/A' }}">
									{{ $package->influencer?->display_name ?? 'Unknown' }} - {{ $package->name }} - {{ $package->currency }} {{ number_format((float) $package->base_price, 2) }}
								</option>
							@endforeach
						</select>
						@error('package_id')
							<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
						@enderror
					</div>

					<!-- Package Details Display -->
					<div x-show="selectedPackageId" x-cloak
						class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Package Details</h4>
						<div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
							<div>
								<p class="text-gray-600 dark:text-gray-400">Influencer</p>
								<p class="font-medium text-gray-900 dark:text-white" x-text="packageDetails.influencer"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Platform</p>
								<p class="font-medium text-gray-900 dark:text-white capitalize" x-text="packageDetails.platform"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Price</p>
								<p class="font-medium text-gray-900 dark:text-white"
									x-text="`${packageDetails.currency} ${packageDetails.price}`"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Delivery Days</p>
								<p class="font-medium text-gray-900 dark:text-white" x-text="`${packageDetails.delivery} days`"></p>
							</div>
							<div>
								<p class="text-gray-600 dark:text-gray-400">Revisions Included</p>
								<p class="font-medium text-gray-900 dark:text-white" x-text="`${packageDetails.revisions} revision(s)`"></p>
							</div>
							<div class="md:col-span-2">
								<p class="text-gray-600 dark:text-gray-400">Description</p>
								<p class="font-medium text-gray-900 dark:text-white line-clamp-2"
									x-text="packageDetails.description || 'No description provided'"></p>
							</div>
						</div>
					</div>

					<!-- Select Brands with Chips -->
					<div>
						<label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
							Select Brands <span class="text-red-500">*</span>
						</label>

						<div x-data="brandChipsSelector()" class="space-y-3">
							<!-- Search and Filter -->
							<input type="search" @input="filterBrands($event)" placeholder="Search brands by name or email..."
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white" />

							<!-- Brand Chips Selection -->
							<div class="flex flex-wrap gap-2 p-3 min-h-[3rem] rounded-lg border border-gray-300 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
								<template x-for="brand in filteredBrands" :key="brand.id">
									<button type="button" @click.prevent="toggleBrand(brand)"
										@keydown.enter.prevent="toggleBrand(brand)"
										:class="isBrandSelected(brand.id) ?
											'bg-emerald-600 text-white dark:bg-emerald-500' :
											'bg-white text-gray-700 border border-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600'"
										class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
										<span x-text="brand.brand_name"></span>
										<svg v-if="isBrandSelected(brand.id)" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
											<path fill-rule="evenodd"
												d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
												clip-rule="evenodd" />
										</svg>
									</button>
								</template>

								<template x-if="filteredBrands.length === 0">
									<p class="text-sm text-gray-500 dark:text-gray-400">No brands match your search.</p>
								</template>
							</div>

							<!-- Selected Brands Summary -->
							<div x-show="selectedBrandIds.length > 0" x-cloak
								class="text-sm text-gray-600 dark:text-gray-400">
								Selected: <span class="font-medium text-gray-900 dark:text-white"
									x-text="`${selectedBrandIds.length} brand(s)`"></span>
							</div>

							<!-- Hidden input for selected brand IDs -->
							<template x-for="brandId in selectedBrandIds" :key="`hidden-brand-${brandId}`">
								<input type="hidden" name="brand_ids[]" :value="brandId" />
							</template>

							@error('brand_ids')
								<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<!-- Form Actions -->
					<div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:justify-end dark:border-gray-800">
						<a href="{{ route('dashboard.packages.index') }}"
							class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
							Cancel
						</a>
						<button type="submit"
							class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
							Purchase Package for Brands
						</button>
					</div>
				</div>

				<!-- Sidebar - Info Cards -->
				<div class="space-y-4">
					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Active Brands</h4>
						<p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $activeBrandsCount }}</p>
						<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Ready to purchase</p>
					</div>

					<div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
						<h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Active Packages</h4>
						<p class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $activePackagesCount }}</p>
						<p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Available for purchase</p>
					</div>
				</div>
			</div>
		</form>
	</div>

	<!-- Recent Purchases Section -->
	<div class="mt-6 space-y-4">
		<div>
			<h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Purchases</h3>
			<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest package purchases by brands</p>
		</div>

		<div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
			@if ($latestPurchases->count() > 0)
				<div class="overflow-x-auto">
					<table class="w-full text-sm">
						<thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
							<tr>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Order #</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Brand</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Purchased By</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Total Amount</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
								<th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Purchased Date</th>
							</tr>
						</thead>
						<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
							@foreach ($latestPurchases as $purchase)
								@php
									$statusClass = match ($purchase->status) {
										'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
										'accepted' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
										'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
										'rejected' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
										default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
									};
								@endphp
								<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
									<td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
										<a href="{{ route('dashboard.orders.show', $purchase->id) }}"
											class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
											{{ $purchase->order_number }}
										</a>
									</td>
									<td class="px-4 py-3 text-gray-600 dark:text-gray-400">
										{{ $purchase->brand?->brand_name ?? 'N/A' }}
									</td>
									<td class="px-4 py-3 text-gray-600 dark:text-gray-400">
										{{ $purchase->buyer?->name ?? 'N/A' }}
									</td>
									<td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
										{{ $purchase->currency }} {{ number_format((float) $purchase->total_amount, 2) }}
									</td>
									<td class="px-4 py-3">
										<span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
											{{ \Illuminate\Support\Str::headline($purchase->status) }}
										</span>
									</td>
									<td class="px-4 py-3 text-gray-600 dark:text-gray-400">
										{{ $purchase->placed_at?->format('M d, Y') ?? 'N/A' }}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@else
				<div class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-600 dark:bg-gray-800/50">
					<p class="text-gray-600 dark:text-gray-400">No package purchases yet.</p>
				</div>
			@endif
		</div>
	</div>

	<script>
		function packagePurchaseForm() {
			return {
				selectedPackageId: '',
				packageDetails: {
					name: '',
					influencer: '',
					platform: '',
					price: '0',
					currency: 'USD',
					delivery: '0',
					revisions: '0',
					description: ''
				},
				updatePackageDetails() {
					const select = document.getElementById('package_id');
					const selected = select.options[select.selectedIndex];

					if (selected.value) {
						this.packageDetails = {
							name: selected.dataset.name,
							influencer: selected.dataset.influencer,
							platform: selected.dataset.platform,
							price: parseFloat(selected.dataset.price).toFixed(2),
							currency: selected.dataset.currency,
							delivery: selected.dataset.delivery,
							revisions: selected.dataset.revisions,
							description: selected.dataset.description
						};
					} else {
						this.packageDetails = {
							name: '', influencer: '', platform: '',
							price: '0', currency: 'USD', delivery: '0',
							revisions: '0', description: ''
						};
					}
				}
			};
		}

		function brandChipsSelector() {
			return {
				selectedBrandIds: [],
				filteredBrands: @js($brands->map(fn($b) => [
					'id' => $b->id,
					'brand_name' => $b->brand_name,
					'email' => $b->user?->email
				])),
				allBrands: @js($brands->map(fn($b) => [
					'id' => $b->id,
					'brand_name' => $b->brand_name,
					'email' => $b->user?->email
				])),
				filterBrands(event) {
					const searchTerm = event.target.value.toLowerCase();
					this.filteredBrands = this.allBrands.filter(brand =>
						brand.brand_name.toLowerCase().includes(searchTerm) ||
						brand.email.toLowerCase().includes(searchTerm)
					);
				},
				toggleBrand(brand) {
					const index = this.selectedBrandIds.indexOf(brand.id);
					if (index > -1) {
						this.selectedBrandIds.splice(index, 1);
					} else {
						this.selectedBrandIds.push(brand.id);
					}
				},
				isBrandSelected(brandId) {
					return this.selectedBrandIds.includes(brandId);
				}
			};
		}
	</script>
@endsection
