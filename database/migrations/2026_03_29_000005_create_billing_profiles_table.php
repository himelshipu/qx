<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_profiles', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('user_type', ['brand', 'creator']);
            $table->string('legal_company_name')->nullable();
            $table->string('vat_id', 120)->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city', 120)->nullable();
            $table->string('billing_country', 120)->nullable();
            $table->string('billing_postal_code', 30)->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_profiles');
    }
};
