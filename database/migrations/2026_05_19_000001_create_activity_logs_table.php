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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('log_type'); // e.g., 'login_failed', 'login_success', 'register_failed', 'register_success', 'otp_failed', 'email_verification_failed', 'password_reset_failed', 'profile_update_failed', 'file_upload_failed', 'payment_failed', 'suspicious_activity', 'logout'
            $table->text('message'); // Pesan detail
            $table->string('email')->nullable(); // Email user
            $table->string('user_id')->nullable(); // User ID (UUID)
            $table->string('ip_address')->nullable(); // IP Address
            $table->text('user_agent')->nullable(); // Browser/Device info
            $table->string('endpoint')->nullable(); // URL/Endpoint yang diakses
            $table->json('payload')->nullable(); // Request payload
            $table->json('response')->nullable(); // Response data
            $table->text('stack_trace')->nullable(); // Stack trace jika ada error
            $table->string('status')->default('success'); // success, failed, warning, etc
            $table->string('severity')->default('info'); // info, warning, critical
            $table->integer('attempt_count')->default(1); // Untuk tracking multiple attempts
            $table->timestamp('created_at')->useCurrent();
            
            // Index untuk performa query
            $table->index(['log_type', 'created_at']);
            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
