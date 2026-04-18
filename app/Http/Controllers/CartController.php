<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Support\PlatformPricing;
use App\Services\Auth\ConversationService;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class CartController extends Controller
{
    private const MAX_CART_INFLUENCERS = 5;

    /**
     * View cart contents
     */
    public function index(): View
    {
        $user = Auth::user();

        // Get or create cart for user
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id]
        );

        // Load items with all relationships
        $cart->load(['items.package.influencer.user']);
        $items = $cart->items()->with('package.influencer.user')->get();

        return view('frontend.pages.cart', [
            'cart'  => $cart,
            'items' => $items
        ]);
    }

    /**
     * Start add to cart flow - handles authentication and authorization before adding to cart
     * Similar to startNegotiation, this handles unauthenticated users by redirecting to login
     */
    public function startAddToCart(Package $package): RedirectResponse
    {
        // Return URL is the influencer's profile page
        $returnUrl = route('influencer.profile', ['slug' => $package->influencer->user->slug]);

        // If not authenticated, remember the action and redirect to login
        if (!Auth::check()) {
            app(PendingPostAuthActionService::class)->rememberAddToCart(
                $package->id,
                $returnUrl
            );

            return redirect()
                ->route('login')
                ->with('warning', 'Please login first to add packages to your cart.');
        }

        $user = Auth::user();

        // Only brands can add to cart
        if ($user->user_type !== 'brand') {
            return redirect($returnUrl)
                ->with('brand_action_required_modal', true)
                ->with('brand_action_required_message', 'Only brand accounts can add to cart or negotiate packages.');
        }

        // User is authenticated as a brand, proceed with adding to cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Check if package already in cart
        /** @var \App\Models\CartItem|null $existingItem */
        $existingItem = $cart->items()
            ->where('package_id', $package->id)
            ->first();

        $influencerAlreadyInCart = $cart->items()
            ->where('influencer_id', $package->influencer_id)
            ->exists();

        if (!$existingItem && !$influencerAlreadyInCart) {
            $influencerCount = (int) $cart->items()
                ->distinct('influencer_id')
                ->count('influencer_id');

            if ($influencerCount >= self::MAX_CART_INFLUENCERS) {
                return redirect($returnUrl)
                    ->with('warning', 'You can not add more than 5 influencers before placing your current orders.');
            }
        }

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + 1
            ]);
        } else {
            CartItem::create([
                'cart_id'       => $cart->id,
                'package_id'    => $package->id,
                'influencer_id' => $package->influencer_id,
                'quantity'      => 1,
                'unit_price'    => $package->base_price,
                'currency'      => $package->currency ?? 'USD'
            ]);
        }

        $this->updateCartStatus($cart);

        return redirect($returnUrl)
            ->with('success', 'Package added to cart!');
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id'
        ]);

        if (!Auth::check()) {
            app(PendingPostAuthActionService::class)->rememberAddToCart(
                (int) $validated['package_id'],
                $request->headers->get('referer')
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success'       => false,
                    'requires_auth' => true,
                    'message'       => 'Please login first to add packages to your cart.',
                    'redirect_url'  => route('login')
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('warning', 'Please login first to add packages to your cart.');
        }

        $user = Auth::user();

        if ($user->user_type !== 'brand') {
            $message = 'Only brand accounts can add packages to cart.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 403);
            }

            return redirect()->back()->with('warning', $message);
        }

        $package = Package::findOrFail($validated['package_id']);

        // Get or create cart
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id]
        );

        // Check if package already in cart
        /** @var \App\Models\CartItem|null $existingItem */
        $existingItem = $cart->items()
            ->where('package_id', $package->id)
            ->first();

        $influencerAlreadyInCart = $cart->items()
            ->where('influencer_id', $package->influencer_id)
            ->exists();

        if (!$existingItem && !$influencerAlreadyInCart) {
            $influencerCount = (int) $cart->items()
                ->distinct('influencer_id')
                ->count('influencer_id');

            if ($influencerCount >= self::MAX_CART_INFLUENCERS) {
                $limitMessage = 'You can not add more than 5 influencers before placing your current orders.';

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $limitMessage
                    ], 422);
                }

                return redirect()->back()->with('warning', $limitMessage);
            }
        }

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + 1
            ]);
        } else {
            CartItem::create([
                'cart_id'       => $cart->id,
                'package_id'    => $package->id,
                'influencer_id' => $package->influencer_id,
                'quantity'      => 1,
                'unit_price'    => $package->base_price,
                'currency'      => $package->currency ?? 'USD'
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
        if ($cart->user_id !== Auth::id()) {
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
        if ($cart->user_id !== Auth::id()) {
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
        $user = Auth::user();
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
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $cart->load(['items.package.influencer.user']);

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
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)
            ->with('items.package.influencer.user')
            ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        try {
            $latestConversation = null;
            $groupedByInfluencer = $cart->items->groupBy('influencer_id');

            $checkoutSubtotal = (float) $cart->items->sum(function ($item) {
                return $item->unit_price * $item->quantity;
            });
            $checkoutPricing = PlatformPricing::calculateFromNet($checkoutSubtotal);

            $parentOrder = DB::transaction(function () use ($groupedByInfluencer, $user, $checkoutPricing, &$latestConversation): Order {
                $parent = Order::create([
                    'order_number'  => 'ORD-' . uniqid(),
                    'buyer_user_id' => $user->id,
                    'brand_id'      => $user->brand?->id,
                    'subtotal'      => $checkoutPricing['net_subtotal'],
                    'service_fee'   => $checkoutPricing['platform_charge'],
                    'tax_amount'    => 0,
                    'total_amount'  => $checkoutPricing['gross_total'],
                    'currency'      => 'USD',
                    'status'        => 'pending',
                    'placed_at'     => now()
                ]);

                // Create one child order per influencer and attach all that influencer's package items.
                foreach ($groupedByInfluencer as $influencerId => $influencerCartItems) {
                    $latestOrderId = null;
                    $packageLines  = [];
                    $influencerSubtotal = (float) $influencerCartItems->sum(function ($item) {
                        return $item->unit_price * $item->quantity;
                    });
                    $influencerPricing = PlatformPricing::calculateFromNet($influencerSubtotal);

                    $childOrder = Order::create([
                        'order_number'  => 'ORD-' . uniqid(),
                        'buyer_user_id' => $user->id,
                        'brand_id'      => $user->brand?->id,
                        'parent_order_id' => $parent->id,
                        'accepted_for_influencer_id' => (int) $influencerId,
                        'subtotal'      => $influencerPricing['net_subtotal'],
                        'service_fee'   => $influencerPricing['platform_charge'],
                        'tax_amount'    => 0,
                        'total_amount'  => $influencerPricing['gross_total'],
                        'currency'      => 'USD',
                        'status'        => 'pending',
                        'placed_at'     => now()
                    ]);

                    foreach ($influencerCartItems as $cartItem) {
                        $package = $cartItem->package;
                        $dueDate = null;
                        $currency = strtoupper($cartItem->currency ?? $package->currency ?? 'USD');
                        $unitPrice = (float) $cartItem->unit_price;
                        $lineTotal = $unitPrice * (int) $cartItem->quantity;

                        if ($childOrder->placed_at && $package->delivery_days !== null) {
                            $dueDate = Carbon::parse($childOrder->placed_at)->addDays((int) $package->delivery_days)->toDateString();
                        }

                        OrderItem::create([
                            'order_id'      => $childOrder->id,
                            'package_id'    => $package->id,
                            'influencer_id' => $package->influencer_id,
                            'title'         => $package->name,
                            'quantity'      => $cartItem->quantity,
                            'unit_price'    => $cartItem->unit_price,
                            'line_total'    => $cartItem->unit_price * $cartItem->quantity,
                            'status'        => 'pending',
                            'due_date'      => $dueDate,
                        ]);

                        $latestOrderId  = $childOrder->id;
                        $packageLines[] = sprintf(
                            '%dx %s (%s %s each, total %s %s)',
                            (int) $cartItem->quantity,
                            $package->name,
                            $currency,
                            number_format($unitPrice, 2),
                            $currency,
                            number_format($lineTotal, 2)
                        );
                    }

                    $influencerSampleItem = $influencerCartItems->first();
                    $influencer           = $influencerSampleItem?->package?->influencer;
                    $influencerName       = $influencer?->display_name ?: ($influencer?->user?->name ?? 'there');

                    $conversation = Conversation::query()
                        ->where('brand_user_id', $user->id)
                        ->where('influencer_id', (int) $influencerId)
                        ->latest('updated_at')
                        ->first();

                    if (!$conversation) {
                        $conversation = ConversationService::createForPackageOrder(
                            (int) $user->id,
                            (int) $influencerId,
                            $latestOrderId ? (int) $latestOrderId : null
                        );
                    }

                    if (!$conversation->order_id && $latestOrderId) {
                        $conversation->update([
                            'order_id'          => $latestOrderId,
                            'conversation_type' => $conversation->conversation_type ?: 'order'
                        ]);
                    }

                    $orderConfirmationMessage = sprintf(
                        "hello %s,i want to confirm order for this package:\n- %s",
                        $influencerName,
                        implode("\n- ", $packageLines)
                    );

                    Message::create([
                        'conversation_id' => $conversation->id,
                        'sender_user_id'  => $user->id,
                        'sender_role'     => $user->user_type,
                        'message'         => $orderConfirmationMessage,
                        'read_at'         => null
                    ]);

                    $conversation->touch();
                    $latestConversation = $conversation;
                }

                return $parent;
            });

            // Clear cart
            $cart->items()->delete();

            if ($latestConversation) {
                return redirect()
                    ->route('frontend.conversations.show', $latestConversation->public_id)
                    ->with('success', 'Orders created! Latest conversation opened.');
            }

            return redirect()
                ->route('frontend.conversations.index')
                ->with('success', 'Orders created!');
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
        $cart->load(['items.package.influencer.user']);

        $items = $cart->items->map(function ($item) {
            $influencerUser = $item->package->influencer->user;
            $avatarPath = $influencerUser->profile_image_path
                ?? $item->package->influencer->profile_image_path
                ?? '/default.webp';
            $avatarUrl = image_url($avatarPath);

            return [
                'id'            => $item->id,
                'name'          => $influencerUser->name,
                'package'       => $item->package->name,
                'price'         => (int) $item->unit_price,
                'quantity'      => $item->quantity,
                'image'         => $avatarUrl,
                'avatar_url'    => $avatarUrl,
                'influencer_id' => $item->influencer_id,
                'country'       => $influencerUser->country ?? null
            ];
        })->toArray();

        $subtotal = $cart->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });

        $pricing = PlatformPricing::calculateFromNet((float) $subtotal);

        return [
            'items'         => $items,
            'subtotal'      => $pricing['net_subtotal'],
            'platformCharge' => $pricing['platform_charge'],
            'total'         => $pricing['gross_total'],
            'itemCount'     => $cart->items->count(),
            'totalQuantity' => $cart->items->sum('quantity')
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
