<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}