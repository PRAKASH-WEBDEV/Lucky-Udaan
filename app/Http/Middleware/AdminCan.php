<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Usage: ->middleware('admin.can:donations')
 * Blocks deactivated admins and admins whose role lacks the module.
 */
class AdminCan
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $admin = $request->user('admin');

        if (! $admin || ! $admin->can('module', $module)) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
