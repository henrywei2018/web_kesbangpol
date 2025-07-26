<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Turnstile extends Component
{
    public $turnstileResponse;

    public function validateTurnstile()
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $this->turnstileResponse,
            'remoteip' => request()->ip(),
        ]);

        return $response->json('success') ?? false;
    }

    public function render()
    {
        return view('livewire.turnstile', [
            'siteKey' => config('services.turnstile.sitekey'),
        ]);
    }
}
