<?php

namespace App\Console\Commands;

use App\Models\OtpVerification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupExpiredOtps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup {--days=1 : Number of days to keep verified OTPs} {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old OTP verification records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $force = $this->option('force');
        
        $this->info("🧹 Starting OTP cleanup process...");
        
        // Clean up expired OTPs (regardless of verification status)
        $expiredCount = $this->cleanupExpiredOtps();
        $this->info("✅ Cleaned up {$expiredCount} expired OTP records");
        
        // Clean up old verified OTPs
        $oldVerifiedCount = $this->cleanupOldVerifiedOtps($days);
        $this->info("✅ Cleaned up {$oldVerifiedCount} old verified OTP records (older than {$days} days)");
        
        // Clean up duplicate unverified OTPs
        $duplicateCount = $this->cleanupDuplicateUnverifiedOtps();
        $this->info("✅ Cleaned up {$duplicateCount} duplicate unverified OTP records");
        
        // Show statistics
        $this->showStatistics();
        
        $this->info("🎉 OTP cleanup completed successfully!");
        
        return 0;
    }

    /**
     * Clean up expired OTPs
     */
    private function cleanupExpiredOtps(): int
    {
        return OtpVerification::where('expires_at', '<', now())
            ->delete();
    }

    /**
     * Clean up old verified OTPs
     */
    private function cleanupOldVerifiedOtps(int $days): int
    {
        return OtpVerification::where('is_verified', true)
            ->where('updated_at', '<', now()->subDays($days))
            ->delete();
    }

    /**
     * Clean up duplicate unverified OTPs (keep only the latest for each email/type)
     */
    private function cleanupDuplicateUnverifiedOtps(): int
    {
        $deleted = 0;
        
        // Get all email/type combinations that have multiple unverified OTPs
        $duplicates = OtpVerification::selectRaw('email, type, COUNT(*) as count')
            ->where('is_verified', false)
            ->groupBy('email', 'type')
            ->having('count', '>', 1)
            ->get();
        
        foreach ($duplicates as $duplicate) {
            // Keep only the latest OTP for each email/type combination
            $otpsToDelete = OtpVerification::where('email', $duplicate->email)
                ->where('type', $duplicate->type)
                ->where('is_verified', false)
                ->orderBy('created_at', 'desc')
                ->skip(1) // Skip the latest one
                ->get();
            
            foreach ($otpsToDelete as $otp) {
                $otp->delete();
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Show current OTP statistics
     */
    private function showStatistics(): void
    {
        $stats = [
            'Total OTPs' => OtpVerification::count(),
            'Verified OTPs' => OtpVerification::where('is_verified', true)->count(),
            'Unverified OTPs' => OtpVerification::where('is_verified', false)->count(),
            'Registration OTPs' => OtpVerification::where('type', 'registration')->count(),
            'Password Reset OTPs' => OtpVerification::where('type', 'password_reset')->count(),
            'Email Verification OTPs' => OtpVerification::where('type', 'email_verification')->count(),
        ];
        
        $this->table(['Metric', 'Count'], collect($stats)->map(fn($count, $metric) => [$metric, $count])->toArray());
    }
}