<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class Login extends Component
{
    public string $currentStep = 'login';
    
    // Login form
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    
    // Email verification OTP
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
                'password' => 'required|string|min:6',
            ];
        } elseif ($this->currentStep === 'email_verification') {
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
        ];
    }

    /**
     * Handle login attempt with email verification check
     */
    public function login()
    {
        // Rate limiting
        $key = 'login:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->errorMessage = "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.";
            return;
        }

        $this->validate();

        $credentials = [
            'email' => $this->email,
            'password' => $this->password,
        ];

        // Attempt to authenticate
        if (!Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 300); // 5 minutes lockout
            $this->errorMessage = 'Email atau password salah.';
            $this->password = '';
            return;
        }

        // Authentication successful, now check email verification
        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            // Email not verified, send OTP for verification
            $this->handleUnverifiedEmail($user);
            return;
        }

        // Email is verified, complete login
        $this->completeLogin();
    }

    /**
     * Handle user with unverified email
     */
    protected function handleUnverifiedEmail(User $user)
    {
        // Don't keep user logged in if email not verified
        Auth::logout();

        $otpService = app(OtpService::class);
        
        // Send email verification OTP
        $result = $otpService->sendOtp($user->email, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Store email for verification process
        session(['verify_email' => $user->email]);

        // Switch to email verification step
        $this->currentStep = 'email_verification';
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = 'Email Anda belum terverifikasi. Kode verifikasi telah dikirim ke email Anda.';
        $this->errorMessage = '';
    }

    /**
     * Complete successful login
     */
    protected function completeLogin()
    {
        $user = Auth::user();
        
        session()->regenerate();
        RateLimiter::clear('login:' . request()->ip());
        
        $this->successMessage = 'Login berhasil! Mengarahkan ke dashboard...';
        
        // Redirect based on user role
        $redirectUrl = $this->getRedirectUrl($user);
        $this->dispatch('redirect-after-delay', url: $redirectUrl, delay: 1500);
    }

    /**
     * Verify email with OTP
     */
    public function verifyEmail()
    {
        $this->validate();

        $email = session('verify_email');

        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan. Silakan login ulang.';
            $this->backToLogin();
            return;
        }

        $otpService = app(OtpService::class);
        
        // Verify OTP
        $result = $otpService->verifyOtp($email, $this->otp_code, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->otp_code = '';
            return;
        }

        // Mark email as verified and complete login
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->markEmailAsVerified();
            
            // Log the user in
            Auth::login($user, $this->remember);
            
            // Clean up session
            session()->forget('verify_email');
            
            // Complete login process
            $this->completeLogin();
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
        $email = session('verify_email');
        
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);
        
        // Resend OTP
        $result = $otpService->resendOtp($email, 'email_verification');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Update UI state
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = false;
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
        $this->otp_code = '';

        $this->dispatch('enable-resend-after-delay', delay: 60000);
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
        session()->forget('verify_email');
    }

    /**
     * Get redirect URL based on user role
     */
    protected function getRedirectUrl(User $user): string
    {
        if ($user->hasRole('super_admin')) {
            return route('filament.admin.pages.dashboard');
        }
        
        if ($user->hasRole('public')) {
            return '/panel/';
        }
        
        // Default fallback
        return '/panel/';
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth');
    }
}