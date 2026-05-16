@extends('backend.layouts.app')

@section('title', 'Received Payment Details')

@section('content')
    @php
        $order = $brandPayment->order;
        $isCampaign = (bool) $order?->campaign_id;
        $submittedAt = $brandPayment->submitted_at ?: $brandPayment->created_at;
        $statusClass = match ($brandPayment->status) {
            'confirmed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'rejected' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
            default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        };
    @endphp

    <x-backend.shell.breadcrumb :links="[['label' => 'Payouts', 'url' => route('dashboard.payments.index')], ['label' => 'Received Payments', 'url' => route('dashboard.received-payments.index')]]" pageTitle="Payment #PM-{{ str_pad((string) $brandPayment->id, 6, '0', STR_PAD_LEFT) }}" />

    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 dark:border-blue-900/40 dark:bg-blue-900/20">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-700 dark:text-blue-300">Amount</p>
                <p class="mt-2 text-2xl font-semibold text-blue-700 dark:text-blue-200">{{ strtoupper($brandPayment->currency) }} {{ number_format((float) $brandPayment->amount, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Method</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ strtoupper((string) $brandPayment->payment_method) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</p>
                <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $isCampaign ? 'Campaign Order Payment' : 'Package Order Payment' }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</p>
                <p class="mt-2"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($brandPayment->status) }}</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-200 p-5 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Details</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted By</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $brandPayment->brandUser?->name ?? 'Brand User' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $brandPayment->brandUser?->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted At</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $submittedAt?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Reference Number</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $brandPayment->reference_number ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Invoice ID</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $brandPayment->invoice_id ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">PayPal Order ID</p>
                            <p class="mt-1 break-all text-sm font-medium text-gray-900 dark:text-white">{{ $brandPayment->paypal_order_id ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">PayPal Transaction ID</p>
                            <p class="mt-1 break-all text-sm font-medium text-gray-900 dark:text-white">{{ $brandPayment->paypal_transaction_id ?: 'N/A' }}</p>
                        </div>
                        @if ($brandPayment->brand_note)
                            <div class="sm:col-span-2">
                                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand Note</p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $brandPayment->brand_note }}</p>
                            </div>
                        @endif
                        @if ($brandPayment->admin_note)
                            <div class="sm:col-span-2">
                                <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Admin Note</p>
                                <p class="mt-1 text-sm text-gray-700 dark:text-gray-200">{{ $brandPayment->admin_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-200 p-5 dark:border-gray-800 flex items-center justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order Details</h3>
                        @if ($order)
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Open Order</a>
                        @endif
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Number</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order?->order_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Status</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', (string) $order?->status)) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Brand</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order?->brand?->brand_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Buyer</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order?->buyer?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Order Value</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ strtoupper((string) $order?->currency) }} {{ number_format((float) ($order?->total_amount ?? 0), 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Placed At</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order?->placed_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Campaign</p>
                            <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $order?->campaign?->title ?? 'Not a campaign order' }}</p>
                        </div>
                    </div>

                    @if ($order && !$isCampaign && $order->items->isNotEmpty())
                        <div class="border-t border-gray-200 p-5 dark:border-gray-800">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Package Items</h4>
                            <div class="space-y-2">
                                @foreach ($order->items as $item)
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->title ?: 'Package Item' }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Influencer: {{ $item->influencer?->display_name ?: $item->influencer?->user?->name ?: 'N/A' }} • Line Total: {{ strtoupper((string) $order->currency) }} {{ number_format((float) $item->line_total, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($order && $isCampaign)
                        <div class="border-t border-gray-200 p-5 dark:border-gray-800">
                            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">Campaign Sub-Orders</h4>
                            <div class="space-y-2">
                                @forelse ($order->subOrders as $subOrder)
                                    <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/60">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $subOrder->influencer?->display_name ?: $subOrder->influencer?->user?->name ?: 'Influencer' }}</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300">Status: {{ ucfirst(str_replace('_', ' ', $subOrder->status)) }} • Amount: {{ strtoupper((string) $subOrder->currency) }} {{ number_format((float) $subOrder->amount, 2) }}</p>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No campaign sub-orders found.</p>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="border-b border-gray-200 p-5 dark:border-gray-800">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Actions</h3>
                    </div>
                    <div class="space-y-4 p-5">
                        @if ($brandPayment->status === 'pending' && auth()->user()?->hasPermission('brand-payments.review'))
                            <form action="{{ route('dashboard.brand-payments.review', $brandPayment) }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <label class="block space-y-1">
                                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Admin note (optional)</span>
                                    <textarea name="admin_note" rows="3" maxlength="1000" placeholder="Optional note for confirmation"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                                </label>
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Confirm Payment</button>
                            </form>

                            <form action="{{ route('dashboard.brand-payments.review', $brandPayment) }}" method="POST" class="space-y-3">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <label class="block space-y-1">
                                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Rejection note (optional)</span>
                                    <textarea name="admin_note" rows="3" maxlength="1000" placeholder="Optional note for rejection"
                                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"></textarea>
                                </label>
                                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Reject Payment</button>
                            </form>
                        @else
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-300">
                                <p><span class="font-medium text-gray-900 dark:text-white">Current status:</span> {{ ucfirst($brandPayment->status) }}</p>
                                @if ($brandPayment->confirmed_at)
                                    <p class="mt-1"><span class="font-medium text-gray-900 dark:text-white">Confirmed by:</span> {{ $brandPayment->confirmedBy?->name ?? 'Admin' }}</p>
                                    <p class="mt-1"><span class="font-medium text-gray-900 dark:text-white">Confirmed at:</span> {{ $brandPayment->confirmed_at->format('M d, Y h:i A') }}</p>
                                @endif
                                @if ($brandPayment->rejected_at)
                                    <p class="mt-1"><span class="font-medium text-gray-900 dark:text-white">Rejected by:</span> {{ $brandPayment->rejectedBy?->name ?? 'Admin' }}</p>
                                    <p class="mt-1"><span class="font-medium text-gray-900 dark:text-white">Rejected at:</span> {{ $brandPayment->rejected_at->format('M d, Y h:i A') }}</p>
                                @endif
                            </div>
                        @endif

                        <a href="{{ route('dashboard.received-payments.index') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Back to Received Payments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
