<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class LogoutController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Store the guard being used
        $guard = Auth::getDefaultDriver();
        
        // Logout from all guards to be safe
        Auth::guard('web')->logout();
        
        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear any OTP sessions
        $request->session()->forget([
            'registration_data',
            'otp_email',
            'reset_email',
            'verify_email_hash'
        ]);
        
        // Redirect to login page
        return redirect()->route('login')->with('status', 'Successfully logged out.');
    }
}