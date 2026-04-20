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
            // Modify the status enum to include 'approved' and 'rejected'
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'on_review', 'approved', 'rejected', 'completed', 'cancelled'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_orders', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'on_review', 'completed', 'cancelled'])->change();
        });
    }
};
