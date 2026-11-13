<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

/**
 * Spam protection for public forms without a captcha:
 *  - a hidden "website" field that humans never fill in;
 *  - an encrypted render timestamp: forms posted in under 3 seconds are bots.
 * Bots get a fake success message so they don't learn anything.
 */
class Honeypot
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post')) {
            $tooFast = true;
            try {
                $tooFast = (time() - (int) Crypt::decryptString((string) $request->input('_hp_t'))) < 3;
            } catch (\Throwable) {
            }

            if (filled($request->input('website')) || $tooFast) {
                return back()->with('success', 'Thank you! Your submission has been received.');
            }
        }

        return $next($request);
    }
}
