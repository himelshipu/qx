<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rbac_audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Who made the change
            $table->unsignedBigInteger('admin_user_id');
            
            // Action type
            $table->enum('action_type', [
                'role_assigned',
                'role_removed',
                'permission_added',
                'permission_removed',
                'role_created',
                'role_updated',
                'role_deleted',
                'user_roles_synced',
            ]);
            
            // Affected resources
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('permission_id')->nullable();
            
            // Change details
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->text('description')->nullable();
            
            // Request details for security audit
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('admin_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('target_user_id')->references('id')->on('users')->nullOnDelete();
            
            // Indexes for faster queries
            $table->index(['admin_user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['target_user_id', 'created_at']);
            $table->index(['role_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rbac_audit_logs');
    }
};
