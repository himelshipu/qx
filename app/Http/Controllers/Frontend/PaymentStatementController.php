<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentStatementController extends Controller
{
    public function __construct()
    {
        // Only influencers can view their payment statements
        $this->middleware(function ($request, $next) {
            if ($request->user()?->influencer === null) {
                abort(403, 'Only influencers can view payment statements');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $influencer = $request->user()->influencer;

        // Get all paid items for this influencer grouped by month
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

        // Group by month
        $statementsByMonth = collect();

        foreach ($paidItems as $item) {
            $month = $item->paid_at->format('Y-m');
            if (!$statementsByMonth->has($month)) {
                $statementsByMonth[$month] = [
                    'month' => $item->paid_at->format('F Y'),
                    'items' => collect(),
                    'total' => 0
                ];
            }

            $statementsByMonth[$month]['items']->push([
                'type'              => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->title ?? $item->order?->brand?->company_name ?? 'N/A',
                'package'           => $item->package->title ?? 'Package',
                'description'       => $item->description,
                'amount'            => $item->payout_amount,
                'reference'         => $item->payout_reference,
                'marked_by'         => $item->payoutMarkedBy?->name ?? 'Admin',
                'paid_date'         => $item->paid_at
            ]);

            $statementsByMonth[$month]['total'] += $item->payout_amount;
        }

        foreach ($paidSubOrders as $subOrder) {
            $month = $subOrder->paid_at->format('Y-m');
            if (!$statementsByMonth->has($month)) {
                $statementsByMonth[$month] = [
                    'month' => $subOrder->paid_at->format('F Y'),
                    'items' => collect(),
                    'total' => 0
                ];
            }

            $statementsByMonth[$month]['items']->push([
                'type'              => 'Campaign Work',
                'campaign_or_order' => $subOrder->order?->campaign?->title ?? 'N/A',
                'package'           => 'Campaign',
                'description'       => 'Campaign deliverable',
                'amount'            => $subOrder->payout_amount,
                'reference'         => $subOrder->payout_reference,
                'marked_by'         => $subOrder->payoutMarkedBy?->name ?? 'Admin',
                'paid_date'         => $subOrder->paid_at
            ]);

            $statementsByMonth[$month]['total'] += $subOrder->payout_amount;
        }

        $statementsByMonth = $statementsByMonth->sortByDesc('month')->values();

        // Overall statistics
        $totalPaid         = $paidItems->sum('payout_amount') + $paidSubOrders->sum('payout_amount');
        $totalTransactions = $paidItems->count() + $paidSubOrders->count();

        return view('frontend.payment-statement.index', compact(
            'influencer',
            'statementsByMonth',
            'totalPaid',
            'totalTransactions',
            'paidItems',
            'paidSubOrders'
        ));
    }

    public function pdf(Request $request)
    {
        $influencer = $request->user()->influencer;

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

        // Build statement data
        $statement = collect();
        foreach ($paidItems as $item) {
            $statement->push([
                'type'              => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->title ?? $item->order?->brand?->company_name ?? 'N/A',
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
                'campaign_or_order' => $subOrder->order?->campaign?->title ?? 'N/A',
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

        $pdf = Pdf::loadView('frontend.payment-statement.pdf', compact(
            'influencer',
            'statement',
            'totalPaid',
            'itemCount',
            'dateRange'
        ));

        return $pdf->download('Payment_Statement_' . $influencer->display_name . '_' . now()->format('Y-m-d') . '.pdf');
    }

    public function monthlyPdf(Request $request)
    {
        $influencer = $request->user()->influencer;
        $month      = $request->query('month');

        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            return redirect()->route('payment-statements.index')->with('error', 'Invalid month format');
        }

        // Get paid items for the specific month
        $paidItems = OrderItem::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', substr($month, 0, 4))
            ->whereMonth('paid_at', substr($month, 5, 2))
            ->with(['order.brand', 'order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Get paid sub-orders for the specific month
        $paidSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', substr($month, 0, 4))
            ->whereMonth('paid_at', substr($month, 5, 2))
            ->with(['order.campaign', 'payoutMarkedBy'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Build statement data
        $statement = collect();
        foreach ($paidItems as $item) {
            $statement->push([
                'type'              => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->title ?? $item->order?->brand?->company_name ?? 'N/A',
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
                'campaign_or_order' => $subOrder->order?->campaign?->title ?? 'N/A',
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

        $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');

        $pdf = Pdf::loadView('frontend.payment-statement.pdf', compact(
            'influencer',
            'statement',
            'totalPaid',
            'itemCount',
            'dateRange',
            'monthLabel'
        ));

        return $pdf->download('Payment_Statement_' . $influencer->display_name . '_' . $month . '.pdf');
    }
}
