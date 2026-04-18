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
        if (!Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table): void {
            if (!Schema::hasColumn('brands', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_verified');
            }

            if (!Schema::hasColumn('brands', 'featured_order')) {
                $table->unsignedInteger('featured_order')->nullable()->after('is_featured');
            }

            $table->index(['is_featured', 'featured_order', 'deleted_at'], 'brands_featured_order_deleted_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('brands')) {
            return;
        }

        Schema::table('brands', function (Blueprint $table): void {
            $table->dropIndex('brands_featured_order_deleted_idx');

            if (Schema::hasColumn('brands', 'featured_order')) {
                $table->dropColumn('featured_order');
            }

            if (Schema::hasColumn('brands', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
        });
    }
};
