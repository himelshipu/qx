<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('featured_collaborations', function (Blueprint $table): void {
            $table->id();
            $table->string('brand_name')->nullable();
            $table->enum('asset_type', ['image', 'video']);
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('thumbnail_path', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('featured_collaborations');
    }
};