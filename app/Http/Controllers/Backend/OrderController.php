<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SubOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q', ''));
        $status = (string) $request->string('status', 'all');
        $type = (string) $request->string('type', 'all');

        $orders = Order::query()
            ->with([
                'buyer:id,name,email,user_type',
                'brand:id,brand_name',
                'campaign:id,title',
            ])
            ->withCount([
                'items as package_items_count' => fn ($query) => $query->whereNotNull('package_id'),
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('order_number', 'like', '%'.$search.'%')
                        ->orWhereHas('buyer', function ($buyerQuery) use ($search) {
                            $buyerQuery
                                ->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('brand', function ($brandQuery) use ($search) {
                            $brandQuery->where('brand_name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('campaign', function ($campaignQuery) use ($search) {
                            $campaignQuery->where('title', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('items', function ($itemQuery) use ($search) {
                            $itemQuery->where('title', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($type === 'campaign', fn ($query) => $query->whereNotNull('campaign_id'))
            ->when($type === 'package', function ($query) {
                $query
                    ->whereNull('campaign_id')
                    ->whereHas('items', fn ($itemQuery) => $itemQuery->whereNotNull('package_id'));
            })
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'revenue' => (float) Order::where('status', 'completed')->sum('total_amount'),
        ];

        if ($request->ajax()) {
            return view('backend.pages.orders._results', [
                'orders' => $orders,
            ]);
        }

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
            'type' => $type,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'buyer:id,name,email,phone',
            'brand:id,brand_name',
            'campaign:id,title,status',
            'acceptedBy:id,name,email',
            'acceptedForInfluencer:id,user_id,display_name',
            'acceptedForInfluencer.user:id,name',
            'items:id,order_id,influencer_id,package_id,title,quantity,unit_price,line_total,status,due_date,paid_at',
            'items.influencer:id,user_id,display_name',
            'items.influencer.user:id,name',
            'items.package:id,name,base_price,currency',
            'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at',
        ]);

        return view('backend.pages.orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update overall order status
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in-progress,in_progress,completed,cancelled',
        ]);

        $status = $validated['status'];

        // Normalize status (convert in-progress to in_progress for database)
        if ($status === 'in-progress') {
            $status = 'in_progress';
        }

        $order->update([
            'status' => $status,
        ]);

        // Update timestamps based on status
        if ($status === 'accepted') {
            $order->update(['accepted_at' => $order->accepted_at ?? now()]);
        } elseif ($status === 'completed') {
            $order->update(['completed_at' => now()]);
        } elseif ($status === 'cancelled') {
            $order->update(['cancelled_at' => now()]);
        }

        return redirect()
            ->back()
            ->with('success', 'Order status updated successfully');
    }

    /**
     * Create master order with sub-orders from approved influencers for a campaign
     * Workflow A: Campaign Order
     */
    public function createFromCampaign(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $campaign = Campaign::findOrFail($validated['campaign_id']);
        $brand = Brand::findOrFail($validated['brand_id']);

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
            'order_number' => 'ORD-'.time(),
            'buyer_user_id' => auth()->id(),
            'brand_id' => $brand->id,
            'campaign_id' => $campaign->id,
            'status' => 'pending',
            'subtotal' => $totalAmount,
            'service_fee' => 0,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'currency' => 'USD',
            'placed_at' => now(),
        ]);

        // Create sub-orders for each approved influencer
        foreach ($approvedInfluencers as $influencer) {
            SubOrder::create([
                'order_id' => $order->id,
                'campaign_influencer_id' => $influencer->id,
                'accepted_for_influencer_id' => $influencer->id,
                'status' => 'pending',
                'amount' => $influencer->pivot->agreed_rate ?? 0,
                'currency' => 'USD',
            ]);
        }

        return redirect()
            ->route('dashboard.orders.show', $order)
            ->with('success', 'Master order created with '.$approvedInfluencers->count().' sub-orders');
    }

    /**
     * Update sub-order status
     */
    public function updateSubOrderStatus(Request $request, SubOrder $subOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,on_review,completed,cancelled',
        ]);

        $subOrder->update([
            'status' => $validated['status'],
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
    public function markSubOrderPaid(Request $request, SubOrder $subOrder): RedirectResponse
    {
        $subOrder->update([
            'paid_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Payment recorded for influencer');
    }

    /**
     * Update order item status
     */
    public function updateOrderItemStatus(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,delivered,approved,rejected,cancelled,completed',
        ]);

        $orderItem->update([
            'status' => $validated['status'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order item status updated successfully');
    }

    /**
     * Mark order item as paid
     */
    public function markOrderItemPaid(OrderItem $orderItem): RedirectResponse
    {
        $orderItem->update([
            'paid_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order item marked as paid');
    }
}
