<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportTicketMessageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('en_US');
        $tickets = DB::table('support_tickets')->get();

        foreach ($tickets as $ticket) {
            $participants = array_values(array_filter([
                $ticket->requester_user_id,
                $ticket->assigned_to_user_id,
            ]));

            if (empty($participants)) {
                continue;
            }

            $messageCount = random_int(2, 4);
            for ($i = 1; $i <= $messageCount; $i++) {
                $isInternal = (bool) ($ticket->assigned_to_user_id && random_int(1, 10) <= 2);
                $senderId = $isInternal && $ticket->assigned_to_user_id
                    ? $ticket->assigned_to_user_id
                    : $faker->randomElement($participants);

                DB::table('support_ticket_messages')->updateOrInsert(
                    [
                        'support_ticket_id' => $ticket->id,
                        'sender_user_id' => $senderId,
                        'message' => $isInternal
                            ? 'Internal note: escalation reviewed, awaiting user response.'
                            : $faker->randomElement([
                                'Thanks for the update. Could you please confirm the expected resolution timeline?',
                                'We checked your ticket and need one additional screenshot to proceed.',
                                'Issue reproduced on our side. A fix has been queued for deployment.',
                                'Your request has been verified and moved to the billing team.',
                            ]),
                    ],
                    [
                        'is_internal_note' => $isInternal,
                        'created_at' => now()->subDays(random_int(0, 14)),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
