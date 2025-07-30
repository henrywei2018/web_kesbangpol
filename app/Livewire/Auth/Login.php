<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Traits\HasTurnstile;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class Login extends Component
{
    use HasTurnstile;

    public string $currentStep = 'login';
    
    // Login form
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    
    // OTP verification
    public string $otp_code = '';
    public int $timeLeft = 0;
    public bool $canResend = true;
    
    public string $errorMessage = '';
    public string $successMessage = '';

    protected $listeners = ['otpTimerExpired' => 'handleOtpExpired'];

    public function rules()
    {
        if ($this->currentStep === 'login') {
            return [
                'email' => 'required|email',
                'password' => 'required|min:6',
                'turnstileResponse' => app()->environment('local') ? '' : 'required',
            ];
        } elseif ($this->currentStep === 'verification') {
            return [
                'otp_code' => 'required|string|size:6',
            ];
        }
        
        return [];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
            'turnstileResponse.required' => 'Harap selesaikan verifikasi keamanan.',
        ];
    }

    /**
     * Handle login attempt with Turnstile validation and proper redirect
     */
    public function login()
    {
        $this->validate();

        // Validate Turnstile first
        if (!$this->validateTurnstile()) {
            return;
        }

        // Rate limiting with enhanced key
        $key = 'login-attempts:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->errorMessage = "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.";
            $this->resetTurnstile(); // Reset Turnstile on rate limit
            return;
        }

        // Attempt authentication
        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            RateLimiter::clear($key);
            
            $user = Auth::user();
            
            // Log successful login
            Log::info('User logged in successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => request()->ip(),
            ]);
            
            // Check if user needs email verification
            if (!$user->hasVerifiedEmail()) {
                $this->sendEmailVerification($user);
                return;
            }

            // Successful login - use AuthService for smart redirect
            session()->regenerate();
            $this->successMessage = 'Login berhasil! Mengalihkan...';
            
            $authService = app(AuthService::class);
            $redirectUrl = $authService->handlePostLoginRedirect($user);
            
            // Log redirect URL for debugging
            Log::info('Login redirect', [
                'user_id' => $user->id,
                'user_roles' => $user->getRoleNames()->toArray(),
                'redirect_url' => $redirectUrl,
            ]);
            
            // Use immediate redirect for better UX
            return $this->redirect($redirectUrl);
            
        } else {
            RateLimiter::hit($key);
            $this->errorMessage = 'Email atau password tidak valid.';
            $this->resetTurnstile(); // Reset Turnstile on failed login
            
            // Log failed login attempt
            Log::warning('Failed login attempt', [
                'email' => $this->email,
                'ip' => request()->ip(),
            ]);
        }
    }

    /**
     * Send email verification for unverified users
     */
    protected function sendEmailVerification(User $user)
    {
        Auth::logout();
        
        $otpService = app(OtpService::class);
        $result = $otpService->sendOtp($user->email, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->resetTurnstile();
            return;
        }

        // Store email for verification step
        session(['verify_email' => $user->email]);
        
        // Move to verification step
        $this->currentStep = 'verification';
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = 'Email belum terverifikasi. Kami telah mengirim kode verifikasi ke email Anda.';
        $this->errorMessage = '';
    }

    /**
     * Verify email with OTP - using correct OtpService method
     */
    public function verifyEmail()
    {
        $this->validate();

        $email = session('verify_email');
        if (!$email) {
            $this->errorMessage = 'Sesi verifikasi telah berakhir. Silakan login ulang.';
            $this->backToLogin();
            return;
        }

        $otpService = app(OtpService::class);
        
        // Use the correct method: verifyOtp (not verifyEmailOtp)
        $result = $otpService->verifyOtp($email, $this->otp_code, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->otp_code = '';
            return;
        }

        // Find user and mark as verified, then login
        $user = User::where('email', $email)->first();
        if ($user) {
            // Mark email as verified if not already
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }
            
            // Login the user
            Auth::login($user, $this->remember);
            session()->regenerate();
            
            // Clear verification session
            session()->forget('verify_email');
            
            $this->successMessage = 'Email berhasil diverifikasi! Mengalihkan...';
            
            // Use AuthService for smart redirect
            $authService = app(AuthService::class);
            $redirectUrl = $authService->handlePostLoginRedirect($user);
            
            // Use immediate redirect
            return $this->redirect($redirectUrl);
        } else {
            $this->errorMessage = 'User tidak ditemukan. Silakan login ulang.';
            $this->backToLogin();
        }
    }

    /**
     * Resend email verification OTP
     */
    public function resendEmailVerification()
    {
        if (!$this->canResend) {
            return;
        }

        $email = session('verify_email');
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);
        
        // Use the correct method: sendOtp (or resendOtp)
        $result = $otpService->sendOtp($email, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
        $this->otp_code = '';
    }

    /**
     * Handle OTP timer expiration
     */
    public function handleOtpExpired()
    {
        $this->canResend = true;
        $this->timeLeft = 0;
        $this->errorMessage = 'Kode OTP telah kedaluarsa. Silakan minta kode baru.';
    }

    /**
     * Go back to login form
     */
    public function backToLogin()
    {
        $this->currentStep = 'login';
        $this->otp_code = '';
        $this->password = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->timeLeft = 0;
        $this->canResend = true;
        $this->resetTurnstile(); // Reset Turnstile when going back
        session()->forget('verify_email');
    }

    public function render()
    {
        return view('livewire.auth.login', [
            'siteKey' => $this->getTurnstileSiteKey(),
        ])->layout('components.layouts.auth');
    }
}