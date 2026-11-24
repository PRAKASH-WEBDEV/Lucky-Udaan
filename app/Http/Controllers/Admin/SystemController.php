<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DatabaseUpdate;
use Illuminate\Http\Request;

/** Super Admin maintenance actions. */
class SystemController extends Controller
{
    public function updateDatabase(Request $request)
    {
        abort_unless($request->user('admin')?->isSuperAdmin(), 403);

        $pending = DatabaseUpdate::pending();
        if (! $pending) {
            return back()->with('success', 'The database is already up to date.');
        }

        try {
            DatabaseUpdate::run();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Database update failed: '.$e->getMessage());
        }

        return back()->with('success', 'Database updated ('.count($pending).' change'.(count($pending) > 1 ? 's' : '').' applied).');
    }
}
