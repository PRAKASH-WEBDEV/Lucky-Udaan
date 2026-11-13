<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Copy bundled brand + sample photos into public/uploads/seed.
        File::ensureDirectoryExists(public_path('uploads/seed'));
        File::copyDirectory(database_path('seeders/assets'), public_path('uploads/seed'));

        $this->call([
            AdminSeeder::class,
            SettingSeeder::class,
            PageSeeder::class,
            ProgramSeeder::class,
            ContentSeeder::class,
        ]);

        // Sample donors, donations, volunteers and enquiries — never in production.
        if (! app()->environment('production')) {
            $this->call(DemoActivitySeeder::class);
        }

        Cache::flush();
    }
}
