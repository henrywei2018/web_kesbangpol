// resources/views/livewire/registration-wizard.blade.php
<div>
    <form wire:submit="create">
        {{ $this->form }}
        
        @if($currentStep === 'verification')
            <div class="mt-4">
                @livewire('otp-timer', ['email' => $data['email'] ?? ''])
                
                <button 
                    type="button" 
                    wire:click="resendOtp"
                    class="mt-2 text-sm text-blue-600 hover:text-blue-800"
                >
                    Kirim Ulang Kode
                </button>
            </div>
        @endif
    </form>
</div>