<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Creator;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportAndMessagingSeeder extends Seeder
{
    /**
     * Seed support tickets and moderator-on-behalf conversation flows.
     */
    public function run(): void
    {
        $faker = fake();

        $supportCategories = DB::table('support_categories')->pluck('id');
        $requesters        = User::whereIn('user_type', ['brand', 'creator'])->get();
        $handlers          = User::whereIn('user_type', ['moderator', 'admin'])->get();

        if ($supportCategories->isEmpty() || $requesters->isEmpty() || $handlers->isEmpty()) {
            return;
        }

        for ($i = 1; $i <= 20; $i++) {
            $requester    = $requesters->random();
            $handler      = $handlers->random();
            $ticketStatus = $faker->randomElement(['open', 'in_progress', 'resolved', 'closed']);

            $ticketId = DB::table('support_tickets')->insertGetId([
                'ticket_number'       => 'ST-' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'requester_user_id'   => $requester->id,
                'support_category_id' => $supportCategories->random(),
                'assigned_to_user_id' => $handler->id,
                'subject'             => $faker->randomElement([
                    'Unable to upload campaign assets',
                    'Need update on pending order',
                    'Profile visibility issue',
                    'Payment confirmation pending'
                ]),
                'description'         => $faker->paragraph(),
                'priority'            => $faker->randomElement(['low', 'medium', 'high']),
                'status'              => $ticketStatus,
                'source'              => 'web',
                'resolved_at'         => in_array($ticketStatus, ['resolved', 'closed'], true) ? now()->subDays(rand(0, 5)) : null,
                'closed_at'           => $ticketStatus === 'closed' ? now()->subDays(rand(0, 3)) : null,
                'created_at'          => now()->subDays(rand(0, 15)),
                'updated_at'          => now()
            ]);

            $firstMessageId = DB::table('support_ticket_messages')->insertGetId([
                'support_ticket_id' => $ticketId,
                'sender_user_id'    => $requester->id,
                'message'           => $faker->sentence(16),
                'is_internal_note'  => false,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            DB::table('support_ticket_messages')->insert([
                'support_ticket_id' => $ticketId,
                'sender_user_id'    => $handler->id,
                'message'           => $faker->sentence(14),
                'is_internal_note'  => false,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);

            if ($faker->boolean(40)) {
                DB::table('support_ticket_attachments')->insert([
                    'support_ticket_message_id' => $firstMessageId,
                    'uploaded_by_user_id'       => $requester->id,
                    'file_path'                 => 'uploads/support/ticket-' . $ticketId . '-context.png',
                    'file_name'                 => 'context-screenshot.png',
                    'mime_type'                 => 'image/png',
                    'file_size'                 => rand(80000, 250000),
                    'created_at'                => now(),
                    'updated_at'                => now()
                ]);
            }
        }

        $orders = Order::with('items')->whereIn('status', ['accepted', 'in_progress', 'completed'])->take(20)->get();

        foreach ($orders as $order) {
            if (!$order->items->count()) {
                continue;
            }

            foreach ($order->items as $item) {
                $creator = Creator::find($item->creator_id);
                if (!$creator) {
                    continue;
                }

                $handler     = $order->acceptedBy ?? $handlers->random();
                $handlerRole = $handler->user_type === 'admin' ? 'admin' : 'moderator';

                $conversation = Conversation::create([
                    'conversation_type'              => 'order',
                    'creator_id'                     => $creator->id,
                    'brand_user_id'                  => $order->buyer_user_id,
                    'handled_by_user_id'             => $handler->id,
                    'order_id'                       => $order->id,
                    'creator_direct_message_enabled' => false,
                    'title'                          => 'Order #' . $order->order_number . ' - Creator Coordination'
                ]);

                ConversationParticipant::create([
                    'conversation_id'  => $conversation->id,
                    'user_id'          => $order->buyer_user_id,
                    'participant_role' => 'brand',
                    'joined_at'        => now()->subDays(rand(0, 10))
                ]);

                ConversationParticipant::create([
                    'conversation_id'  => $conversation->id,
                    'user_id'          => $handler->id,
                    'participant_role' => $handlerRole,
                    'joined_at'        => now()->subDays(rand(0, 10))
                ]);

                Message::create([
                    'conversation_id'         => $conversation->id,
                    'sender_user_id'          => $order->buyer_user_id,
                    'sender_role'             => 'brand',
                    'on_behalf_of_creator_id' => null,
                    'message'                 => 'Please align deliverable timeline with campaign milestones.'
                ]);

                Message::create([
                    'conversation_id'         => $conversation->id,
                    'sender_user_id'          => $handler->id,
                    'sender_role'             => $handlerRole,
                    'on_behalf_of_creator_id' => $creator->id,
                    'message'                 => 'Creator side acknowledged. Draft assets will be shared within 48 hours.'
                ]);

                DB::table('notifications')->insert([
                    [
                        'user_id'    => $order->buyer_user_id,
                        'type'       => 'conversation.update',
                        'title'      => 'Conversation updated',
                        'body'       => 'Moderator replied on behalf of creator.',
                        'data_json'  => json_encode(['conversation_id' => $conversation->id]),
                        'is_read'    => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'user_id'    => $handler->id,
                        'type'       => 'conversation.assigned',
                        'title'      => 'Conversation assigned',
                        'body'       => 'You are handling an order conversation.',
                        'data_json'  => json_encode(['conversation_id' => $conversation->id]),
                        'is_read'    => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'user_id'    => $creator->user_id,
                        'type'       => 'creator.thread_update',
                        'title'      => 'Conversation handled by moderator',
                        'body'       => 'A moderator sent a brand update on your behalf.',
                        'data_json'  => json_encode(['conversation_id' => $conversation->id]),
                        'is_read'    => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }
        }

        $brandUsers = User::where('user_type', 'brand')->get();
        $creators   = Creator::all();

        for ($i = 0; $i < 10; $i++) {
            if ($brandUsers->isEmpty() || $creators->isEmpty()) {
                break;
            }

            $brandUser   = $brandUsers->random();
            $creator     = $creators->random();
            $handler     = $handlers->random();
            $handlerRole = $handler->user_type === 'admin' ? 'admin' : 'moderator';

            $conversation = Conversation::create([
                'conversation_type'              => 'creator_profile',
                'creator_id'                     => $creator->id,
                'brand_user_id'                  => $brandUser->id,
                'handled_by_user_id'             => $handler->id,
                'order_id'                       => null,
                'creator_direct_message_enabled' => false,
                'title'                          => 'Creator Profile Inquiry'
            ]);

            ConversationParticipant::create([
                'conversation_id'  => $conversation->id,
                'user_id'          => $brandUser->id,
                'participant_role' => 'brand',
                'joined_at'        => now()
            ]);

            ConversationParticipant::create([
                'conversation_id'  => $conversation->id,
                'user_id'          => $handler->id,
                'participant_role' => $handlerRole,
                'joined_at'        => now()
            ]);

            Message::create([
                'conversation_id'         => $conversation->id,
                'sender_user_id'          => $brandUser->id,
                'sender_role'             => 'brand',
                'on_behalf_of_creator_id' => null,
                'message'                 => 'Interested in your creator profile for an upcoming campaign.'
            ]);

            Message::create([
                'conversation_id'         => $conversation->id,
                'sender_user_id'          => $handler->id,
                'sender_role'             => $handlerRole,
                'on_behalf_of_creator_id' => $creator->id,
                'message'                 => 'Thanks for reaching out. Sharing creator availability and package recommendations.'
            ]);
        }
    }
}
