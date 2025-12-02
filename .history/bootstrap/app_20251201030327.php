<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Đăng ký middleware alias
        $middleware->alias([
            'jwt.web' => \App\Http\Middleware\JwtWebMiddleware::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'admin' => \App\Http\Middleware\CheckAdmin::class,
        ]);
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\ForceJsonResponse::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Http\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Http\Middleware\SetServerEncoding::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
