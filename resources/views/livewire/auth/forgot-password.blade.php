<div>
<form class="login100-form validate-form" wire:submit="{{ 
    $currentStep === 'email' ? 'sendResetCode' : 
    ($currentStep === 'verification' ? 'verifyResetCode' : 'resetPassword') 
}}">
    <span class="login100-form-title">
        @if($currentStep === 'email')
            Reset Password
        @elseif($currentStep === 'verification')
            Verifikasi Kode
        @elseif($currentStep === 'reset')
            Password Baru
        @else
            Reset Berhasil
        @endif
    </span>

    {{-- Progress Indicator --}}
    @if($currentStep !== 'success')
        <div class="step-indicator">
            <div class="step {{ $currentStep === 'email' ? 'active' : 'completed' }}">1</div>
            <div class="step-connector {{ $currentStep !== 'email' ? 'completed' : '' }}"></div>
            <div class="step {{ $currentStep === 'verification' ? 'active' : ($currentStep === 'reset' || $currentStep === 'success' ? 'completed' : 'inactive') }}">2</div>
            <div class="step-connector {{ $currentStep === 'reset' || $currentStep === 'success' ? 'completed' : '' }}"></div>
            <div class="step {{ $currentStep === 'reset' ? 'active' : ($currentStep === 'success' ? 'completed' : 'inactive') }}">3</div>
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

    {{-- Step 1: Email Input --}}
    @if($currentStep === 'email')
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; color: #667eea; margin-bottom: 15px;">
                <i class="fa fa-key"></i>
            </div>
            <p style="color: #718096; margin-bottom: 5px;">Masukkan email Anda untuk menerima kode reset password</p>
        </div>

        <div class="wrap-input100 validate-input @error('email') has-error @enderror">
            <input class="input100" type="email" wire:model="email" placeholder="Masukkan email Anda">
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

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit" 
                    wire:loading.class="loading-btn" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Kirim Kode Reset</span>
                <span wire:loading>Mengirim...</span>
            </button>
        </div>

        <div class="text-center p-t-12">
            <a class="txt2" href="{{ route('login') }}">
                <i class="fa fa-long-arrow-left m-r-5" aria-hidden="true"></i>
                Kembali ke Login
            </a>
        </div>
    @endif

    {{-- Step 2: OTP Verification --}}
    @if($currentStep === 'verification')
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; color: #667eea; margin-bottom: 15px;">
                <i class="fa fa-envelope-open"></i>
            </div>
            <p style="color: #718096; margin-bottom: 5px;">Kami telah mengirim kode reset ke:</p>
            <p style="color: #2d3748; font-weight: 600;">{{ session('reset_email') }}</p>
        </div>

        <div class="wrap-input100 validate-input @error('otp_code') has-error @enderror">
            <input class="input100" type="text" wire:model="otp_code" placeholder="Masukkan 6 digit kode OTP" maxlength="6">
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

        @if($timeLeft > 0)
            <div style="text-align: center; margin-bottom: 20px; color: #718096;">
                Kirim ulang kode dalam: <span style="color: #667eea; font-weight: 600;" id="countdown">{{ $timeLeft }}</span> detik
            </div>
        @endif

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit" 
                    wire:loading.class="loading-btn" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Verifikasi Kode</span>
                <span wire:loading>Memverifikasi...</span>
            </button>
        </div>

        <div class="text-center p-t-12">
            <button type="button" class="btn-link" wire:click="resendOtp" 
                    style="background: none; border: none; color: #667eea; text-decoration: underline; cursor: pointer;"
                    {{ !$canResend ? 'disabled' : '' }}>
                {{ $canResend ? 'Kirim Ulang Kode' : 'Tunggu untuk kirim ulang' }}
            </button>
        </div>

        <div class="text-center p-t-12">
            <button type="button" class="btn-link" wire:click="backToEmail"
                    style="background: none; border: none; color: #718096; text-decoration: underline; cursor: pointer;">
                <i class="fa fa-long-arrow-left m-r-5" aria-hidden="true"></i>
                Kembali ke Email
            </button>
        </div>
    @endif

    {{-- Step 3: Password Reset --}}
    @if($currentStep === 'reset')
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="font-size: 48px; color: #667eea; margin-bottom: 15px;">
                <i class="fa fa-lock"></i>
            </div>
            <p style="color: #718096; margin-bottom: 5px;">Masukkan password baru Anda</p>
        </div>

        {{-- New Password Field with Show/Hide Toggle --}}
        <div class="wrap-input100 validate-input @error('password') has-error @enderror" style="position: relative;">
            <input class="input100" type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" placeholder="Password Baru">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
            <span class="password-toggle" wire:click="togglePasswordVisibility">
                <i class="fa {{ $showPassword ? 'fa-eye-slash' : 'fa-eye' }}" aria-hidden="true"></i>
            </span>
        </div>
        @error('password')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        {{-- Password Confirmation Field with Show/Hide Toggle --}}
        <div class="wrap-input100 validate-input @error('password_confirmation') has-error @enderror" style="position: relative;">
            <input class="input100" type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" wire:model="password_confirmation" placeholder="Konfirmasi Password Baru">
            <span class="focus-input100"></span>
            <span class="symbol-input100">
                <i class="fa fa-lock" aria-hidden="true"></i>
            </span>
            <span class="password-toggle" wire:click="togglePasswordConfirmationVisibility">
                <i class="fa {{ $showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye' }}" aria-hidden="true"></i>
            </span>
        </div>
        @error('password_confirmation')
            <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                {{ $message }}
            </div>
        @enderror

        <div class="container-login100-form-btn">
            <button class="login100-form-btn" type="submit" 
                    wire:loading.class="loading-btn" 
                    wire:loading.attr="disabled">
                <span wire:loading.remove>Reset Password</span>
                <span wire:loading>Memproses...</span>
            </button>
        </div>

        <div class="text-center p-t-12">
            <button type="button" class="btn-link" wire:click="backToEmail"
                    style="background: none; border: none; color: #718096; text-decoration: underline; cursor: pointer;">
                <i class="fa fa-long-arrow-left m-r-5" aria-hidden="true"></i>
                Mulai Ulang
            </button>
        </div>
    @endif

    {{-- Step 4: Success Message --}}
    @if($currentStep === 'success')
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 48px; color: #48bb78; margin-bottom: 20px;">
                <i class="fa fa-check-circle"></i>
            </div>
            <h3 style="color: #2d3748; margin-bottom: 10px;">Password Berhasil Direset!</h3>
            <p style="color: #718096; margin-bottom: 20px;">Anda akan diarahkan ke halaman login dalam beberapa detik...</p>
            
            <div style="width: 100%; background-color: #e2e8f0; border-radius: 10px; height: 4px; margin-top: 20px;">
                <div style="background-color: #667eea; height: 4px; border-radius: 10px; animation: progress 3s ease-in-out;"></div>
            </div>

            <div style="margin-top: 20px;">
                <a href="{{ route('login') }}" class="login100-form-btn" style="display: inline-block; text-decoration: none; padding: 12px 24px;">
                    Login Sekarang
                </a>
            </div>
        </div>
    @endif
