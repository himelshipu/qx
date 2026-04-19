<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status enum to include 'completed'
        DB::statement("ALTER TABLE order_items MODIFY status ENUM('pending', 'accepted', 'in_progress', 'delivered', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum
        DB::statement("ALTER TABLE order_items MODIFY status ENUM('pending', 'accepted', 'in_progress', 'delivered', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'");
    }
};
