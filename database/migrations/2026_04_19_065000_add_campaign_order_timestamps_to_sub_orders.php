<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            // Add missing timestamp columns for state transitions
            if (!Schema::hasColumn('sub_orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('accepted_at');
            }
            if (!Schema::hasColumn('sub_orders', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('delivered_at');
            }
            if (!Schema::hasColumn('sub_orders', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('completed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            $table->dropColumn(['delivered_at', 'approved_at', 'reviewed_at']);
        });
    }
};
