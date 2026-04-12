<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class PaymentAuditController extends Controller
{
    public function __construct()
    {
        // Only influencers can view their payment audit log
        $this->middleware(function ($request, $next) {
            if ($request->user()?->influencer === null) {
                abort(403, 'Only influencers can view payment audit log');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $influencer = $request->user()->influencer;

        // Get influencer's marked-as-paid OrderItems
        $paidItems = OrderItem::where('influencer_id', $influencer->id)
            ->with([
                'order.brand',
                'order.campaign',
                'payoutMarkedBy',
                'package'
            ])
            ->whereNotNull('paid_at')
            ->orderBy('paid_at', 'desc')
            ->paginate(20);

        // Get influencer's marked-as-paid SubOrders
        $paidSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->with([
                'order.campaign',
                'payoutMarkedBy'
            ])
            ->whereNotNull('paid_at')
            ->orderBy('paid_at', 'desc')
            ->paginate(20);

        // Combine for timeline view
        $allPayments = collect();
        foreach ($paidItems as $item) {
            $allPayments->push([
                'type'              => 'Package Work',
                'id'                => $item->id,
                'order_id'          => $item->order_id,
                'description'       => $item->package->title ?? 'Package',
                'campaign_or_brand' => $item->order->campaign->title ?? $item->order->brand->company_name ?? 'N/A',
                'amount'            => $item->payout_amount,
                'reference'         => $item->payout_reference,
                'note'              => $item->payout_note,
                'marked_by'         => $item->payoutMarkedBy?->name ?? 'Admin',
                'marked_at'         => $item->paid_at,
                'item'              => $item
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $allPayments->push([
                'type'              => 'Campaign Work',
                'id'                => $subOrder->id,
                'order_id'          => $subOrder->order_id,
                'description'       => $subOrder->order?->campaign?->title . ' - Campaign Deliverable' ?? 'Campaign Work',
                'campaign_or_brand' => $subOrder->order?->campaign?->title ?? 'N/A',
                'amount'            => $subOrder->payout_amount,
                'reference'         => $subOrder->payout_reference,
                'note'              => $subOrder->payout_note,
                'marked_by'         => $subOrder->payoutMarkedBy?->name ?? 'Admin',
                'marked_at'         => $subOrder->paid_at,
                'item'              => $subOrder
            ]);
        }

        $allPayments = $allPayments->sortByDesc('marked_at')->values();

        // Statistics
        $totalPaid         = $paidItems->sum('payout_amount') + $paidSubOrders->sum('payout_amount');
        $totalTransactions = $paidItems->count() + $paidSubOrders->count();

        $paidThisMonth = OrderItem::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');
        $paidThisMonth += SubOrder::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');

        return view('frontend.payment-audit.index', compact(
            'allPayments',
            'paidItems',
            'paidSubOrders',
            'totalPaid',
            'totalTransactions',
            'paidThisMonth'
        ));
    }
}
