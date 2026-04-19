<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Add sub_order_id column
            if (!Schema::hasColumn('reviews', 'sub_order_id')) {
                $table->foreignId('sub_order_id')->nullable()->after('order_item_id')->constrained('sub_orders')->cascadeOnDelete();
            }

            // Make order_item_id nullable by modifying the column
            // Note: We use raw SQL since Laravel doesn't handle constraint changes well
        });

        // Drop unique constraint using raw SQL if it exists
        try {
            DB::statement('ALTER TABLE reviews MODIFY order_item_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE reviews DROP INDEX reviews_order_item_id_unique');
        } catch (\Exception $e) {
            // Index might not exist, that's ok
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            if (Schema::hasColumn('reviews', 'sub_order_id')) {
                $table->dropForeignIdFor('SubOrder');
                $table->dropColumn('sub_order_id');
            }
        });

        // Restore unique constraint
        try {
            DB::statement('ALTER TABLE reviews MODIFY order_item_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE reviews ADD UNIQUE reviews_order_item_id_unique(order_item_id)');
        } catch (\Exception $e) {
            // Constraint might not be restorable
        }
    }
};
