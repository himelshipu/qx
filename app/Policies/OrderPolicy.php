<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Admin can view all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can view orders for their own brand
        if ($user->user_type === 'brand' && $order->brand_id === $user->brand?->id) {
            return true;
        }

        // Creator can view orders where they have items
        if ($user->user_type === 'influencer') {
            return $order->items()
                ->where('influencer_id', $user->influencer?->id)
                ->exists();
        }

        return false;
    }

    /**
     * Determine if the user can create orders.
     */
    public function create(User $user): bool
    {
        return in_array($user->user_type, ['brand', 'admin', 'superadmin']);
    }

    /**
     * Determine if the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Admin can update all
        if (in_array($user->user_type, ['admin', 'superadmin'])) {
            return true;
        }

        // Brand can update their own orders (only status to some extent)

        return $user->user_type === 'brand' && $order->brand_id === $user->brand?->id;
    }
}
