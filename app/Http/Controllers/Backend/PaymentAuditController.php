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
        $paidItems = OrderItem::query()
            ->forPaymentDashboard()
            ->paidForDashboard()
            ->with([
                'order:id,brand_id,campaign_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
                'payoutMarkedBy:id,name,email',
                'package:id,name',
            ])
            ->orderByDesc('payout_marked_at')
            ->paginate(30)
            ->withQueryString();

        // Get all marked-as-paid SubOrders
        $paidSubOrders = SubOrder::query()
            ->forPaymentDashboard()
            ->paidForDashboard()
            ->with([
                'order:id,campaign_id',
                'order.campaign:id,title',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
                'payoutMarkedBy:id,name,email',
            ])
            ->orderByDesc('payout_marked_at')
            ->paginate(30)
            ->withQueryString();

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
        $totalMarked = OrderItem::query()->paidForDashboard()->count() + SubOrder::query()->paidForDashboard()->count();
        $totalAmount = (float) OrderItem::query()->paidForDashboard()->sum('payout_amount') + (float) SubOrder::query()->paidForDashboard()->sum('payout_amount');

        $markedThisMonth = OrderItem::query()->paidForDashboard()
            ->where('payout_marked_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');
        $markedThisMonth += SubOrder::query()->paidForDashboard()
            ->where('payout_marked_at', '>=', now()->startOfMonth())
            ->sum('payout_amount');

        // By admin
        $paymentsByAdmin = OrderItem::query()
            ->paidForDashboard()
            ->whereNotNull('payout_marked_by_user_id')
            ->selectRaw('payout_marked_by_user_id, COUNT(*) as count, SUM(payout_amount) as amount')
            ->groupBy('payout_marked_by_user_id')
            ->with('payoutMarkedBy:id,name,email')
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
