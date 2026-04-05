<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('campaign_influencer_id')->constrained('campaign_influencers')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'on_review', 'completed', 'cancelled'])->default('pending');
            $table->longText('deliverables')->nullable();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Index for quick lookups
            $table->index('order_id');
            $table->index('creator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_orders');
    }
};
