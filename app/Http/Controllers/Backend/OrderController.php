<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');

        $orders = Order::query()
            ->with([
                'buyer:id,name,email',
                'brand:id,brand_name',
                'campaign:id,title'
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('order_number', 'like', '%' . $search . '%')
                        ->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                            $buyerQuery
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('brand', function ($brandQuery) use ($search) {
                            $brandQuery->where('brand_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                            $campaignQuery->where('title', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($status !== 'all', fn($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'revenue'   => (float) Order::where('status', 'completed')->sum('total_amount')
        ];

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'stats'  => $stats,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'buyer:id,name,email,phone',
            'brand:id,brand_name',
            'campaign:id,title,status',
            'acceptedBy:id,name,email',
            'acceptedForCreator:id,user_id,display_name',
            'acceptedForCreator.user:id,name',
            'items:id,order_id,creator_id,package_id,title,quantity,unit_price,line_total,status,due_date',
            'items.creator:id,user_id,display_name',
            'items.creator.user:id,name',
            'items.package:id,name,base_price,currency',
            'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at'
        ]);

        return view('backend.pages.orders.show', [
            'order' => $order
        ]);
    }

    /**
     * Create master order with sub-orders from approved influencers for a campaign
     * Workflow A: Campaign Order
     */
    public function createFromCampaign(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'brand_id'    => 'required|exists:brands,id'
        ]);

        $campaign = \App\Models\Campaign::findOrFail($validated['campaign_id']);
        $brand    = \App\Models\Brand::findOrFail($validated['brand_id']);

        // Get approved influencers for this campaign
        $approvedInfluencers = $campaign->approvedInfluencers()->get();

        if ($approvedInfluencers->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No approved influencers for this campaign');
        }

        // Calculate totals
        $totalAmount = $approvedInfluencers->sum(function ($influencer) {
            return $influencer->pivot->agreed_rate ?? 0;
        });

        // Create master order
        $order = Order::create([
            'order_number'  => 'ORD-' . time(),
            'buyer_user_id' => auth()->id(),
            'brand_id'      => $brand->id,
            'campaign_id'   => $campaign->id,
            'status'        => 'pending',
            'subtotal'      => $totalAmount,
            'service_fee'   => 0,
            'tax_amount'    => 0,
            'total_amount'  => $totalAmount,
            'currency'      => 'USD',
            'placed_at'     => now()
        ]);

        // Create sub-orders for each approved influencer
        foreach ($approvedInfluencers as $influencer) {
            \App\Models\SubOrder::create([
                'order_id'               => $order->id,
                'campaign_influencer_id' => $influencer->id,
                'creator_id'             => $influencer->creator_id,
                'status'                 => 'pending',
                'amount'                 => $influencer->pivot->agreed_rate ?? 0,
                'currency'               => 'USD'
            ]);
        }

        return redirect()
            ->route('dashboard.orders.show', $order)
            ->with('success', 'Master order created with ' . $approvedInfluencers->count() . ' sub-orders');
    }

    /**
     * Update sub-order status
     */
    public function updateSubOrderStatus(Request $request, \App\Models\SubOrder $subOrder): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,on_review,completed,cancelled'
        ]);

        $subOrder->update([
            'status' => $validated['status']
        ]);

        if ($validated['status'] === 'accepted') {
            $subOrder->update(['accepted_at' => now()]);
        } elseif ($validated['status'] === 'completed') {
            $subOrder->update(['completed_at' => now()]);
        } elseif ($validated['status'] === 'cancelled') {
            $subOrder->update(['cancelled_at' => now()]);
        }

        return redirect()
            ->back()
            ->with('success', 'Sub-order status updated');
    }

    /**
     * Record payment for a sub-order
     */
    public function markSubOrderPaid(Request $request, \App\Models\SubOrder $subOrder): \Illuminate\Http\RedirectResponse
    {
        $subOrder->update([
            'paid_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Payment recorded for influencer');
    }

    /**
     * Update order item status
     */
    public function updateOrderItemStatus(Request $request, OrderItem $orderItem): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,delivered,approved,rejected,cancelled'
        ]);

        $orderItem->update([
            'status' => $validated['status']
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order item status updated successfully');
    }

    /**
     * Mark order item as paid
     */
    public function markOrderItemPaid(OrderItem $orderItem): \Illuminate\Http\RedirectResponse
    {
        $orderItem->update([
            'paid_at' => now()
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order item marked as paid');
    }
}
