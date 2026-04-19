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
        Schema::table('sub_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sub_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('accepted_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            if (Schema::hasColumn('sub_orders', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
        });
    }
};
