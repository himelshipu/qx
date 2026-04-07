<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartManagerController extends Controller
{
    /**
     * Show all carts (for admin/superadmin only)
     * Or show the authenticated user's cart (for brands)
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Check if user is admin/superadmin
        $isAdmin = $user && ($user->hasRole('super_admin') || $user->hasRole('admin'));

        if ($isAdmin) {
            // Admin/Superadmin: Show all carts with filters
            $query = Cart::with(['user.brand', 'items.package.influencer.user']);

            // Filter by brand if requested
            if ($request->filled('brand_id')) {
                $query->whereHas('user.brand', function ($q) {
                    $q->where('id', request('brand_id'));
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', request('status'));
            }

            // Search by brand name or user email
            if ($request->filled('search')) {
                $search = request('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($subQ) use ($search) {
                        $subQ->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
                });
            }

            $carts = $query->latest()->paginate(15);

            // Get brands for filter dropdown
            $brands = User::whereHas('brand')->with('brand')->get()->pluck('brand')->unique('id');

            return view('backend.commerce.carts.index', [
                'carts' => $carts,
                'brands' => $brands,
                'isAdmin' => true,
            ]);
        }

        // Brand user: Show only their cart
        $cart = Cart::where('user_id', $user->id)
            ->with(['items.package.influencer.user'])
            ->first();

        if (! $cart) {
            $cart = Cart::create(['user_id' => $user->id]);
        }

        return view('backend.commerce.carts.show', [
            'cart' => $cart,
            'isAdmin' => false,
        ]);
    }

    /**
     * Show a specific cart (detail view)
     */
    public function show(Cart $cart): View
    {
        $user = auth()->user();
        $isAdmin = $user && ($user->hasRole('super_admin') || $user->hasRole('admin'));

        // Authorization: Brand can only view their own cart
        if (! $isAdmin && $cart->user_id !== $user->id) {
            abort(403, 'Unauthorized to view this cart.');
        }

        $cart->load(['user.brand', 'items.package.influencer.user']);

        return view('backend.commerce.carts.show', [
            'cart' => $cart,
            'isAdmin' => $isAdmin,
        ]);
    }

    /**
     * Calculate cart totals and summary
     */
    private function getCartSummary(Cart $cart): array
    {
        $cart->load('items.package');

        $subtotal = $cart->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        return [
            'itemCount' => $cart->items->count(),
            'subtotal' => $subtotal,
            'tax' => 0, // Add tax calculation if needed
            'total' => $subtotal,
        ];
    }
}
