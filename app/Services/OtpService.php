<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OtpService
{
    protected $expiry = 15; // minutes

    public function generateOtp(string $email, string $type = 'registration', ?string $userId = null): string
    {
        // Generate 6-digit numeric code
        $otp = rand(100000, 999999);
        
        // Get or find user if exists
        $user = $userId ? User::find($userId) : User::where('email', $email)->first();
        
        // Clean up old OTPs for this email and type
        $this->cleanupOldOtps($email, $type);
        
        // Create new OTP record
        OtpVerification::create([
            'email' => $email,
            'user_id' => $user?->id,
            'otp_code' => (string) $otp,
            'type' => $type,
            'attempts' => 0,
            'is_verified' => false,
            'expires_at' => now()->addMinutes($this->expiry),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        return (string) $otp;
    }

    public function verifyOtp(string $email, string $otp, string $type = 'registration'): bool
    {
        // Find the latest valid OTP
        $otpRecord = OtpVerification::forEmail($email)
            ->forType($type)
            ->valid()
            ->latest()
            ->first();
        
        if (!$otpRecord) {
            return false;
        }
        
        if (!$otpRecord->canAttempt()) {
            return false;
        }
        
        // Increment attempts
        $otpRecord->incrementAttempts();
        
        // Check if OTP matches
        if ($otpRecord->otp_code === $otp) {
            $otpRecord->markAsVerified();
            
            // Link to user if exists and not linked yet
            if (!$otpRecord->user_id) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $otpRecord->linkToUser($user);
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function sendOtpEmail(string $email, string $otp, string $type = 'registration'): void
    {
        $user = User::where('email', $email)->first();
        
        $data = [
            'otp' => $otp,
            'appName' => config('app.name'),
            'type' => $type,
            'expiryMinutes' => $this->expiry,
            'userName' => $user ? $user->full_name : 'User',
            'userFirstName' => $user?->firstname ?? 'User',
        ];

        Mail::send('emails.otp', $data, function ($message) use ($email, $type, $user) {
            $subject = match($type) {
                'registration' => 'Kode Verifikasi Registrasi',
                'password_reset' => 'Kode Reset Password',
                'login_verification' => 'Kode Verifikasi Login',
                'email_change' => 'Kode Verifikasi Perubahan Email',
                default => 'Kode Verifikasi Email'
            };
            
            $message->to($email, $user?->full_name)->subject($subject);
        });
    }

    public function resendOtp(string $email, string $type = 'registration'): bool
    {
        $cacheKey = "otp_resend_{$type}_{$email}";
        
        if (Cache::has($cacheKey)) {
            return false;
        }
        
        $user = User::where('email', $email)->first();
        $otp = $this->generateOtp($email, $type, $user?->id);
        $this->sendOtpEmail($email, $otp, $type);
        
        Cache::put($cacheKey, true, 60);
        
        return true;
    }

    public function getRemainingTime(string $email, string $type = 'registration'): int
    {
        $otpRecord = OtpVerification::forEmail($email)
            ->forType($type)
            ->valid()
            ->latest()
            ->first();
        
        return $otpRecord ? $otpRecord->getRemainingTime() : 0;
    }

    public function getAttempts(string $email, string $type = 'registration'): int
    {
        $otpRecord = OtpVerification::forEmail($email)
            ->forType($type)
            ->valid()
            ->latest()
            ->first();
        
        return $otpRecord ? $otpRecord->attempts : 0;
    }

    public function canResend(string $email, string $type = 'registration'): bool
    {
        $cacheKey = "otp_resend_{$type}_{$email}";
        return !Cache::has($cacheKey);
    }

    public function clearOtp(string $email, string $type = 'registration'): void
    {
        OtpVerification::forEmail($email)
            ->forType($type)
            ->where('is_verified', false)
            ->update(['is_verified' => true]);
    }

    protected function cleanupOldOtps(string $email, string $type): void
    {
        // Mark old unverified OTPs as expired by updating is_verified
        OtpVerification::forEmail($email)
            ->forType($type)
            ->where('is_verified', false)
            ->update(['is_verified' => true]);
    }

    // User-specific methods
    public function getUserOtpHistory(string $userId, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return OtpVerification::forUser($userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getEmailOtpHistory(string $email, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return OtpVerification::forEmail($email)
            ->latest()
            ->limit($limit)
            ->get();
    }

    // Security methods
    public function checkSuspiciousActivity(string $email): array
    {
        $recentOtps = OtpVerification::forEmail($email)
            ->recent(1) // Last 1 hour
            ->get();
        
        $totalSent = $recentOtps->count();
        $failedAttempts = $recentOtps->where('attempts', '>=', 3)->count();
        $uniqueIps = $recentOtps->pluck('ip_address')->unique()->count();
        
        return [
            'is_suspicious' => $totalSent > 5 || $failedAttempts > 2 || $uniqueIps > 3,
            'total_sent' => $totalSent,
            'failed_attempts' => $failedAttempts,
            'unique_ips' => $uniqueIps,
            'risk_level' => $this->calculateRiskLevel($totalSent, $failedAttempts, $uniqueIps),
        ];
    }

    protected function calculateRiskLevel(int $totalSent, int $failedAttempts, int $uniqueIps): string
    {
        $score = 0;
        
        if ($totalSent > 10) $score += 3;
        elseif ($totalSent > 5) $score += 2;
        elseif ($totalSent > 3) $score += 1;
        
        if ($failedAttempts > 3) $score += 3;
        elseif ($failedAttempts > 1) $score += 2;
        elseif ($failedAttempts > 0) $score += 1;
        
        if ($uniqueIps > 5) $score += 2;
        elseif ($uniqueIps > 3) $score += 1;
        
        return match(true) {
            $score >= 6 => 'high',
            $score >= 3 => 'medium',
            $score >= 1 => 'low',
            default => 'normal'
        };
    }

    public function getOtpStats(string $type = null): array
    {
        $query = OtpVerification::query();
        
        if ($type) {
            $query->forType($type);
        }
        
        $today = now()->startOfDay();
        
        return [
            'total_sent' => $query->count(),
            'total_verified' => $query->where('is_verified', true)->count(),
            'total_expired' => $query->where('expires_at', '<', now())->count(),
            'sent_today' => $query->where('created_at', '>=', $today)->count(),
            'verified_today' => $query->where('is_verified', true)
                                     ->where('updated_at', '>=', $today)->count(),
            'unique_users_today' => $query->where('created_at', '>=', $today)
                                         ->whereNotNull('user_id')
                                         ->distinct('user_id')
                                         ->count(),
            'by_type' => OtpVerification::selectRaw('type, COUNT(*) as count')
                                      ->groupBy('type')
                                      ->pluck('count', 'type')
                                      ->toArray(),
        ];
    }

    public function cleanupExpiredOtps(): int
    {
        return OtpVerification::where('expires_at', '<', now()->subHours(24))
            ->delete();
    }
}