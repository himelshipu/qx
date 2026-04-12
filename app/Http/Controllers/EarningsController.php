<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function __construct()
    {
        // Only influencers can view earnings
        $this->middleware(function ($request, $next) {
            if ($request->user()?->influencer === null) {
                abort(403, 'Only influencers can view earnings');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $influencer = $request->user()->influencer;

        // Get all completed order items for this influencer
        $completedItems = OrderItem::where('influencer_id', $influencer->id)
            ->where('status', 'approved')
            ->with(['order.brand', 'order.campaign', 'package'])
            ->orderBy('approved_at', 'desc')
            ->get();

        // Get completed sub-orders for this influencer
        $completedSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->where('status', 'completed')
            ->with(['order.campaign'])
            ->orderBy('completed_at', 'desc')
            ->get();

        // Calculate statistics
        $totalEarned = $completedItems->sum('line_total') + $completedSubOrders->sum('amount');
        $totalPaid   = $completedItems->where('paid_at', '!=', null)->sum('payout_amount')
         + $completedSubOrders->where('paid_at', '!=', null)->sum('payout_amount');
        $pendingPayment = $totalEarned - $totalPaid;

        // Payment history
        $paidItems = $completedItems->filter(fn($item) => $item->paid_at)
            ->sortByDesc('paid_at');
        $paidSubOrders = $completedSubOrders->filter(fn($so) => $so->paid_at)
            ->sortByDesc('paid_at');

        // Unpaid items
        $unpaidItems     = $completedItems->filter(fn($item) => !$item->paid_at)->count();
        $unpaidSubOrders = $completedSubOrders->filter(fn($so) => !$so->paid_at)->count();
        $totalUnpaid     = $unpaidItems + $unpaidSubOrders;

        // Monthly breakdown
        $monthlyEarnings = collect();
        $all             = $completedItems->merge($completedSubOrders);
        foreach ($all->groupBy(fn($item) => $item->approved_at?->format('Y-m') ?? $item->completed_at->format('Y-m')) as $month => $items) {
            $monthlyEarnings->put($month, [
                'earned'  => $items->sum(fn($item) => $item->line_total ?? $item->amount),
                'paid'    => $items->filter(fn($item) => $item->paid_at)->sum(fn($item) => $item->payout_amount ?? ($item->line_total ?? $item->amount)),
                'pending' => $items->where('paid_at', null)->sum(fn($item) => $item->line_total ?? $item->amount)
            ]);
        }
        $monthlyEarnings = $monthlyEarnings->sortKeys()->reverse();

        return view('influencer.earnings.index', compact(
            'influencer',
            'completedItems',
            'completedSubOrders',
            'totalEarned',
            'totalPaid',
            'pendingPayment',
            'paidItems',
            'paidSubOrders',
            'unpaidItems',
            'unpaidSubOrders',
            'totalUnpaid',
            'monthlyEarnings',
        ));
    }
}
