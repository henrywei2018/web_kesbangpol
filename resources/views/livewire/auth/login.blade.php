<form class="login100-form validate-form" wire:submit="login">
    <span class="login100-form-title">
        Masuk
    </span>

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

    <div class="wrap-input100 validate-input @error('email') has-error @enderror">
        <input class="input100" type="email" wire:model="email" placeholder="Email" 
               value="{{ old('email') }}">
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

    <div class="text-center p-t-12" style="margin-bottom: 15px;">
        <label style="display: flex; align-items: center; justify-content: center; font-size: 14px;">
            <input type="checkbox" wire:model="remember" style="margin-right: 8px;">
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
        <span class="txt1">Lupa</span>
        <a class="txt2" href="{{ route('forgot-password') }}">Username / Password?</a>
    </div>

    <div class="text-center p-t-136">
        <a class="txt2" href="{{ route('register') }}">
            Buat Akun Baru
            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
        </a>
    </div>
</form>