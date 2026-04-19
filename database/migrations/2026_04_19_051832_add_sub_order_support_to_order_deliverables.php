<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_deliverables', function (Blueprint $table) {
            // Make order_item_id nullable to support both package and campaign orders
            $table->foreignId('order_item_id')->nullable()->change();

            // Add foreign key to sub_orders for campaign order deliverables
            $table->foreignId('sub_order_id')->nullable()->constrained('sub_orders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_deliverables', function (Blueprint $table) {
            // Drop the sub_order_id foreign key and column
            $table->dropForeignIdFor('SubOrder');
            $table->dropColumn('sub_order_id');

            // Restore order_item_id as not nullable
            $table->foreignId('order_item_id')->nullable(false)->change();
        });
    }
};
