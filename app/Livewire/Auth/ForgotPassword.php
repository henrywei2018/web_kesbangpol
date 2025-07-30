<?php

namespace App\Livewire\Auth;

use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;

class ForgotPassword extends Component
{
    public string $currentStep = 'email';
    
    // Email form
    public string $email = '';
    
    // OTP verification
    public string $otp_code = '';
    public int $timeLeft = 0;
    public bool $canResend = true;
    
    // Password reset with show/hide functionality
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showPassword = false;
    public bool $showPasswordConfirmation = false;
    
    public string $errorMessage = '';
    public string $successMessage = '';

    protected $listeners = ['otpTimerExpired' => 'handleOtpExpired'];

    public function rules()
    {
        if ($this->currentStep === 'email') {
            return [
                'email' => 'required|email|exists:users,email',
            ];
        } elseif ($this->currentStep === 'verification') {
            return [
                'otp_code' => 'required|string|size:6',
            ];
        } elseif ($this->currentStep === 'reset') {
            return [
                'password' => ['required', 'confirmed', Password::defaults()],
            ];
        }
        
        return [];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar dalam sistem.',
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }

    // Toggle password visibility
    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    // Toggle password confirmation visibility
    public function togglePasswordConfirmationVisibility()
    {
        $this->showPasswordConfirmation = !$this->showPasswordConfirmation;
    }

    /**
     * Send reset code - delegate to enhanced OtpService
     */
    public function sendResetCode()
    {
        $this->validate();

        $otpService = app(OtpService::class);
        
        // Delegate to enhanced service
        $result = $otpService->sendOtp($this->email, 'password_reset');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Store email in session for next steps
        session(['reset_email' => $this->email]);

        // Update UI state based on service response
        $this->currentStep = 'verification';
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
    }

    /**
     * Verify reset code - delegate to enhanced OtpService
     */
    public function verifyResetCode()
    {
        $this->validate();

        $email = session('reset_email');

        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan. Silakan mulai ulang.';
            $this->backToEmail();
            return;
        }

        $otpService = app(OtpService::class);
        
        // Delegate to enhanced service - just verify, don't complete reset yet
        $result = $otpService->verifyOtp($email, $this->otp_code, 'password_reset');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->otp_code = '';
            return;
        }

        // Move to password reset step
        $this->currentStep = 'reset';
        $this->successMessage = 'Kode berhasil diverifikasi. Silakan masukkan password baru.';
        $this->errorMessage = '';
    }

    /**
     * Reset password - delegate to enhanced OtpService
     */
    public function resetPassword()
    {
        $this->validate();

        $email = session('reset_email');

        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan. Silakan mulai ulang.';
            $this->backToEmail();
            return;
        }

        $otpService = app(OtpService::class);
        
        // Delegate complete password reset to enhanced service
        $result = $otpService->completePasswordReset($email, $this->otp_code, $this->password);
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Clear session data
        session()->forget('reset_email');

        // Move to success step
        $this->currentStep = 'success';
        $this->successMessage = $result['message'];

        $this->dispatch('redirect-after-delay', url: route('login'), delay: 3000);
    }

    /**
     * Resend OTP - delegate to enhanced OtpService
     */
    public function resendOtp()
    {
        if (!$this->canResend) {
            return;
        }

        $email = session('reset_email');
        
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);
        
        // Delegate to enhanced service
        $result = $otpService->sendOtp($email, 'password_reset');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Update UI state based on service response
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
    }

    /**
     * Go back to email step
     */
    public function backToEmail()
    {
        $this->currentStep = 'email';
        $this->otp_code = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->timeLeft = 0;
        $this->canResend = true;
        session()->forget('reset_email');
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.auth');
    }
}