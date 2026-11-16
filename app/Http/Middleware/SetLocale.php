<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** ?lang=hi / ?lang=en switches the site language (remembered in the session) for tr() texts. */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($lang = $request->query('lang'), ['en', 'hi'], true) && $request->hasSession()) {
            $request->session()->put('lang', $lang);
        }
        $locale = $request->hasSession() ? $request->session()->get('lang') : null;
        if (in_array($locale, ['en', 'hi'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
