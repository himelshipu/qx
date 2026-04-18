<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\SubOrder;
use App\Support\PlatformPricing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->string('q', '')),
            'status' => (string) $request->string('status', 'all'),
            'type' => (string) $request->string('type', 'all'),
        ];

        $orders = Order::query()
            ->whereNull('parent_order_id')
            ->forDashboard()
            ->dashboardSearch($filters['q'])
            ->dashboardStatus($filters['status'])
            ->dashboardType($filters['type'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $stats = Order::query()
            ->whereNull('parent_order_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END), 0) as revenue")
            ->first();

        if ($request->ajax()) {
            return view('backend.pages.orders._results', [
                'orders' => $orders,
            ]);
        }

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'stats' => [
                'total' => (int) ($stats?->total ?? 0),
                'pending' => (int) ($stats?->pending ?? 0),
                'completed' => (int) ($stats?->completed ?? 0),
                'revenue' => (float) ($stats?->revenue ?? 0),
            ],
            'search' => $filters['q'],
            'status' => $filters['status'],
            'type' => $filters['type'],
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
            'items:id,order_id,influencer_id,package_id,title,quantity,unit_price,line_total,status,due_date,paid_at,payout_amount,payout_reference,payout_note,payout_marked_by_user_id,payout_marked_at',
            'items.influencer:id,user_id,display_name',
            'items.influencer.user:id,name',
            'items.payoutMarkedBy:id,name,email',
            'items.package:id,name,base_price,currency',
            'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at',
            'subOrders:id,order_id,campaign_influencer_id,influencer_id,status,amount,currency,accepted_at,completed_at,paid_at,payout_amount,payout_reference,payout_note,payout_marked_by_user_id,payout_marked_at',
            'subOrders.influencer:id,user_id,display_name',
            'subOrders.influencer.user:id,name,slug',
            'subOrders.payoutMarkedBy:id,name,email',
            'childOrders:id,parent_order_id,buyer_user_id,brand_id,campaign_id,status,accepted_for_influencer_id,subtotal,service_fee,tax_amount,total_amount,currency,placed_at,created_at',
            'childOrders.acceptedForInfluencer:id,user_id,display_name',
            'childOrders.acceptedForInfluencer.user:id,name,slug',
            'childOrders.items:id,order_id,influencer_id,package_id,title,quantity,unit_price,line_total,status,due_date,paid_at,payout_amount,payout_reference,payout_note,payout_marked_by_user_id,payout_marked_at',
            'childOrders.items.influencer:id,user_id,display_name',
            'childOrders.items.influencer.user:id,name',
            'childOrders.items.package:id,name,base_price,currency',
            'childOrders.items.payoutMarkedBy:id,name,email',
        ]);

        if ($order->items->isEmpty() && $order->childOrders->isNotEmpty()) {
            $flattenedItems = $order->childOrders
                ->flatMap(fn ($childOrder) => $childOrder->items)
                ->values();

            $order->setRelation('items', $flattenedItems);
        }

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
            'status' => 'required|in:pending,accepted,in-progress,in_progress,delivered,approved,completed,cancelled',
            'force_transition' => 'nullable|boolean',
            'transition_note' => 'nullable|string|max:1000',
        ]);

        $status = $validated['status'];

        // Normalize status (convert in-progress to in_progress for database)
        if ($status === 'in-progress') {
            $status = 'in_progress';
        }

        // Backward-compatibility: order-level "approved" maps to "delivered".
        // The orders table enum does not include "approved".
        if ($status === 'approved') {
            $status = 'delivered';
        }

        $forceTransition = (bool) ($validated['force_transition'] ?? false);
        $transitionNote = $validated['transition_note'] ?? null;

        if (! $forceTransition && ! $this->canTransitionOrderStatus((string) $order->status, $status)) {
            return redirect()->back()->with('error', 'Invalid order status transition.');
        }

        if ($forceTransition && ! $transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $oldStatus = (string) $order->status;

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

        if ($oldStatus !== $status) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'changed_by_user_id' => $request->user()->id,
                'note' => $forceTransition
                    ? ('FORCE: '.$transitionNote)
                    : ($transitionNote ?: 'Admin updated order status'),
            ]);
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

        // Get approved influencer assignments for this campaign.
        $approvedInfluencers = $campaign->approvedInfluencers()
            ->with('influencer.user:id,name')
            ->get();

        if ($approvedInfluencers->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No approved influencers for this campaign');
        }

        $invalidAssignments = $approvedInfluencers->filter(
            fn ($assignment) => (float) ($assignment->agreed_amount ?? 0) <= 0
        );

        if ($invalidAssignments->isNotEmpty()) {
            $names = $invalidAssignments
                ->map(fn ($assignment) => $assignment->influencer?->display_name ?: $assignment->influencer?->user?->name ?: ('#'.$assignment->influencer_id))
                ->take(3)
                ->implode(', ');

            return redirect()
                ->back()
                ->with('error', 'Set agreed amount before creating order. Missing amount for: '.$names);
        }

        // Calculate totals.
        $subtotal = (float) $approvedInfluencers->sum(
            fn ($assignment) => (float) $assignment->agreed_amount
        );

        if ($campaign->budget_min === null && $campaign->budget_max === null) {
            return redirect()
                ->back()
                ->with('error', 'Confirm campaign budget range before creating campaign order.');
        }

        if ($campaign->budget_max !== null && $subtotal > (float) $campaign->budget_max) {
            return redirect()
                ->back()
                ->with('error', 'Approved influencer total exceeds campaign maximum budget.');
        }

        if ($campaign->budget_min !== null && $subtotal < (float) $campaign->budget_min) {
            return redirect()
                ->back()
                ->with('error', 'Approved influencer total is below campaign minimum budget. Confirm budget before creating order.');
        }

        $pricing = PlatformPricing::calculateFromNet($subtotal);
        $buyerUserId = $request->user()->id;

        $order = DB::transaction(function () use ($buyerUserId, $brand, $campaign, $pricing, $approvedInfluencers) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(Order::SOURCE_CAMPAIGN),
                'buyer_user_id' => $buyerUserId,
                'brand_id' => $brand->id,
                'campaign_id' => $campaign->id,
                'status' => 'pending',
                'subtotal' => $pricing['net_subtotal'],
                'service_fee' => $pricing['platform_charge'],
                'tax_amount' => 0,
                'total_amount' => $pricing['gross_total'],
                'currency' => $campaign->currency ?: 'USD',
                'placed_at' => now(),
            ]);

            foreach ($approvedInfluencers as $assignment) {
                SubOrder::create([
                    'order_id' => $order->id,
                    'campaign_influencer_id' => $assignment->id,
                    'influencer_id' => $assignment->influencer_id,
                    'status' => 'pending',
                    'amount' => (float) $assignment->agreed_amount,
                    'currency' => $order->currency,
                ]);
            }

            return $order;
        });

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
            'force_transition' => 'nullable|boolean',
            'transition_note' => 'nullable|string|max:1000',
        ]);

        $newStatus = (string) $validated['status'];
        $forceTransition = (bool) ($validated['force_transition'] ?? false);
        $transitionNote = $validated['transition_note'] ?? null;

        if (! $forceTransition && ! $this->canTransitionSubOrderStatus((string) $subOrder->status, $newStatus)) {
            return redirect()->back()->with('error', 'Invalid campaign work status transition.');
        }

        if ($forceTransition && ! $transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $subOrder->update([
            'status' => $newStatus,
        ]);

        if ($newStatus === 'accepted') {
            $subOrder->update(['accepted_at' => now()]);
        } elseif ($newStatus === 'completed') {
            $subOrder->update(['completed_at' => now()]);
        } elseif ($newStatus === 'cancelled') {
            $subOrder->update(['cancelled_at' => now()]);
        }

        // Auto-complete order when all sub-orders are completed
        $this->checkAndCompleteOrder($subOrder->order);

        return redirect()
            ->back()
            ->with('success', 'Sub-order status updated');
    }

    /**
     * Record payment for a sub-order
     */
    public function markSubOrderPaid(Request $request, SubOrder $subOrder): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payout_reference' => 'nullable|string|max:120',
            'payout_note' => 'nullable|string|max:1000',
        ]);

        $subOrder->update([
            'paid_at' => now(),
            'payout_amount' => round((float) $validated['amount'], 2),
            'payout_reference' => $validated['payout_reference'] ?? null,
            'payout_note' => $validated['payout_note'] ?? null,
            'payout_marked_by_user_id' => $request->user()->id,
            'payout_marked_at' => now(),
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
            'force_transition' => 'nullable|boolean',
            'transition_note' => 'nullable|string|max:1000',
        ]);

        $newStatus = (string) $validated['status'];
        $forceTransition = (bool) ($validated['force_transition'] ?? false);
        $transitionNote = $validated['transition_note'] ?? null;

        if (! $forceTransition && ! $this->canTransitionPackageItemStatus((string) $orderItem->status, $newStatus)) {
            return redirect()->back()->with('error', 'Invalid item status transition.');
        }

        if ($forceTransition && ! $transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $updates = [
            'status' => $newStatus,
        ];

        if ($newStatus === 'accepted' && $orderItem->accepted_at === null) {
            $updates['accepted_at'] = now();
        }

        if ($newStatus === 'delivered') {
            $updates['delivered_at'] = now();
        }

        if ($newStatus === 'approved') {
            $updates['approved_at'] = now();
        }

        if (in_array($newStatus, ['pending', 'accepted', 'in_progress', 'rejected', 'cancelled'], true)) {
            if ($newStatus !== 'accepted') {
                $updates['accepted_at'] = null;
            }
            if ($newStatus !== 'delivered') {
                $updates['delivered_at'] = null;
            }
            if ($newStatus !== 'approved') {
                $updates['approved_at'] = null;
            }
        }

        $orderItem->update($updates);

        $this->syncOrderStatusFromItems($orderItem->order);

        return redirect()
            ->back()
            ->with('success', 'Order item status updated successfully');
    }

    /**
     * Mark order item as paid
     */
    public function markOrderItemPaid(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payout_reference' => 'nullable|string|max:120',
            'payout_note' => 'nullable|string|max:1000',
        ]);

        $orderItem->update([
            'paid_at' => now(),
            'payout_amount' => round((float) $validated['amount'], 2),
            'payout_reference' => $validated['payout_reference'] ?? null,
            'payout_note' => $validated['payout_note'] ?? null,
            'payout_marked_by_user_id' => $request->user()->id,
            'payout_marked_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order item marked as paid');
    }

    /**
     * Helper: Check if all sub-orders are completed and auto-complete the order
     */
    private function checkAndCompleteOrder(Order $order): void
    {
        // Only check campaign orders with sub-orders
        if (!$order->campaign_id) {
            return;
        }

        $subOrders = $order->subOrders()->get();
        if ($subOrders->isEmpty()) {
            return;
        }

        // Check if all sub-orders are completed (not cancelled)
        $completedCount = $subOrders->where('status', 'completed')->count();
        $notCancelledCount = $subOrders->filter(fn ($so) => $so->status !== 'cancelled')->count();

        // If all non-cancelled sub-orders are completed, auto-complete the order
        if ($completedCount === $notCancelledCount && $notCancelledCount > 0) {
            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    private function syncOrderStatusFromItems(?Order $order): void
    {
        if (! $order) {
            return;
        }

        $statuses = $order->items()->pluck('status');
        if ($statuses->isEmpty()) {
            return;
        }

        if ($statuses->every(fn ($status) => $status === 'pending')) {
            $this->applyOrderStatus($order, 'pending', [
                'status' => 'pending',
                'accepted_at' => null,
                'completed_at' => null,
            ], 'Admin sync from item statuses');

            return;
        }

        if ($statuses->every(fn ($status) => $status === 'accepted')) {
            $this->applyOrderStatus($order, 'accepted', [
                'status' => 'accepted',
                'accepted_at' => $order->accepted_at ?? now(),
                'completed_at' => null,
            ], 'Admin sync from item statuses');

            return;
        }

        if ($statuses->every(fn ($status) => in_array($status, ['delivered', 'approved', 'completed'], true))) {
            $this->applyOrderStatus($order, 'delivered', [
                'status' => 'delivered',
                'completed_at' => null,
            ], 'Admin sync from item statuses');

            return;
        }

        $this->applyOrderStatus($order, 'in_progress', [
            'status' => 'in_progress',
            'completed_at' => null,
        ], 'Admin sync from item statuses');
    }

    private function canTransitionPackageItemStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['in_progress', 'cancelled'],
            'in_progress' => ['delivered', 'cancelled'],
            'delivered' => ['approved', 'rejected'],
            'rejected' => ['delivered', 'cancelled'],
            'approved' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function canTransitionSubOrderStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['in_progress', 'cancelled'],
            'in_progress' => ['on_review', 'cancelled'],
            'on_review' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function canTransitionOrderStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending' => ['accepted', 'cancelled'],
            'accepted' => ['in_progress', 'cancelled'],
            'in_progress' => ['delivered', 'cancelled'],
            'delivered' => ['completed', 'cancelled'],
            'approved' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function applyOrderStatus(Order $order, string $newStatus, array $payload, string $note): void
    {
        $oldStatus = (string) $order->status;
        $order->update($payload);

        if ($oldStatus !== $newStatus) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by_user_id' => Auth::id(),
                'note' => $note,
            ]);
        }
    }
}
