<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders for the authenticated user.
     * Brands see orders they created.
     * Creators see orders where they have items.
     */
    public function index(Request $request): View
    {
        $user   = auth()->user();
        $search = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');

        if ($user->user_type === 'brand') {
            // Brand sees orders for their own brand
            $brandId = $user->brand?->id;

            $orders = Order::where('brand_id', $brandId)
                ->with(['items.package.creator.user', 'acceptedBy'])
                ->when($search !== '', fn($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(15);

            return view('frontend.orders.brand-index', compact('orders', 'search', 'status'));
        } elseif ($user->user_type === 'creator') {
            // Creator sees orders where they have items
            $orders = Order::whereHas('items', fn($q) => $q->where('creator_id', $user->creator->id))
                ->with(['buyer.user', 'items.package'])
                ->when($search !== '', fn($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->orderByDesc('created_at')
                ->paginate(15);

            return view('frontend.orders.creator-index', compact('orders', 'search', 'status'));
        } else {
            abort(403, 'Unauthorized');
        }
    }

    /**
     * Display the specified order.
     * Authorization: brand sees orders they bought, creator sees orders where they have items
     */
    public function show(Order $order): View
    {
        $user = auth()->user();

        // Authorization check
        if (!in_array($user->user_type, ['brand', 'creator'])) {
            abort(403, 'Unauthorized');
        }

        // Brand user: check if they are the buyer
        if ($user->user_type === 'brand') {
            // Check if buyer belongs to this brand user
            if ($order->buyer_user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }
        }

        // Creator user: check if they have items in this order
        if ($user->user_type === 'creator') {
            $hasItems = $order->items()->where('creator_id', $user->creator->id)->exists();
            if (!$hasItems) {
                abort(403, 'Unauthorized');
            }
        }

        $order->load([
            'buyer:id,name,email,phone',
            'items:id,order_id,creator_id,package_id,title,description,quantity,unit_price,line_total,status,due_date,paid_at',
            'items.creator:id,user_id,display_name',
            'items.creator.user:id,name'
        ]);

        return view('frontend.orders.show', compact('order'));
    }
}
