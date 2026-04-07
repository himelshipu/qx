<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moderator_assignments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('moderator_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('influencer_id')->constrained('influencers')->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('unassigned_at')->nullable();
            $table->timestamps();

            // One moderator can have multiple creators, but each creator can have only one active moderator
            $table->index('moderator_user_id');
            $table->index('influencer_id');
            $table->unique(['influencer_id', 'unassigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderator_assignments');
    }
};
