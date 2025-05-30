<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'jwt.check' => \Tymon\JWTAuth\Http\Middleware\Check::class,
    'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
    'jwt.verify' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
    'permission' => PermissionMiddleware::class,
    'role' => RoleMiddleware::class,
    
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
