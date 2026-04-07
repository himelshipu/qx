<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('influencer_platform_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('influencer_id')->constrained('influencers')->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'tiktok', 'youtube', 'linkedin', 'facebook', 'x', 'twitch', 'ugc', 'other']);
            $table->string('handle')->nullable();
            $table->string('profile_url', 500)->nullable();
            $table->unsignedBigInteger('follower_count')->nullable();
            $table->unsignedBigInteger('avg_views')->nullable();
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['influencer_id', 'platform']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('influencer_platform_stats');
    }
};