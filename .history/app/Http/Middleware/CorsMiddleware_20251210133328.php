<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request)
            ->header('Access-Control-Allow-Origin', '*') // Cho phép mọi domain truy cập
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') // Cho phép các phương thức HTTP
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization') // Cho phép các headers
            ->header('Access-Control-Allow-Credentials'
