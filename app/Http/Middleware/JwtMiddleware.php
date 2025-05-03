<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = substr($authHeader, 7);
        
        try {
            $decoded = JWT::decode($token, new Key(config('app.jwt-key'), 'HS256'));

            $user = DB::selectOne("SELECT * FROM user WHERE id = ?", [$decoded->sub]);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }

        return $next($request);
    }
}
