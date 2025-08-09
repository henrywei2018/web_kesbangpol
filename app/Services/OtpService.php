<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;

class OtpService
{
    protected $expiry = 15; // minutes

    /**
     * Handle the complete OTP sending process
     */
    public function sendOtp(string $email, string $type = 'registration', array $context = []): array
    {
        try {
            // Check rate limiting
            $rateLimitResult = $this->checkRateLimit($email, $type);
            if (!$rateLimitResult['allowed']) {
                return [
                    'success' => false,
                    'message' => $rateLimitResult['message'],
                    'data' => []
                ];
            }

            // Check if user exists for password reset
            if ($type === 'password_reset') {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'Email tidak terdaftar.',
                        'data' => []
                    ];
                }
            }

            // Check for duplicate email in registration
            if ($type === 'registration') {
                $existingUser = User::where('email', $email)->first();
                if ($existingUser) {
                    return [
                        'success' => false,
                        'message' => 'Email sudah terdaftar.',
                        'data' => []
                    ];
                }
            }

            // Cleanup old OTPs
            $this->cleanupOldOtps($email, $type);

            // Generate and send OTP
            $otp = $this->generateOtp($email, $type);
            $this->sendOtpEmail($email, $otp, $type);

            // Hit rate limiter
            $this->hitRateLimit($email, $type);

