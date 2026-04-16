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
        if (!Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->index(['is_active', 'deleted_at'], 'categories_active_deleted_idx');
            $table->index(['is_featured', 'featured_order', 'deleted_at'], 'categories_featured_order_deleted_idx');
            $table->index(['name', 'deleted_at'], 'categories_name_deleted_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropIndex('categories_active_deleted_idx');
            $table->dropIndex('categories_featured_order_deleted_idx');
            $table->dropIndex('categories_name_deleted_idx');
        });
    }
};
