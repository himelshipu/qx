<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentStatementController extends Controller
{
    public function index(Request $request)
    {
        $influencers = Influencer::with('user')
            ->whereHas('orderItems', function ($q) {
                $q->whereNotNull('paid_at');
            })
            ->orWhereHas('subOrders', function ($q) {
                $q->whereNotNull('paid_at');
            })
            ->orderBy('display_name')
            ->get();

        return view('backend.pages.payment-statement.index', compact('influencers'));
    }

    public function show(Request $request, Influencer $influencer)
    {
        // Get all paid items for this influencer
        $paidItems = OrderItem::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with(['order.brand', 'order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Get all paid sub-orders
        $paidSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with(['order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Combined statement
        $statement = collect();
        foreach ($paidItems as $item) {
            $statement->push([
                'type'              => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->name ?? $item->order?->brand?->brand_name ?? 'N/A',
                'description'       => $item->description,
                'amount'            => $item->payout_amount,
                'reference'         => $item->payout_reference,
                'marked_by'         => $item->payoutMarkedBy?->name ?? 'Admin',
                'paid_date'         => $item->paid_at
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $statement->push([
                'type'              => 'Campaign Work',
                'campaign_or_order' => $subOrder->order?->campaign?->name ?? 'N/A',
                'description'       => 'Campaign deliverable',
                'amount'            => $subOrder->payout_amount,
                'reference'         => $subOrder->payout_reference,
                'marked_by'         => $subOrder->payoutMarkedBy?->name ?? 'Admin',
                'paid_date'         => $subOrder->paid_at
            ]);
        }

        $statement = $statement->sortByDesc('paid_date');

        // Statistics
        $totalPaid = $paidItems->sum('payout_amount') + $paidSubOrders->sum('payout_amount');
        $itemCount = $paidItems->count() + $paidSubOrders->count();
        $dateRange = [
            'start' => $statement->last()?->get('paid_date') ?? now(),
            'end'   => $statement->first()?->get('paid_date') ?? now()
        ];

        return view('backend.pages.payment-statement.show', compact(
            'influencer',
            'statement',
            'totalPaid',
            'itemCount',
            'dateRange',
        ));
    }

    public function pdf(Request $request, Influencer $influencer)
    {
        // Get all paid items for this influencer
        $paidItems = OrderItem::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with(['order.brand', 'order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Get all paid sub-orders
        $paidSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with(['order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Combined statement
        $statement = collect();
        foreach ($paidItems as $item) {
            $statement->push([
                'type'              => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->name ?? $item->order?->brand?->brand_name ?? 'N/A',
                'description'       => $item->description,
                'amount'            => $item->payout_amount,
                'reference'         => $item->payout_reference,
                'paid_date'         => $item->paid_at
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $statement->push([
                'type'              => 'Campaign Work',
                'campaign_or_order' => $subOrder->order?->campaign?->name ?? 'N/A',
                'description'       => 'Campaign deliverable',
                'amount'            => $subOrder->payout_amount,
                'reference'         => $subOrder->payout_reference,
                'paid_date'         => $subOrder->paid_at
            ]);
        }

        $statement = $statement->sortByDesc('paid_date');

        // Statistics
        $totalPaid = $paidItems->sum('payout_amount') + $paidSubOrders->sum('payout_amount');
        $dateRange = [
            'start' => $statement->last()?->get('paid_date') ?? now(),
            'end'   => $statement->first()?->get('paid_date') ?? now()
        ];

        $pdf = Pdf::loadView('backend.pages.payment-statement.pdf', [
            'influencer' => $influencer,
            'statement'  => $statement,
            'totalPaid'  => $totalPaid,
            'dateRange'  => $dateRange
        ]);

        return $pdf->download("Payment-Statement-{$influencer->display_name}-{$dateRange['end']->format('Y-m-d')}.pdf");
    }
}
