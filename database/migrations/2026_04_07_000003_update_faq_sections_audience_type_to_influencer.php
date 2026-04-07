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
        if (Schema::hasTable('faq_sections')) {
            // First, extend the enum to include 'influencer'
            Schema::table('faq_sections', function (Blueprint $table) {
                DB::statement("ALTER TABLE `faq_sections` MODIFY `audience_type` ENUM('all', 'brand', 'creator', 'influencer') DEFAULT 'all'");
            });

            // Update existing faq_sections with audience_type = 'creator' to 'influencer'
            DB::table('faq_sections')
                ->where('audience_type', 'creator')
                ->update(['audience_type' => 'influencer']);

            // Remove 'creator' from the enum
            Schema::table('faq_sections', function (Blueprint $table) {
                DB::statement("ALTER TABLE `faq_sections` MODIFY `audience_type` ENUM('all', 'brand', 'influencer') DEFAULT 'all'");
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum including 'creator'
        if (Schema::hasTable('faq_sections')) {
            Schema::table('faq_sections', function (Blueprint $table) {
                DB::statement("ALTER TABLE `faq_sections` MODIFY `audience_type` ENUM('all', 'brand', 'creator') DEFAULT 'all'");
            });
        }

        // Restore audience_type values from 'influencer' back to 'creator'
        DB::table('faq_sections')
            ->where('audience_type', 'influencer')
            ->update(['audience_type' => 'creator']);
    }
};
