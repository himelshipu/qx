<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->decimal('influencer_offer', 12, 2)->nullable()->after('pitch_message');
            $table->decimal('brand_offer', 12, 2)->nullable()->after('influencer_offer');
            $table->enum('last_counter_by', ['brand', 'influencer'])->nullable()->after('brand_offer');
            $table->timestamp('last_counter_at')->nullable()->after('last_counter_by');
            $table->timestamp('agreed_at')->nullable()->after('last_counter_at');
            $table->timestamp('declined_at')->nullable()->after('agreed_at');
            $table->enum('declined_by', ['brand', 'influencer'])->nullable()->after('declined_at');
        });

        DB::statement("ALTER TABLE campaign_applications MODIFY status ENUM('invited','applied','countered_by_brand','countered_by_influencer','shortlisted','approved','rejected','declined_by_brand','declined_by_influencer','completed') NOT NULL DEFAULT 'applied'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE campaign_applications MODIFY status ENUM('invited','applied','shortlisted','approved','rejected','completed') NOT NULL DEFAULT 'applied'");

        Schema::table('campaign_applications', function (Blueprint $table): void {
            $table->dropColumn([
                'influencer_offer',
                'brand_offer',
                'last_counter_by',
                'last_counter_at',
                'agreed_at',
                'declined_at',
                'declined_by',
            ]);
        });
    }
};
