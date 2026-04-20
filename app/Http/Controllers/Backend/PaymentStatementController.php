<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class PaymentStatementController extends Controller
{
    public function index(Request $request)
    {
        $influencers = Influencer::query()
            ->select(['id', 'user_id', 'display_name'])
            ->with('user:id,name,email')
            ->where(function ($query): void {
                $query->whereHas('orderItems', function ($q): void {
                    $q->paidForDashboard();
                })->orWhereHas('subOrders', function ($q): void {
                    $q->paidForDashboard();
                });
            })
            ->orderBy('display_name')
            ->get();

        return view('backend.pages.payment-statement.index', compact('influencers'));
    }

    public function show(Request $request, Influencer $influencer)
    {
        $data = $this->buildStatementData($influencer, $request->query('month'));

        if ($request->ajax()) {
            return view('backend.pages.payment-statement._statement', $data);
        }

        return view('backend.pages.payment-statement.show', $data);
    }

    public function pdf(Request $request, Influencer $influencer)
    {
        $data = $this->buildStatementData($influencer, $request->query('month'));

        $pdf = app('dompdf.wrapper')->loadView('backend.pages.payment-statement.pdf', [
            'influencer' => $data['influencer'],
            'statement'  => $data['statement'],
            'totalPaid'  => $data['totalPaid'],
            'itemCount'  => $data['itemCount'],
            'dateRange'  => $data['dateRange']
        ]);

        return $pdf->download("Payment-Statement-{$influencer->display_name}-{$data['dateRange']['end']->format('Y-m-d')}.pdf");
    }

    private function buildStatementData(Influencer $influencer, ?string $month = null): array
    {
        $paidItems = OrderItem::query()
            ->forPaymentDashboard()
            ->paidForDashboard()
            ->where('influencer_id', $influencer->id)
            ->with([
                'order:id,brand_id,campaign_id',
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

        $paidSubOrders = SubOrder::query()
            ->forPaymentDashboard()
            ->paidForDashboard()
            ->where('influencer_id', $influencer->id)
            ->with([
                'order:id,campaign_id',
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
                'campaign_or_order' => $item->order?->campaign?->title ?? $item->order?->brand?->brand_name ?? 'N/A',
                'description' => $item->description,
                'amount' => $item->payout_amount,
                'reference' => $item->payout_reference,
                'marked_by' => $item->payoutMarkedBy?->name ?? 'Admin',
                'paid_date' => $item->paid_at,
            ]);
        }

        foreach ($paidSubOrders as $subOrder) {
            $statement->push([
                'type' => 'Campaign Work',
                'campaign_or_order' => $subOrder->order?->campaign?->title ?? 'N/A',
                'description' => 'Campaign deliverable',
                'amount' => $subOrder->payout_amount,
                'reference' => $subOrder->payout_reference,
                'marked_by' => $subOrder->payoutMarkedBy?->name ?? 'Admin',
                'paid_date' => $subOrder->paid_at,
            ]);
        }

        $statement = $statement->sortByDesc('paid_date')->values();
        $totalPaid = $paidItems->sum('payout_amount') + $paidSubOrders->sum('payout_amount');
        $itemCount = $statement->count();
        $dateRange = [
            'start' => $statement->last()['paid_date'] ?? now(),
            'end' => $statement->first()['paid_date'] ?? now(),
        ];

        return compact('influencer', 'statement', 'totalPaid', 'itemCount', 'dateRange');
    }
}
