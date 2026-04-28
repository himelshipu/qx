<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderBrandPayment;
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
            'q'      => trim((string) $request->string('q', '')),
            'status' => (string) $request->string('status', 'all'),
            'type'   => (string) $request->string('type', 'all')
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
                'orders' => $orders
            ]);
        }

        return view('backend.pages.orders.index', [
            'orders' => $orders,
            'stats'  => [
                'total'     => (int) ($stats?->total ?? 0),
                'pending'   => (int) ($stats?->pending ?? 0),
                'completed' => (int) ($stats?->completed ?? 0),
                'revenue'   => (float) ($stats?->revenue ?? 0)
            ],
            'search' => $filters['q'],
            'status' => $filters['status'],
            'type'   => $filters['type']
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
            'items.deliverables:id,order_item_id,sub_order_id,uploaded_by_user_id,deliverable_type,file_path,external_url,notes,status,created_at',
            'items.deliverables.uploadedBy:id,name',
            'payments:id,order_id,status,amount,currency,payment_provider,paid_at,created_at',
            'brandPayments:id,order_id,brand_user_id,brand_id,amount,currency,reference_number,invoice_id,brand_note,admin_note,status,submitted_at,confirmed_at,rejected_at,confirmed_by_user_id,rejected_by_user_id,created_at',
            'brandPayments.brandUser:id,name,email',
            'brandPayments.confirmedBy:id,name,email',
            'brandPayments.rejectedBy:id,name,email',
            'subOrders:id,order_id,campaign_influencer_id,influencer_id,status,amount,currency,accepted_at,completed_at,paid_at,payout_amount,payout_reference,payout_note,payout_marked_by_user_id,payout_marked_at',
            'subOrders.influencer:id,user_id,display_name',
            'subOrders.influencer.user:id,name,slug',
            'subOrders.payoutMarkedBy:id,name,email',
            'subOrders.deliverables:id,order_item_id,sub_order_id,uploaded_by_user_id,deliverable_type,file_path,external_url,notes,status,created_at',
            'subOrders.deliverables.uploadedBy:id,name',
            'childOrders:id,parent_order_id,buyer_user_id,brand_id,campaign_id,status,accepted_for_influencer_id,subtotal,service_fee,tax_amount,total_amount,currency,placed_at,created_at',
            'childOrders.acceptedForInfluencer:id,user_id,display_name',
            'childOrders.acceptedForInfluencer.user:id,name,slug',
            'childOrders.items:id,order_id,influencer_id,package_id,title,quantity,unit_price,line_total,status,due_date,paid_at,payout_amount,payout_reference,payout_note,payout_marked_by_user_id,payout_marked_at',
            'childOrders.items.influencer:id,user_id,display_name',
            'childOrders.items.influencer.user:id,name',
            'childOrders.items.package:id,name,base_price,currency',
            'childOrders.items.payoutMarkedBy:id,name,email'
        ]);

        if ($order->items->isEmpty() && $order->childOrders->isNotEmpty()) {
            $flattenedItems = $order->childOrders
                ->flatMap(fn($childOrder) => $childOrder->items)
                ->values();

            $order->setRelation('items', $flattenedItems);
        }

        $brandPayments = $order->brandPayments
            ->sortByDesc(fn(OrderBrandPayment $payment): int => $payment->submitted_at?->timestamp ?? $payment->created_at?->timestamp ?? 0)
            ->values();
        $brandConfirmedTotal = round((float) $brandPayments->where('status', 'confirmed')->sum('amount'), 2);
        $brandPendingTotal   = round((float) $brandPayments->where('status', 'pending')->sum('amount'), 2);
        $brandRejectedTotal  = round((float) $brandPayments->where('status', 'rejected')->sum('amount'), 2);
        $brandTotalAmount    = round((float) $order->total_amount, 2);
        $brandBalanceDue     = round(max($brandTotalAmount - $brandConfirmedTotal, 0), 2);
        $brandOverpaidAmount = round(max($brandConfirmedTotal - $brandTotalAmount, 0), 2);

        return view('backend.pages.orders.show', [
            'order'               => $order,
            'brandPayments'       => $brandPayments,
            'brandConfirmedTotal' => $brandConfirmedTotal,
            'brandPendingTotal'   => $brandPendingTotal,
            'brandRejectedTotal'  => $brandRejectedTotal,
            'brandTotalAmount'    => $brandTotalAmount,
            'brandBalanceDue'     => $brandBalanceDue,
            'brandOverpaidAmount' => $brandOverpaidAmount
        ]);
    }

    /**
     * Update overall order status
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => 'required|in:pending,accepted,in-progress,in_progress,delivered,approved,completed,cancelled',
            'force_transition' => 'nullable|boolean',
            'transition_note'  => 'nullable|string|max:1000'
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
        $transitionNote  = $validated['transition_note'] ?? null;

        if (!$forceTransition && !$this->canTransitionOrderStatus((string) $order->status, $status)) {
            return redirect()->back()->with('error', 'Invalid order status transition.');
        }

        if ($forceTransition && !$transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $oldStatus = (string) $order->status;

        $order->update([
            'status' => $status
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
                'order_id'           => $order->id,
                'old_status'         => $oldStatus,
                'new_status'         => $status,
                'changed_by_user_id' => $request->user()->id,
                'note'               => $forceTransition
                ? ('FORCE: ' . $transitionNote)
                : ($transitionNote ?: 'Admin updated order status')
            ]);

            if ((int) $order->buyer_user_id > 0) {
                Notification::create([
                    'user_id'         => (int) $order->buyer_user_id,
                    'type'            => 'order',
                    'title'           => 'Order status updated by admin',
                    'body'            => sprintf('Order %s moved from %s to %s.', $order->order_number, ucfirst(str_replace('_', ' ', $oldStatus)), ucfirst(str_replace('_', ' ', $status))),
                    'data_json'       => [
                        'action_url' => route('frontend.orders.show', $order),
                        'order_id'   => $order->id,
                        'status'     => $status
                    ],
                    'notifiable_type' => Order::class,
                    'notifiable_id'   => $order->id,
                    'is_read'         => false
                ]);
            }
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
            'brand_id'    => 'required|exists:brands,id'
        ]);

        $campaign = Campaign::findOrFail($validated['campaign_id']);
        $brand    = Brand::findOrFail($validated['brand_id']);

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
            fn($assignment) => (float) ($assignment->agreed_amount ?? 0) <= 0
        );

        if ($invalidAssignments->isNotEmpty()) {
            $names = $invalidAssignments
                ->map(fn($assignment) => $assignment->influencer?->display_name ?: $assignment->influencer?->user?->name ?: ('#' . $assignment->influencer_id))
                ->take(3)
                ->implode(', ');

            return redirect()
                ->back()
                ->with('error', 'Set agreed amount before creating order. Missing amount for: ' . $names);
        }

        // Calculate totals.
        $subtotal = (float) $approvedInfluencers->sum(
            fn($assignment) => (float) $assignment->agreed_amount
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

        $pricing     = PlatformPricing::calculateFromNet($subtotal);
        $buyerUserId = $request->user()->id;

        $order = DB::transaction(function () use ($buyerUserId, $brand, $campaign, $pricing, $approvedInfluencers) {
            $order = Order::create([
                'order_number'  => Order::generateOrderNumber(Order::SOURCE_CAMPAIGN),
                'buyer_user_id' => $buyerUserId,
                'brand_id'      => $brand->id,
                'campaign_id'   => $campaign->id,
                'status'        => 'pending',
                'subtotal'      => $pricing['net_subtotal'],
                'service_fee'   => $pricing['platform_charge'],
                'tax_amount'    => 0,
                'total_amount'  => $pricing['gross_total'],
                'currency'      => $campaign->currency ?: 'USD',
                'placed_at'     => now()
            ]);

            foreach ($approvedInfluencers as $assignment) {
                SubOrder::create([
                    'order_id'               => $order->id,
                    'campaign_influencer_id' => $assignment->id,
                    'influencer_id'          => $assignment->influencer_id,
                    'status'                 => 'pending',
                    'amount'                 => (float) $assignment->agreed_amount,
                    'currency'               => $order->currency
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('dashboard.orders.show', $order)
            ->with('success', 'Master order created with ' . $approvedInfluencers->count() . ' sub-orders');
    }

    /**
     * Update sub-order status
     */
    public function updateSubOrderStatus(Request $request, SubOrder $subOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => 'required|in:pending,accepted,in_progress,delivered,on_review,approved,changes_requested,completed,review_pending,reviewed,cancelled',
            'force_transition' => 'nullable|boolean',
            'transition_note'  => 'nullable|string|max:1000'
        ]);

        $newStatus       = (string) $validated['status'];
        $forceTransition = (bool) ($validated['force_transition'] ?? false);
        $transitionNote  = $validated['transition_note'] ?? null;

        if (!$forceTransition && !$this->canTransitionSubOrderStatus((string) $subOrder->status, $newStatus)) {
            return redirect()->back()->with('error', 'Invalid campaign work status transition.');
        }

        if ($forceTransition && !$transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $subOrder->update([
            'status' => $newStatus
        ]);

        $subOrder->loadMissing(['influencer.user', 'order']);
        $influencerUserId = (int) ($subOrder->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            Notification::create([
                'user_id'         => $influencerUserId,
                'type'            => 'order',
                'title'           => 'Campaign task updated by admin',
                'body'            => sprintf('Your campaign task status is now %s.', ucfirst(str_replace('_', ' ', $newStatus))),
                'data_json'       => [
                    'action_url'   => route('frontend.orders.show', $subOrder->order_id),
                    'order_id'     => $subOrder->order_id,
                    'sub_order_id' => $subOrder->id,
                    'status'       => $newStatus
                ],
                'notifiable_type' => SubOrder::class,
                'notifiable_id'   => $subOrder->id,
                'is_read'         => false
            ]);
        }

        $brandUserId = (int) ($subOrder->order?->buyer_user_id ?? 0);
        if ($brandUserId > 0) {
            Notification::create([
                'user_id'         => $brandUserId,
                'type'            => 'order',
                'title'           => 'Campaign order task updated',
                'body'            => sprintf('Admin changed a campaign sub-order to %s.', ucfirst(str_replace('_', ' ', $newStatus))),
                'data_json'       => [
                    'action_url'   => route('frontend.orders.show', $subOrder->order_id),
                    'order_id'     => $subOrder->order_id,
                    'sub_order_id' => $subOrder->id,
                    'status'       => $newStatus
                ],
                'notifiable_type' => SubOrder::class,
                'notifiable_id'   => $subOrder->id,
                'is_read'         => false
            ]);
        }

        if ($newStatus === 'accepted') {
            $subOrder->update(['accepted_at' => now()]);
        } elseif ($newStatus === 'delivered') {
            $subOrder->update(['delivered_at' => now()]);
        } elseif ($newStatus === 'approved') {
            $subOrder->update(['approved_at' => now()]);
        } elseif ($newStatus === 'completed') {
            $subOrder->update(['completed_at' => now()]);
        } elseif ($newStatus === 'reviewed') {
            $subOrder->update(['reviewed_at' => now()]);
        } elseif ($newStatus === 'cancelled') {
            $subOrder->update(['cancelled_at' => now()]);
        }

        // Auto-sync order status based on all sub-orders
        $this->syncOrderStatusFromSubOrders($subOrder->order);

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
            'amount'           => 'required|numeric|min:0.01',
            'payout_reference' => 'nullable|string|max:120',
            'payout_note'      => 'nullable|string|max:1000'
        ]);

        $subOrder->update([
            'paid_at'                  => now(),
            'payout_amount'            => round((float) $validated['amount'], 2),
            'payout_reference'         => $validated['payout_reference'] ?? null,
            'payout_note'              => $validated['payout_note'] ?? null,
            'payout_marked_by_user_id' => $request->user()->id,
            'payout_marked_at'         => now()
        ]);

        $influencerUserId = (int) ($subOrder->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            Notification::create([
                'user_id'         => $influencerUserId,
                'type'            => 'payment',
                'title'           => 'Campaign payout recorded',
                'body'            => sprintf('A payout of %s %.2f was recorded for your campaign task.', (string) $subOrder->currency, (float) $validated['amount']),
                'data_json'       => [
                    'action_url'    => route('frontend.orders.show', $subOrder->order_id),
                    'order_id'      => $subOrder->order_id,
                    'sub_order_id'  => $subOrder->id,
                    'payout_amount' => (float) $validated['amount']
                ],
                'notifiable_type' => SubOrder::class,
                'notifiable_id'   => $subOrder->id,
                'is_read'         => false
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Payment recorded for influencer');
    }

    /**
     * Approve/reject/request changes on deliverables
     */
    public function approveDeliverable(Request $request, SubOrder $subOrder): RedirectResponse
    {
        $validated = $request->validate([
            'deliverable_id' => 'required|integer|exists:order_deliverables,id',
            'status'         => 'required|in:approved,changes_requested,rejected'
        ]);

        $deliverable = \App\Models\OrderDeliverable::findOrFail($validated['deliverable_id']);

        // Ensure deliverable belongs to this sub-order
        if ($deliverable->sub_order_id !== $subOrder->id) {
            return redirect()->back()->with('error', 'Deliverable does not belong to this order.');
        }

        $deliverable->update([
            'status' => $validated['status']
        ]);

        $statusLabel = match ($validated['status']) {
            'approved'          => 'Approved',
            'changes_requested' => 'Changes requested',
            'rejected'          => 'Rejected',
            default             => 'Updated',
        };

        return redirect()
            ->back()
            ->with('success', "Deliverable {$statusLabel}");
    }

    /**
     * Update order item status
     */
    public function updateOrderItemStatus(Request $request, OrderItem $orderItem): RedirectResponse
    {
        $validated = $request->validate([
            'status'           => 'required|in:pending,accepted,in_progress,delivered,approved,rejected,cancelled,completed',
            'force_transition' => 'nullable|boolean',
            'transition_note'  => 'nullable|string|max:1000'
        ]);

        $newStatus       = (string) $validated['status'];
        $forceTransition = (bool) ($validated['force_transition'] ?? false);
        $transitionNote  = $validated['transition_note'] ?? null;

        if (!$forceTransition && !$this->canTransitionPackageItemStatus((string) $orderItem->status, $newStatus)) {
            return redirect()->back()->with('error', 'Invalid item status transition.');
        }

        if ($forceTransition && !$transitionNote) {
            return redirect()->back()->with('error', 'Transition note is required for force transition.');
        }

        $updates = [
            'status' => $newStatus
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

        $orderItem->loadMissing(['influencer.user', 'order']);
        $influencerUserId = (int) ($orderItem->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            Notification::create([
                'user_id'         => $influencerUserId,
                'type'            => 'order',
                'title'           => 'Package task updated by admin',
                'body'            => sprintf('Your package task "%s" is now %s.', (string) ($orderItem->title ?? 'Task'), ucfirst(str_replace('_', ' ', $newStatus))),
                'data_json'       => [
                    'action_url'    => route('frontend.orders.show', $orderItem->order_id),
                    'order_id'      => $orderItem->order_id,
                    'order_item_id' => $orderItem->id,
                    'status'        => $newStatus
                ],
                'notifiable_type' => OrderItem::class,
                'notifiable_id'   => $orderItem->id,
                'is_read'         => false
            ]);
        }

        $brandUserId = (int) ($orderItem->order?->buyer_user_id ?? 0);
        if ($brandUserId > 0) {
            Notification::create([
                'user_id'         => $brandUserId,
                'type'            => 'order',
                'title'           => 'Package item updated',
                'body'            => sprintf('Admin changed package item "%s" to %s.', (string) ($orderItem->title ?? 'Task'), ucfirst(str_replace('_', ' ', $newStatus))),
                'data_json'       => [
                    'action_url'    => route('frontend.orders.show', $orderItem->order_id),
                    'order_id'      => $orderItem->order_id,
                    'order_item_id' => $orderItem->id,
                    'status'        => $newStatus
                ],
                'notifiable_type' => OrderItem::class,
                'notifiable_id'   => $orderItem->id,
                'is_read'         => false
            ]);
        }

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
            'amount'           => 'required|numeric|min:0.01',
            'payout_reference' => 'nullable|string|max:120',
            'payout_note'      => 'nullable|string|max:1000'
        ]);

        $orderItem->update([
            'paid_at'                  => now(),
            'payout_amount'            => round((float) $validated['amount'], 2),
            'payout_reference'         => $validated['payout_reference'] ?? null,
            'payout_note'              => $validated['payout_note'] ?? null,
            'payout_marked_by_user_id' => $request->user()->id,
            'payout_marked_at'         => now()
        ]);

        $orderItem->loadMissing(['influencer.user']);
        $influencerUserId = (int) ($orderItem->influencer?->user_id ?? 0);
        if ($influencerUserId > 0) {
            Notification::create([
                'user_id'         => $influencerUserId,
                'type'            => 'payment',
                'title'           => 'Package payout recorded',
                'body'            => sprintf('A payout of %.2f was recorded for package task "%s".', (float) $validated['amount'], (string) ($orderItem->title ?? 'Task')),
                'data_json'       => [
                    'action_url'    => route('frontend.orders.show', $orderItem->order_id),
                    'order_id'      => $orderItem->order_id,
                    'order_item_id' => $orderItem->id,
                    'payout_amount' => (float) $validated['amount']
                ],
                'notifiable_type' => OrderItem::class,
                'notifiable_id'   => $orderItem->id,
                'is_read'         => false
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Order item marked as paid');
    }

    public function reviewBrandPayment(Request $request, OrderBrandPayment $brandPayment): RedirectResponse
    {
        $validated = $request->validate([
            'status'     => 'required|in:confirmed,rejected',
            'admin_note' => 'nullable|string|max:1000'
        ]);

        if ($brandPayment->status !== 'pending') {
            return redirect()->back()->with('error', 'This payment has already been reviewed.');
        }

        $status = $validated['status'];
        $brandPayment->update([
            'status'               => $status,
            'admin_note'           => $validated['admin_note'] ?? null,
            'confirmed_at'         => $status === 'confirmed' ? now() : null,
            'confirmed_by_user_id' => $status === 'confirmed' ? $request->user()->id : null,
            'rejected_at'          => $status === 'rejected' ? now() : null,
            'rejected_by_user_id'  => $status === 'rejected' ? $request->user()->id : null
        ]);

        $brandPayment->loadMissing(['order:id,order_number,brand_id,buyer_user_id']);

        Notification::create([
            'user_id'         => (int) $brandPayment->brand_user_id,
            'type'            => 'payment',
            'title'           => $status === 'confirmed' ? 'Payment confirmed' : 'Payment rejected',
            'body'            => $status === 'confirmed'
            ? sprintf('Your payment for order %s has been confirmed.', $brandPayment->order?->order_number ?? 'N/A')
            : sprintf(
                'Your payment for order %s needs attention%s.',
                $brandPayment->order?->order_number ?? 'N/A',
                $brandPayment->admin_note ? ' ' . $brandPayment->admin_note : ''
            ),
            'data_json'       => [
                'action_url'       => route('frontend.orders.show', $brandPayment->order),
                'order_id'         => $brandPayment->order_id,
                'brand_payment_id' => $brandPayment->id,
                'status'           => $status
            ],
            'notifiable_type' => OrderBrandPayment::class,
            'notifiable_id'   => $brandPayment->id,
            'is_read'         => false
        ]);

        return redirect()
            ->back()
            ->with('success', $status === 'confirmed' ? 'Brand payment confirmed.' : 'Brand payment rejected.');
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
        $completedCount    = $subOrders->where('status', 'completed')->count();
        $notCancelledCount = $subOrders->filter(fn($so) => $so->status !== 'cancelled')->count();

        // If all non-cancelled sub-orders are completed, auto-complete the order
        if ($completedCount === $notCancelledCount && $notCancelledCount > 0) {
            $order->update([
                'status'       => 'completed',
                'completed_at' => now()
            ]);
        }
    }

    /**
     * Sync campaign order status based on all sub-orders
     * Similar to syncOrderStatusFromItems but for campaign orders
     */
    private function syncOrderStatusFromSubOrders(?Order $order): void
    {
        if (!$order || !$order->campaign_id) {
            return;
        }

        $subOrders = $order->subOrders()->get();
        if ($subOrders->isEmpty()) {
            return;
        }

        $statuses = $subOrders->pluck('status');

        // All suborders are pending
        if ($statuses->every(fn($s) => $s === 'pending')) {
            $this->applyOrderStatus($order, 'pending', [
                'status'       => 'pending',
                'accepted_at'  => null,
                'completed_at' => null
            ], 'Auto-sync: all influencers pending');

            return;
        }

        // All suborders are accepted
        if ($statuses->every(fn($s) => $s === 'accepted')) {
            $this->applyOrderStatus($order, 'accepted', [
                'status'       => 'accepted',
                'accepted_at'  => $order->accepted_at ?? now(),
                'completed_at' => null
            ], 'Auto-sync: all influencers accepted');

            return;
        }

        // All non-cancelled are completed or reviewed (final states)
        $nonCancelledStatuses = $statuses->filter(fn($s) => $s !== 'cancelled');
        if ($nonCancelledStatuses->isNotEmpty() &&
            $nonCancelledStatuses->every(fn($s) => in_array($s, ['completed', 'reviewed'], true))) {
            $this->applyOrderStatus($order, 'completed', [
                'status'       => 'completed',
                'completed_at' => now()
            ], 'Auto-sync: all influencers completed');

            return;
        }

        // Otherwise: in_progress (any mix of other states)
        $this->applyOrderStatus($order, 'in_progress', [
            'status'       => 'in_progress',
            'completed_at' => null
        ], 'Auto-sync: campaign in progress');
    }

    private function syncOrderStatusFromItems(?Order $order): void
    {
        if (!$order) {
            return;
        }

        $statuses = $order->items()->pluck('status');
        if ($statuses->isEmpty()) {
            return;
        }

        if ($statuses->every(fn($status) => $status === 'pending')) {
            $this->applyOrderStatus($order, 'pending', [
                'status'       => 'pending',
                'accepted_at'  => null,
                'completed_at' => null
            ], 'Admin sync from item statuses');

            return;
        }

        if ($statuses->every(fn($status) => $status === 'accepted')) {
            $this->applyOrderStatus($order, 'accepted', [
                'status'       => 'accepted',
                'accepted_at'  => $order->accepted_at ?? now(),
                'completed_at' => null
            ], 'Admin sync from item statuses');

            return;
        }

        if ($statuses->every(fn($status) => in_array($status, ['delivered', 'approved', 'completed'], true))) {
            $this->applyOrderStatus($order, 'delivered', [
                'status'       => 'delivered',
                'completed_at' => null
            ], 'Admin sync from item statuses');

            return;
        }

        $this->applyOrderStatus($order, 'in_progress', [
            'status'       => 'in_progress',
            'completed_at' => null
        ], 'Admin sync from item statuses');
    }

    private function canTransitionPackageItemStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending'     => ['accepted', 'cancelled'],
            'accepted'    => ['in_progress', 'cancelled'],
            'in_progress' => ['delivered', 'cancelled'],
            'delivered'   => ['approved', 'rejected'],
            'rejected'    => ['delivered', 'cancelled'],
            'approved'    => ['completed'],
            'completed'   => [],
            'cancelled'   => []
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function canTransitionSubOrderStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            // Influencer can: accept, start work, mark delivered, resubmit after changes
            'pending'           => ['accepted', 'cancelled'],
            'accepted'          => ['in_progress', 'cancelled'],
            'in_progress'       => ['delivered', 'cancelled'],
            'changes_requested' => ['delivered', 'in_progress', 'cancelled'], // Can resubmit as delivered

            // Admin/Brand review & approve
            'delivered'         => ['on_review', 'cancelled'],
            'on_review'         => ['approved', 'changes_requested', 'cancelled'],

            // Approvals lead to completion
            'approved'          => ['completed', 'cancelled'],

            // Post-completion review phase
            'completed'         => ['review_pending', 'cancelled'],
            'review_pending'    => ['reviewed', 'cancelled'],

            // Terminal states
            'reviewed'          => [],
            'cancelled'         => []
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function canTransitionOrderStatus(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        $allowed = [
            'pending'     => ['accepted', 'cancelled'],
            'accepted'    => ['in_progress', 'cancelled'],
            'in_progress' => ['delivered', 'cancelled'],
            'delivered'   => ['completed', 'cancelled'],
            'approved'    => ['completed', 'cancelled'],
            'completed'   => [],
            'cancelled'   => []
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    private function applyOrderStatus(Order $order, string $newStatus, array $payload, string $note): void
    {
        $oldStatus = (string) $order->status;
        $order->update($payload);

        if ($oldStatus !== $newStatus) {
            OrderStatusHistory::create([
                'order_id'           => $order->id,
                'old_status'         => $oldStatus,
                'new_status'         => $newStatus,
                'changed_by_user_id' => Auth::id(),
                'note'               => $note
            ]);
        }
    }
}