            return [
                'success' => true,
                'message' => $this->getSuccessMessage($type),
                'data' => [
                    'remaining_time' => $this->getRemainingTime($email, $type),
                    'can_resend' => false
                ]
            ];

        } catch (\Exception $e) {
            logger()->error('OTP send failed', [
                'email' => $email,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim kode verifikasi.',
                'data' => []
            ];
        }
    }

    /**
     * Handle complete OTP verification process
     */
    public function verifyOtp(string $email, string $otp, string $type = 'registration'): array
    {
        try {
            // Find the latest valid OTP
            $otpRecord = OtpVerification::forEmail($email)
                ->forType($type)
                ->valid()
                ->latest()
                ->first();
            
            if (!$otpRecord) {
                return [
                    'success' => false,
                    'message' => 'Kode OTP tidak ditemukan atau sudah kedaluarsa.',
                    'data' => []
                ];
            }
            
            if (!$otpRecord->canAttempt()) {
                return [
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan atau kode sudah kedaluarsa.',
                    'data' => []
                ];
            }
            
            // Increment attempts
            $otpRecord->incrementAttempts();
            
            // Check if OTP matches
            if ($otpRecord->otp_code !== $otp) {
                return [
                    'success' => false,
                    'message' => 'Kode OTP salah.',
                    'data' => [
                        'attempts_left' => 3 - $otpRecord->attempts
                    ]
                ];
            }

            // Mark as verified
            $otpRecord->markAsVerified();
            
            // Link to user if exists and not linked yet
            if (!$otpRecord->user_id) {
                $user = User::where('email', $email)->first();
                if ($user) {
                    $otpRecord->linkToUser($user);
                }
            }
            
            return [
                'success' => true,
                'message' => 'Kode OTP berhasil diverifikasi.',
                'data' => ['otp_record' => $otpRecord]
            ];

        } catch (\Exception $e) {
            logger()->error('OTP verification failed', [
                'email' => $email,
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi kode.',
                'data' => []
            ];
        }
    }

    /**
     * Handle complete user registration process
     */
    public function completeRegistration(array $registrationData, string $email, string $otpCode): array
    {
        try {
            // Verify OTP first
            $otpResult = $this->verifyOtp($email, $otpCode, 'registration');
            if (!$otpResult['success']) {
                return $otpResult;
            }

            // Ensure username uniqueness
            $finalUsername = $this->ensureUniqueUsername($registrationData['username']);

            // Create user
            $user = User::create([
                'firstname' => $registrationData['firstname'],
                'lastname' => $registrationData['lastname'],
                'username' => $finalUsername,
                'email' => $registrationData['email'],
                'password' => Hash::make($registrationData['password']),
                'email_verified_at' => now(),
                'no_ktp' => null,
                'domisili' => null,
                'alamat' => null,
                'no_telepon' => $registrationData['no_telepon'],
            ]);

            // Assign default role
            $user->assignRole('public'); // or whatever your default role is

            // Mark email as verified
            $user->markEmailAsVerified();

            // Login user
            Auth::login($user);

            // Clean up OTP records
            $this->deleteOtp($email, 'registration');

            return [
                'success' => true,
                'message' => 'Registrasi berhasil! Anda akan diarahkan ke dashboard.',
                'data' => [
                    'user' => $user,
                    'redirect_url' => $this->getRedirectUrl($user)
                ]
            ];

        } catch (\Exception $e) {
            logger()->error('User registration failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat akun.',
                'data' => []
            ];
        }
    }

    /**
     * Handle complete password reset process
     */
    public function completePasswordReset(string $email, string $otpCode, string $newPassword): array
    {
        try {
            // Re-verify OTP for extra security
            $otpResult = $this->verifyOtp($email, $otpCode, 'password_reset');
            if (!$otpResult['success']) {
                return $otpResult;
            }

            // Find user
            $user = User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                    'data' => []
                ];
            }

            // Update password
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // Clean up OTP records
            $this->deleteOtp($email, 'password_reset');

            return [
                'success' => true,
                'message' => 'Password berhasil direset! Anda akan diarahkan ke halaman login.',
                'data' => ['user' => $user]
            ];

        } catch (\Exception $e) {
            logger()->error('Password reset failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mereset password. Silakan coba lagi.',
                'data' => []
            ];
        }
    }
    

    /**
     * Handle OTP resend
     */
    public function resendOtp(string $email, string $type = 'registration'): array
    {
        $cacheKey = "otp_resend_{$type}_{$email}";
        
        if (Cache::has($cacheKey)) {
            $remainingTime = Cache::store('default')->getMemcached()->ttl($cacheKey) ?? 60;
            return [
                'success' => false,
                'message' => "Tunggu {$remainingTime} detik sebelum mengirim ulang.",
                'data' => ['retry_after' => $remainingTime]
            ];
        }
        
        return $this->sendOtp($email, $type);
    }
    public function sendEmailVerification(string $email): array
    {
        try {
            // Check if user exists
            $user = User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                    'data' => []
                ];
            }

            // Check if already verified
            if ($user->hasVerifiedEmail()) {
                return [
                    'success' => false,
                    'message' => 'Email sudah terverifikasi.',
                    'data' => []
                ];
            }

            // Use the existing sendOtp method
            return $this->sendOtp($email, 'email_verification');

        } catch (\Exception $e) {
            logger()->error('Email verification send failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim kode verifikasi.',
                'data' => []
            ];
        }
    }

    /**
     * Complete email verification process
     */
    public function completeEmailVerification(string $email, string $otpCode): array
    {
        try {
            // Verify OTP
            $otpResult = $this->verifyOtp($email, $otpCode, 'email_verification');
            if (!$otpResult['success']) {
                return $otpResult;
            }

            // Find user and mark email as verified
            $user = User::where('email', $email)->first();
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan.',
                    'data' => []
                ];
            }

            // Mark email as verified
            $user->markEmailAsVerified();

            // Clean up OTP records
            $this->deleteOtp($email, 'email_verification');

            return [
                'success' => true,
                'message' => 'Email berhasil diverifikasi!',
                'data' => ['user' => $user]
            ];

        } catch (\Exception $e) {
            logger()->error('Email verification failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi email.',
                'data' => []
            ];
        }
    }

    // Update the getSuccessMessage method to include email verification
    protected function getSuccessMessage(string $type): string
    {
        return match($type) {
            'registration' => 'Kode verifikasi telah dikirim ke email Anda.',
            'password_reset' => 'Kode reset password telah dikirim ke email Anda.',
            'email_verification' => 'Kode verifikasi email telah dikirim ke email Anda.',
            'login_verification' => 'Kode verifikasi login telah dikirim ke email Anda.',
            default => 'Kode verifikasi telah dikirim ke email Anda.'
        };
    }

    // ========================================
    // PRIVATE/PROTECTED HELPER METHODS
    // ========================================

    protected function generateOtp(string $email, string $type): string
    {
        $otp = rand(100000, 999999);
        $now = now();
        $expiresAt = $now->copy()->addMinutes($this->expiry);

        // Find existing unverified OTP
        $existing = OtpVerification::where('email', $email)
            ->where('type', $type)
            ->where('is_verified', false)
            ->first();

        if ($existing) {
            $existing->update([
                'otp_code' => $otp,
                'attempts' => 0,
                'expires_at' => $expiresAt,
                'updated_at' => $now,
            ]);
        } else {
            OtpVerification::create([
                'email' => $email,
                'type' => $type,
                'otp_code' => $otp,
                'attempts' => 0,
                'is_verified' => false,
                'expires_at' => $expiresAt,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $otp;
    }

    protected function sendOtpEmail(string $email, string $otp, string $type = 'registration'): void
    {
        $user = User::where('email', $email)->first();
        
        $data = [
            'otp' => $otp,
            'appName' => config('app.name'),
            'type' => $type,
            'expiryMinutes' => $this->expiry,
            'userName' => $user ? $user->firstname . ' ' . $user->lastname : 'User',
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
            
            $message->to($email, $user?->firstname)->subject($subject);
        });
    }

    protected function checkRateLimit(string $email, string $type): array
    {
        $key = "otp_send_{$type}:" . request()->ip() . ":{$email}";
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return [
                'allowed' => false,
                'message' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ];
        }

        return ['allowed' => true];
    }

    protected function hitRateLimit(string $email, string $type): void
    {
        $key = "otp_send_{$type}:" . request()->ip() . ":{$email}";
        RateLimiter::hit($key, 300); // 5 minutes
        
        // Also set resend cache
        $resendKey = "otp_resend_{$type}_{$email}";
        Cache::put($resendKey, true, 60); // 1 minute
    }

    protected function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $baseWithoutNumbers = preg_replace('/\d+$/', '', $baseUsername);
            $username = $baseWithoutNumbers . $counter;
            $counter++;
            
            if ($counter > 9999) {
                $username = $baseWithoutNumbers . time();
                break;
            }
        }
        
        return $username;
    }


    protected function getRedirectUrl(User $user): string
    {
        // Customize redirect based on user role
        if ($user->hasRole('super_admin')) {
            return '/admin';
        }
        
        if ($user->hasRole('public')) {
            return '/panel/';
        }
        
        return '/panel/'; // Default for all registered users (public role)
    }

    protected function cleanupOldOtps(string $email, string $type): void
    {
        OtpVerification::forEmail($email)
            ->forType($type)
            ->where('is_verified', false)
            ->update(['is_verified' => true]);
    }

    // ========================================
    // PUBLIC UTILITY METHODS
    // ========================================

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

    public function deleteOtp(string $email, string $type = 'registration'): void
    {
        OtpVerification::forEmail($email)
            ->forType($type)
            ->delete();
    }

    public function cleanupExpiredOtps(): int
    {
        return OtpVerification::where('expires_at', '<', now()->subHours(24))
            ->delete();
    }
}