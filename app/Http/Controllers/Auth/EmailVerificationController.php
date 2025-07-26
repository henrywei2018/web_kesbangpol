<?php

namespace App\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class EmailVerificationController extends Controller
{
   public function __invoke($hash): RedirectResponse
   {
       if (!Filament::auth()->check()) {
           session()->put('verify_email_hash', $hash);
           return redirect()->route('filament.admin.auth.login');
       }

       $user = Filament::auth()->user();
       if (!$user || !hash_equals($hash, sha1($user->email))) {
           return redirect()->to(Filament::getEmailVerificationPromptUrl());
       }

       if ($user->hasVerifiedEmail()) {
           return redirect()->to(Filament::getHomeUrl());
       }

       $user->markEmailAsVerified();
       
       return redirect()->to(Filament::getHomeUrl());
   }
}