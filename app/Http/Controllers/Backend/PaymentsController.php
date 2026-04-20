<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index()
    {
        $entries = $this->buildPayoutLedgerEntries();
        $paginatedEntries = $this->paginateEntries($entries, 15);

        $totalPayouts = $entries->count();
        $totalAmount = (float) $entries->sum('amount');
        $packageAmount = (float) $entries->where('type', 'Package Work')->sum('amount');
        $campaignAmount = (float) $entries->where('type', 'Campaign Work')->sum('amount');
        $thisMonthAmount = (float) $entries
            ->filter(fn (array $entry): bool => $entry['paid_at']->greaterThanOrEqualTo(now()->startOfMonth()))
            ->sum('amount');
        $uniqueInfluencers = $entries->pluck('influencer_id')->filter()->unique()->count();

        $topInfluencers = $entries
            ->groupBy('influencer_id')
            ->map(function (Collection $group) {
                $first = $group->first();

                return [
                    'influencer' => $first['influencer'],
                    'count' => $group->count(),
                    'amount' => (float) $group->sum('amount'),
                ];
            })
            ->sortByDesc('amount')
            ->values()
            ->take(5);

        $recentEntries = $entries->take(8);

        return view('backend.pages.payments.index', compact(
            'paginatedEntries',
            'totalPayouts',
            'totalAmount',
            'packageAmount',
            'campaignAmount',
            'thisMonthAmount',
            'uniqueInfluencers',
            'topInfluencers',
            'recentEntries'
        ));
    }

    public function show(Request $request)
    {
        $type = $request->query('type');
        $id = (int) $request->query('id');

        if ($type === 'item') {
            $entry = OrderItem::query()
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
                ->findOrFail($id);

            return response()->json($this->formatOrderItemEntry($entry));
        }

        if ($type === 'sub-order') {
            $entry = SubOrder::query()
                ->forPaymentDashboard()
                ->paidForDashboard()
                ->with([
                    'order:id,campaign_id,brand_id',
                    'order.campaign:id,title',
                    'order.brand:id,brand_name',
                    'influencer:id,user_id,display_name',
                    'influencer.user:id,name,email',
                    'payoutMarkedBy:id,name,email',
                ])
                ->findOrFail($id);

            return response()->json($this->formatSubOrderEntry($entry));
        }

        abort(404);
    }

    public function refund(Request $request)
    {
        abort(404);
    }

    public function retry(Request $request)
    {
        abort(404);
    }

    private function buildPayoutLedgerEntries(): Collection
    {
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
            ->get()
            ->map(fn (OrderItem $item): array => $this->formatOrderItemEntry($item));

        $paidSubOrders = SubOrder::query()
            ->forPaymentDashboard()
            ->paidForDashboard()
            ->with([
                'order:id,campaign_id,brand_id',
                'order.campaign:id,title',
                'order.brand:id,brand_name',
                'influencer:id,user_id,display_name',
                'influencer.user:id,name,email',
                'payoutMarkedBy:id,name,email',
            ])
            ->orderByDesc('payout_marked_at')
            ->get()
            ->map(fn (SubOrder $subOrder): array => $this->formatSubOrderEntry($subOrder));

        return $paidItems
            ->concat($paidSubOrders)
            ->sortByDesc('paid_at')
            ->values();
    }

    private function formatOrderItemEntry(OrderItem $item): array
    {
        return [
            'source_type' => 'item',
            'source_id' => $item->id,
            'type' => 'Package Work',
            'influencer_id' => $item->influencer_id,
            'influencer' => $item->influencer,
            'campaign_or_brand' => $item->order?->campaign?->title ?? $item->order?->brand?->brand_name ?? 'N/A',
            'description' => $item->description ?: ($item->title ?: 'Package work'),
            'amount' => (float) $item->payout_amount,
            'reference' => $item->payout_reference,
            'note' => $item->payout_note,
            'marked_by' => $item->payoutMarkedBy?->name ?? 'Admin',
            'paid_at' => $item->paid_at,
        ];
    }

    private function formatSubOrderEntry(SubOrder $subOrder): array
    {
        return [
            'source_type' => 'sub-order',
            'source_id' => $subOrder->id,
            'type' => 'Campaign Work',
            'influencer_id' => $subOrder->influencer_id,
            'influencer' => $subOrder->influencer,
            'campaign_or_brand' => $subOrder->order?->campaign?->title ?? $subOrder->order?->brand?->brand_name ?? 'N/A',
            'description' => 'Campaign deliverable',
            'amount' => (float) $subOrder->payout_amount,
            'reference' => $subOrder->payout_reference,
            'note' => $subOrder->payout_note,
            'marked_by' => $subOrder->payoutMarkedBy?->name ?? 'Admin',
            'paid_at' => $subOrder->paid_at,
        ];
    }

    private function paginateEntries(Collection $entries, int $perPage = 15): LengthAwarePaginator
    {
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $items = $entries->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator(
            $items,
            $entries->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }
}
