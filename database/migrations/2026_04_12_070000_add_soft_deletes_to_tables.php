<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Add soft deletes to core tables
     */
    public function up(): void
    {
        // Brands table
        if (Schema::hasTable('brands') && !Schema::hasColumn('brands', 'deleted_at')) {
            Schema::table('brands', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Influencers table
        if (Schema::hasTable('influencers') && !Schema::hasColumn('influencers', 'deleted_at')) {
            Schema::table('influencers', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Packages table
        if (Schema::hasTable('packages') && !Schema::hasColumn('packages', 'deleted_at')) {
            Schema::table('packages', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Orders table
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'deleted_at')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Categories table
        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'deleted_at')) {
            Schema::table('categories', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Reviews table
        if (Schema::hasTable('reviews') && !Schema::hasColumn('reviews', 'deleted_at')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Testimonials table
        if (Schema::hasTable('testimonials') && !Schema::hasColumn('testimonials', 'deleted_at')) {
            Schema::table('testimonials', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Case Studies table
        if (Schema::hasTable('case_studies') && !Schema::hasColumn('case_studies', 'deleted_at')) {
            Schema::table('case_studies', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Users table
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Support Tickets table
        if (Schema::hasTable('support_tickets') && !Schema::hasColumn('support_tickets', 'deleted_at')) {
            Schema::table('support_tickets', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Payments table
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'deleted_at')) {
            Schema::table('payments', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }

        // Payouts table
        if (Schema::hasTable('payouts') && !Schema::hasColumn('payouts', 'deleted_at')) {
            Schema::table('payouts', function (Blueprint $table): void {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('influencers', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('packages', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('testimonials', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('case_studies', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('payments', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });

        Schema::table('payouts', function (Blueprint $table): void {
            $table->dropSoftDeletes();
        });
    }
};
