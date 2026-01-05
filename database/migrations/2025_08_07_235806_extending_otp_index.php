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
        Schema::table('otp_verifications', function (Blueprint $table) {
            // Drop the problematic unique index
            $table->dropIndex('unique_active_otp');
            
            // Add better indexes
            $table->index(['email', 'type', 'is_verified', 'expires_at'], 'idx_otp_lookup');
            $table->index(['email', 'type', 'created_at'], 'idx_otp_cleanup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_verifications', function (Blueprint $table) {
            // Drop the new indexes
            $table->dropIndex('idx_otp_lookup');
            $table->dropIndex('idx_otp_cleanup');
            
            // Recreate the old unique index (if you want to revert)
            // Note: This might fail if there are duplicate records
            try {
                $table->unique(['email', 'type', 'is_verified'], 'unique_active_otp');
            } catch (\Exception $e) {
                // Log the error but don't fail the migration
                logger()->warning('Could not recreate unique_active_otp index: ' . $e->getMessage());
            }
        });
    }
};