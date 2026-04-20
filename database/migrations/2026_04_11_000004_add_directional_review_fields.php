<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('reviews', 'reviewer_type')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->string('reviewer_type', 20)->default('influencer')->after('influencer_id');
            });
        }

        if (!Schema::hasColumn('reviews', 'reviewee_type')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->string('reviewee_type', 20)->default('brand')->after('reviewer_type');
            });
        }

        DB::table('reviews')->whereNull('reviewer_type')->update(['reviewer_type' => 'influencer']);
        DB::table('reviews')->whereNull('reviewee_type')->update(['reviewee_type' => 'brand']);

        // Check if the legacy unique constraint exists (MySQL and PostgreSQL compatible)
        $hasLegacyUnique = false;
        if (DB::getDriverName() === 'mysql') {
            $hasLegacyUnique = collect(DB::select("SHOW INDEX FROM reviews"))
                ->contains(fn($index) => ($index->Key_name ?? null) === 'reviews_order_item_id_unique');
        } elseif (DB::getDriverName() === 'pgsql') {
            $hasLegacyUnique = DB::selectOne(
                "SELECT to_regclass('public.reviews_order_item_id_unique') as exists"
            )?->exists ?? false;
        }
        // SQLite doesn't have SHOW INDEX, so we'll skip this check for SQLite

        if ($hasLegacyUnique) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropForeign('reviews_order_item_id_foreign');
                $table->dropUnique('reviews_order_item_id_unique');
                $table->index('order_item_id', 'reviews_order_item_id_index');
                $table->foreign('order_item_id', 'reviews_order_item_id_foreign')
                    ->references('id')
                    ->on('order_items')
                    ->cascadeOnDelete();
            });
        }

        // Check if the composite unique constraint exists (MySQL and PostgreSQL compatible)
        $hasCompositeUnique = false;
        if (DB::getDriverName() === 'mysql') {
            $hasCompositeUnique = collect(DB::select("SHOW INDEX FROM reviews"))
                ->contains(fn($index) => ($index->Key_name ?? null) === 'reviews_item_reviewer_unique');
        } elseif (DB::getDriverName() === 'pgsql') {
            $hasCompositeUnique = DB::selectOne(
                "SELECT to_regclass('public.reviews_item_reviewer_unique') as exists"
            )?->exists ?? false;
        }
        // SQLite doesn't have SHOW INDEX, so we'll skip this check for SQLite

        if (!$hasCompositeUnique) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->unique(['order_item_id', 'reviewer_type'], 'reviews_item_reviewer_unique');
            });
        }
    }

    public function down(): void
    {
        // Check if the composite unique constraint exists (MySQL and PostgreSQL compatible)
        $hasCompositeUnique = false;
        if (DB::getDriverName() === 'mysql') {
            $hasCompositeUnique = collect(DB::select("SHOW INDEX FROM reviews"))
                ->contains(fn($index) => ($index->Key_name ?? null) === 'reviews_item_reviewer_unique');
        } elseif (DB::getDriverName() === 'pgsql') {
            $hasCompositeUnique = DB::selectOne(
                "SELECT to_regclass('public.reviews_item_reviewer_unique') as exists"
            )?->exists ?? false;
        }
        // SQLite doesn't have SHOW INDEX, so we'll skip this check for SQLite

        if ($hasCompositeUnique) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropUnique('reviews_item_reviewer_unique');
            });
        }

        $hasLegacyUnique = collect(DB::select("SHOW INDEX FROM reviews"))
            ->contains(fn($index) => ($index->Key_name ?? null) === 'reviews_order_item_id_unique');

        if (!$hasLegacyUnique) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropForeign('reviews_order_item_id_foreign');
                $table->dropIndex('reviews_order_item_id_index');
                $table->unique('order_item_id', 'reviews_order_item_id_unique');
                $table->foreign('order_item_id', 'reviews_order_item_id_foreign')
                    ->references('id')
                    ->on('order_items')
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('reviews', 'reviewer_type') || Schema::hasColumn('reviews', 'reviewee_type')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropColumn(['reviewer_type', 'reviewee_type']);
            });
        }
    }
};
