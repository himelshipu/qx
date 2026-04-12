<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class PaymentAuditController extends Controller
{
    public function index(Request $request)
    {
        // Get all marked-as-paid OrderItems
        $paidItems = OrderItem::with([
            'order.brand',
            'order.campaign',
            'influencer.user',
            'payoutMarkedBy',
            'package'
        ])
            ->whereNotNull('paid_at')
            ->orderBy('payout_marked_at', 'desc')
            ->paginate(30);

        // Get all marked-as-paid SubOrders
        $paidSubOrders = SubOrder::with([
            'order.campaign',
            'influencer.user',
            'payoutMarkedBy'
        ])
            ->whereNotNull('paid_at')
            ->orderBy('payout_marked_at', 'desc')
            ->paginate(30);

        // Combine for timeline view
        $allPayments = collect();
        foreach ($paidItems as $item) {
            $allPayments->push([
                'type'        => 'OrderItem',
                'id'          => $item->id,
                'order_id'    => $item->order_id,
                'influencer'  => $item->influencer->display_name,
                'description' => $item->description,
                'amount'      => $item->payout_amount,
                'reference'   => $item->payout_reference,
                'note'        => $item->payout_note,
                'marked_by'   => $item->payoutMarkedBy?->name ?? 'Unknown',
                'marked_at'   => $item->payout_marked_at,
                'item'        => $item
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $allPayments->push([
                'type'        => 'SubOrder',
                'id'          => $subOrder->id,
                'order_id'    => $subOrder->order_id,
                'influencer'  => $subOrder->influencer->display_name,
                'description' => $subOrder->order?->campaign?->name . ' - Campaign Work',
                'amount'      => $subOrder->payout_amount,
                'reference'   => $subOrder->payout_reference,
                'note'        => $subOrder->payout_note,
                'marked_by'   => $subOrder->payoutMarkedBy?->name ?? 'Unknown',
                'marked_at'   => $subOrder->payout_marked_at,
                'item'        => $subOrder
            ]);
        }

        $allPayments = $allPayments->sortByDesc('marked_at');

        // Statistics
        $totalMarked = OrderItem::whereNotNull('paid_at')->count() + SubOrder::whereNotNull('paid_at')->count();
        $totalAmount = OrderItem::whereNotNull('paid_at')->sum('payout_amount') + SubOrder::whereNotNull('paid_at')->sum('payout_amount');

        $markedThisMonth = OrderItem::whereNotNull('paid_at')
            ->where('payout_marked_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');
        $markedThisMonth += SubOrder::whereNotNull('paid_at')
            ->where('payout_marked_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');

        // By admin
        $paymentsByAdmin = OrderItem::whereNotNull('payout_marked_by_user_id')
            ->selectRaw('payout_marked_by_user_id, COUNT(*) as count, SUM(payout_amount) as amount')
            ->groupBy('payout_marked_by_user_id')
            ->with('payoutMarkedBy')
            ->get();

        return view('backend.pages.payment-audit.index', compact(
            'allPayments',
            'totalMarked',
            'totalAmount',
            'markedThisMonth',
            'paymentsByAdmin',
        ));
    }

    public function undo(Request $request, OrderItem $orderItem)
    {
        // Check authorization
        if (!$request->user()->can('update', $orderItem)) {
            abort(403);
        }

        $oldAmount = $orderItem->payout_amount;

        $orderItem->update([
            'paid_at'                  => null,
            'payout_amount'            => null,
            'payout_reference'         => null,
            'payout_note'              => null,
            'payout_marked_by_user_id' => null,
            'payout_marked_at'         => null
        ]);

        return redirect()
            ->back()
            ->with('success', "Undid payment marking for item (was \${$oldAmount})");
    }

    public function undoSubOrder(Request $request, SubOrder $subOrder)
    {
        // Check authorization
        if (!$request->user()->can('update', $subOrder)) {
            abort(403);
        }

        $oldAmount = $subOrder->payout_amount;

        $subOrder->update([
            'paid_at'                  => null,
            'payout_amount'            => null,
            'payout_reference'         => null,
            'payout_note'              => null,
            'payout_marked_by_user_id' => null,
            'payout_marked_at'         => null
        ]);

        return redirect()
            ->back()
            ->with('success', "Undid payment marking for campaign work (was \${$oldAmount})");
    }
}
