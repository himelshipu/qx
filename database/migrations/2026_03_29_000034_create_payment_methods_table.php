<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('provider', ['stripe', 'manual'])->default('stripe');
            $table->string('provider_payment_method_id')->nullable()->comment('Stripe payment method ID or similar from external provider');
            $table->string('last4', 4)->comment('Last 4 digits of card for display only');
            $table->string('brand', 50)->nullable()->comment('Card brand: Visa, Mastercard, Amex, etc.');
            $table->unsignedTinyInteger('expiry_month')->nullable();
            $table->unsignedSmallInteger('expiry_year')->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->timestamps();

            // Foreign key relationship
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Indexes for efficient queries
            $table->index('user_id');
            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
