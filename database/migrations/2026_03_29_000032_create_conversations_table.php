<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('public_id', 32)->nullable()->unique();
            $table->enum('conversation_type', ['influencer_profile', 'order'])->default('influencer_profile');
            $table->foreignId('influencer_id')->constrained('influencers')->cascadeOnDelete();
            $table->foreignId('brand_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('influencer_direct_message_enabled')->default(false);
            $table->string('title')->nullable();
            $table->timestamps();

            $table->index(['brand_user_id', 'updated_at'], 'conversations_brand_user_id_updated_at_index');
            $table->index('influencer_id');
            $table->index('handled_by_user_id');
            $table->index('order_id');
            $table->index('public_id');
            $table->index(['brand_user_id', 'influencer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};