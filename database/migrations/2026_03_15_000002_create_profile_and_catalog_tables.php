<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('brand_name');
            $table->text('description')->nullable();
            $table->string('industry', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('website', 500)->nullable();
            $table->string('location')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->string('profile_image_path', 500)->nullable();
            $table->string('cover_image_path', 500)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('brand_social_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->unique()->constrained('brands')->cascadeOnDelete();
            $table->string('instagram_url', 500)->nullable();
            $table->string('tiktok_url', 500)->nullable();
            $table->string('facebook_url', 500)->nullable();
            $table->string('x_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->string('other_url', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('brand_billing_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->unique()->constrained('brands')->cascadeOnDelete();
            $table->string('legal_company_name')->nullable();
            $table->string('vat_id', 120)->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city', 120)->nullable();
            $table->string('billing_country', 120)->nullable();
            $table->string('billing_postal_code', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('brand_onboarding_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->unique()->constrained('brands')->cascadeOnDelete();
            $table->string('objective', 120)->nullable();
            $table->string('budget_range', 120)->nullable();
            $table->string('business_type', 120)->nullable();
            $table->string('company_size', 120)->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->text('description')->nullable();
            $table->string('icon_path', 500)->nullable();
            $table->string('image_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('brand_onboarding_industries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_onboarding_profile_id')->constrained('brand_onboarding_profiles')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['brand_onboarding_profile_id', 'category_id'], 'uq_onboarding_industry');
        });

        Schema::create('creators', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('title_name')->nullable();
            $table->text('description')->nullable();
            $table->text('audience')->nullable();
            $table->text('brands_worked_with')->nullable();
            $table->string('location')->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 120)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('profile_image_path', 500)->nullable();
            $table->string('cover_image_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('creator_social_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->unique()->constrained('creators')->cascadeOnDelete();
            $table->string('facebook_url', 500)->nullable();
            $table->string('youtube_url', 500)->nullable();
            $table->string('tiktok_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('x_url', 500)->nullable();
            $table->string('other_url', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('creator_platform_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'tiktok', 'youtube', 'linkedin', 'facebook', 'x', 'twitch', 'ugc', 'other']);
            $table->string('handle')->nullable();
            $table->string('profile_url', 500)->nullable();
            $table->unsignedBigInteger('follower_count')->nullable();
            $table->unsignedBigInteger('avg_views')->nullable();
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['creator_id', 'platform'], 'uq_creator_platform');
        });

        Schema::create('creator_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['creator_id', 'category_id'], 'uq_creator_category');
        });

        Schema::create('badge_definitions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 120)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('creator_badges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('badge_definition_id')->constrained('badge_definitions')->cascadeOnDelete();
            $table->timestamp('earned_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['creator_id', 'badge_definition_id'], 'uq_creator_badge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_badges');
        Schema::dropIfExists('badge_definitions');
        Schema::dropIfExists('creator_categories');
        Schema::dropIfExists('creator_platform_stats');
        Schema::dropIfExists('creator_social_links');
        Schema::dropIfExists('creators');
        Schema::dropIfExists('brand_onboarding_industries');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brand_onboarding_profiles');
        Schema::dropIfExists('brand_billing_profiles');
        Schema::dropIfExists('brand_social_links');
        Schema::dropIfExists('brands');
    }
};
