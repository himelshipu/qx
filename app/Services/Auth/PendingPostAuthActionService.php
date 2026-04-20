<?php

namespace App\Services\Auth;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Conversation;
use App\Models\Influencer;
use App\Models\Message;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class PendingPostAuthActionService
{
    private const MAX_CART_INFLUENCERS = 5;

    public const ACTION_KEY = 'auth_pending_action';

    public const ACTION_ADD_TO_CART = 'add_to_cart';

    public const ACTION_NEGOTIATE = 'negotiate';

    public const PACKAGE_ID_KEY = 'auth_pending_package_id';

    public const INFLUENCER_ID_KEY = 'auth_pending_influencer_id';

    public const RETURN_URL_KEY = 'auth_pending_return_url';

    private const BRAND_ONLY_MESSAGE = 'Only brand accounts can add to cart or negotiate packages.';

    private const LEGACY_KEYS = [
        'pending_conversation_id',
        'pending_influencer_id',
        'pending_package_id'
    ];

    public function rememberAddToCart(int $packageId, ?string $returnUrl = null): void
    {
        session([
            self::ACTION_KEY     => self::ACTION_ADD_TO_CART,
            self::PACKAGE_ID_KEY => $packageId,
            self::RETURN_URL_KEY => $returnUrl ?: url()->previous()
        ]);
    }

    public function rememberNegotiate(int $influencerId, ?string $returnUrl = null): void
    {
        session([
            self::ACTION_KEY        => self::ACTION_NEGOTIATE,
            self::INFLUENCER_ID_KEY => $influencerId,
            self::RETURN_URL_KEY    => $returnUrl ?: url()->previous()
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
        $returnUrl = $this->resolveReturnUrl();

        if ($user->user_type !== 'brand') {
            $this->clearPendingKeys();

            return redirect($returnUrl)
                ->with('warning', self::BRAND_ONLY_MESSAGE)
                ->with('brand_action_required_modal', true)
                ->with('brand_action_required_message', self::BRAND_ONLY_MESSAGE);
        }

        $this->clearPendingKeys();

        if ($packageId <= 0) {
            return redirect($returnUrl)->with('warning', 'Please add a package to continue.');
        }

        $package = Package::find($packageId);

        if (!$package) {
            return redirect($returnUrl)->with('error', 'The selected package is no longer available.');
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

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
                    ->with('warning', 'You can not add more than 5 influencers before placing your current orders.')
                    ->with('auto_open_cart_sidebar', true);
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

        $cart->update([
            'status' => 'active'
        ]);

        return redirect($returnUrl)
            ->with('success', 'Package added to cart successfully.')
            ->with('auto_open_cart_sidebar', true);
    }

    private function consumeNegotiate(User $user): RedirectResponse
    {
        $influencerId = (int) session(self::INFLUENCER_ID_KEY);
        $returnUrl    = $this->resolveReturnUrl();

        if ($user->user_type !== 'brand') {
            $this->clearPendingKeys();

            return redirect($returnUrl)
                ->with('brand_action_required_modal', true)
                ->with('brand_action_required_message', self::BRAND_ONLY_MESSAGE);
        }

        $this->clearPendingKeys();

        $influencer = Influencer::find($influencerId);

        if (!$influencer) {
            return redirect($returnUrl)->with('error', 'The selected influencer is no longer available.');
        }

        $conversation = Conversation::query()
            ->where('brand_user_id', $user->id)
            ->where('influencer_id', $influencer->id)
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'brand_user_id' => $user->id,
                'influencer_id' => $influencer->id
            ]);
        }

        $influencerName = $influencer->display_name ?: ($influencer->user?->name ?? 'there');
        $messageText    = sprintf('hello %s,i want to discuss with you for a custom package', $influencerName);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id'  => $user->id,
            'sender_role'     => $user->user_type,
            'message'         => $messageText,
            'read_at'         => null
        ]);

        $conversation->touch();

        return redirect()
            ->route('frontend.conversations.show', ['conversation' => $conversation])
            ->with('success', 'Negotiation started successfully.');
    }

    private function resolveReturnUrl(): string
    {
        $returnUrl = (string) session(self::RETURN_URL_KEY, '');

        if ($returnUrl !== '' && str_starts_with($returnUrl, url('/'))) {
            return $returnUrl;
        }

        return route('home');
    }

    private function clearPendingKeys(): void
    {
        session()->forget([
            self::ACTION_KEY,
            self::PACKAGE_ID_KEY,
            self::INFLUENCER_ID_KEY,
            self::RETURN_URL_KEY,
            ...self::LEGACY_KEYS
        ]);
    }
}
