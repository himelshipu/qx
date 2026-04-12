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
        // Add sort_order to influencers table
        if (Schema::hasTable('influencers') && !Schema::hasColumn('influencers', 'sort_order')) {
            Schema::table('influencers', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('is_featured');
            });
        }

        // Add sort_order to brands table
        if (Schema::hasTable('brands') && !Schema::hasColumn('brands', 'sort_order')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove sort_order from influencers
        if (Schema::hasTable('influencers') && Schema::hasColumn('influencers', 'sort_order')) {
            Schema::table('influencers', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        // Remove sort_order from brands
        if (Schema::hasTable('brands') && Schema::hasColumn('brands', 'sort_order')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};
