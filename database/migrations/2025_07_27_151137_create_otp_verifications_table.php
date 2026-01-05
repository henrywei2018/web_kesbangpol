<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            
            // User relation - using email as foreign key since it's more reliable for OTP flow
            $table->string('email');
            $table->char('user_id', 36)->nullable(); // UUID foreign key, nullable for pre-registration OTPs
            
            $table->string('otp_code', 6);
            $table->enum('type', ['registration', 'password_reset', 'login_verification', 'email_change']);
            $table->integer('attempts')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at');
            $table->ipAddress('ip_address')->nullable(); // Track IP for security
            $table->text('user_agent')->nullable(); // Track user agent
            $table->timestamps();
            
            // Foreign key constraint (nullable since OTP might be sent before user creation)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes for performance
            $table->index(['email', 'type', 'is_verified']);
            $table->index(['user_id', 'type']);
            $table->index('expires_at');
            $table->index('created_at');
            
            // Unique constraint to prevent duplicate active OTPs
            $table->unique(['email', 'type', 'is_verified'], 'unique_active_otp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};