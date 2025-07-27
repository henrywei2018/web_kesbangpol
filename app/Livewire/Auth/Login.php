<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $errorMessage = '';
    public string $successMessage = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ];

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 6 karakter.',
    ];

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

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            RateLimiter::clear($key);
            
            $this->successMessage = 'Login berhasil! Mengarahkan ke dashboard...';
            
            // Redirect after success
            $this->dispatch('redirect-after-delay', url: route('filament.admin.home'), delay: 1500);
        } else {
            RateLimiter::hit($key, 300); // 5 minutes lockout
            $this->errorMessage = 'Email atau password salah.';
            $this->password = '';
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.auth');
    }
}