</form>

{{-- Styles --}}
<style>
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
        font-size: 16px;
        z-index: 10;
        padding: 5px;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #333;
    }

    .wrap-input100 {
        position: relative;
    }

    /* Progress animation for success page */
    @keyframes progress {
        from { width: 0%; }
        to { width: 100%; }
    }

    /* Loading button style */
    .loading-btn {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Alert styles */
    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #fee2e2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .alert-success {
        background-color: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    /* Step indicator styles */
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 30px;
    }

    .step {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        color: white;
        transition: all 0.3s ease;
    }

    .step.active {
        background-color: #667eea;
    }

    .step.completed {
        background-color: #48bb78;
    }

    .step.inactive {
        background-color: #e2e8f0;
        color: #718096;
    }

    .step-connector {
        width: 50px;
        height: 2px;
        background-color: #e2e8f0;
        transition: all 0.3s ease;
    }

    .step-connector.completed {
        background-color: #48bb78;
    }

    /* Button link styles */
    .btn-link:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        color: #a0aec0 !important;
    }
</style>

{{-- Scripts --}}
@if($currentStep === 'verification')
<script>
    document.addEventListener('livewire:init', () => {
        // Countdown timer
        let timeLeft = @js($timeLeft);
        const countdownElement = document.getElementById('countdown');
        
        if (timeLeft > 0 && countdownElement) {
            const timer = setInterval(() => {
                timeLeft--;
                countdownElement.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    @this.call('handleOtpExpired');
                }
            }, 1000);
        }

        // Handle redirect after delay
        Livewire.on('redirect-after-delay', (event) => {
            setTimeout(() => {
                window.location.href = event.url;
            }, event.delay);
        });
    });
</script>
@endif

@if($currentStep === 'success')
<script>
    document.addEventListener('livewire:init', () => {
        // Handle redirect after delay
        Livewire.on('redirect-after-delay', (event) => {
            setTimeout(() => {
                window.location.href = event.url;
            }, event.delay);
        });
    });
</script>
@endif
</div>