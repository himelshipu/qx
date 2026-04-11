<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     * Brands see orders they created.
     * Influencers see orders where they have items.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');

        if ($user->user_type === 'brand') {
            // Brand sees parent orders for their own brand.
            $brandId = $user->brand?->id;

            $orders = Order::where('brand_id', $brandId)
                ->whereNull('parent_order_id')
                ->with([
                    'items.package',
                    'items.influencer.user',
                    'childOrders.items.package',
                    'childOrders.items.influencer.user',
                    'acceptedBy',
                ])
                ->when($search !== '', fn ($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->when($status !== 'all', fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(15);

            return view('frontend.orders.brand-index', compact('orders', 'search', 'status'));
        } elseif ($user->user_type === 'influencer') {
            // Influencer sees child orders assigned to them.
            $orders = Order::where(function ($query) use ($user) {
                $query
                    ->where('accepted_for_influencer_id', $user->influencer->id)
                    ->orWhereHas('items', fn ($itemQuery) => $itemQuery->where('influencer_id', $user->influencer->id));
            })
                ->with(['buyer.brand', 'items.package', 'items.influencer.user'])
                ->when($search !== '', fn ($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->when($status !== 'all', fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(15);

            return view('frontend.orders.influencer-index', compact('orders', 'search', 'status'));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display the specified order.
     * Authorization: brand sees orders they bought, influencer sees orders where they have items
     */
    public function show(Order $order): View
    {
        $user = Auth::user();

        $this->authorizeOrderAccess($order, $user);

        $order->load([
            'buyer:id,name,slug,email,phone',
            'items:id,order_id,influencer_id,package_id,title,description,quantity,unit_price,line_total,status,due_date,paid_at,accepted_at,delivered_at,created_at,updated_at',
            'items.influencer:id,user_id,display_name',
            'items.influencer.user:id,name,slug',
            'items.brandToInfluencerReview:id,order_item_id,influencer_id,brand_id,reviewer_type,reviewee_type,rating,title,comment,created_at',
            'items.influencerToBrandReview:id,order_item_id,influencer_id,brand_id,reviewer_type,reviewee_type,rating,title,comment,created_at',
            'childOrders:id,parent_order_id,buyer_user_id,brand_id,status,accepted_for_influencer_id,subtotal,service_fee,tax_amount,total_amount,currency,placed_at,created_at',
            'childOrders.items:id,order_id,influencer_id,package_id,title,description,quantity,unit_price,line_total,status,due_date,paid_at,accepted_at,delivered_at,created_at,updated_at',
            'childOrders.items.influencer:id,user_id,display_name',
            'childOrders.items.influencer.user:id,name,slug',
            'childOrders.items.brandToInfluencerReview:id,order_item_id,influencer_id,brand_id,reviewer_type,reviewee_type,rating,title,comment,created_at',
            'childOrders.items.influencerToBrandReview:id,order_item_id,influencer_id,brand_id,reviewer_type,reviewee_type,rating,title,comment,created_at',
            'parentOrder:id,order_number,parent_order_id,buyer_user_id,brand_id,status,accepted_for_influencer_id,subtotal,service_fee,tax_amount,total_amount,currency,placed_at,created_at',
            'parentOrder.buyer:id,name,email',
            'acceptedForInfluencer:id,user_id,display_name',
            'acceptedForInfluencer.user:id,name,slug',
            'conversations:id,conversation_type,influencer_id,brand_user_id,handled_by_user_id,order_id,title,public_id,updated_at',
            'conversations.influencer:id,user_id,display_name',
            'conversations.influencer.user:id,name,slug',
            'conversations.brandUser:id,name',
            'conversations.handledBy:id,name',
            'conversations.messages:id,conversation_id,sender_user_id,message,created_at',
        ]);

        // Parent package orders keep items in child orders; flatten for page rendering.
        if ($order->items->isEmpty() && $order->childOrders->isNotEmpty()) {
            $flattenedItems = $order->childOrders
                ->flatMap(fn ($childOrder) => $childOrder->items)
                ->values();

            $order->setRelation('items', $flattenedItems);
        }

        $timeline = collect();
        $timeline->push([
            'label' => 'Order placed',
            'value' => $order->placed_at?->format('M d g:iA') ?? 'Pending',
            'state' => 'done',
        ]);

        $timeline->push([
            'label' => $order->parent_order_id ? 'Child order created' : 'Parent order created',
            'value' => $order->created_at?->format('M d g:iA') ?? 'Pending',
            'state' => $order->created_at ? 'done' : 'pending',
        ]);

        $timeline->push([
            'label' => 'Accepted',
            'value' => $order->accepted_at?->format('M d g:iA') ?? 'Waiting',
            'state' => $order->accepted_at ? 'done' : 'pending',
        ]);

        $timeline->push([
            'label' => 'Completed',
            'value' => $order->completed_at?->format('M d g:iA') ?? 'Not completed',
            'state' => $order->completed_at ? 'done' : 'pending',
        ]);

        $orderContext = [
            'is_parent' => $order->parent_order_id === null,
            'parent_order' => $order->parentOrder,
            'child_orders_count' => $order->childOrders->count(),
            'conversations' => $order->conversations->sortByDesc('updated_at')->values(),
            'conversation_by_influencer' => $order->conversations
                ->sortByDesc('updated_at')
                ->groupBy('influencer_id')
                ->map(fn ($list) => $list->first()),
        ];

        $influencerIds = $order->items
            ->pluck('influencer_id')
            ->filter()
            ->unique()
            ->values();

        $influencers = Influencer::query()
            ->with(['user:id,name,slug'])
            ->whereIn('id', $influencerIds)
            ->get()
            ->keyBy('id');

        $ratingSummary = Review::query()
            ->selectRaw('influencer_id, AVG(rating) as avg_rating, COUNT(*) as reviews_count')
            ->whereIn('influencer_id', $influencerIds)
            ->where('reviewee_type', 'influencer')
            ->where('is_public', true)
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $recentPublicReviews = Review::query()
            ->with(['brand:id,brand_name'])
            ->whereIn('influencer_id', $influencerIds)
            ->where('reviewee_type', 'influencer')
            ->where('is_public', true)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('influencer_id')
            ->map(fn ($reviews) => $reviews->take(3));

        $orderInfluencers = $order->items
            ->groupBy('influencer_id')
            ->map(function ($items, $influencerId) use ($influencers, $ratingSummary, $recentPublicReviews) {
                $influencer = $influencers->get($influencerId);
                $summary = $ratingSummary->get($influencerId);

                return [
                    'influencer' => $influencer,
                    'has_order_review' => $items->contains(fn ($item) => $item->influencerToBrandReview !== null),
                    'avg_rating' => $summary && $summary->avg_rating !== null ? round((float) $summary->avg_rating, 1) : null,
                    'reviews_count' => (int) ($summary->reviews_count ?? 0),
                    'recent_reviews' => $recentPublicReviews->get($influencerId, collect()),
                ];
            })
            ->filter(fn ($entry) => $entry['influencer'] !== null)
            ->values();

        $hasSubmittedReview = false;
        if ($user->user_type === 'influencer' && $user->influencer) {
            $hasSubmittedReview = $order->items
                ->where('influencer_id', $user->influencer->id)
                ->contains(fn ($item) => $item->influencerToBrandReview !== null);
        }

        $canLeaveReview = $user->user_type === 'influencer' && $this->isReviewUnlocked($order) && ! $hasSubmittedReview;

        return view('frontend.orders.show', compact('order', 'orderInfluencers', 'canLeaveReview', 'hasSubmittedReview', 'timeline', 'orderContext'));
    }

    /**
     * Store a review from influencer for a completed order.
     */
    public function storeReview(Order $order, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'influencer' || ! $user->influencer) {
            abort(403, 'Unauthorized');
        }

        if (! $this->isReviewUnlocked($order)) {
            return back()->with('error', 'You can only review the brand after the order is completed.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1200'],
        ]);

        $orderItems = $order->items()
            ->where('influencer_id', $user->influencer->id)
            ->with('influencerToBrandReview:id,order_item_id')
            ->orderBy('id')
            ->get();

        if ($orderItems->isEmpty()) {
            return back()->with('error', 'No items were assigned to your influencer account for this order.');
        }

        if ($orderItems->contains(fn ($item) => $item->influencerToBrandReview !== null)) {
            return back()->with('error', 'You have already reviewed this brand for this order.');
        }

        $reviewableItem = $orderItems->first();

        Review::create([
            'order_item_id' => $reviewableItem->id,
            'brand_id' => (int) $order->brand_id,
            'influencer_id' => (int) $user->influencer->id,
            'reviewer_type' => 'influencer',
            'reviewee_type' => 'brand',
            'rating' => (int) $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_public' => true,
        ]);

        return back()->with('success', 'Review submitted successfully.');
    }

    /**
     * Brand marks a completed parent order after all child items are delivered.
     */
    public function completeOrder(Order $order): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand' || $order->buyer_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (! $order->isParentOrder()) {
            return back()->with('error', 'Only the parent checkout can be completed here.');
        }

        $itemsForCompletion = $this->resolveItemsForStatusSync($order);

        $readyToComplete = $itemsForCompletion->isNotEmpty() && $itemsForCompletion->every(function ($item) {
            return in_array($item->status, ['approved', 'completed'], true);
        });

        if (! $readyToComplete) {
            return back()->with('error', 'All child items must be approved before completing the order.');
        }

        $this->applyOrderStatus($order, 'completed', [
            'status' => 'completed',
            'completed_at' => now(),
        ], 'Brand completed parent order after all tasks approved');

        return back()->with('success', 'Order completed successfully.');
    }

    /**
     * Influencer updates their own order item status.
     * Influencer cannot mark item as completed.
     */
    public function updateItemStatus(Order $order, OrderItem $item, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'influencer') {
            abort(403, 'Unauthorized');
        }

        if ((int) $item->order_id !== (int) $order->id) {
            abort(404);
        }

        $influencerId = $user->influencer?->id;
        if (! $influencerId || (int) $item->influencer_id !== (int) $influencerId) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,delivered',
        ]);

        $newStatus = (string) $validated['status'];

        if (! $this->canTransitionPackageItemStatus($item->status, $newStatus)) {
            return back()->with('error', 'Invalid task status transition.');
        }

        $updates = ['status' => $newStatus];
        if ($newStatus === 'accepted' && $item->accepted_at === null) {
            $updates['accepted_at'] = now();
        }
        if ($newStatus === 'delivered') {
            $updates['delivered_at'] = now();
        }

        $item->update($updates);

        $this->syncOrderStatusFromItems($order);

        if ($order->parentOrder) {
            $this->syncOrderStatusFromItems($order->parentOrder);
        }

        return back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Brand approves or rejects an influencer-delivered item from a parent checkout.
     */
    public function updateBrandItemDecision(Order $order, OrderItem $item, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand' || $order->buyer_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (! $order->isParentOrder()) {
            return back()->with('error', 'Use the parent checkout to review item decisions.');
        }

        $allowedOrderIds = $order->childOrders()->pluck('id')->push($order->id)->all();
        if (! in_array((int) $item->order_id, $allowedOrderIds, true)) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $decision = (string) $validated['status'];

        if (! $this->canTransitionPackageItemStatus($item->status, $decision)) {
            return back()->with('error', 'Invalid review decision transition.');
        }

        $updates = [
            'status' => $decision,
            'approved_at' => $decision === 'approved' ? now() : null,
        ];

        $item->update($updates);

        $childOrder = $item->order;
        if ($childOrder) {
            $this->syncOrderStatusFromItems($childOrder);
        }

        $this->syncOrderStatusFromItems($order);

        $successMessage = $decision === 'approved'
            ? 'Task approved successfully. You can now submit a review.'
            : 'Task rejected. The influencer has been notified and can resubmit their work.';

        return back()->with('success', $successMessage);
    }

    /**
     * Brand leaves a review for an influencer for a specific approved task.
     */
    public function storeBrandTaskReview(Order $order, OrderItem $item, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand' || $order->buyer_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (! $order->isParentOrder()) {
            return back()->with('error', 'Use the parent checkout to submit task reviews.');
        }

        $allowedOrderIds = $order->childOrders()->pluck('id')->push($order->id)->all();
        if (! in_array((int) $item->order_id, $allowedOrderIds, true)) {
            abort(404);
        }

        if (! in_array((string) $item->status, ['approved', 'completed'], true)) {
            return back()->with('error', 'Only approved tasks can be reviewed.');
        }

        if ($item->brandToInfluencerReview()->exists()) {
            return back()->with('error', 'This task already has a review.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1200'],
        ]);

        Review::create([
            'order_item_id' => (int) $item->id,
            'brand_id' => (int) $order->brand_id,
            'influencer_id' => (int) $item->influencer_id,
            'reviewer_type' => 'brand',
            'reviewee_type' => 'influencer',
            'rating' => (int) $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_public' => true,
        ]);

        return back()->with('success', 'Task review submitted successfully.');
    }

    private function authorizeOrderAccess(Order $order, mixed $user): void
    {
        if (! in_array($user->user_type, ['brand', 'influencer'])) {
            abort(403, 'Unauthorized');
        }

        if ($user->user_type === 'brand' && $order->buyer_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if ($user->user_type === 'influencer') {
            $assignedToInfluencer = $order->accepted_for_influencer_id === $user->influencer->id;
            $legacyHasItems = $order->items()->where('influencer_id', $user->influencer->id)->exists();

            if (! $assignedToInfluencer && ! $legacyHasItems) {
                abort(403, 'Unauthorized');
            }
        }
    }

    private function isOrderCompleted(Order $order): bool
    {
        return $order->status === 'completed' || $order->completed_at !== null;
    }

    private function isReviewUnlocked(Order $order): bool
    {
        if ($this->isOrderCompleted($order)) {
            return true;
        }

        return $order->parentOrder !== null && $this->isOrderCompleted($order->parentOrder);
    }

    private function syncOrderStatusFromItems(Order $order): void
    {
        $statuses = $this->resolveItemsForStatusSync($order)->pluck('status');

        if ($statuses->isEmpty()) {
            return;
        }

        if ($statuses->every(fn ($status) => $status === 'pending')) {
            $this->applyOrderStatus($order, 'pending', [
                'status' => 'pending',
                'accepted_at' => null,
                'completed_at' => null,
            ], 'Synced from task statuses');

            return;
        }

        if ($statuses->every(fn ($status) => $status === 'accepted')) {
            $this->applyOrderStatus($order, 'accepted', [
                'status' => 'accepted',
                'accepted_at' => $order->accepted_at ?? now(),
                'completed_at' => null,
            ], 'Synced from task statuses');

            return;
        }

        if ($statuses->every(fn ($status) => in_array($status, ['approved', 'completed'], true))) {
            $this->applyOrderStatus($order, 'delivered', [
                'status' => 'delivered',
                'completed_at' => null,
            ], 'Synced from task statuses');

            return;
        }

        if ($statuses->every(fn ($status) => in_array($status, ['delivered', 'approved', 'completed'], true))) {
            $this->applyOrderStatus($order, 'delivered', [
                'status' => 'delivered',
                'completed_at' => null,
            ], 'Synced from task statuses');

            return;
        }

        $this->applyOrderStatus($order, 'in_progress', [
            'status' => 'in_progress',
            'completed_at' => null,
        ], 'Synced from task statuses');
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

    private function resolveItemsForStatusSync(Order $order)
    {
        $items = $order->items()->get(['id', 'status']);
        if ($items->isNotEmpty()) {
            return $items;
        }

        if ($order->isParentOrder()) {
            return OrderItem::query()
                ->whereIn('order_id', $order->childOrders()->pluck('id'))
                ->get(['id', 'status']);
        }

        return $items;
    }
}
