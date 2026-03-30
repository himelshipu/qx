<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $brands = DB::table('brands')->get();
        $campaignByBrand = DB::table('campaigns')->get()->groupBy('brand_id');
        $creators = DB::table('creators')->pluck('id')->all();
        $statuses = ['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'];

        if (empty($creators)) {
            return;
        }

        $counter = 1;
        foreach ($brands as $brand) {
            $buyerUserId = $brand->user_id;
            $campaignIds = ($campaignByBrand[$brand->id] ?? collect())->pluck('id')->all();

            for ($i = 0; $i < 2; $i++) {
                $status = $faker->randomElement($statuses);
                $subtotal = random_int(300, 2500);
                $serviceFee = (float) round($subtotal * 0.08, 2);
                $tax = (float) round($subtotal * 0.05, 2);
                $total = (float) round($subtotal + $serviceFee + $tax, 2);
                $placedAt = now()->subDays(random_int(3, 50));

                $acceptedAt = in_array($status, ['accepted', 'in_progress', 'delivered', 'completed'], true)
                    ? $placedAt->copy()->addDay()
                    : null;
                $completedAt = $status === 'completed' ? $placedAt->copy()->addDays(random_int(7, 20)) : null;
                $cancelledAt = in_array($status, ['cancelled', 'refunded'], true) ? $placedAt->copy()->addDays(random_int(2, 7)) : null;

                DB::table('orders')->updateOrInsert(
                    ['order_number' => sprintf('ROCKIES-ORD-%06d', $counter)],
                    [
                        'buyer_user_id' => $buyerUserId,
                        'brand_id' => $brand->id,
                        'campaign_id' => !empty($campaignIds) ? $faker->randomElement($campaignIds) : null,
                        'status' => $status,
                        'accepted_by_user_id' => $acceptedAt ? $buyerUserId : null,
                        'accepted_for_creator_id' => $acceptedAt ? $faker->randomElement($creators) : null,
                        'subtotal' => $subtotal,
                        'service_fee' => $serviceFee,
                        'tax_amount' => $tax,
                        'total_amount' => $total,
                        'currency' => 'USD',
                        'placed_at' => $placedAt,
                        'accepted_at' => $acceptedAt,
                        'completed_at' => $completedAt,
                        'cancelled_at' => $cancelledAt,
                        'created_at' => $placedAt,
                        'updated_at' => now(),
                    ]
                );

                $counter++;
            }
        }
    }
}
