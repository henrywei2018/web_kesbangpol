<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BasePage;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LoginResponse;

class Login extends BasePage
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($response && session()->has('verify_email_hash')) {
            $loggedInUser = Filament::auth()->user();
            $hash = session()->pull('verify_email_hash');
            
            if ($loggedInUser && hash_equals($hash, sha1($loggedInUser->email))) {
                $loggedInUser->markEmailAsVerified();
            }
        }

        return $response;
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()->label('Email'),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }
    
    public function getHeading(): string | Htmlable
    {
        return __('Selamat Datang');
    }
}