<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Creator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommerceSeeder extends Seeder
{
    /**
     * Seed package, cart, and order lifecycle data.
     */
    public function run(): void
    {
        $faker = fake();

        $managerIds = User::whereIn('user_type', ['admin', 'moderator'])->pluck('id');
        $brandUsers = User::where('user_type', 'brand')->with('brand')->get();
        $creators   = Creator::all();
        $campaigns  = Campaign::where('is_active', true)->get();

        if ($managerIds->isEmpty() || $brandUsers->isEmpty() || $creators->isEmpty()) {
            return;
        }

        $platforms = ['instagram', 'tiktok', 'youtube', 'ugc', 'other'];
        for ($i = 1; $i <= 18; $i++) {
            Package::create([
                'platform'           => $faker->randomElement($platforms),
                'name'               => $faker->randomElement(['Starter', 'Growth', 'Launch', 'Premium']) . ' Package #' . $i,
                'description'        => $faker->sentence(14),
                'base_price'         => rand(80, 1400),
                'currency'           => 'USD',
                'delivery_days'      => rand(3, 14),
                'revisions_included' => rand(1, 4),
                'created_by_user_id' => $managerIds->random(),
                'is_active'          => true
            ]);
        }

        $packages = Package::all();
        if ($packages->isEmpty()) {
            return;
        }

        $orderCounter = 1000;

        foreach ($brandUsers as $brandUser) {
            $cart = Cart::create([
                'user_id'    => $brandUser->id,
                'status'     => 'active',
                'expires_at' => now()->addDays(7)
            ]);

            $selectedCreators = $creators->random(min(rand(2, 4), $creators->count()));
            $selectedCreators = $selectedCreators instanceof Creator ? collect([$selectedCreators]) : $selectedCreators;

            foreach ($selectedCreators as $creator) {
                $package    = $packages->random();
                $quantity   = rand(1, 2);
                $campaignId = $faker->boolean(50) && $campaigns->isNotEmpty() ? $campaigns->random()->id : null;

                CartItem::create([
                    'cart_id'     => $cart->id,
                    'package_id'  => $package->id,
                    'creator_id'  => $creator->id,
                    'campaign_id' => $campaignId,
                    'quantity'    => $quantity,
                    'unit_price'  => $package->base_price,
                    'currency'    => 'USD',
                    'notes'       => $faker->sentence()
                ]);
            }

            if (!$faker->boolean(80)) {
                $cart->update(['status' => 'abandoned']);
                continue;
            }

            $managerId   = $managerIds->random();
            $orderStatus = $faker->randomElement(['pending', 'accepted', 'in_progress', 'completed']);
            $acceptedBy  = $orderStatus === 'pending' ? null : $managerId;

            $cartItems   = $cart->items()->with(['creator', 'package'])->get();
            $subtotal    = $cartItems->sum(fn(CartItem $item) => (float) $item->unit_price * $item->quantity);
            $serviceFee  = round($subtotal * 0.08, 2);
            $taxAmount   = round($subtotal * 0.05, 2);
            $totalAmount = round($subtotal + $serviceFee + $taxAmount, 2);

            $firstCreatorId = $cartItems->first()?->creator_id;

            $order = Order::create([
                'order_number'            => 'ORD-' . now()->format('ymd') . '-' . $orderCounter++,
                'buyer_user_id'           => $brandUser->id,
                'brand_id'                => $brandUser->brand?->id,
                'campaign_id'             => $cartItems->firstWhere('campaign_id', '!=', null)?->campaign_id,
                'status'                  => $orderStatus,
                'accepted_by_user_id'     => $acceptedBy,
                'accepted_for_creator_id' => $acceptedBy ? $firstCreatorId : null,
                'subtotal'                => $subtotal,
                'service_fee'             => $serviceFee,
                'tax_amount'              => $taxAmount,
                'total_amount'            => $totalAmount,
                'currency'                => 'USD',
                'placed_at'               => now()->subDays(rand(1, 20)),
                'accepted_at'             => $acceptedBy ? now()->subDays(rand(0, 10)) : null,
                'completed_at'            => $orderStatus === 'completed' ? now()->subDays(rand(0, 5)) : null
            ]);

            $statusToItemStatus = [
                'pending'     => 'pending',
                'accepted'    => 'accepted',
                'in_progress' => 'in_progress',
                'completed'   => 'approved'
            ];

            $createdOrderItems = collect();
            foreach ($cartItems as $cartItem) {
                $lineTotal = (float) $cartItem->unit_price * $cartItem->quantity;

                $item = OrderItem::create([
                    'order_id'            => $order->id,
                    'creator_id'          => $cartItem->creator_id,
                    'package_id'          => $cartItem->package_id,
                    'campaign_id'         => $cartItem->campaign_id,
                    'title'               => $cartItem->package->name . ' for ' . $cartItem->creator->display_name,
                    'description'         => $cartItem->notes,
                    'quantity'            => $cartItem->quantity,
                    'unit_price'          => $cartItem->unit_price,
                    'line_total'          => $lineTotal,
                    'status'              => $statusToItemStatus[$orderStatus],
                    'due_date'            => now()->addDays(rand(5, 21))->toDateString(),
                    'accepted_by_user_id' => $acceptedBy,
                    'accepted_at'         => $acceptedBy ? now()->subDays(rand(0, 8)) : null,
                    'delivered_at'        => $orderStatus === 'completed' ? now()->subDays(rand(0, 3)) : null,
                    'approved_at'         => $orderStatus === 'completed' ? now()->subDays(rand(0, 2)) : null
                ]);

                $createdOrderItems->push($item);
            }

            DB::table('order_status_history')->insert([
                [
                    'order_id'           => $order->id,
                    'old_status'         => null,
                    'new_status'         => 'pending',
                    'changed_by_user_id' => $brandUser->id,
                    'note'               => 'Order submitted from cart.',
                    'created_at'         => now(),
                    'updated_at'         => now()
                ],
                [
                    'order_id'           => $order->id,
                    'old_status'         => 'pending',
                    'new_status'         => $orderStatus,
                    'changed_by_user_id' => $acceptedBy,
                    'note'               => $acceptedBy ? 'Order reviewed by moderator/admin.' : 'Awaiting moderation.',
                    'created_at'         => now(),
                    'updated_at'         => now()
                ]
            ]);

            DB::table('order_messages')->insert([
                [
                    'order_id'       => $order->id,
                    'sender_user_id' => $brandUser->id,
                    'message'        => 'Please prioritize content delivery this week.',
                    'is_system'      => false,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ],
                [
                    'order_id'       => $order->id,
                    'sender_user_id' => $acceptedBy ?? $brandUser->id,
                    'message'        => $acceptedBy ? 'Order accepted and assigned on creator side.' : 'Order is in moderation queue.',
                    'is_system'      => false,
                    'created_at'     => now(),
                    'updated_at'     => now()
                ]
            ]);

            if ($orderStatus !== 'pending') {
                DB::table('payments')->insert([
                    'order_id'            => $order->id,
                    'payment_provider'    => 'stripe',
                    'provider_payment_id' => 'pi_' . strtolower($faker->bothify('??######')),
                    'amount'              => $order->total_amount,
                    'currency'            => 'USD',
                    'status'              => $orderStatus === 'completed' ? 'captured' : 'authorized',
                    'paid_at'             => $orderStatus === 'completed' ? now()->subDays(rand(0, 4)) : null,
                    'created_at'          => now(),
                    'updated_at'          => now()
                ]);
            }

            if ($orderStatus === 'completed') {
                $groupedByCreator = $createdOrderItems->groupBy('creator_id');

                foreach ($groupedByCreator as $creatorId => $items) {
                    $accountId = DB::table('payout_accounts')
                        ->where('creator_id', $creatorId)
                        ->value('id');

                    if (!$accountId) {
                        $accountId = DB::table('payout_accounts')->insertGetId([
                            'creator_id'         => $creatorId,
                            'provider'           => 'bank_transfer',
                            'account_identifier' => 'acct_' . strtolower($faker->bothify('??######')),
                            'account_name'       => $faker->name(),
                            'is_default'         => true,
                            'is_active'          => true,
                            'created_at'         => now(),
                            'updated_at'         => now()
                        ]);
                    }

                    $payoutAmount = round($items->sum('line_total') * 0.8, 2);
                    $payoutId     = DB::table('payouts')->insertGetId([
                        'creator_id'         => $creatorId,
                        'payout_account_id'  => $accountId,
                        'amount'             => $payoutAmount,
                        'currency'           => 'USD',
                        'status'             => 'paid',
                        'external_payout_id' => 'po_' . strtolower($faker->bothify('??######')),
                        'paid_at'            => now()->subDays(rand(0, 2)),
                        'created_at'         => now(),
                        'updated_at'         => now()
                    ]);

                    foreach ($items as $item) {
                        DB::table('payout_items')->insert([
                            'payout_id'     => $payoutId,
                            'order_item_id' => $item->id,
                            'amount'        => round((float) $item->line_total * 0.8, 2),
                            'currency'      => 'USD',
                            'created_at'    => now(),
                            'updated_at'    => now()
                        ]);

                        if ($brandUser->brand) {
                            Review::create([
                                'order_item_id' => $item->id,
                                'brand_id'      => $brandUser->brand->id,
                                'creator_id'    => $item->creator_id,
                                'rating'        => rand(4, 5),
                                'title'         => $faker->randomElement(['Great collaboration', 'Strong performance', 'Smooth communication']),
                                'comment'       => $faker->sentence(10),
                                'is_public'     => true
                            ]);
                        }
                    }
                }
            }

            $cart->update(['status' => 'converted']);
        }

        foreach ($brandUsers as $brandUser) {
            $wishlistId = DB::table('wishlists')->insertGetId([
                'user_id'    => $brandUser->id,
                'name'       => 'Top Picks',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $savedCreators = $creators->random(min(rand(3, 6), $creators->count()));
            $savedCreators = $savedCreators instanceof Creator ? collect([$savedCreators]) : $savedCreators;

            foreach ($savedCreators as $creator) {
                DB::table('wishlist_items')->insert([
                    'wishlist_id' => $wishlistId,
                    'creator_id'  => $creator->id,
                    'notes'       => $faker->randomElement(['Potential fit', 'Great audience overlap', 'Good UGC quality']),
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        }
    }
}
