<?php

declare (strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('creators', function (Blueprint $table): void {
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->unsignedSmallInteger('featured_priority')->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('creators', function (Blueprint $table): void {
            $table->dropColumn(['is_featured', 'featured_priority']);
        });
    }
};
