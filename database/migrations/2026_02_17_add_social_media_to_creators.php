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
        Schema::table('creators', function (Blueprint $table) {
            $table->string('facebook')->nullable()->after('user_id');
            $table->string('tiktok')->nullable()->after('facebook');
            $table->string('linkedin')->nullable()->after('tiktok');
            $table->string('instagram')->nullable()->after('linkedin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('creators', function (Blueprint $table) {
            $table->dropColumn(['facebook', 'tiktok', 'linkedin', 'instagram']);
        });
    }
};
