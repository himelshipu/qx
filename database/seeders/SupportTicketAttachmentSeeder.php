<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportTicketAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $messages = DB::table('support_ticket_messages')->get(['id', 'sender_user_id']);
        $extensions = [
            ['ext' => 'png', 'mime' => 'image/png'],
            ['ext' => 'jpg', 'mime' => 'image/jpeg'],
            ['ext' => 'pdf', 'mime' => 'application/pdf'],
        ];

        foreach ($messages as $message) {
            if (random_int(1, 10) > 3) {
                continue;
            }

            $file = $extensions[array_rand($extensions)];
            $fileName = 'attachment-' . $message->id . '.' . $file['ext'];

            DB::table('support_ticket_attachments')->updateOrInsert(
                [
                    'support_ticket_message_id' => $message->id,
                    'file_name' => $fileName,
                ],
                [
                    'uploaded_by_user_id' => $message->sender_user_id,
                    'file_path' => 'support/tickets/' . $message->id . '/' . $fileName,
                    'mime_type' => $file['mime'],
                    'file_size' => random_int(90000, 3500000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
