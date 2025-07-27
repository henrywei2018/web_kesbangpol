<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\OtpService;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class Register extends Component
{
    public string $currentStep = 'registration';
    
    // Simplified registration form fields
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    
    public string $generated_username = '';
    
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
                $username = $baseUsername . rand(10000, 99999);
                break;
            }
        }
        
        $this->generated_username = $username;
    }

    protected function cleanName(string $name): string
    {
        // Remove extra spaces and special characters
        $name = trim($name);
        $name = preg_replace('/\s+/', '', $name); // Remove all spaces
        $name = preg_replace('/[^\p{L}]/u', '', $name); // Keep only letters
        
        // Transliterate non-ASCII characters
        $name = $this->transliterate($name);
        
        return $name;
    }

    protected function transliterate(string $text): string
    {
        // Common Indonesian/international name transliterations
        $transliterations = [
            'ä' => 'a', 'ö' => 'o', 'ü' => 'u', 'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
            // Add more as needed
        ];
        
        return str_replace(array_keys($transliterations), array_values($transliterations), strtolower($text));
    }

    public function submitRegistration()
    {
        $key = 'register:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $this->errorMessage = "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.";
            return;
        }

        // Generate username if not already done
        $this->generateUsername();

        $this->validate();

        // Double-check username uniqueness before proceeding
        if (User::where('username', $this->generated_username)->exists()) {
            $this->generateUsername();
        }

        try {
            $otpService = app(OtpService::class);
            $otp = $otpService->generateOtp($this->email, 'registration');
            $otpService->sendOtpEmail($this->email, $otp, 'registration');

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

            $this->currentStep = 'verification';
            $this->timeLeft = $otpService->getRemainingTime($this->email, 'registration');
            $this->successMessage = 'Kode verifikasi telah dikirim ke email Anda.';
            $this->errorMessage = '';

            RateLimiter::hit($key, 300);

        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat mengirim kode verifikasi. Silakan coba lagi.';
            logger()->error('Registration OTP send failed', [
                'email' => $this->email,
                'error' => $e->getMessage()
            ]);
        }
    }

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

        if (!$otpService->verifyOtp($email, $this->otp_code, 'registration')) {
            $this->errorMessage = 'Kode OTP salah atau sudah kedaluarsa.';
            $this->otp_code = '';
            return;
        }

        try {
            // Final username uniqueness check before creating user
            $finalUsername = $registrationData['username'];
            if (User::where('username', $finalUsername)->exists()) {
                // Regenerate if someone took it during OTP verification
                $baseUsername = preg_replace('/\d+$/', '', $finalUsername);
                $counter = 1;
                do {
                    $finalUsername = $baseUsername . $counter;
                    $counter++;
                } while (User::where('username', $finalUsername)->exists() && $counter < 10000);
            }

            // Create user account with only required fields
            $user = User::create([
                'firstname' => $registrationData['firstname'],
                'lastname' => $registrationData['lastname'],
                'username' => $finalUsername,
                'email' => $registrationData['email'],
                'password' => Hash::make($registrationData['password']),
                'email_verified_at' => now(),
                // Optional fields are set to null by default
                'no_ktp' => null,
                'domisili' => null,
                'alamat' => null,
                'no_telepon' => null,
            ]);

            event(new Registered($user));
            Auth::login($user);

            session()->forget(['registration_data', 'otp_email']);

            $this->currentStep = 'success';
            $this->successMessage = 'Registrasi berhasil! Anda akan diarahkan ke dashboard.';

            $this->dispatch('redirect-after-delay', url: route('filament.admin.pages.dashboard'), delay: 3000);

        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.';
            logger()->error('User creation failed', [
                'email' => $email,
                'error' => $e->getMessage(),
                'username' => $finalUsername ?? 'unknown'
            ]);
        }
    }

    public function resendOtp()
    {
        $email = session('otp_email');
        
        if (!$email) {
            $this->errorMessage = 'Data email tidak ditemukan.';
            return;
        }

        $otpService = app(OtpService::class);

        if ($otpService->resendOtp($email, 'registration')) {
            $this->timeLeft = $otpService->getRemainingTime($email, 'registration');
            $this->canResend = false;
            $this->successMessage = 'Kode verifikasi baru telah dikirim.';
            $this->errorMessage = '';
            $this->otp_code = '';

            $this->dispatch('enable-resend-after-delay', delay: 60000);
        } else {
            $this->errorMessage = 'Tidak dapat mengirim ulang kode. Silakan tunggu sebentar.';
        }
    }

    public function backToRegistration()
    {
        $this->currentStep = 'registration';
        $this->otp_code = '';
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->generated_username = '';
        session()->forget(['registration_data', 'otp_email']);
    }

    public function handleOtpExpired()
    {
        $this->canResend = true;
        $this->errorMessage = 'Kode OTP telah kedaluarsa. Silakan minta kode baru.';
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('components.layouts.auth');
    }
}