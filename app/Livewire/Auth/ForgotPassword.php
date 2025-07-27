<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
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
    
    // Password reset
    public string $password = '';
    public string $password_confirmation = '';
    
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

    public function sendResetCode()
    {
        // Rate limiting
        $key = 'forgot-password:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->errorMessage = "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.";
            return;
        }

        $this->validate();

        try {
            // Generate and send OTP
            $otpService = app(OtpService::class);
            $otp = $otpService->generateOtp($this->email, 'password_reset');
            $otpService->sendOtpEmail($this->email, $otp, 'password_reset');

            // Store email in session
            session(['reset_email' => $this->email]);

            // Move to verification step
            $this->currentStep = 'verification';
            $this->timeLeft = $otpService->getRemainingTime($this->email, 'password_reset');
            $this->successMessage = 'Kode reset password telah dikirim ke email Anda.';
            $this->errorMessage = '';

            RateLimiter::hit($key, 300);

        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat mengirim kode reset. Silakan coba lagi.';
            logger()->error('Password reset OTP send failed', [
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);
        }
    }

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

        if (!$otpService->verifyOtp($email, $this->otp_code, 'password_reset')) {
            $this->errorMessage = 'Kode OTP salah atau sudah kedaluarsa.';
            $this->otp_code = '';
            return;
        }

        // Move to password reset step
        $this->currentStep = 'reset';
        $this->successMessage = 'Kode berhasil diverifikasi. Silakan masukkan password baru.';
        $this->errorMessage = '';
    }

    public function resetPassword()
    {
        $this->validate();

        $email = session('reset_email');

        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan. Silakan mulai ulang.';
            $this->backToEmail();
            return;
        }

        try {
            // Update user password
            $user = User::where('email', $email)->first();
            $user->update([
                'password' => Hash::make($this->password)
            ]);

            // Clear session data
            session()->forget('reset_email');

            // Move to success step
            $this->currentStep = 'success';
            $this->successMessage = 'Password berhasil direset! Anda akan diarahkan ke halaman login.';

            $this->dispatch('redirect-after-delay', url: route('login'), delay: 3000);

        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat mereset password. Silakan coba lagi.';
            logger()->error('Password reset failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function resendOtp()
    {
        $email = session('reset_email');
        
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);

        if ($otpService->resendOtp($email, 'password_reset')) {
            $this->timeLeft = $otpService->getRemainingTime($email, 'password_reset');
            $this->canResend = false;
            $this->successMessage = 'Kode reset password baru telah dikirim.';
            $this->errorMessage = '';
            $this->otp_code = '';

            $this->dispatch('enable-resend-after-delay', delay: 60000);
        } else {
            $this->errorMessage = 'Tidak dapat mengirim ulang kode. Silakan tunggu sebentar.';
        }
    }

    public function backToEmail()
    {
        $this->currentStep = 'email';
        $this->otp_code = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        session()->forget('reset_email');
    }

    public function handleOtpExpired()
    {
        $this->canResend = true;
        $this->errorMessage = 'Kode OTP telah kedaluarsa. Silakan minta kode baru.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.auth');
    }
}