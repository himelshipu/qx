<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $faker             = \Faker\Factory::create('en_US');
        $orders            = DB::table('orders')->get();
        $packagesByInfluencer = DB::table('packages')->get()->groupBy('influencer_id');
        $orderItemStatuses = ['pending', 'accepted', 'in_progress', 'delivered', 'approved', 'rejected', 'cancelled'];

        foreach ($orders as $order) {
            $influencerId = $order->accepted_for_influencer_id ?: DB::table('influencers')->inRandomOrder()->value('id');
            if (!$influencerId) {
                continue;
            }

            $packageId = ($packagesByInfluencer[$influencerId] ?? collect())->random()->id ?? null;
            $quantity  = random_int(1, 2);
            $unitPrice = $packageId
            ? (float) DB::table('packages')->where('id', $packageId)->value('base_price')
            : (float) random_int(150, 900);
            $lineTotal   = (float) round($quantity * $unitPrice, 2);
            $status      = $faker->randomElement($orderItemStatuses);
            $acceptedAt  = in_array($status, ['accepted', 'in_progress', 'delivered', 'approved'], true) ? now()->subDays(random_int(1, 12)) : null;
            $deliveredAt = in_array($status, ['delivered', 'approved'], true) && $acceptedAt ? $acceptedAt->copy()->addDays(random_int(2, 7)) : null;
            $approvedAt  = $status === 'approved' && $deliveredAt ? $deliveredAt->copy()->addDays(random_int(1, 3)) : null;

            DB::table('order_items')->updateOrInsert(
                [
                    'order_id' => $order->id,
                    'title'    => $packageId ? (DB::table('packages')->where('id', $packageId)->value('name') ?? 'Influencer deliverable') : 'Influencer deliverable'
                ],
                [
                    'influencer_id'       => $influencerId,
                    'package_id'          => $packageId,
                    'campaign_id'         => $order->campaign_id,
                    'description'         => 'Deliverable as per brief requirements and brand guidelines.',
                    'quantity'            => $quantity,
                    'unit_price'          => $unitPrice,
                    'line_total'          => $lineTotal,
                    'status'              => $status,
                    'due_date'            => now()->addDays(random_int(5, 21))->toDateString(),
                    'accepted_by_user_id' => $acceptedAt ? $order->buyer_user_id : null,
                    'accepted_at'         => $acceptedAt,
                    'delivered_at'        => $deliveredAt,
                    'approved_at'         => $approvedAt,
                    'created_at'          => now(),
                    'updated_at'          => now()
                ]
            );
        }
    }
}
