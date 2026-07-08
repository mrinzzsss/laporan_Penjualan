<?php

use App\Http\Middleware\EnsureUserIsAuthenticated;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
            'guest.redirect' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role' => \App\Http\Middleware\CheckRole::class, // tambahkan baris ini
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
