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
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('page_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('pages')->cascadeOnDelete();
            $table->string('section_key', 120);
            $table->string('heading')->nullable();
            $table->string('subheading', 500)->nullable();
            $table->json('content_json')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('case_studies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->string('cover_image_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('author_name');
            $table->string('author_role')->nullable();
            $table->string('company_name')->nullable();
            $table->text('quote');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('featured_collaborations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('brand_name')->nullable();
            $table->enum('asset_type', ['image', 'video']);
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('thumbnail_path', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('faq_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->string('section_code', 120)->unique();
            $table->string('section_title');
            $table->enum('audience_type', ['all', 'brand', 'creator'])->default('all');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faq_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('faq_section_id')->constrained('faq_sections')->cascadeOnDelete();
            $table->string('question', 500);
            $table->longText('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('support_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('description', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('support_articles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('body');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('support_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->string('question', 500);
            $table->longText('answer');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_number', 60)->unique();
            $table->foreignId('requester_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('support_category_id')->constrained('support_categories')->cascadeOnDelete();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject');
            $table->longText('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_user', 'resolved', 'closed'])->default('open');
            $table->enum('source', ['web', 'email', 'admin'])->default('web');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('support_ticket_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('message');
            $table->boolean('is_internal_note')->default(false);
            $table->timestamps();
        });

        Schema::create('support_ticket_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_message_id')->constrained('support_ticket_messages')->cascadeOnDelete();
            $table->foreignId('uploaded_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('file_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('conversations', function (Blueprint $table): void {
            $table->id();
            $table->enum('conversation_type', ['creator_profile', 'order'])->default('creator_profile');
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->foreignId('brand_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->boolean('creator_direct_message_enabled')->default(false);
            $table->string('title')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('participant_role', ['brand', 'moderator', 'admin']);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id'], 'uq_conversation_user');
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('sender_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('sender_role', ['brand', 'moderator', 'admin', 'system']);
            $table->foreignId('on_behalf_of_creator_id')->nullable()->constrained('creators')->nullOnDelete();
            $table->longText('message');
            $table->string('attachment_path', 500)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 120);
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data_json')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_library');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('support_ticket_attachments');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_questions');
        Schema::dropIfExists('support_articles');
        Schema::dropIfExists('support_categories');
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('faq_sections');
        Schema::dropIfExists('featured_collaborations');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};
