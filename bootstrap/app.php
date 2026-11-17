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
            Route::middleware('web')->prefix('staff')->name('staff.')->group(base_path('routes/staff.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Vercel terminates TLS at its edge; trust its X-Forwarded-* headers.
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);

        $middleware->alias([
            'admin.can' => \App\Http\Middleware\AdminCan::class,
            'admin.active' => \App\Http\Middleware\EnsureAdminIsActive::class,
            'honeypot' => \App\Http\Middleware\Honeypot::class,
        ]);

        // Unauthenticated requests go to the matching login page (staff portal or admin dashboard).
        $middleware->redirectGuestsTo(fn ($request) => $request->is('staff', 'staff/*') ? route('staff.login') : route('admin.login'));
        $middleware->redirectUsersTo(fn ($request) => $request->is('staff', 'staff/*') ? route('staff.dashboard') : route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
