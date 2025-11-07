<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return redirect()->route('login')->with([
                'toast' => [
                    'type' => 'error',
                    'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
                ]
            ]);
        }
        return $next($request);
    }
}
