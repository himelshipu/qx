<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\Request;

class PaymentQueueController extends Controller
{
    public function __construct()
    {
        // Only influencers can view their payment queue
        $this->middleware(function ($request, $next) {
            if ($request->user()?->influencer === null) {
                abort(403, 'Only influencers can view payment queue');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $influencer = $request->user()->influencer;

        // Get influencer's unpaid OrderItems (Package flow)
        $unpaidItems = OrderItem::where('influencer_id', $influencer->id)
            ->with([
                'order.brand',
                'order.campaign',
                'package'
            ])
            ->where('status', 'approved')
            ->whereNull('paid_at')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get influencer's unpaid SubOrders (Campaign flow)
        $unpaidSubOrders = SubOrder::where('influencer_id', $influencer->id)
            ->with([
                'order.campaign'
            ])
            ->where('status', 'completed')
            ->whereNull('paid_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Statistics
        $totalUnpaidItems     = $unpaidItems->count();
        $totalUnpaidSubOrders = $unpaidSubOrders->count();
        $totalUnpaidAmount    = $unpaidItems->sum('line_total') + $unpaidSubOrders->sum('amount');

        // Items with due dates approaching
        $dueSoon = $unpaidItems->filter(function ($item) {
            return $item->due_date && $item->due_date->diffInDays(now()) <= 7 && $item->due_date->isFuture();
        });

        // Overdue items
        $overdue = $unpaidItems->filter(function ($item) {
            return $item->due_date && $item->due_date->isPast();
        });

        return view('frontend.payment-queue.index', compact(
            'unpaidItems',
            'unpaidSubOrders',
            'totalUnpaidItems',
            'totalUnpaidSubOrders',
            'totalUnpaidAmount',
            'dueSoon',
            'overdue'
        ));
    }
}
