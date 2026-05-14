<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_brand_payments', function (Blueprint $table): void {
            $table->string('payment_method', 50)->default('manual')->after('currency');
            $table->text('paypal_token')->nullable()->after('payment_method');
            $table->string('paypal_transaction_id', 200)->nullable()->unique()->after('paypal_token');
        });
    }

    public function down(): void
    {
        Schema::table('order_brand_payments', function (Blueprint $table): void {
            $table->dropColumn('payment_method');
            $table->dropColumn('paypal_token');
            $table->dropUnique(['paypal_transaction_id']);
            $table->dropColumn('paypal_transaction_id');
        });
    }
};
