<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('campaign_assets', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('campaign_influencers', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('campaign_target_countries', function (Blueprint $table): void {
            $table->softDeletes();
        });

        Schema::table('campaign_targeting', function (Blueprint $table): void {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('campaign_assets', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('campaign_influencers', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('campaign_target_countries', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('campaign_targeting', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
