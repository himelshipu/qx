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
        // Update enum values: 'creator' -> 'influencer'
        Schema::table('users', function (Blueprint $table) {
            // Modify the user_type enum to replace 'creator' with 'influencer'
            DB::statement("ALTER TABLE `users` MODIFY `user_type` ENUM('brand', 'creator', 'influencer', 'moderator', 'admin') DEFAULT 'brand'");
        });

        // Update existing rows from 'creator' to 'influencer'
        DB::table('users')->where('user_type', 'creator')->update(['user_type' => 'influencer']);

        // Remove obsolete 'creator' value from enum
        DB::statement("ALTER TABLE `users` MODIFY `user_type` ENUM('brand', 'influencer', 'moderator', 'admin') DEFAULT 'brand'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: update 'influencer' back to 'creator'
        DB::table('users')->where('user_type', 'influencer')->update(['user_type' => 'creator']);

        // Restore original enum with 'creator'
        DB::statement("ALTER TABLE `users` MODIFY `user_type` ENUM('brand', 'creator', 'moderator', 'admin') DEFAULT 'brand'");
    }
};
