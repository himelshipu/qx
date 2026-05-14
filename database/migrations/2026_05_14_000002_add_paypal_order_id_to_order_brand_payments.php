<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_brand_payments', function (Blueprint $table): void {
            $table->string('paypal_order_id', 200)->nullable()->unique()->after('paypal_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_brand_payments', function (Blueprint $table): void {
            $table->dropUnique(['paypal_order_id']);
            $table->dropColumn('paypal_order_id');
        });
    }
};
