<div wire:id="{{ $this->getId() }}">
    <span class="login100-form-title">
        Daftar Akun
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

    {{-- Registration Form --}}
    @if($currentStep === 'registration')
        <form wire:submit.prevent="register">
            {{-- First Name --}}
            <div class="wrap-input100 validate-input @error('firstname') has-error @enderror">
                <input class="input100" type="text" wire:model.live="firstname" placeholder="Nama Depan">
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

            {{-- Last Name --}}
            <div class="wrap-input100 validate-input @error('lastname') has-error @enderror">
                <input class="input100" type="text" wire:model.live="lastname" placeholder="Nama Belakang">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </span>
            </div>
            @error('lastname')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Generated Username Display --}}
            @if($generated_username)
                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 12px; color: #666;">
                    <i class="fa fa-info-circle" style="color: #007bff;"></i> Username yang akan dibuat: <strong>{{ $generated_username }}</strong>
                </div>
            @endif

            {{-- Email --}}
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

            {{-- Phone Number --}}
            <div class="wrap-input100 validate-input @error('no_telepon') has-error @enderror">
                <input class="input100" type="tel" wire:model="no_telepon" placeholder="Nomor HP (08xxxxxxxxx)">
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

            {{-- Password --}}
            <div class="wrap-input100 validate-input @error('password') has-error @enderror" style="position: relative;" x-data="{ show: false }">
                <input class="input100" type="password" wire:model="password" placeholder="Password" x-ref="passwordInput" autocomplete="new-password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
                {{-- Use Alpine.js for password toggle --}}
                <span class="password-toggle" 
                      @click="show = !show; $refs.passwordInput.type = show ? 'text' : 'password'">
                    <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </span>
            </div>
            @error('password')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Password Confirmation --}}
            <div class="wrap-input100 validate-input @error('password_confirmation') has-error @enderror" style="position: relative;" x-data="{ showConf: false }">
                <input class="input100" type="password" wire:model="password_confirmation" placeholder="Konfirmasi Password" x-ref="passwordConfInput" autocomplete="new-password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
                <span class="password-toggle" 
                      @click="showConf = !showConf; $refs.passwordConfInput.type = showConf ? 'text' : 'password'">
                    <i class="fa" :class="showConf ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </span>
            </div>
            @error('password_confirmation')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- TURNSTILE - Only show in production environment --}}
            @if(!app()->environment('local'))
                <div class="mb-3" style="margin-bottom: 20px;" wire:ignore>
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
                    <span wire:loading.remove>Daftar</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>

            <div class="text-center p-t-12">
                <a class="txt2" href="{{ route('login') }}">
                    Sudah punya akun? Masuk
                    <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                </a>
            </div>
        </form>
    @endif

    {{-- OTP Verification Step --}}
    @if($currentStep === 'verification')
        <form wire:submit.prevent="verifyOtp">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #333; margin-bottom: 10px;">Verifikasi Email</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                    Kode verifikasi telah dikirim ke:
                </p>
                <strong style="color: #007bff;">{{ session('otp_email') }}</strong>
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
                    Kirim Ulang Kode
                </button>
            </div>

            <div class="text-center p-t-24">
                <button type="button" class="btn-link" wire:click="backToRegistration">
                    Kembali ke Pendaftaran
                </button>
            </div>
        </form>
    @endif

    {{-- Success Step --}}
    @if($currentStep === 'success')
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 48px; color: #48bb78; margin-bottom: 20px;">
                <i class="fa fa-check-circle"></i>
            </div>
            <h3 style="color: #2d3748; margin-bottom: 10px;">Registrasi Berhasil!</h3>
            <p style="color: #718096; margin-bottom: 20px;">Akun Anda telah berhasil dibuat. Anda akan diarahkan ke halaman login dalam beberapa detik...</p>
            
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
</div>

@push('scripts')
{{-- Include Alpine.js for password toggle only if not already loaded --}}
<script>
if (typeof window.Alpine === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
    script.defer = true;
    document.head.appendChild(script);
}
</script>

@if(!app()->environment('local'))
{{-- Include Turnstile Service --}}
<script src="{{ asset('js/turnstile-service.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Check for conflicting scripts
        const conflictingScripts = document.querySelectorAll('script[src*="challenges.cloudflare.com/turnstile"]');
        if (conflictingScripts.length > 1) {
            console.warn('⚠️ Multiple Turnstile scripts detected:', conflictingScripts.length);
            conflictingScripts.forEach((script, i) => {
                console.log(`Script ${i+1}:`, script.src);
            });
        }
        
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
            },
            errorCallback: function(error) {
                // Error handled by service
            },
            expiredCallback: function() {
                // Expiry handled by service
            }
        });

        // Handle Livewire updates
        document.addEventListener('livewire:updated', function(event) {
            const container = document.querySelector('#turnstile-container');
            const isRegistrationStep = document.querySelector('form[wire\\:submit\\.prevent="register"]');
            
            if (container && isRegistrationStep) {
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