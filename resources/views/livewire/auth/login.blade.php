<div wire:id="{{ $this->getId() }}">
    <span class="login100-form-title">
        Masuk
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

    {{-- Login Form --}}
    @if($currentStep === 'login')
        <form wire:submit.prevent="login">
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

            <div class="wrap-input100 validate-input @error('password') has-error @enderror">
                <input class="input100" type="password" wire:model="password" placeholder="Password">
                <span class="focus-input100"></span>
                <span class="symbol-input100">
                    <i class="fa fa-lock" aria-hidden="true"></i>
                </span>
            </div>
            @error('password')
                <div style="color: #e74c3c; font-size: 12px; margin-top: -10px; margin-bottom: 15px;">
                    {{ $message }}
                </div>
            @enderror

            {{-- Remember Me --}}
            <div class="contact100-form-checkbox">
                <input class="input-checkbox100" id="remember" type="checkbox" wire:model="remember">
                <label class="label-checkbox100" for="remember">
                    Ingat saya
                </label>
            </div>

            {{-- TURNSTILE - With wire:ignore to prevent DOM updates --}}
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
                    <span wire:loading.remove>Masuk</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>

            <div class="text-center p-t-12">
                <span class="txt1">Lupa </span>
                <a class="txt2" href="{{ route('forgot-password') }}">Password?</a>
            </div>

            <div class="text-center p-t-136">
                <a class="txt2" href="{{ route('register') }}">
                    Buat Akun Baru
                    <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                </a>
            </div>
        </form>
    @endif

    {{-- Email Verification Step --}}
    @if($currentStep === 'email_verification')
        <form wire:submit.prevent="verifyEmail">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="color: #333; margin-bottom: 10px;">Verifikasi Email</h3>
                <p style="color: #666; font-size: 14px; margin-bottom: 10px;">
                    Email Anda belum terverifikasi. Kode verifikasi telah dikirim ke:
                </p>
                <strong style="color: #007bff;">{{ session('verify_email') }}</strong>
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
                    @livewire('auth.otp-timer', ['timeLeft' => $timeLeft, 'email' => session('verify_email', '')])
                </div>
            @endif

            <div class="container-login100-form-btn">
                <button class="login100-form-btn" type="submit" 
                        wire:loading.class="loading-btn" 
                        wire:loading.attr="disabled">
                    <span wire:loading.remove>Verifikasi Email</span>
                    <span wire:loading>Memverifikasi...</span>
                </button>
            </div>

            <div class="text-center p-t-12">
                <button type="button" class="btn-link" wire:click="resendEmailVerification" 
                        {{ !$canResend ? 'disabled' : '' }}>
                    Kirim Ulang Kode
                </button>
            </div>

            <div class="text-center p-t-24">
                <button type="button" class="btn-link" wire:click="backToLogin">
                    Kembali ke Login
                </button>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let turnstileInstance = null;
        const SITE_KEY = '{{ $siteKey ?? "" }}';
        
        console.log('🔧 Login Turnstile Debug:', {
            siteKey: SITE_KEY,
            environment: '{{ app()->environment() }}',
            hasContainer: !!document.querySelector('#turnstile-container')
        });

        function initTurnstile() {
            const container = document.querySelector('#turnstile-container');
            if (!container) {
                console.error('🔧 Login: Turnstile container not found');
                return;
            }

            if (!SITE_KEY) {
                console.error('🔧 Login: No site key available');
                container.innerHTML = '<div style="color: red; font-size: 12px;">No Turnstile Site Key</div>';
                return;
            }

            try {
                // Clean up existing instance
                if (turnstileInstance) {
                    console.log('🔧 Login: Removing existing Turnstile instance');
                    turnstile.remove(turnstileInstance);
                    turnstileInstance = null;
                }
                
                // Clear container
                container.innerHTML = '';
                
                console.log('🔧 Login: Creating Turnstile widget...');
                
                // Create new instance
                turnstileInstance = turnstile.render('#turnstile-container', {
                    sitekey: SITE_KEY,
                    theme: 'light',
                    callback: function(token) {
                        console.log('🔧 Login: Turnstile callback received token');
                        if (window.Livewire) {
                            const el = document.querySelector('[wire\\:id]');
                            if (el) {
                                window.Livewire.find(el.getAttribute('wire:id'))
                                    .set('turnstileResponse', token);
                            }
                        }
                    },
                    'error-callback': function(error) {
                        console.error('🔧 Login: Turnstile error:', error);
                    },
                    'expired-callback': function() {
                        console.log('🔧 Login: Turnstile expired, reinitializing...');
                        setTimeout(initTurnstile, 1000);
                    }
                });
                
                console.log('🔧 Login: Turnstile instance created:', turnstileInstance);
                
            } catch (error) {
                console.error('🔧 Login: Turnstile initialization error:', error);
                container.innerHTML = '<div style="color: red; font-size: 12px;">Turnstile Init Error</div>';
            }
        }

        // Wait for Turnstile API to load
        window.onTurnstileLoad = function() {
            console.log('🔧 Login: Turnstile API loaded via callback');
            initTurnstile();
        };

        // Also try immediate initialization if script already loaded
        if (typeof turnstile !== 'undefined') {
            console.log('🔧 Login: Turnstile API already available');
            initTurnstile();
        } else {
            console.log('🔧 Login: Waiting for Turnstile API to load...');
        }

        // IMPROVED: Handle Livewire updates more intelligently  
        document.addEventListener('livewire:updated', function(event) {
            console.log('🔧 Login: Livewire updated, checking Turnstile...');
            
            // Only reinitialize if we're on login step and container exists
            const container = document.querySelector('#turnstile-container');
            const isLoginStep = document.querySelector('form[wire\\:submit\\.prevent="login"]');
            
            if (container && isLoginStep && typeof turnstile !== 'undefined') {
                // Check if Turnstile widget is missing
                const hasWidget = container.querySelector('.cf-turnstile iframe') || 
                                container.querySelector('.cf-turnstile > div');
                
                if (!hasWidget) {
                    console.log('🔧 Login: Turnstile widget missing, reinitializing...');
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
                    console.error('🔧 Login: Turnstile cleanup error:', error);
                }
            }
        });
    });
</script>

<!-- Load Turnstile API -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onTurnstileLoad" async defer></script>
@endpush