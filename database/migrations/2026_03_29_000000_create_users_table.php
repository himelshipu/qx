<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('user_type', ['brand', 'influencer', 'moderator', 'admin'])->default('brand');
            $table->string('phone', 30)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('country', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('address_line', 255)->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image_path', 500)->nullable();
            $table->string('cover_image_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('verification_code', 20)->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('stripe_customer_id')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};