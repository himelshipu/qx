<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->string('question', 500);
            $table->longText('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_questions');
    }
};