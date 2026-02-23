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
        Schema::table('brands', function (Blueprint $table) {
            if (!Schema::hasColumn('brands', 'description')) {
                $table->text('description')->nullable()->after('brand_name');
            }
            if (!Schema::hasColumn('brands', 'website')) {
                $table->string('website')->nullable()->after('description');
            }
            if (!Schema::hasColumn('brands', 'phone')) {
                $table->string('phone')->nullable()->after('website');
            }
            if (!Schema::hasColumn('brands', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('brands', 'location')) {
                $table->string('location')->nullable()->after('email');
            }
            if (!Schema::hasColumn('brands', 'city')) {
                $table->string('city')->nullable()->after('location');
            }
            if (!Schema::hasColumn('brands', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('brands', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('country');
            }
            if (!Schema::hasColumn('brands', 'profile_image_path')) {
                $table->string('profile_image_path')->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('brands', 'cover_image_path')) {
                $table->string('cover_image_path')->nullable()->after('profile_image_path');
            }
            if (!Schema::hasColumn('brands', 'categories')) {
                $table->json('categories')->nullable()->after('cover_image_path');
            }
            if (!Schema::hasColumn('brands', 'social_links')) {
                $table->json('social_links')->nullable()->after('categories');
            }
            if (!Schema::hasColumn('brands', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('social_links');
            }
            if (!Schema::hasColumn('brands', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $columns = [
                'description', 'website', 'phone', 'email', 'location', 
                'city', 'country', 'postal_code', 'profile_image_path', 
                'cover_image_path', 'categories', 'social_links', 
                'is_verified', 'is_active'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('brands', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
