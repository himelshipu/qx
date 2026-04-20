<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Get order items from completed orders only (realistic: only completed orders get reviews)
        $orderItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select('order_items.id', 'order_items.influencer_id', 'orders.brand_id')
            ->get();

        // Get existing reviews to avoid duplicates
        $existingReviews = DB::table('reviews')->pluck('order_item_id')->toArray();

        $reviews      = [];
        $reviewTitles = [
            'Amazing collaboration!',
            'Great experience working together',
            'Professional and creative',
            'Exceeded expectations',
            'Highly recommended',
            'Will definitely work again',
            'Excellent communication',
            'Outstanding results',
            'Smooth process from start to finish',
            'Very satisfied with the outcome'
        ];

        $reviewContents = [
            'The influencer was professional and delivered high-quality content ahead of schedule. The audience engagement was fantastic!',
            'Great collaboration! The content resonated well with our target audience and drove excellent engagement metrics.',
            'Very happy with the results. The influencer understood our brand vision perfectly and delivered exceptional content.',
            'Professional, reliable, and creative. Would definitely work with this influencer again on future campaigns.',
            'The campaign exceeded all our KPIs. The influencer went above and beyond expectations.',
            'Excellent communication throughout the project. The deliverables were submitted on time and were of high quality.',
            'One of the best collaborations we\'ve had. The influencer was responsive and flexible with changes.',
            'The content performed exceptionally well, generating great ROI for our campaign.',
            'Very professional approach. The influencer provided regular updates and delivered outstanding work.',
            'Smooth collaboration from start to finish. Highly recommend working with this influencer.'
        ];

        foreach ($orderItems as $item) {
            // Skip if review already exists for this order item
            if (in_array($item->id, $existingReviews)) {
                continue;
            }

            // Only create reviews for a portion of completed orders (80% realistically)
            if (rand(1, 100) <= 80) {
                $reviews[] = [
                    'order_item_id' => $item->id,
                    'brand_id'      => $item->brand_id,
                    'influencer_id' => $item->influencer_id,
                    'rating'        => $faker->numberBetween(3, 5), // Most reviews are positive (3-5 stars)
                    'title'         => $reviewTitles[array_rand($reviewTitles)],
                    'comment'       => $reviewContents[array_rand($reviewContents)],
                    'is_public'     => true, // Most reviews are public
                    'created_at'    => Carbon::now()->subDays(rand(1, 60)),
                    'updated_at'    => Carbon::now()
                ];
            }
        }

        // Insert reviews in batches to avoid memory issues
        if (!empty($reviews)) {
            foreach (array_chunk($reviews, 50) as $chunk) {
                DB::table('reviews')->insert($chunk);
            }

            $this->command->info('Created ' . count($reviews) . ' reviews');
        } else {
            $this->command->info('No new reviews to create');
        }
    }
}
