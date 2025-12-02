<?php

namespace App\Http\Middleware;

use Closure;

class ForceJsonResponse
{
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
