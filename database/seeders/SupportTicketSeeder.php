<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportTicketSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $requesters = DB::table('users')->pluck('id')->all();
        $categories = DB::table('support_categories')->pluck('id')->all();
        $agents = DB::table('users')
            ->whereIn('user_type', ['admin', 'moderator'])
            ->pluck('id')
            ->all();

        if (empty($requesters) || empty($categories)) {
            return;
        }

        $statuses = ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $sources = ['web', 'email', 'admin'];

        for ($i = 1; $i <= 20; $i++) {
            $status = $faker->randomElement($statuses);
            $createdAt = now()->subDays(random_int(1, 60));
            $resolvedAt = in_array($status, ['resolved', 'closed'], true) ? $createdAt->copy()->addDays(random_int(1, 7)) : null;
            $closedAt = $status === 'closed' && $resolvedAt ? $resolvedAt->copy()->addDays(random_int(0, 3)) : null;
            $requesterId = $faker->randomElement($requesters);

            DB::table('support_tickets')->updateOrInsert(
                ['ticket_number' => sprintf('TK-%06d', $i)],
                [
                    'requester_user_id' => $requesterId,
                    'support_category_id' => $faker->randomElement($categories),
                    'assigned_to_user_id' => !empty($agents) ? $faker->randomElement($agents) : null,
                    'subject' => $faker->randomElement([
                        'Unable to update billing details',
                        'Campaign status not updating',
                        'Order marked delivered but file missing',
                        'Account verification code issue',
                    ]),
                    'description' => $faker->paragraphs(2, true),
                    'priority' => $faker->randomElement($priorities),
                    'status' => $status,
                    'source' => $faker->randomElement($sources),
                    'resolved_at' => $resolvedAt,
                    'closed_at' => $closedAt,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
