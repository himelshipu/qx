@extends('backend.layouts.app')

@section('title', 'Received Payments')

@section('content')
    <x-backend.shell.breadcrumb :links="[['label' => 'Payouts', 'url' => route('dashboard.payments.index')]]" pageTitle="Received Payments" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Submissions</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-300">Pending Review</p>
                <p class="mt-2 text-2xl font-semibold text-amber-700 dark:text-amber-200">{{ $stats['pending'] }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/40 dark:bg-emerald-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">Confirmed</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700 dark:text-emerald-200">{{ $stats['confirmed'] }}</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Confirmed Amount</p>
                <p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">${{ number_format($stats['amount'], 2) }}</p>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 p-5 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payments Received From Brands</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Package and campaign order payments submitted by brands.</p>
            </div>

            <div class="p-5">
                <form method="GET" action="{{ route('dashboard.received-payments.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="Search by order #, reference, invoice, transaction, brand"
                            class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    </div>
                    <div class="md:col-span-2">
                        <select name="type" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" @selected($filters['type'] === 'all')>All Types</option>
                            <option value="package" @selected($filters['type'] === 'package')>Package</option>
                            <option value="campaign" @selected($filters['type'] === 'campaign')>Campaign</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="method" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" @selected($filters['method'] === 'all')>All Methods</option>
                            <option value="manual" @selected($filters['method'] === 'manual')>Manual</option>
                            <option value="paypal" @selected($filters['method'] === 'paypal')>PayPal</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="status" class="h-10 w-full rounded-lg border border-gray-200 bg-transparent px-3 text-sm text-gray-900 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="all" @selected($filters['status'] === 'all')>All Status</option>
                            <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                            <option value="confirmed" @selected($filters['status'] === 'confirmed')>Confirmed</option>
                            <option value="rejected" @selected($filters['status'] === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="h-10 w-full rounded-lg bg-gray-900 px-3 text-sm font-medium text-white transition hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600">Filter</button>
                        <a href="{{ route('dashboard.received-payments.index') }}" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-center text-sm font-medium leading-10 text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Reset</a>
                    </div>
                </form>

                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-800">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800/60">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Payment</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Order</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
                            @forelse ($payments as $payment)
                                @php
                                    $statusClass = match ($payment->status) {
                                        'confirmed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                        'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                                        default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                    };
                                    $isCampaign = (bool) $payment->order?->campaign_id;
                                    $submittedAt = $payment->submitted_at ?: $payment->created_at;
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <p class="font-semibold text-gray-900 dark:text-white">#PM-{{ str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $isCampaign ? 'Campaign' : 'Package' }} Payment</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <a href="{{ route('dashboard.orders.show', $payment->order_id) }}" class="font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">{{ $payment->order?->order_number ?? 'N/A' }}</a>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $payment->order?->campaign?->title ?? 'Package Order' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $payment->brandUser?->name ?? 'Brand User' }}</p>
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $payment->brandUser?->email ?? 'N/A' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        @if ($payment->payment_method === 'paypal')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">PayPal</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-300">Manual</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($payment->currency) }} {{ number_format((float) $payment->amount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($payment->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-200">{{ $submittedAt?->format('M d, Y h:i A') }}</td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <a href="{{ route('dashboard.received-payments.show', $payment) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No received payments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $payments->links() }}</div>
            </div>
        </div>
    </div>
@endsection
