<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_sections', function (Blueprint $table): void {
            $table->id();
            $table->string('section_code', 120)->unique();
            $table->string('section_title');
            $table->enum('audience_type', ['all', 'brand', 'creator'])->default('all');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_sections');
    }
};