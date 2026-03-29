<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_library', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('disk', 60)->default('public');
            $table->string('path', 500);
            $table->string('file_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('entity_type', 120)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_library');
    }
};