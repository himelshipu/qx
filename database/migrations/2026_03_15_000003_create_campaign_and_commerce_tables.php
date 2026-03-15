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
        Schema::create('campaigns', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->enum('campaign_type', ['instagram', 'tiktok', 'ugc', 'youtube', 'twitch', 'other']);
            $table->text('description')->nullable();
            $table->longText('instructions')->nullable();
            $table->enum('status', ['draft', 'published', 'paused', 'closed', 'archived'])->default('draft');
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->char('currency', 3)->default('USD');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaign_targeting', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->unique()->constrained('campaigns')->cascadeOnDelete();
            $table->unsignedInteger('influencer_count')->nullable();
            $table->enum('target_gender', ['any', 'male', 'female', 'other'])->default('any');
            $table->unsignedTinyInteger('age_min')->nullable();
            $table->unsignedTinyInteger('age_max')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('campaign_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'category_id'], 'uq_campaign_category');
        });

        Schema::create('campaign_target_countries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->char('country_code', 2);
            $table->string('country_name', 120);
            $table->timestamps();

            $table->unique(['campaign_id', 'country_code'], 'uq_campaign_country');
        });

        Schema::create('follower_ranges', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('label', 120);
            $table->unsignedBigInteger('min_followers')->nullable();
            $table->unsignedBigInteger('max_followers')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('campaign_target_follower_ranges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('follower_range_id')->constrained('follower_ranges')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'follower_range_id'], 'uq_campaign_follower_range');
        });

        Schema::create('campaign_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->enum('asset_type', ['image', 'video', 'document', 'other']);
            $table->string('file_path', 500);
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('title')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('campaign_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->enum('status', ['invited', 'applied', 'shortlisted', 'approved', 'rejected', 'completed'])->default('applied');
            $table->text('pitch_message')->nullable();
            $table->decimal('proposed_rate', 12, 2)->nullable();
            $table->decimal('agreed_rate', 12, 2)->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'creator_id'], 'uq_campaign_creator_application');
        });

        Schema::create('packages', function (Blueprint $table): void {
            $table->id();
            $table->enum('platform', ['instagram', 'tiktok', 'youtube', 'ugc', 'other']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->unsignedSmallInteger('revisions_included')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('carts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['active', 'converted', 'abandoned'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('buyer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'delivered', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_for_creator_id')->nullable()->constrained('creators')->nullOnDelete();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'delivered', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->foreignId('accepted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('order_deliverables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('deliverable_type', ['image', 'video', 'document', 'link', 'other']);
            $table->string('file_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['submitted', 'approved', 'changes_requested', 'rejected'])->default('submitted');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('payment_provider', 80);
            $table->string('provider_payment_id', 120)->nullable();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'authorized', 'captured', 'failed', 'refunded', 'partially_refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->string('provider', 80);
            $table->string('account_identifier');
            $table->string('account_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('payout_account_id')->constrained('payout_accounts')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'processing', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('external_payout_id', 120)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payout_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('payout_id')->constrained('payouts')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->timestamps();

            $table->unique(['payout_id', 'order_item_id'], 'uq_payout_order_item');
        });

        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->unique()->constrained('order_items')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 150);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('wishlist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wishlist_id')->constrained('wishlists')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['wishlist_id', 'creator_id'], 'uq_wishlist_creator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('payout_items');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('payout_accounts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_deliverables');
        Schema::dropIfExists('order_messages');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('campaign_applications');
        Schema::dropIfExists('campaign_assets');
        Schema::dropIfExists('campaign_target_follower_ranges');
        Schema::dropIfExists('follower_ranges');
        Schema::dropIfExists('campaign_target_countries');
        Schema::dropIfExists('campaign_categories');
        Schema::dropIfExists('campaign_targeting');
        Schema::dropIfExists('campaigns');
    }
};
