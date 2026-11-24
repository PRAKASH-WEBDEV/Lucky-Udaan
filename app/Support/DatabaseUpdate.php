<?php

namespace App\Support;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Lets a Super Admin apply new database migrations from the dashboard
 * (needed on Vercel, where there is no terminal to run `php artisan migrate`).
 */
class DatabaseUpdate
{
    /** Names of migration files that have not been run on this database yet. */
    public static function pending(): array
    {
        try {
            $files = collect(glob(database_path('migrations/*.php')))->map(fn ($f) => basename($f, '.php'));
            $ran = DB::table('migrations')->pluck('migration');

            return $files->diff($ran)->values()->all();
        } catch (\Throwable) {
            return [];
        }
    }

    /** Run pending migrations; returns the console output. */
    public static function run(): string
    {
        Artisan::call('migrate', ['--force' => true]);

        return trim(Artisan::output());
    }
}
