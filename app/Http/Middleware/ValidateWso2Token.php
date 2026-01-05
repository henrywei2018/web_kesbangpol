<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class ValidateWso2Token
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            
            // Get WSO2 public key from config
            $publicKey = config('services.wso2.public_key');
            
            // Verify token
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            
            // Add decoded token to request
            $request->attributes->set('token_data', $decoded);
            
            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
        }
    }
}