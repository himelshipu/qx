<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_target_countries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->char('country_code', 2);
            $table->timestamps();

            $table->unique(['campaign_id', 'country_code'], 'campaign_country_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_target_countries');
    }
};
