<?php

namespace App\Services\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Conversation;
use App\Models\Creator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class PendingPostAuthActionService
{
    public const ACTION_KEY = 'auth_pending_action';
    public const ACTION_ADD_TO_CART = 'add_to_cart';
    public const ACTION_NEGOTIATE = 'negotiate';

    public const PACKAGE_ID_KEY = 'auth_pending_package_id';
    public const CREATOR_ID_KEY = 'auth_pending_creator_id';

    private const LEGACY_KEYS = [
        'pending_conversation_id',
        'pending_creator_id',
        'pending_package_id',
    ];

    public function rememberAddToCart(int $packageId): void
    {
        session([
            self::ACTION_KEY => self::ACTION_ADD_TO_CART,
            self::PACKAGE_ID_KEY => $packageId,
        ]);
    }

    public function rememberNegotiate(int $creatorId): void
    {
        session([
            self::ACTION_KEY => self::ACTION_NEGOTIATE,
            self::CREATOR_ID_KEY => $creatorId,
        ]);
    }

    public function consume(User $user): ?RedirectResponse
    {
        $action = session(self::ACTION_KEY);

        if ($action === self::ACTION_ADD_TO_CART) {
            return $this->consumeAddToCart($user);
        }

        if ($action === self::ACTION_NEGOTIATE) {
            return $this->consumeNegotiate($user);
        }

        // Safety cleanup for stale legacy keys so normal logins never jump to conversation unexpectedly.
        session()->forget(self::LEGACY_KEYS);

        return null;
    }

    private function consumeAddToCart(User $user): RedirectResponse
    {
        $packageId = (int) session(self::PACKAGE_ID_KEY);
        $this->clearPendingKeys();

        if ($packageId <= 0) {
            return redirect()->route('cart.index')->with('warning', 'Please add a package to continue.');
        }

        $package = Package::find($packageId);

        if (!$package) {
            return redirect()->route('cart.index')->with('error', 'The selected package is no longer available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $existingItem = $cart->items()
            ->where('package_id', $package->id)
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + 1,
            ]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'package_id' => $package->id,
                'creator_id' => $package->creator_id,
                'quantity' => 1,
                'unit_price' => $package->base_price,
                'currency' => $package->currency ?? 'USD',
            ]);
        }

        $cart->update([
            'status' => 'active',
        ]);

        return redirect()->route('cart.index')->with('success', 'Package added to cart successfully.');
    }

    private function consumeNegotiate(User $user): RedirectResponse
    {
        $creatorId = (int) session(self::CREATOR_ID_KEY);
        $this->clearPendingKeys();

        if ($user->user_type !== 'brand') {
            return redirect()->route('dashboard.index')->with('error', 'Only brands can negotiate with creators.');
        }

        $creator = Creator::find($creatorId);

        if (!$creator) {
            return redirect()->route('dashboard.index')->with('error', 'The selected creator is no longer available.');
        }

        $conversation = Conversation::query()
            ->where('brand_user_id', $user->id)
            ->where('creator_id', $creator->id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'brand_user_id' => $user->id,
                'creator_id' => $creator->id,
            ]);
        }

        return redirect()
            ->route('dashboard.conversations.show', ['conversation' => $conversation])
            ->with('success', 'Negotiation started successfully.');
    }

    private function clearPendingKeys(): void
    {
        session()->forget([
            self::ACTION_KEY,
            self::PACKAGE_ID_KEY,
            self::CREATOR_ID_KEY,
            ...self::LEGACY_KEYS,
        ]);
    }
}
