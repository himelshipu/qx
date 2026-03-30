<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_target_follower_ranges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('follower_range_id')->constrained('follower_ranges')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'follower_range_id'], 'campaign_follower_range_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_target_follower_ranges');
    }
};
