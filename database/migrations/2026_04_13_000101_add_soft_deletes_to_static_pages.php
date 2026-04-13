<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('static_pages') && ! Schema::hasColumn('static_pages', 'deleted_at')) {
            Schema::table('static_pages', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('static_pages') && Schema::hasColumn('static_pages', 'deleted_at')) {
            Schema::table('static_pages', function (Blueprint $table): void {
                $table->dropSoftDeletes();
            });
        }
    }
};