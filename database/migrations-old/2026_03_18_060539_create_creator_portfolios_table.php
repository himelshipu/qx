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
        Schema::create('creator_portfolios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id');
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->string('file_path', 500)->nullable();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('creators')->onDelete('cascade');
            $table->index('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_portfolios');
    }
};
