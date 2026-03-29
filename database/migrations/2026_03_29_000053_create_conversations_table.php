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
            $table->enum('conversation_type', ['creator_profile', 'order'])->default('creator_profile');
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('brand_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('creator_direct_message_enabled')->default(false);
            $table->string('title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};