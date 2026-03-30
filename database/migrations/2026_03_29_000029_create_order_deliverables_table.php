<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_deliverables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('deliverable_type', ['image', 'video', 'document', 'link', 'other']);
            $table->string('file_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'approved', 'changes_requested', 'rejected'])->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_deliverables');
    }
};