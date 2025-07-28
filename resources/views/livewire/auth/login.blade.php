   
<div>
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
                        {{ !$canResend ? 'disabled' : '' }}
                        style="background: none; border: none; color: #007bff; text-decoration: underline; cursor: pointer;">
                    {{ $canResend ? 'Kirim Ulang Kode' : 'Tunggu untuk kirim ulang' }}
                </button>
            </div>

            <div class="text-center p-t-20">
                <button type="button" class="btn-link" wire:click="backToLogin"
                        style="background: none; border: none; color: #666; text-decoration: underline; cursor: pointer;">
                    ← Kembali ke Login
                </button>
            </div>
        </form>
    @endif
</div>