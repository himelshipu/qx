<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('title');
            $table->enum('campaign_type', ['instagram', 'tiktok', 'ugc', 'youtube', 'twitch', 'other']);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'paused', 'closed', 'archived'])->default('draft');
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};