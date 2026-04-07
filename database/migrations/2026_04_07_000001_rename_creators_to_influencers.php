<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rename all 'creators' related tables and columns to 'influencers' to avoid confusion
     * with the CREATE action. This single migration handles all naming convention updates.
     */
    public function up(): void
    {
        // Step 1: Drop all foreign keys first using raw SQL
        $this->dropForeignKeys();

        // Step 2: Rename main creators table to influencers
        if (Schema::hasTable('creators')) {
            Schema::rename('creators', 'influencers');
        }

        // Step 3: Rename creator-related tables
        if (Schema::hasTable('creator_portfolios')) {
            Schema::rename('creator_portfolios', 'influencer_portfolios');
        }
        if (Schema::hasTable('creator_social_links')) {
            Schema::rename('creator_social_links', 'influencer_social_links');
        }
        if (Schema::hasTable('creator_platform_stats')) {
            Schema::rename('creator_platform_stats', 'influencer_platform_stats');
        }
        if (Schema::hasTable('creator_categories')) {
            Schema::rename('creator_categories', 'influencer_categories');
        }
        if (Schema::hasTable('creator_badges')) {
            Schema::rename('creator_badges', 'influencer_badges');
        }

        // Step 4: Rename creator_id columns to influencer_id
        foreach (['packages', 'cart_items', 'order_items', 'conversations', 'reviews', 'payout_accounts', 'payouts', 'campaign_applications', 'campaign_influencers', 'moderator_assignments', 'sub_orders', 'wishlist_items'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'creator_id')) {
                        $table->renameColumn('creator_id', 'influencer_id');
                    }
                });
            }
        }

        // Step 5: Rename special columns
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'accepted_for_creator_id')) {
                    $table->renameColumn('accepted_for_creator_id', 'accepted_for_influencer_id');
                }
            });
        }
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'on_behalf_of_creator_id')) {
                    $table->renameColumn('on_behalf_of_creator_id', 'on_behalf_of_influencer_id');
                }
            });
        }

        // Step 6: Recreate foreign keys with new column/table names
        $this->createForeignKeys();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all new foreign keys
        $this->dropForeignKeysReverse();

        // Reverse table renames
        if (Schema::hasTable('influencers')) {
            Schema::rename('influencers', 'creators');
        }
        if (Schema::hasTable('influencer_portfolios')) {
            Schema::rename('influencer_portfolios', 'creator_portfolios');
        }
        if (Schema::hasTable('influencer_social_links')) {
            Schema::rename('influencer_social_links', 'creator_social_links');
        }
        if (Schema::hasTable('influencer_platform_stats')) {
            Schema::rename('influencer_platform_stats', 'creator_platform_stats');
        }
        if (Schema::hasTable('influencer_categories')) {
            Schema::rename('influencer_categories', 'creator_categories');
        }
        if (Schema::hasTable('influencer_badges')) {
            Schema::rename('influencer_badges', 'creator_badges');
        }

        // Reverse column renames
        foreach (['packages', 'cart_items', 'order_items', 'conversations', 'reviews', 'payout_accounts', 'payouts', 'campaign_applications', 'campaign_influencers', 'moderator_assignments', 'sub_orders', 'wishlist_items'] as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'influencer_id')) {
                        $table->renameColumn('influencer_id', 'creator_id');
                    }
                });
            }
        }

        // Reverse special column renames
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'accepted_for_influencer_id')) {
                    $table->renameColumn('accepted_for_influencer_id', 'accepted_for_creator_id');
                }
            });
        }
        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                if (Schema::hasColumn('messages', 'on_behalf_of_influencer_id')) {
                    $table->renameColumn('on_behalf_of_influencer_id', 'on_behalf_of_creator_id');
                }
            });
        }

        // Recreate original foreign keys
        $this->createForeignKeysReverse();
    }

    private function dropForeignKeys(): void
    {
        // Drop all foreign keys using raw SQL - try all possible names
        $constraints = [
            'packages'              => ['packages_creator_id_foreign'],
            'cart_items'            => ['cart_items_creator_id_foreign'],
            'order_items'           => ['order_items_creator_id_foreign'],
            'conversations'         => ['conversations_creator_id_foreign'],
            'reviews'               => ['reviews_creator_id_foreign'],
            'payout_accounts'       => ['payout_accounts_creator_id_foreign'],
            'payouts'               => ['payouts_creator_id_foreign'],
            'campaign_applications' => ['campaign_applications_creator_id_foreign'],
            'campaign_influencers'  => ['campaign_influencers_creator_id_foreign'],
            'moderator_assignments' => ['moderator_assignments_creator_id_foreign'],
            'sub_orders'            => ['sub_orders_creator_id_foreign'],
            'wishlist_items'        => ['wishlist_items_creator_id_foreign'],
            'orders'                => ['orders_accepted_for_creator_id_foreign'],
            'messages'              => ['messages_on_behalf_of_creator_id_foreign']
        ];

        foreach ($constraints as $table => $fkNames) {
            if (Schema::hasTable($table)) {
                foreach ($fkNames as $fkName) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
                    } catch (\Exception $e) {
                        // Constraint doesn't exist, skip
                    }
                }
            }
        }
    }

    private function dropForeignKeysReverse(): void
    {
        // Drop all new foreign keys using raw SQL
        $constraints = [
            'packages'              => ['packages_influencer_id_foreign'],
            'cart_items'            => ['cart_items_influencer_id_foreign'],
            'order_items'           => ['order_items_influencer_id_foreign'],
            'conversations'         => ['conversations_influencer_id_foreign'],
            'reviews'               => ['reviews_influencer_id_foreign'],
            'payout_accounts'       => ['payout_accounts_influencer_id_foreign'],
            'payouts'               => ['payouts_influencer_id_foreign'],
            'campaign_applications' => ['campaign_applications_influencer_id_foreign'],
            'campaign_influencers'  => ['campaign_influencers_influencer_id_foreign'],
            'moderator_assignments' => ['moderator_assignments_influencer_id_foreign'],
            'sub_orders'            => ['sub_orders_influencer_id_foreign'],
            'wishlist_items'        => ['wishlist_items_influencer_id_foreign'],
            'orders'                => ['orders_accepted_for_influencer_id_foreign'],
            'messages'              => ['messages_on_behalf_of_influencer_id_foreign']
        ];

        foreach ($constraints as $table => $fkNames) {
            if (Schema::hasTable($table)) {
                foreach ($fkNames as $fkName) {
                    try {
                        DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fkName}`");
                    } catch (\Exception $e) {
                        // Constraint doesn't exist, skip
                    }
                }
            }
        }
    }

    private function createForeignKeys(): void
    {
        // Recreate foreign keys with influencer naming
        $configs = [
            ['table' => 'packages', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'cart_items', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'order_items', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'conversations', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'reviews', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'payout_accounts', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'payouts', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'campaign_applications', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'campaign_influencers', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'moderator_assignments', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'sub_orders', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'wishlist_items', 'column' => 'influencer_id', 'on' => 'influencers'],
            ['table' => 'orders', 'column' => 'accepted_for_influencer_id', 'on' => 'influencers'],
            ['table' => 'messages', 'column' => 'on_behalf_of_influencer_id', 'on' => 'influencers']
        ];

        foreach ($configs as $config) {
            if (Schema::hasTable($config['table']) && Schema::hasColumn($config['table'], $config['column'])) {
                try {
                    Schema::table($config['table'], function (Blueprint $table) use ($config) {
                        $table->foreign($config['column'])->references('id')->on($config['on'])->cascadeOnDelete();
                    });
                } catch (\Exception $e) {
                    // Foreign key already exists or other conflict, skip
                }
            }
        }
    }

    private function createForeignKeysReverse(): void
    {
        // Recreate foreign keys with creator naming
        $configs = [
            ['table' => 'packages', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'cart_items', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'order_items', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'conversations', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'reviews', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'payout_accounts', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'payouts', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'campaign_applications', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'campaign_influencers', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'moderator_assignments', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'sub_orders', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'wishlist_items', 'column' => 'creator_id', 'on' => 'creators'],
            ['table' => 'orders', 'column' => 'accepted_for_creator_id', 'on' => 'creators'],
            ['table' => 'messages', 'column' => 'on_behalf_of_creator_id', 'on' => 'creators']
        ];

        foreach ($configs as $config) {
            if (Schema::hasTable($config['table']) && Schema::hasColumn($config['table'], $config['column'])) {
                try {
                    Schema::table($config['table'], function (Blueprint $table) use ($config) {
                        $table->foreign($config['column'])->references('id')->on($config['on'])->cascadeOnDelete();
                    });
                } catch (\Exception $e) {
                    // Foreign key already exists or other conflict, skip
                }
            }
        }
    }
};
