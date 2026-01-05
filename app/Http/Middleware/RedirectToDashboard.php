<?php
namespace App\Http\Middleware;

class RedirectToDashboard
{
    public function handle($request, $next)
    {
        if (request()->is('admin')) {
            return redirect()->to('/admin/panel');
        }
        return $next($request);
    }
}