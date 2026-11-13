<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Program;
use App\Support\Seo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Seo::class, fn () => new Seo);

        // Shared hosting where public/ is copied into public_html (see README → Deploying).
        if ($publicPath = config('app.public_path')) {
            $this->app->usePublicPath($publicPath);
        }
    }

    public function boot(): void
    {
        // Behind Vercel's proxy PHP sees plain http; without this, asset and form
        // URLs are generated as http:// and browsers block them on the https page.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();

        // Module-level authorisation for the dashboard: Gate::allows('module', 'donations').
        Gate::define('module', fn ($user, string $module) => $user instanceof Admin && $user->canAccess($module));

        // Public form submissions: 5 per minute per IP.
        RateLimiter::for('forms', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Programs for the mega menu and footer (cached; cleared when a program changes).
        View::composer(['layouts.site', 'site.partials.*'], function ($view) {
            $view->with('navPrograms', Cache::remember('nav_programs', now()->addHour(), function () {
                try {
                    return Program::active()->ordered()->get(['id', 'title', 'slug', 'icon']);
                } catch (\Throwable) {
                    return collect();
                }
            }));
        });
        Program::saved(fn () => Cache::forget('nav_programs'));
        Program::deleted(fn () => Cache::forget('nav_programs'));
    }
}
