<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_influencers', function (Blueprint $table): void {
            $table->decimal('agreed_amount', 12, 2)->nullable()->after('status');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('payout_amount', 12, 2)->nullable()->after('paid_at');
            $table->string('payout_reference', 120)->nullable()->after('payout_amount');
            $table->text('payout_note')->nullable()->after('payout_reference');
            $table->foreignId('payout_marked_by_user_id')->nullable()->after('payout_note')->constrained('users')->nullOnDelete();
            $table->timestamp('payout_marked_at')->nullable()->after('payout_marked_by_user_id');
        });

        Schema::table('sub_orders', function (Blueprint $table): void {
            $table->decimal('payout_amount', 12, 2)->nullable()->after('paid_at');
            $table->string('payout_reference', 120)->nullable()->after('payout_amount');
            $table->text('payout_note')->nullable()->after('payout_reference');
            $table->foreignId('payout_marked_by_user_id')->nullable()->after('payout_note')->constrained('users')->nullOnDelete();
            $table->timestamp('payout_marked_at')->nullable()->after('payout_marked_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('sub_orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('payout_marked_by_user_id');
            $table->dropColumn(['payout_amount', 'payout_reference', 'payout_note', 'payout_marked_at']);
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('payout_marked_by_user_id');
            $table->dropColumn(['payout_amount', 'payout_reference', 'payout_note', 'payout_marked_at']);
        });

        Schema::table('campaign_influencers', function (Blueprint $table): void {
            $table->dropColumn('agreed_amount');
        });
    }
};
