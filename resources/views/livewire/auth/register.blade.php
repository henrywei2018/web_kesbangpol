<form class="login100-form validate-form" wire:submit="{{ $currentStep === 'registration' ? 'register' : 'verifyOtp' }}">
    <span class="login100-form-title">
        @if($currentStep === 'registration')
            Daftar Akun
        @elseif($currentStep === 'verification')
            Verifikasi Email
        @else
            Registrasi Berhasil
        @endif
    </span>

    {{-- Progress Indicator --}}
    @if($currentStep !== 'success')
        <div class="step-indicator">
            <div class="step {{ $currentStep === 'registration' ? 'active' : 'completed' }}">1</div>
            <div class="step-connector {{ $currentStep !== 'registration' ? 'completed' : '' }}"></div>
            <div class="step {{ $currentStep === 'verification' ? 'active' : ($currentStep === 'success' ? 'completed' : 'inactive') }}">2</div>
        </div>
    @endif

    {{-- Alert Messages --}}
    @if($errorMessage)
        <div class="alert alert-danger">
            {{ $errorMessage }}
        </div>
    @endif

    @if($successMessage)
        <div class="alert alert-success">
            {{ $successMessage }}
        </div>
    @endif

    {{-- Registration Form --}}
    @if($currentStep === 'registration')
        <div class="wrap-input100 validate-input @error('firstname') has-error @enderror">
            <input class="input100" type="text" wire:model="firstname" placeholder="First Name">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-user" aria-hidden="true"></i>
            </span>
        </div>
        @error('firstname')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        <div class="wrap-input100 validate-input @error('lastname') has-error @enderror">
            <input class="input100" type="text" wire:model="lastname" placeholder="Last Name">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-user-circle" aria-hidden="true"></i>
            </span>
        </div>
        @error('lastname')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        <div class="wrap-input100 validate-input @error('email') has-error @enderror">
            <input class="input100" type="email" wire:model="email" placeholder="Email">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-envelope" aria-hidden="true"></i>
            </span>
        </div>
        @error('email')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror
        <div class="wrap-input100 validate-input @error('no_telepon') has-error @enderror">
            <input class="input100" type="tel" wire:model="no_telepon" placeholder="Nomor WhatsApp (contoh: +6281234567890)">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-phone" aria-hidden="true"></i>
            </span>
        </div>
        @error('no_telepon')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        <div class="wrap-input100 validate-input @error('password') has-error @enderror" style="position: relative;">
            <input class="input100" :type="showPassword ? 'text' : 'password'" wire:model="password" placeholder="Password" id="password">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
            <span onclick="togglePassword('password', this)" class="password-toggle">
                <i class="fa fa-eye"></i>
            </span>
        </div>
        @error('password')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        <div class="wrap-input100 validate-input" style="position: relative;">
            <input class="input100" :type="showConfirm ? 'text' : 'password'" wire:model="password_confirmation" placeholder="Konfirmasi Password" id="password_confirmation">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
            <span onclick="togglePassword('password_confirmation', this)" class="password-toggle">
                <i class="fa fa-eye"></i>
            </span>
        </div>

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit" 
                    wire:loading.class="loading-btn" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Daftar</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>

        <div class="text-center p-t-12">
            <a class="txt2" href="{{ route('login') }}">
                Sudah punya akun? Masuk di sini
                <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
            </a>
        </div>
    @endif

    {{-- OTP Verification Form --}}
    @if($currentStep === 'verification')
        <div style="text-align: center; margin-bottom: 20px; font-size: 14px; color: #666;">
            Kode dikirim ke: <strong>{{ session('otp_email') }}</strong>
        </div>

        <div class="wrap-input100 validate-input @error('otp_code') has-error @enderror">
            <input class="input100 otp-input" type="text" wire:model="otp_code" placeholder="000000" maxlength="6">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-key" aria-hidden="true"></i>
            </span>
        </div>
        @error('otp_code')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        {{-- Timer --}}
        @if($timeLeft > 0)
            <div style="text-align: center; margin-bottom: 15px;">
                @livewire('auth.otp-timer', ['timeLeft' => $timeLeft, 'email' => session('otp_email', '')])
            </div>
        @endif

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit" 
                    wire:loading.class="loading-btn" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Verifikasi</span>
                <span wire:loading>Memverifikasi...</span>
            </button>
        </div>

        <div class="text-center p-t-12">
            <button type="button" class="btn-link" wire:click="resendOtp" 
                    {{ !$canResend ? 'disabled' : '' }}>
                {{ $canResend ? 'Kirim Ulang Kode' : 'Tunggu untuk kirim ulang' }}
            </button>
        </div>

        <div class="text-center p-t-12">
            <button type="button" class="btn-link" wire:click="backToRegistration">
                Kembali ke Form Registrasi
            </button>
        </div>
    @endif

    {{-- Success Message --}}
    @if($currentStep === 'success')
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 48px; color: #48bb78; margin-bottom: 20px;">
                <i class="fa fa-check-circle"></i>
            </div>
            <h3 style="color: #2d3748; margin-bottom: 10px;">Registrasi Berhasil!</h3>
            <p style="color: #718096; margin-bottom: 20px;">Anda akan diarahkan ke dashboard dalam beberapa detik...</p>
            
            <div style="width: 100%; background-color: #e2e8f0; border-radius: 10px; height: 4px; margin-top: 20px;">
                <div style="background-color: #667eea; height: 4px; border-radius: 10px; animation: progress 3s ease-in-out;"></div>
            </div>
        </div>
        
        <style>
            @keyframes progress {
                from { width: 0%; }
                to { width: 100%; }
            }
            .password-toggle {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #aaa;
                font-size: 16px;
            }

            .password-toggle:hover {
                color: #333;
            }
        </style>
    @endif
</form>

{{-- Scripts specific to registration component --}}
@if($currentStep === 'verification')
<script>
    
    document.addEventListener('livewire:init', () => {
        Livewire.on('enable-resend-after-delay', (event) => {
            setTimeout(() => {
                @this.set('canResend', true);
            }, event.delay);
        });
    })
    function togglePassword(fieldId, toggleIcon) {
        const input = document.getElementById(fieldId);
        if (input.type === "password") {
            input.type = "text";
            toggleIcon.innerHTML = '<i class="fa fa-eye-slash"></i>';
        } else {
            input.type = "password";
            toggleIcon.innerHTML = '<i class="fa fa-eye"></i>';
        }
    };
</script>
@endif