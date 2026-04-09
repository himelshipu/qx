<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\Order;
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
            // Brand sees orders for their own brand
            $brandId = $user->brand?->id;

            $orders = Order::where('brand_id', $brandId)
                ->with(['items.package', 'items.influencer.user', 'acceptedBy'])
                ->when($search !== '', fn ($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->when($status !== 'all', fn ($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(15);

            return view('frontend.orders.brand-index', compact('orders', 'search', 'status'));
        } elseif ($user->user_type === 'influencer') {
            // Influencer sees orders where they have items
            $orders = Order::whereHas('items', fn ($q) => $q->where('influencer_id', $user->influencer->id))
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
            'buyer:id,name,email,phone',
            'items:id,order_id,influencer_id,package_id,title,description,quantity,unit_price,line_total,status,due_date,paid_at',
            'items.influencer:id,user_id,display_name',
            'items.influencer.user:id,name,slug',
            'items.review:id,order_item_id,influencer_id,rating,title,comment,created_at',
        ]);

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
            ->where('is_public', true)
            ->groupBy('influencer_id')
            ->get()
            ->keyBy('influencer_id');

        $recentPublicReviews = Review::query()
            ->with(['brand:id,brand_name'])
            ->whereIn('influencer_id', $influencerIds)
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
                    'has_order_review' => $items->contains(fn ($item) => $item->review !== null),
                    'avg_rating' => $summary && $summary->avg_rating !== null ? round((float) $summary->avg_rating, 1) : null,
                    'reviews_count' => (int) ($summary->reviews_count ?? 0),
                    'recent_reviews' => $recentPublicReviews->get($influencerId, collect()),
                ];
            })
            ->filter(fn ($entry) => $entry['influencer'] !== null)
            ->values();

        $canLeaveReview = $user->user_type === 'brand' && $this->isOrderCompleted($order);

        return view('frontend.orders.show', compact('order', 'orderInfluencers', 'canLeaveReview'));
    }

    /**
     * Store a review from brand to influencer for a completed order.
     */
    public function storeReview(Order $order, Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->user_type !== 'brand' || $order->buyer_user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (! $this->isOrderCompleted($order)) {
            return back()->with('error', 'You can only review influencers after the order is completed.');
        }

        if (! $user->brand) {
            return back()->with('error', 'Brand profile is required to submit a review.');
        }

        $validated = $request->validate([
            'influencer_id' => ['required', 'integer', 'exists:influencers,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1200'],
        ]);

        $orderItems = $order->items()
            ->where('influencer_id', (int) $validated['influencer_id'])
            ->with('review:id,order_item_id')
            ->orderBy('id')
            ->get();

        if ($orderItems->isEmpty()) {
            return back()->with('error', 'Selected influencer is not part of this order.');
        }

        $reviewableItem = $orderItems->first(fn ($item) => $item->review === null);
        if (! $reviewableItem) {
            return back()->with('error', 'You have already reviewed this influencer for this order.');
        }

        Review::create([
            'order_item_id' => $reviewableItem->id,
            'brand_id' => $user->brand->id,
            'influencer_id' => (int) $validated['influencer_id'],
            'rating' => (int) $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'is_public' => true,
        ]);

        return back()->with('success', 'Review submitted successfully.');
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
            $hasItems = $order->items()->where('influencer_id', $user->influencer->id)->exists();
            if (! $hasItems) {
                abort(403, 'Unauthorized');
            }
        }
    }

    private function isOrderCompleted(Order $order): bool
    {
        return $order->status === 'completed' || $order->completed_at !== null;
    }
}
