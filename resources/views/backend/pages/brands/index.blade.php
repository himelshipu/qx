@extends('backend.layouts.app')

@section('content')
<x-backend.shell.breadcrumb pageTitle="Brands" />

<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    
    <!-- Header with Search & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative flex-1 max-w-md">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                <x-icons.search class="w-4 h-4" />
            </span>
            <input type="text" 
                placeholder="Search brands by name or email..." 
                class="w-full h-10 pl-9 pr-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-gray-600">
        </div>
        
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-icons.filter class="w-4 h-4" />
                <span class="hidden sm:inline">Filter</span>
            </button>
            
            <button @click="$dispatch('open-brand-modal')" 
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                <x-icons.plus class="w-4 h-4" />
                <span>Add Brand</span>
            </button>
        </div>
    </div>

    <!-- Brands Table -->
    <div x-data="brandManager()" class="mt-2">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[900px]">
                
                <!-- Table Header -->
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Person</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                    @forelse($brands as $brand)
                        @php
                            $setupData = is_array($brand->setup_data) ? $brand->setup_data : (json_decode($brand->setup_data, true) ?? []);
                            $brandName = $setupData['brand_name'] ?? $brand->brand_name ?? 'N/A';
                            $industry = $setupData['industry'] ?? 'N/A';
                            $contactPerson = $setupData['full_name'] ?? $setupData['contact_person'] ?? 'N/A';
                            $email = $setupData['email'] ?? $brand->user->email ?? 'N/A';
                            $phone = $setupData['phone'] ?? 'N/A';
                            $website = $setupData['website'] ?? 'N/A';
                            $address = $setupData['address'] ?? 'N/A';
                            $logo = $setupData['logo'] ?? '/images/brand/brand-default.svg';
                            $status = $setupData['status'] ?? 'active';
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                            
                            <!-- Brand Name with Logo -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        @if($logo && $logo !== 'N/A')
                                            <img src="{{ $logo }}" alt="{{ $brandName }}" class="w-6 h-6 object-contain">
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white block">{{ $brandName }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $industry }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Person -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $contactPerson }}</span>
                            </td>

                            <!-- Email -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $email }}</span>
                            </td>

                            <!-- Phone -->
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $phone }}</span>
                            </td>

                            <!-- Status Toggle -->
                            <td class="px-4 py-3">
                                <div class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors"
                                    :class="'{{ $status }}' === 'active' ? 'bg-green-500' : 'bg-gray-300 dark:bg-gray-600'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                          :class="'{{ $status }}' === 'active' ? 'translate-x-5' : 'translate-x-0.5'">
                                    </span>
                                </div>
                            </td>

                            <!-- Action Icons -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('dashboard.brands.view', $brand->id) }}" 
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <x-icons.eye class="w-4 h-4" />
                                    </a>
                                    <button @click="editBrand({
                                        id: {{ $brand->id }},
                                        name: '{{ addslashes($brandName) }}',
                                        industry: '{{ addslashes($industry) }}',
                                        contact: '{{ addslashes($contactPerson) }}',
                                        email: '{{ addslashes($email) }}',
                                        phone: '{{ addslashes($phone) }}',
                                        website: '{{ addslashes($website) }}',
                                        address: '{{ addslashes($address) }}',
                                        logo: '{{ addslashes($logo) }}',
                                        status: '{{ $status }}'
                                    })" 
                                        class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                        <x-icons.edit class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteBrand({
                                        id: {{ $brand->id }},
                                        name: '{{ addslashes($brandName) }}'
                                    })" 
                                        class="p-2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <x-icons.trash class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Empty State -->
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <x-icons.building class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">No brands found</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by adding your first brand</p>
                                    <button @click="$dispatch('open-brand-modal')" 
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                                        <x-icons.plus class="w-4 h-4" />
                                        Add Brand
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Showing <span>{{ $brands->count() }}</span> brands
            </p>
            <div class="flex gap-2">
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50">
                    <x-icons.chevron-left class="w-4 h-4" />
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 disabled:opacity-50">
                    <x-icons.chevron-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Brand Modal (Add/Edit) -->
