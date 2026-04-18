<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
            $table->boolean('show_on_footer')->default(true)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'show_on_footer']);
        });
    }
};
