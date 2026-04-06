<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add indexes for scalability and performance on conversations/messages tables
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Composite index for querying brand conversations
            if (!Schema::hasIndex('conversations', 'conversations_brand_user_id_updated_at_index')) {
                $table->index(['brand_user_id', 'updated_at'], 'conversations_brand_user_id_updated_at_index');
            }

            // Index for querying creator conversations
            if (!Schema::hasIndex('conversations', 'conversations_creator_id_index')) {
                $table->index('creator_id');
            }

            // Index for moderator-assigned conversations
            if (!Schema::hasIndex('conversations', 'conversations_handled_by_user_id_index')) {
                $table->index('handled_by_user_id');
            }

            // Index for order relationship lookups
            if (!Schema::hasIndex('conversations', 'conversations_order_id_index')) {
                $table->index('order_id');
            }

            // Index for public_id (route model binding)
            if (!Schema::hasIndex('conversations', 'conversations_public_id_index')) {
                $table->index('public_id');
            }

            // Composite index for brand + creator lookups (finding or creating conversations)
            if (!Schema::hasIndex('conversations', 'conversations_brand_creator_index')) {
                $table->index(['brand_user_id', 'creator_id']);
            }
        });

        Schema::table('messages', function (Blueprint $table) {
            // Index for querying messages in a conversation
            if (!Schema::hasIndex('messages', 'messages_conversation_id_created_at_index')) {
                $table->index(['conversation_id', 'created_at'], 'messages_conversation_id_created_at_index');
            }

            // Index for unread messages
            if (!Schema::hasIndex('messages', 'messages_conversation_id_read_at_index')) {
                $table->index(['conversation_id', 'read_at'], 'messages_conversation_id_read_at_index');
            }

            // Index for sender lookups
            if (!Schema::hasIndex('messages', 'messages_sender_user_id_index')) {
                $table->index('sender_user_id');
            }

            // Index for recent messages across all conversations
            if (!Schema::hasIndex('messages', 'messages_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndexIfExists('conversations_brand_user_id_updated_at_index');
            $table->dropIndexIfExists('conversations_creator_id_index');
            $table->dropIndexIfExists('conversations_handled_by_user_id_index');
            $table->dropIndexIfExists('conversations_order_id_index');
            $table->dropIndexIfExists('conversations_public_id_index');
            $table->dropIndexIfExists('conversations_brand_creator_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndexIfExists('messages_conversation_id_created_at_index');
            $table->dropIndexIfExists('messages_conversation_id_read_at_index');
            $table->dropIndexIfExists('messages_sender_user_id_index');
            $table->dropIndexIfExists('messages_created_at_index');
        });
    }
};
