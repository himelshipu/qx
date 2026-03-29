<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_social_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('instagram_url', 500)->nullable();
            $table->string('tiktok_url', 500)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('x_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_social_links');
    }
};