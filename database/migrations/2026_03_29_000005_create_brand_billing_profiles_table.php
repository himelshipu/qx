<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_billing_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->string('legal_company_name')->nullable();
            $table->string('vat_id', 120)->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city', 120)->nullable();
            $table->string('billing_country', 120)->nullable();
            $table->string('billing_postal_code', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_billing_profiles');
    }
};
