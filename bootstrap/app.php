<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->prefix('admin')->name('admin.')->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.can' => \App\Http\Middleware\AdminCan::class,
            'admin.active' => \App\Http\Middleware\EnsureAdminIsActive::class,
            'honeypot' => \App\Http\Middleware\Honeypot::class,
        ]);

        // Unauthenticated admin requests go to the admin login page.
        $middleware->redirectGuestsTo(fn ($request) => route('admin.login'));
        $middleware->redirectUsersTo(fn ($request) => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
