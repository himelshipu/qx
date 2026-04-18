<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

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
        $data = $this->buildStatementData($influencer);

        $statementsByMonth = $data['statement']
            ->groupBy(fn (array $entry): string => $entry['paid_date']->format('Y-m'))
            ->map(function (Collection $items, string $monthKey): array {
                return [
                    'month_key' => $monthKey,
                    'month' => Carbon::createFromFormat('Y-m', $monthKey)->format('F Y'),
                    'items' => $items->values(),
                    'total' => (float) $items->sum('amount'),
                ];
            })
            ->sortByDesc('month_key')
            ->values();

        $totalPaid = (float) $data['totalPaid'];
        $totalTransactions = (int) $data['itemCount'];
        $paidItems = $data['paidItems'];
        $paidSubOrders = $data['paidSubOrders'];

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
        $data = $this->buildStatementData($influencer);

        $statement = $data['statement'];
        $totalPaid = $data['totalPaid'];
        $itemCount = $data['itemCount'];
        $dateRange = $data['dateRange'];

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

        $data = $this->buildStatementData($influencer, $month);

        $statement = $data['statement'];
        $totalPaid = $data['totalPaid'];
        $itemCount = $data['itemCount'];
        $dateRange = $data['dateRange'];

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

    private function buildStatementData(Influencer $influencer, ?string $month = null): array
    {
        $paidItems = OrderItem::query()
            ->where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with([
                'order:id,order_number,brand_id,campaign_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'package:id,name',
                'payoutMarkedBy:id,name,email',
            ])
            ->when($month, function ($query) use ($month): void {
                [$year, $monthNumber] = array_pad(explode('-', $month, 2), 2, null);

                if (is_numeric($year) && is_numeric($monthNumber)) {
                    $query->whereYear('paid_at', (int) $year)->whereMonth('paid_at', (int) $monthNumber);
                }
            })
            ->orderByDesc('paid_at')
            ->get();

        $paidSubOrders = SubOrder::query()
            ->where('influencer_id', $influencer->id)
            ->whereNotNull('paid_at')
            ->with([
                'order:id,order_number,brand_id,campaign_id',
                'order.brand:id,brand_name',
                'order.campaign:id,title',
                'payoutMarkedBy:id,name,email',
            ])
            ->when($month, function ($query) use ($month): void {
                [$year, $monthNumber] = array_pad(explode('-', $month, 2), 2, null);

                if (is_numeric($year) && is_numeric($monthNumber)) {
                    $query->whereYear('paid_at', (int) $year)->whereMonth('paid_at', (int) $monthNumber);
                }
            })
            ->orderByDesc('paid_at')
            ->get();

        $statement = collect();

        foreach ($paidItems as $item) {
            $statement->push([
                'type' => 'Package Work',
                'campaign_or_order' => $item->order?->campaign?->title
                    ?? $item->order?->brand?->brand_name
                    ?? $item->order?->order_number
                    ?? 'N/A',
                'description' => $item->description ?: ($item->title ?: ($item->package?->name ?: 'Package deliverable')),
                'amount' => (float) ($item->payout_amount ?? $item->line_total ?? 0),
                'reference' => $item->payout_reference,
                'marked_by' => $item->payoutMarkedBy?->name ?? 'Admin',
                'paid_date' => $item->paid_at,
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $statement->push([
                'type' => 'Campaign Work',
                'campaign_or_order' => $subOrder->order?->campaign?->title
                    ?? $subOrder->order?->brand?->brand_name
                    ?? $subOrder->order?->order_number
                    ?? 'N/A',
                'description' => 'Campaign deliverable',
                'amount' => (float) ($subOrder->payout_amount ?? $subOrder->amount ?? 0),
                'reference' => $subOrder->payout_reference,
                'marked_by' => $subOrder->payoutMarkedBy?->name ?? 'Admin',
                'paid_date' => $subOrder->paid_at,
            ]);
        }

        $statement = $statement
            ->filter(fn (array $entry): bool => $entry['paid_date'] instanceof Carbon)
            ->sortByDesc('paid_date')
            ->values();

        $totalPaid = (float) $statement->sum('amount');
        $itemCount = $statement->count();
        $dateRange = [
            'start' => $statement->last()['paid_date'] ?? now(),
            'end' => $statement->first()['paid_date'] ?? now(),
        ];

        return compact('paidItems', 'paidSubOrders', 'statement', 'totalPaid', 'itemCount', 'dateRange');
    }
}
