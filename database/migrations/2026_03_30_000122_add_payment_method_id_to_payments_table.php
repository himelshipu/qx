<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table): void {
            // Add payment_method_id to link payment to the card used
            $table->foreignId('payment_method_id')
                ->nullable()
                ->after('provider_payment_id')
                ->constrained('payment_methods')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Drop foreign key first using raw SQL
        try {
            \DB::statement('ALTER TABLE payments DROP FOREIGN KEY payments_payment_method_id_foreign');
        } catch (\Exception $e) {
            // Foreign key doesn't exist, skip
        }

        // Drop column
        Schema::table('payments', function (Blueprint $table): void {
            $table->dropColumn('payment_method_id');
        });
    }
};
