<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_onboarding_industries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_onboarding_profile_id')->constrained('brand_onboarding_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['brand_onboarding_profile_id', 'category_id'], 'brand_onboarding_profile_category_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_onboarding_industries');
    }
};
