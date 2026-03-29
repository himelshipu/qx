<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follower_ranges', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('label', 120);
            $table->unsignedBigInteger('min_followers')->nullable();
            $table->unsignedBigInteger('max_followers')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follower_ranges');
    }
};