<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->enum('work_status', ['pending', 'accepted', 'in_progress', 'on_review', 'completed'])->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->dropColumn('work_status');
        });
    }
};
