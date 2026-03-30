<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('body');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_articles');
    }
};