<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Register extends Component
{
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

    // Turnstile - SAME as Login
    public $turnstileResponse;

    protected $listeners = ['otpTimerExpired' => 'handleOtpExpired'];

    public function rules()
    {
        if ($this->currentStep === 'registration') {
            $rules = [
                'firstname' => 'required|string|max:255|regex:/^[\p{L}\s\-\'\.]+$/u',
                'lastname' => 'required|string|max:255|regex:/^[\p{L}\s\-\'\.]+$/u',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Password::defaults()],
                'no_telepon' => ['required', 'regex:/^(\+628[1-9][0-9]{6,11}|08[1-9][0-9]{6,11})$/'],
            ];
            
            // Only require turnstile in production environment
            if (!app()->environment('local')) {
                $rules['turnstileResponse'] = 'required';
            }
            
            return $rules;
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

    /**
     * Validate Turnstile - EXACTLY like Login
     */
    public function validateTurnstile()
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $this->turnstileResponse,
            'remoteip' => request()->ip(),
        ]);

        return $response->json('success') ?? false;
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
                $username = $baseUsername . rand(1000, 9999);
                break;
            }
        }
        
        $this->generated_username = $username;
    }

    protected function cleanName(string $name): string
    {
        // Remove special characters, keep only letters
        $name = preg_replace('/[^\p{L}]/u', '', $name);
        return $name;
    }

    protected function normalizePhoneNumber(string $phone): string
    {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert to +62 format
        if (str_starts_with($phone, '08')) {
            $phone = '+628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8') && strlen($phone) >= 9) {
            $phone = '+628' . substr($phone, 1);
        } elseif (str_starts_with($phone, '628')) {
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+62')) {
            // If it doesn't start with +62 and not 08, assume it's missing +628
            if (strlen($phone) >= 8) {
                $phone = '+628' . $phone;
            }
        }
        
        return $phone;
    }

    /**
     * IMPROVED: Register new user with better error handling
     */
    public function register()
    {
        $this->validate();

        // Turnstile validation
        if (!app()->environment('local') && !$this->validateTurnstile()) {
            $this->errorMessage = 'Verifikasi keamanan gagal. Silakan coba lagi.';
            $this->turnstileResponse = ''; // Reset Turnstile
            return;
        }

        // Generate final username
        $this->generateUsername();

        // Prepare registration data
        $registrationData = [
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'username' => $this->generated_username,
            'email' => $this->email,
            'no_telepon' => $this->no_telepon,
            'password' => $this->password,
        ];

        // Store data in session for use during OTP verification
        session([
            'registration_data' => $registrationData,
            'otp_email' => $this->email
        ]);

        // Send OTP using improved service
        $otpService = app(OtpService::class);
        $result = $otpService->sendOtp($this->email, 'registration');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->turnstileResponse = ''; // Reset Turnstile on error
            return;
        }

        // Move to verification step
        $this->currentStep = 'verification';
        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = 'Kode verifikasi telah dikirim ke email Anda.';
        $this->errorMessage = '';
        $this->turnstileResponse = ''; // Clear Turnstile after successful submission
    }

    /**
     * IMPROVED: Verify OTP and complete registration
     */
    public function verifyOtp()
    {
        $this->validate();

        $email = session('otp_email');
        $registrationData = session('registration_data');

        if (!$email || !$registrationData) {
            $this->errorMessage = 'Data registrasi tidak ditemukan. Silakan mulai ulang.';
            $this->backToRegistration();
            return;
        }

        // Use improved OTP service
        $otpService = app(OtpService::class);
        $result = $otpService->completeRegistration($registrationData, $email, $this->otp_code);
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            $this->otp_code = '';
            return;
        }

        // Clear session data
        session()->forget(['registration_data', 'otp_email']);

        // Move to success step
        $this->currentStep = 'success';
        $this->successMessage = $result['message'];

        // Redirect to login after delay
        $redirectUrl = $result['data']['redirect_url'] ?? route('login');
        $this->dispatch('redirect-after-delay', url: $redirectUrl, delay: 3000);
    }

    /**
     * IMPROVED: Resend OTP with better error handling
     */
    public function resendOtp()
    {
        if (!$this->canResend) {
            return;
        }

        $email = session('otp_email');
        if (!$email) {
            $this->errorMessage = 'Email tidak ditemukan. Silakan mulai ulang registrasi.';
            return;
        }

        // Use improved OTP service
        $otpService = app(OtpService::class);
        $result = $otpService->resendOtp($email, 'registration');
        
        if (!$result['success']) {
            $this->errorMessage = $result['message'];
            return;
        }

        $this->timeLeft = $result['data']['remaining_time'];
        $this->canResend = $result['data']['can_resend'];
        $this->successMessage = $result['message'];
        $this->errorMessage = '';
        $this->otp_code = ''; // Clear OTP input
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
        $this->turnstileResponse = ''; // Reset Turnstile
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

    public function render()
    {
        return view('livewire.auth.register')->layout('components.layouts.auth');
    }
}