<x-ui.modal x-data="{ open: false }" @open-brand-modal.window="open = true" x-show="open" x-cloak class="max-w-2xl">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
            <button @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <form class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Brand Name -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Brand Name</label>
                    <input type="text" 
                        x-model="form.name"
                        placeholder="e.g., Nike, Apple"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Industry -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Industry</label>
                    <input type="text" 
                        x-model="form.industry"
                        placeholder="e.g., Fashion, Technology"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Contact Person -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Contact Person</label>
                    <input type="text" 
                        x-model="form.contact"
                        placeholder="Full name"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Email -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" 
                        x-model="form.email"
                        placeholder="contact@brand.com"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Phone -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                    <input type="tel" 
                        x-model="form.phone"
                        placeholder="+1 234 567 890"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Website -->
                <div class="col-span-2 sm:col-span-1">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                    <input type="url" 
                        x-model="form.website"
                        placeholder="https://example.com"
                        class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <!-- Logo Upload -->
                <div class="col-span-2">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Brand Logo</label>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center">
                            <template x-if="form.logo">
                                <img :src="form.logo" class="w-10 h-10 object-contain">
                            </template>
                            <template x-if="!form.logo">
                                <x-icons.image class="w-5 h-5 text-gray-400" />
                            </template>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                accept="image/*"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100 dark:file:bg-gray-800 dark:file:text-gray-300">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-span-2">
                    <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                    <textarea x-model="form.address" rows="2"
                        placeholder="Street address, city, country"
                        class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-2 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center gap-3 pt-4">
                <button type="button" 
                    @click="open = false"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </button>
                <button type="button"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <span x-text="modalAction"></span> Brand
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Delete Confirmation Modal -->
<x-ui.modal x-data="{ open: false }" @open-delete-modal.window="open = true" x-show="open" x-cloak class="max-w-md">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6 text-center">
        
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/20 flex items-center justify-center">
            <x-icons.trash class="w-8 h-8 text-red-500" />
        </div>

        <h3 class="text-lg font-semibold text-gray-900 dark-white mb-2">Delete Brand</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
            Are you sure you want to delete <span class="font-medium text-gray-700 dark:text-gray-300" x-text="deleteBrandName"></span>? 
            This action cannot be undone.
        </p>

        <div class="flex items-center gap-3">
            <button @click="open = false" 
                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Cancel
            </button>
            <button @click="confirmDelete" 
                class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">
                Delete
            </button>
        </div>
    </div>
</x-ui.modal>
@endsection

<script>
@php
    $brandsData = $brands->map(function($brand) {
        $setupData = is_array($brand->setup_data) ? $brand->setup_data : (json_decode($brand->setup_data, true) ?? []);
        return [
            'id' => $brand->id,
            'name' => $setupData['brand_name'] ?? $brand->brand_name ?? 'N/A',
            'industry' => $setupData['industry'] ?? 'N/A',
            'contact' => $setupData['full_name'] ?? $setupData['contact_person'] ?? 'N/A',
            'email' => $setupData['email'] ?? $brand->user->email ?? 'N/A',
            'phone' => $setupData['phone'] ?? 'N/A',
            'website' => $setupData['website'] ?? 'N/A',
            'address' => $setupData['address'] ?? 'N/A',
            'logo' => $setupData['logo'] ?? '/images/brand/brand-default.svg',
            'status' => $setupData['status'] ?? 'active'
        ];
    })->toArray();
@endphp

function brandManager() {
    return {
        brands: @json($brandsData),
        
        modalTitle: 'Add New Brand',
        modalAction: 'Add',
        form: {
            name: '',
            industry: '',
            contact: '',
            email: '',
            phone: '',
            website: '',
            address: '',
            logo: null
        },
        
        deleteBrandName: '',
        
        editBrand(brand) {
            this.modalTitle = 'Edit Brand';
            this.modalAction = 'Save';
            this.form = { ...brand };
            this.$dispatch('open-brand-modal');
        },
        
        deleteBrand(brand) {
            this.deleteBrandName = brand.name;
            this.$dispatch('open-delete-modal');
        },
        
        confirmDelete() {
            this.brands = this.brands.filter(b => b.name !== this.deleteBrandName);
            this.$dispatch('close-delete-modal');
        }
    }
}
</script>