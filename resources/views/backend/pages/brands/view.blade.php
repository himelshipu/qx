@extends('backend.layouts.app')

@section('content')
@php
    // Extract brand data from setup_data
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
    $createdDate = $brand->created_at->format('M d, Y');
    $lastActive = $brand->updated_at->diffForHumans();
@endphp

<x-backend.shell.breadcrumb pageTitle="Brand Details" />

<div class="flex flex-col gap-6" x-data="{ activeModal: null }">
    
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.brands.index') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                <x-icons.chevron-left class="w-5 h-5" />
            </a>
            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">Brand Profile</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="#" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800 transition">
                <x-icons.login class="w-4 h-4" />
                Login as Brand
            </a>
            <button @click="activeModal = 'edit'" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-icons.edit class="w-4 h-4" />
                Edit
            </button>
        </div>
    </div>

    <!-- Brand Overview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Profile Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6 h-full">
                <div class="flex flex-col items-center text-center h-full">
                    <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold mb-3">
                        {{ strtoupper(substr($brandName, 0, 2)) }}
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contactPerson }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ $brandName }}</p>
                    <a href="{{ $website !== 'N/A' ? $website : '#' }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 mb-4 break-all">{{ $website !== 'N/A' ? $website : 'N/A' }}</a>
                    
                    <div class="w-full mt-auto pt-4 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex justify-between text-sm py-2">
                            <span class="text-gray-500">Member Since</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $createdDate }}</span>
                        </div>
                        <div class="flex justify-between text-sm py-2">
                            <span class="text-gray-500">Last Active</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $lastActive }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats & Info -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @php 
                $stats = [
                    ['label' => 'Total Campaigns', 'value' => '24', 'icon' => 'campaign', 'color' => 'blue'],
                    ['label' => 'Active Campaigns', 'value' => '12', 'icon' => 'active', 'color' => 'green'],
                    ['label' => 'Completed', 'value' => '8', 'icon' => 'completed', 'color' => 'purple'],
                    ['label' => 'Total Spent', 'value' => '$12,450', 'icon' => 'spent', 'color' => 'orange'],
                    ['label' => 'Total Deposits', 'value' => '$15,000', 'icon' => 'deposit', 'color' => 'emerald'],
                    ['label' => 'Conversion Rate', 'value' => '3.2%', 'icon' => 'rate', 'color' => 'pink']
                ]; 
                @endphp
                
                @foreach($stats as $stat)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $stat['label'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-lg bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/20 flex items-center justify-center flex-shrink-0">
                            <x-dynamic-component :component="'icons.' . $stat['icon']" class="w-5 h-5 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-xs text-gray-400">Last 30 days</span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Contact Information -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Full Name</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $contactPerson }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Brand Name</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $brandName }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Email Address</p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $email }}</p>
                            @if($email !== 'N/A')
                                <span class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                    <x-icons.check class="w-3 h-3" />
                                    Verified
                                </span>
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Phone Number</p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $phone }}</p>
                            @if($phone !== 'N/A')
                                <span class="inline-flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                                    <x-icons.check class="w-3 h-3" />
                                    Verified
                                </span>
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Address</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $address }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                <button @click="activeModal = 'add'" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-emerald-200 dark:hover:border-emerald-800 hover:bg-emerald-50/50 dark:hover:bg-emerald-900/20 transition group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/20 flex items-center justify-center group-hover:scale-110 transition">
                        <x-icons.plus-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Add Balance</span>
                </button>
                
                <button @click="activeModal = 'subtract'" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-red-200 dark:hover:border-red-800 hover:bg-red-50/50 dark:hover:bg-red-900/20 transition group">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/20 flex items-center justify-center group-hover:scale-110 transition">
                        <x-icons.minus-circle class="w-5 h-5 text-red-600 dark:text-red-400" />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Subtract</span>
                </button>
                
                <button class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-blue-200 dark:hover:border-blue-800 hover:bg-blue-50/50 dark:hover:bg-blue-900/20 transition group">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center group-hover:scale-110 transition">
                        <x-icons.activity class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Activity Log</span>
                </button>
                
                <button @click="activeModal = 'notify'" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-purple-200 dark:hover:border-purple-800 hover:bg-purple-50/50 dark:hover:bg-purple-900/20 transition group">
                    <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center group-hover:scale-110 transition">
                        <x-icons.bell class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Notify</span>
                </button>
                
                <button @click="activeModal = 'ban'" class="flex flex-col items-center justify-center gap-2 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-orange-200 dark:hover:border-orange-800 hover:bg-orange-50/50 dark:hover:bg-orange-900/20 transition group">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center group-hover:scale-110 transition">
                        <x-icons.ban class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                    </div>
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300 text-center">Ban Brand</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Campaigns</h3>
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1">
                View All
                <x-icons.chevron-right class="w-4 h-4" />
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Campaign</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Budget</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Progress</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $campaigns = [
                        ['name' => 'Summer Sale 2024', 'budget' => '$5,000', 'status' => 'Active', 'progress' => 65],
                        ['name' => 'Product Launch', 'budget' => '$12,000', 'status' => 'Pending', 'progress' => 20],
                        ['name' => 'Holiday Special', 'budget' => '$8,500', 'status' => 'Completed', 'progress' => 100],
                    ];
                    @endphp
                    
                    @foreach($campaigns as $campaign)
                    <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50/50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $campaign['name'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $campaign['budget'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @php
                            $statusClass = match($campaign['status']) {
                                'Active' => 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                                'Pending' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                'Completed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            @endphp
                            <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full {{ $statusClass }}">{{ $campaign['status'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-24 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 dark:bg-indigo-500 rounded-full" style="width: {{ $campaign['progress'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $campaign['progress'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                <x-icons.eye class="w-4 h-4" />
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Balance Modal -->
<x-ui.modal x-data="{ open: false }" @open-modal.window="activeModal === 'add' ? open = true : open = false" x-show="open" x-cloak class="max-w-lg">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add Balance</h3>
            <button @click="open = false; activeModal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <form class="space-y-4">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Amount <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" step="0.01" min="0" placeholder="0.00" 
                        class="w-full h-11 pl-8 pr-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Remark</label>
                <textarea rows="3" placeholder="Enter remark..." 
                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="open = false; activeModal = null" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Cancel
                </button>
                <button type="button" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">
                    Add Balance
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Subtract Balance Modal -->
<x-ui.modal x-data="{ open: false }" @open-modal.window="activeModal === 'subtract' ? open = true : open = false" x-show="open" x-cloak class="max-w-lg">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subtract Balance</h3>
            <button @click="open = false; activeModal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <form class="space-y-4">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Amount <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">$</span>
                    <input type="number" step="0.01" min="0" placeholder="0.00" 
                        class="w-full h-11 pl-8 pr-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                <textarea rows="3" placeholder="Enter reason..." 
                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="open = false; activeModal = null" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Cancel
                </button>
                <button type="button" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">
                    Subtract
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Ban Brand Modal -->
<x-ui.modal x-data="{ open: false }" @open-modal.window="activeModal === 'ban' ? open = true : open = false" x-show="open" x-cloak class="max-w-lg">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ban Brand</h3>
            <button @click="open = false; activeModal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <div class="mb-5 p-4 bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800 rounded-lg">
            <p class="text-sm text-orange-700 dark:text-orange-400">
                Banning this brand will prevent them from accessing their dashboard and managing campaigns.
            </p>
        </div>

        <form class="space-y-4">
            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Ban <span class="text-red-500">*</span></label>
                <textarea rows="4" placeholder="Enter reason..." 
                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="open = false; activeModal = null" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Cancel
                </button>
                <button type="button" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700">
                    Ban Brand
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Send Notification Modal -->
<x-ui.modal x-data="{ open: false, type: 'email' }" @open-modal.window="activeModal === 'notify' ? open = true : open = false" x-show="open" x-cloak class="max-w-lg">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Send Notification</h3>
            <button @click="open = false; activeModal = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <x-icons.close class="w-5 h-5" />
            </button>
        </div>

        <div class="flex gap-3 mb-6">
            <button @click="type = 'email'" 
                class="flex-1 py-2 px-3 rounded-lg border text-sm font-medium transition"
                :class="type === 'email' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-900/20 dark:border-indigo-800 dark:text-indigo-400' : 'border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-300'">
                Email
            </button>
            <button @click="type = 'push'" 
                class="flex-1 py-2 px-3 rounded-lg border text-sm font-medium transition"
                :class="type === 'push' ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-900/20 dark:border-indigo-800 dark:text-indigo-400' : 'border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-300'">
                Push Notification
            </button>
        </div>

        <form class="space-y-4">
            <div x-show="type === 'email'">
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                <input type="text" placeholder="Notification subject..." 
                    class="w-full h-11 px-4 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>

            <div>
                <label class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                <textarea rows="4" placeholder="Type your message..." 
                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-200 bg-transparent focus:border-gray-300 focus:ring-1 focus:ring-gray-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="open = false; activeModal = null" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    Cancel
                </button>
                <button type="button" 
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                    Send
                </button>
            </div>
        </form>
    </div>
</x-ui.modal>
@endsection