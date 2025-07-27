<?php   

namespace App\Livewire\Auth;

use Livewire\Component;

class OtpTimer extends Component
{
    public int $timeLeft;
    public string $email;
    public bool $expired = false;

    public function mount(int $timeLeft, string $email = '')
    {
        $this->timeLeft = $timeLeft;
        $this->email = $email;
        $this->expired = $timeLeft <= 0;
    }

    public function render()
    {
        return view('livewire.auth.otp-timer');
    }
}