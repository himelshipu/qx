<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            // Add setup_data column if it doesn't exist
            if (!Schema::hasColumn('brands', 'setup_data')) {
                $table->json('setup_data')->nullable()->after('website');
            }
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('setup_data');
        });
    }
};
