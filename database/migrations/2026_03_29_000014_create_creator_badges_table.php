<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_badges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('badge_definition_id')->constrained('badge_definitions')->cascadeOnDelete();
            $table->timestamp('earned_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['creator_id', 'badge_definition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_badges');
    }
};