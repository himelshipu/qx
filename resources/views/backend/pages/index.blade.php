@extends('backend.layouts.app')

@section('content')

<x-backend.shell.breadcrumb pageTitle="Dashboard" />

<div class="flex flex-col gap-6 p-2">

    <!-- SECTION 1: USER & INFLUENCER STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $userStats = [
                ['label' => 'Total Users', 'value' => $totalUsers, 'color' => 'gray', 'icon' => 'user-circle', 'link' => route('dashboard.brands.index')],
                ['label' => 'Active Users', 'value' => $activeUsers, 'color' => 'gray', 'icon' => 'user-check', 'link' => route('dashboard.brands.index')],
                ['label' => 'Email Unverified Users', 'value' => $emailUnverifiedUsers, 'color' => 'gray', 'icon' => 'mail', 'link' => '#'],
                ['label' => 'Mobile Unverified Users', 'value' => $mobileUnverifiedUsers, 'color' => 'gray', 'icon' => 'phone-off', 'link' => '#'],
                ['label' => 'Total Influencers', 'value' => $totalCreators, 'color' => 'gray', 'icon' => 'users', 'link' => route('dashboard.creators.index')],
                ['label' => 'Active Influencers', 'value' => $activeCreators, 'color' => 'gray', 'icon' => 'user-check', 'link' => route('dashboard.creators.index')],
                ['label' => 'Email Unverified Influencers', 'value' => $emailUnverifiedCreators, 'color' => 'gray', 'icon' => 'mail', 'link' => '#'],
                ['label' => 'Mobile Unverified Influencers', 'value' => $mobileUnverifiedCreators, 'color' => 'gray', 'icon' => 'phone-off', 'link' => '#'],
            ];
        @endphp

        @foreach($userStats as $index => $stat)
        <a href="{{ $stat['link'] ?? '#' }}" class="block">
        <div class="bg-white dark:bg-gray-900 border border-{{ $stat['color'] }}-200 dark:border-{{ $stat['color'] }}-900/50 rounded-lg p-5 flex items-center justify-between group cursor-pointer hover:shadow-md transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-500/10 text-{{ $stat['color'] }}-500">
                    <!-- Standard Icon Mockup -->
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-300">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stat['value'] }}</p>
                </div>
            </div>
            <svg class="w-5 h-5 text-gray-300 group-hover:text-gray-500 dark:group-hover:text-purple-400 dark:text-purple-200 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        </a>
        @endforeach
    </div>

    <!-- SECTION 2: CAMPAIGN STATS (Row 3) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $campaignStats = [
                ['l' => 'Total Campaign', 'v' => $totalCampaigns, 'bg' => 'bg-[#4F46E5]', 'icon' => 'chart'],
                ['l' => 'Pending Campaign', 'v' => $pendingCampaigns, 'bg' => 'bg-[#F59E0B]', 'icon' => 'clock'],
                ['l' => 'Approved Campaign', 'v' => $approvedCampaigns, 'bg' => 'bg-[#10B981]', 'icon' => 'check'],
                ['l' => 'Rejected Campaign', 'v' => $rejectedCampaigns, 'bg' => 'bg-[#EF4444]', 'icon' => 'x'],
            ];
        @endphp

        @foreach($campaignStats as $c)
        <a href="{{ route('dashboard.campaigns.index') }}" class="block">
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl p-5 relative overflow-hidden flex items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="absolute top-2 right-3">
                <span class="text-[10px] font-bold text-purple-500 bg-blue-50 dark:bg-purple-900/20 px-2 py-0.5 rounded border border-blue-100 dark:border-purple-600 uppercase">View All</span>
            </div>
            <div class="w-14 h-14 rounded-lg flex items-center justify-center text-white {{ $c['bg'] }}">
                 <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $c['v'] }}</p>
                <p class="text-sm font-medium text-gray-500">{{ $c['l'] }}</p>
            </div>
            <!-- Background Decoration Pattern -->
            <div class="absolute -bottom-2 -right-2 opacity-5 text-gray-900">
                <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
        </div>
        </a>
        @endforeach
    </div>

    <!-- SECTION 3: DEPOSITS & WITHDRAWALS -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Deposits Box -->
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-6">Deposits</h2>
            <div class="grid grid-cols-2 gap-px bg-gray-100 dark:bg-gray-800 overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                <!-- Sub Item -->
                @foreach([
                    ['l' => 'Total Deposited', 'v' => '$' . number_format($totalDeposited, 2) . ' USD', 'c' => 'purple'],
                    ['l' => 'Pending Deposits', 'v' => $pendingDeposits, 'c' => 'gray'],
                    ['l' => 'Rejected Deposits', 'v' => $rejectedDeposits, 'c' => 'red'],
                    ['l' => 'Deposited Charge', 'v' => '$' . number_format($depositedCharge, 2) . ' USD', 'c' => 'black'],
                ] as $item)
                <div class="bg-white dark:bg-gray-900 p-6 flex items-center justify-between group cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-{{ $item['c'] }}-50 dark:bg-{{ $item['c'] }}-500/10 flex items-center justify-center text-{{ $item['c'] }}-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $item['v'] }}</p>
                            <p class="text-[11px] text-gray-400 font-medium">{{ $item['l'] }}</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Withdrawals Box -->
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-700 dark:text-white mb-6">Withdrawals</h2>
            <div class="grid grid-cols-2 gap-px bg-gray-100 dark:bg-gray-800 overflow-hidden rounded-xl border border-gray-100 dark:border-gray-800">
                @foreach([
                    ['l' => 'Total Withdrawn', 'v' => '$' . number_format($totalWithdrawn, 2) . ' USD', 'c' => 'purple'],
                    ['l' => 'Pending Withdrawals', 'v' => $pendingWithdrawals, 'c' => 'gray'],
                    ['l' => 'Rejected Withdrawals', 'v' => $rejectedWithdrawals, 'c' => 'red'],
                    ['l' => 'Withdrawal Charge', 'v' => '$' . number_format($withdrawalCharge, 2) . ' USD', 'c' => 'black'],
                ] as $item)
                <div class="bg-white dark:bg-gray-900 p-6 flex items-center justify-between group cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-{{ $item['c'] }}-50 dark:bg-{{ $item['c'] }}-500/10 flex items-center justify-center text-{{ $item['c'] }}-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $item['v'] }}</p>
                            <p class="text-[11px] text-gray-400 font-medium">{{ $item['l'] }}</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3" stroke-linecap="round"/></svg>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    <div class="w-fit flex flex-col gap-6">
        <x-backend.shell.chart :chartData="$monthlyUsers" title="Monthly User Registrations" />
        <x-backend.shell.statistics-chart :monthlyOrders="$monthlyOrders" :monthlyCampaigns="$monthlyCampaigns" />
    </div>

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection

