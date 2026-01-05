<div wire:id="{{ $this->getId() }}">
    <span class="login100-form-title">
        Reset Password
    </span>

    {{-- Error Message --}}
    @if($errorMessage)
        <div class="alert-error" style="background-color: #fee2e2; border: 1px solid #fecaca; color: #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
            {{ $errorMessage }}
        </div>
    @endif

    {{-- Success Message --}}
    @if($successMessage)
        <div class="alert-success" style="background-color: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
            {{ $successMessage }}
        </div>
    @endif

    {{-- Step 1: Email Input --}}
    @if($currentStep === 'email')
        <form wire:submit.prevent="sendResetEmail">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 48px; color: #667eea; margin-bottom: 15px;">
                    <i class="fa fa-envelope"></i>
                </div>
                <p style="color: #718096; margin-bottom: 5px;">Masukkan email Anda untuk reset password</p>
            </div>

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

            {{-- TURNSTILE - Only show in production environment --}}
            @if(!app()->environment('local'))
                <div class="mb-3" style="margin-bottom: 20px;">
                    <div 
                        id="turnstile-container"
                        class="cf-turnstile" 
                        data-theme="light"
                        style="display: flex; justify-content: center;"
                    ></div>
                    @error('turnstileResponse')
                        <div style="color: #e74c3c; font-size: 12px; margin-top: 5px; margin-bottom: 15px; text-align: center;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

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
        </form>
    @endif

    {{-- Step 2: OTP Verification --}}
    @if($currentStep === 'verification')
        <form wire:submit.prevent="verifyOtp">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="font-size: 48px; color: #667eea; margin-bottom: 15px;">
                    <i class="fa fa-key"></i>
                </div>
                <p style="color: #718096; margin-bottom: 5px;">Masukkan kode verifikasi</p>
                <p style="color: #718096; font-size: 12px;">Kode telah dikirim ke <strong>{{ session('reset_email') }}</strong></p>
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
                    @livewire('auth.otp-timer', ['timeLeft' => $timeLeft, 'email' => session('reset_email', '')])
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
        </form>
    @endif

    {{-- Step 3: Password Reset --}}
    @if($currentStep === 'reset')
        <form wire:submit.prevent="resetPassword">
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
                    <span wire:loading.remove">Reset Password</span>
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
        </form>
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
        background-color: rgba(226, 232, 240, 0.8);
        color: #718096;
    }

    .step.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: scale(1.2);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .step.completed {
        background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(0, 210, 211, 0.3);
    }

    .step-connector {
        width: 60px;
        height: 4px;
        background-color: rgba(226, 232, 240, 0.6);
        border-radius: 2px;
        transition: all 0.4s ease;
    }

    .step-connector.completed {
        background: linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%);
        box-shadow: 0 2px 10px rgba(0, 210, 211, 0.3);
    }
</style>

@push('scripts')
@if(!app()->environment('local'))
{{-- Include Turnstile Service --}}
<script src="{{ asset('js/turnstile-service.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const SITE_KEY = '{{ $siteKey ?? "" }}';
        
        // Initialize Turnstile
        TurnstileService.init(SITE_KEY, '#turnstile-container', {
            callback: function(token) {
                if (window.Livewire) {
                    const el = document.querySelector('[wire\\:id]');
                    if (el) {
                        window.Livewire.find(el.getAttribute('wire:id'))
                            .set('turnstileResponse', token);
                    }
                }
            }
        });

        // Handle Livewire updates
        document.addEventListener('livewire:updated', function(event) {
            const container = document.querySelector('#turnstile-container');
            
            if (container) {
                // Clean existing widget first
                TurnstileService.cleanup('#turnstile-container');
                
                // Re-initialize after cleanup
                setTimeout(() => {
                    TurnstileService.init(SITE_KEY, '#turnstile-container', {
                        callback: function(token) {
                            if (window.Livewire) {
                                const el = document.querySelector('[wire\\:id]');
                                if (el) {
                                    window.Livewire.find(el.getAttribute('wire:id'))
                                        .set('turnstileResponse', token);
                                }
                            }
                        }
                    });
                }, 200);
            }
        });
    });
</script>
@endif
@endpush
</div>