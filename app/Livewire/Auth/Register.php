<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class Register extends Component
{
    public string $currentStep = 'registration';
    
    // Registration form fields
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Generated username (not editable by user)
    public string $generated_username = '';
    
    // OTP verification fields
    public string $otp_code = '';
    public int $timeLeft = 0;
    public bool $canResend = true;
    public string $errorMessage = '';
    public string $successMessage = '';

    protected $listeners = ['otpTimerExpired' => 'handleOtpExpired'];

    public function rules()
    {
        if ($this->currentStep === 'registration') {
            return [
                'firstname' => 'required|string|max:255|regex:/^[\p{L}\s\-\'\.]+$/u',
                'lastname' => 'required|string|max:255|regex:/^[\p{L}\s\-\'\.]+$/u',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Password::defaults()],
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
            'firstname.required' => 'Nama depan wajib diisi.',
            'firstname.regex' => 'Nama depan hanya boleh mengandung huruf, spasi, dan tanda baca.',
            'lastname.required' => 'Nama belakang wajib diisi.',
            'lastname.regex' => 'Nama belakang hanya boleh mengandung huruf, spasi, dan tanda baca.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
        ];
    }

    // Generate username when firstname or lastname changes
    public function updatedFirstname()
    {
        $this->generateUsername();
    }

    public function updatedLastname()
    {
        $this->generateUsername();
    }

    protected function generateUsername(): void
    {
        if (empty($this->firstname) || empty($this->lastname)) {
            $this->generated_username = '';
            return;
        }

        // Clean and prepare names
        $firstname = $this->cleanName($this->firstname);
        $lastname = $this->cleanName($this->lastname);
        
        // Create base username
        $baseUsername = strtolower($firstname . $lastname);
        
        // Remove any remaining invalid characters
        $baseUsername = preg_replace('/[^a-z0-9]/', '', $baseUsername);
        
        // Ensure minimum length
        if (strlen($baseUsername) < 3) {
            $baseUsername = $baseUsername . '123';
        }
        
        // Check for uniqueness and add suffix if needed
        $username = $baseUsername;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 9999) {
                $username = $baseUsername . time();
                break;
            }
        }
        
        $this->generated_username = $username;
    }

    private function cleanName(string $name): string
    {
        // Remove accents and convert to ASCII
        $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        
        // Remove special characters except letters and spaces
        $name = preg_replace('/[^a-zA-Z\s]/', '', $name);
        
        // Replace multiple spaces with single space and trim
        $name = preg_replace('/\s+/', ' ', trim($name));
        
        return $name;
    }

    /**
     * Handle registration form submission - delegate to service
     */
    public function register()
    {
        // Generate username if not already done
        $this->generateUsername();
        $this->validate();

        $otpService = app(OtpService::class);
        
        // Delegate to service
        $result = $otpService->sendOtp($this->email, 'registration');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        // Store registration data in session
        session([
            'registration_data' => [
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'username' => $this->generated_username,
                'email' => $this->email,
                'password' => $this->password,
            ],
            'otp_email' => $this->email
        ]);

        // Update UI state
        $this->currentStep = 'verification';
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
    }

    /**
     * Handle OTP verification - delegate to service
     */
    public function verifyOtp()
    {
        $this->validate();

        $registrationData = session('registration_data');
        $email = session('otp_email');

        if (!$registrationData || !$email) {
            $this->errorMessage = 'Data registrasi tidak ditemukan. Silakan mulai ulang.';
            $this->backToRegistration();
            return;
        }

        $otpService = app(OtpService::class);
        
        // Delegate complete registration to service
        $result = $otpService->completeRegistration($registrationData, $email, $this->otp_code);
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->otp_code = '';
            return;
        }

        // Clear session data
        session()->forget(['registration_data', 'otp_email']);

        // Update UI state
        $this->currentStep = 'success';
        $this->successMessage = $result['message'];

        // Redirect after delay
        $redirectUrl = $result['data']['redirect_url'] ?? '/dashboard';
        $this->dispatch('redirect-after-delay', url: $redirectUrl, delay: 3000);
    }

    /**
     * Handle OTP resend - delegate to service
     */
    public function resendOtp()
    {
        $email = session('otp_email');
        
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);
        
        // Delegate to service
        $result = $otpService->resendOtp($email, 'registration');
        
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
    }

    /**
     * Go back to registration step
     */
    public function backToRegistration()
    {
        $this->currentStep = 'registration';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->otp_code = '';
        session()->forget(['registration_data', 'otp_email']);
    }

    /**
     * Get redirect URL after successful registration
     */
    protected function getRedirectUrl(): string
    {
        return auth()->user()?->hasRole('super_admin') ? '/admin' : '/panel/';
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}