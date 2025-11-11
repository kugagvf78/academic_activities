<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;           
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException; 
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class JwtWebMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('jwt_token');

        if ($token) {
            try {
                JWTAuth::setToken($token);
                
                $user = JWTAuth::authenticate();
                
                if ($user) {
                    Auth::guard('api')->setUser($user);
                }
            } catch (TokenExpiredException $e) {
                Cookie::queue(Cookie::forget('jwt_token'));
            } catch (TokenInvalidException $e) {
                Cookie::queue(Cookie::forget('jwt_token'));
            } catch (JWTException $e) {
                Cookie::queue(Cookie::forget('jwt_token'));
            }
        }

        return $next($request);
    }
}