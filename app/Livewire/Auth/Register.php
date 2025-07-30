<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\AuthService;
use App\Services\OtpService;
use App\Traits\HasTurnstile;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class Register extends Component
{
    use HasTurnstile;

    public string $currentStep = 'registration';
    
    // Registration form fields
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $no_telepon = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    // Generated username (not editable by user)
    public string $generated_username = '';
    
    // Password visibility states
    public bool $showPassword = false;
    public bool $showPasswordConfirmation = false;
    
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
                'no_telepon' => ['required', 'regex:/^(\+628[1-9][0-9]{6,11}|08[1-9][0-9]{6,11})$/'],
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
            'firstname.required' => 'Nama depan wajib diisi.',
            'firstname.regex' => 'Nama depan hanya boleh mengandung huruf, spasi, dan tanda baca.',
            'lastname.required' => 'Nama belakang wajib diisi.',
            'lastname.regex' => 'Nama belakang hanya boleh mengandung huruf, spasi, dan tanda baca.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'no_telepon.required' => 'Nomor HP wajib diisi.',
            'no_telepon.regex' => 'Nomor HP harus berformat Indonesia. Contoh: 081234567890 atau +6281234567890',
            'otp_code.required' => 'Kode OTP wajib diisi.',
            'otp_code.size' => 'Kode OTP harus 6 digit.',
            'turnstileResponse.required' => 'Harap selesaikan verifikasi keamanan.',
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

    // Auto-normalize phone number when updated
    public function updatedNoTelepon()
    {
        $this->no_telepon = $this->normalizePhoneNumber($this->no_telepon);
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

    protected function normalizePhoneNumber(string $phone): string
    {
        // Hapus spasi, tanda baca, dan karakter tidak valid
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Ubah 08xxxx menjadi +628xxxx
        if (Str::startsWith($phone, '08')) {
            $phone = '+62' . substr($phone, 1);
        }

        // Jika tidak diawali dengan + dan diawali 62, tambahkan +
        if (Str::startsWith($phone, '62') && !Str::startsWith($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Handle registration form submission with Turnstile
     */
    public function register()
    {
        // Generate username if not already done
        $this->generateUsername();
        $this->validate();

        // Validate Turnstile
        if (!$this->validateTurnstile()) {
            return;
        }

        $normalizedPhone = $this->normalizePhoneNumber($this->no_telepon);

        $otpService = app(OtpService::class);
        
        // Delegate to service
        $result = $otpService->sendOtp($this->email, 'registration');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->resetTurnstile(); // Reset Turnstile on error
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
                'no_telepon' => $normalizedPhone,
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
        $authService = app(AuthService::class);
        $redirectUrl = $authService->getDashboardUrl(auth()->user());
        $this->dispatch('redirect-after-delay', url: $redirectUrl, delay: 3000);
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        if (!$this->canResend) {
            return;
        }

        $email = session('otp_email');
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan. Silakan mulai ulang registrasi.';
            return;
        }

        $otpService = app(OtpService::class);
        $result = $otpService->sendOtp($email, 'registration');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
    }

    /**
     * Back to registration form
     */
    public function backToRegistration()
    {
        // Clear session data
        session()->forget(['registration_data', 'otp_email']);
        
        // Reset component state
        $this->currentStep = 'registration';
        $this->otp_code = '';
        $this->timeLeft = 0;
        $this->canResend = true;
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->resetTurnstile(); // Reset Turnstile when going back
    }

    /**
     * Handle OTP timer expiration
     */
    public function handleOtpExpired()
    {
        $this->canResend = true;
        $this->timeLeft = 0;
    }

    public function render()
    {
        return view('livewire.auth.register', [
            'siteKey' => $this->getTurnstileSiteKey(),
        ])->layout('components.layouts.auth');
    }
}