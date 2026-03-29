<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->enum('status', ['invited', 'applied', 'shortlisted', 'approved', 'rejected', 'completed'])->default('applied');
            $table->text('pitch_message')->nullable();
            $table->decimal('proposed_rate', 12, 2)->nullable();
            $table->decimal('agreed_rate', 12, 2)->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'creator_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_applications');
    }
};