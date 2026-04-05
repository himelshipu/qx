<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * View cart contents
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get or create cart for user
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id]
        );

        // Load items with all relationships
        $cart->load(['items.package.creator.user']);
        $items = $cart->items()->with('package.creator.user')->get();

        return view('frontend.pages.cart', [
            'cart'  => $cart,
            'items' => $items
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        $user    = auth()->user();
        $package = Package::findOrFail($validated['package_id']);

        // Get or create cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id]
        );

        // Check if package already in cart
        $existingItem = $cart->items()
            ->where('package_id', $package->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + 1
            ]);
        } else {
            CartItem::create([
                'cart_id'    => $cart->id,
                'package_id' => $package->id,
                'creator_id' => $package->creator_id,
                'quantity'   => 1,
                'unit_price' => $package->base_price,
                'currency'   => $package->currency ?? 'USD'
            ]);
        }

        // Update cart status
        $this->updateCartStatus($cart);

        // Return JSON for AJAX requests
        if (request()->wantsJson()) {
            $cartData = $this->getCartData($cart);

            return response()->json([
                'success' => true,
                'message' => 'Package added to cart!',
                'cart'    => $cartData
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Package added to cart! View your cart');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        $cart = $cartItem->cart;

        // Check authorization
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $cartItem->delete();
            $this->updateCartStatus($cart);

            // Return JSON for AJAX requests
            if (request()->wantsJson()) {
                $cartData = $this->getCartData($cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart'    => $cartData
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Item removed from cart');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove item'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to remove item');
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999'
        ]);

        $cart = $cartItem->cart;

        // Check authorization
        if ($cart->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $cartItem->update([
                'quantity' => $validated['quantity']
            ]);

            $this->updateCartStatus($cart);

            // Return JSON for AJAX requests
            if (request()->wantsJson()) {
                $cartData = $this->getCartData($cart);

                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated',
                    'cart'    => $cartData
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Cart updated');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update cart'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update cart');
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): RedirectResponse
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return redirect()
            ->back()
            ->with('success', 'Cart cleared');
    }

    /**
     * Proceed to checkout (creates order + conversations)
     */
    public function checkout(): View | RedirectResponse
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $cart->load(['items.package.creator.user']);

        return view('frontend.pages.checkout', [
            'cart'  => $cart,
            'items' => $cart->items
        ]);
    }

    /**
     * Complete checkout - create orders and conversations
     */
    public function completeCheckout(): RedirectResponse
    {
        $user = auth()->user();
        $cart = Cart::where('user_id', $user->id)
            ->with('items.package')
            ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        try {
            // Create order for each cart item
            foreach ($cart->items as $cartItem) {
                $package = $cartItem->package;

                // Create order
                $order = \App\Models\Order::create([
                    'order_number'  => 'ORD-' . uniqid(),
                    'buyer_user_id' => $user->id,
                    'total_amount'  => $cartItem->unit_price * $cartItem->quantity,
                    'currency'      => 'USD',
                    'status'        => 'pending',
                    'placed_at'     => now()
                ]);

                // Create order item
                \App\Models\OrderItem::create([
                    'order_id'   => $order->id,
                    'package_id' => $package->id,
                    'creator_id' => $package->creator_id,
                    'title'      => $package->name,
                    'quantity'   => $cartItem->quantity,
                    'unit_price' => $cartItem->unit_price,
                    'line_total' => $cartItem->unit_price * $cartItem->quantity,
                    'status'     => 'pending'
                ]);

                // Create conversation for package order
                \App\Http\Controllers\ConversationController::createForPackageOrder(
                    $user->id,
                    $package->creator_id,
                    $order->id
                );
            }

            // Clear cart
            $cart->items()->delete();

            return redirect()
                ->route('dashboard.conversations.index')
                ->with('success', 'Order created! Check your conversations to message the creators');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to complete order: ' . $e->getMessage());
        }
    }

    /**
     * Get formatted cart data for modal response
     */
    private function getCartData(Cart $cart): array
    {
        $cart->load(['items.package.creator.user']);

        $items = $cart->items->map(function ($item) {
            return [
                'id'       => $item->id,
                'name'     => $item->package->creator->user->name,
                'package'  => $item->package->name,
                'price'    => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'image'    => image_url($item->package->creator->profile_image_path ?? '/default.webp')
            ];
        })->toArray();

        $subtotal = $cart->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        return [
            'items'     => $items,
            'subtotal'  => $subtotal,
            'itemCount' => $cart->items->count()
        ];
    }

    /**
     * Update cart status
     */
    private function updateCartStatus(Cart $cart): void
    {
        if ($cart->items()->count() === 0) {
            $cart->update(['status' => 'abandoned']);
        } else {
            $cart->update(['status' => 'active']);
        }
    }
}
