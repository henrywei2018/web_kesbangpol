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

            {{-- Password - NO wire:model on password toggle --}}
            <div class="wrap-input100 validate-input @error('password') has-error @enderror" style="position: relative;">
                <input class="input100" type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password" placeholder="Password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
                {{-- Use Alpine.js instead of Livewire for password toggle to avoid DOM updates --}}
                <span class="password-toggle" 
                      x-data="{ show: @entangle('showPassword') }" 
                      @click="show = !show; $wire.set('showPassword', show, false)">
                    <i class="fa" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </span>
            </div>
            @error('password')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Password Confirmation - Same Alpine.js approach --}}
            <div class="wrap-input100 validate-input @error('password_confirmation') has-error @enderror" style="position: relative;">
                <input class="input100" type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" wire:model="password_confirmation" placeholder="Konfirmasi Password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
                <span class="password-toggle" 
                      x-data="{ showConf: @entangle('showPasswordConfirmation') }" 
                      @click="showConf = !showConf; $wire.set('showPasswordConfirmation', showConf, false)">
                    <i class="fa" :class="showConf ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </span>
            </div>
            @error('password_confirmation')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- TURNSTILE - With better preservation logic --}}
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

{{-- Include Alpine.js for password toggle --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let turnstileInstance = null;
        const SITE_KEY = '{{ $siteKey ?? "" }}';
        
        console.log('🔧 Register Turnstile Debug:', {
            siteKey: SITE_KEY,
            environment: '{{ app()->environment() }}',
            hasContainer: !!document.querySelector('#turnstile-container')
        });

        function initTurnstile() {
            const container = document.querySelector('#turnstile-container');
            if (!container) {
                console.error('🔧 Register: Turnstile container not found');
                return;
            }

            if (!SITE_KEY) {
                console.error('🔧 Register: No site key available');
                container.innerHTML = '<div style="color: red; font-size: 12px;">No Turnstile Site Key</div>';
                return;
            }

            try {
                // Clean up existing instance
                if (turnstileInstance) {
                    console.log('🔧 Register: Removing existing Turnstile instance');
                    turnstile.remove(turnstileInstance);
                    turnstileInstance = null;
                }
                
                // Clear container
                container.innerHTML = '';
                
                console.log('🔧 Register: Creating Turnstile widget...');
                
                // Create new instance
                turnstileInstance = turnstile.render('#turnstile-container', {
                    sitekey: SITE_KEY,
                    theme: 'light',
                    callback: function(token) {
                        console.log('🔧 Register: Turnstile callback received token');
                        if (window.Livewire) {
                            const el = document.querySelector('[wire\\:id]');
                            if (el) {
                                window.Livewire.find(el.getAttribute('wire:id'))
                                    .set('turnstileResponse', token);
                            }
                        }
                    },
                    'error-callback': function(error) {
                        console.error('🔧 Register: Turnstile error:', error);
                    },
                    'expired-callback': function() {
                        console.log('🔧 Register: Turnstile expired, reinitializing...');
                        setTimeout(initTurnstile, 1000);
                    }
                });
                
                console.log('🔧 Register: Turnstile instance created:', turnstileInstance);
                
            } catch (error) {
                console.error('🔧 Register: Turnstile initialization error:', error);
                container.innerHTML = '<div style="color: red; font-size: 12px;">Turnstile Init Error</div>';
            }
        }

        // Wait for Turnstile API to load
        window.onTurnstileLoad = function() {
            console.log('🔧 Register: Turnstile API loaded via callback');
            initTurnstile();
        };

        // Also try immediate initialization if script already loaded
        if (typeof turnstile !== 'undefined') {
            console.log('🔧 Register: Turnstile API already available');
            initTurnstile();
        } else {
            console.log('🔧 Register: Waiting for Turnstile API to load...');
        }

        // IMPROVED: Handle Livewire updates more intelligently
        document.addEventListener('livewire:updated', function(event) {
            console.log('🔧 Register: Livewire updated, checking Turnstile...');
            
            // Only reinitialize if we're on registration step and container exists
            const container = document.querySelector('#turnstile-container');
            const isRegistrationStep = document.querySelector('form[wire\\:submit\\.prevent="register"]');
            
            if (container && isRegistrationStep && typeof turnstile !== 'undefined') {
                // Check if Turnstile widget is missing
                const hasWidget = container.querySelector('.cf-turnstile iframe') || 
                                container.querySelector('.cf-turnstile > div');
                
                if (!hasWidget) {
                    console.log('🔧 Register: Turnstile widget missing, reinitializing...');
                    setTimeout(initTurnstile, 100);
                }
            }
        });

        // Handle Livewire navigate (for SPA mode)
        document.addEventListener('livewire:navigated', function() {
            if (typeof turnstile !== 'undefined') {
                setTimeout(initTurnstile, 100);
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (turnstileInstance) {
                try {
                    turnstile.remove(turnstileInstance);
                } catch (error) {
                    console.error('🔧 Register: Turnstile cleanup error:', error);
                }
            }
        });
    });
</script>

<!-- Load Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad" async defer></script>
@endpush